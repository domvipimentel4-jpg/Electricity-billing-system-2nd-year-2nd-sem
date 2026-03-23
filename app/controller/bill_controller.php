<?php
// ================================================
// Bill Controller
// app/controller/bill_controller.php
// ================================================

require_once __DIR__ . '/../config/config.php';

// Get rate from settings
function getRate() {
    global $conn;
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'rate_per_kwh'");
    return floatval($result->fetch_assoc()['setting_value']);
}

// Get setting value
function getSetting($key) {
    global $conn;
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['setting_value'] : null;
}

// Get all bills with user info
function getAllBills() {
    global $conn;
    return $conn->query("
        SELECT b.*, 
               u.firstName, u.lastname, u.meter_number,
               CONCAT(u.firstName, ' ', u.lastname) AS full_name
        FROM bill b
        JOIN user u ON b.user_id = u.id
        ORDER BY b.dateCreated DESC
    ");
}

// Get bills by user ID
function getBillsByUser($user_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT * FROM bill
        WHERE user_id = ?
        ORDER BY billing_date DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Get single bill
function getBillById($bill_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT b.*, CONCAT(u.firstName, ' ', u.lastname) AS full_name,
               u.meter_number, u.emailAddress
        FROM bill b
        JOIN user u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Add new bill
function addBill($user_id, $kwh, $billing_date) {
    global $conn;

    // Check duplicate
    $check = $conn->prepare("SELECT id FROM bill WHERE user_id = ? AND billing_date = ?");
    $check->bind_param("is", $user_id, $billing_date);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        return ['success' => false, 'error' => 'This user already has a bill for that date.'];
    }

    $rate       = getRate();
    $amount_due = $kwh * $rate;
    $due_days   = getSetting('due_days') ?? 30;
    $due_date   = date('Y-m-d', strtotime($billing_date . ' + ' . $due_days . ' days'));
    $uuid       = generateUUID();

    $stmt = $conn->prepare("
        INSERT INTO bill (uuid, user_id, kwh_consumed, amount_due, billing_date, due_date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("siddss", $uuid, $user_id, $kwh, $amount_due, $billing_date, $due_date);

    if ($stmt->execute()) {
        return ['success' => true, 'amount' => $amount_due, 'rate' => $rate, 'due_date' => $due_date];
    } else {
        return ['success' => false, 'error' => 'Failed to add bill.'];
    }
}

// Pay bill
function payBill($bill_id, $user_id, $amount, $method) {
    global $conn;

    // Get bill
    $bill = getBillById($bill_id);
    if (!$bill || $bill['status'] === 'paid') {
        return ['success' => false, 'error' => 'Bill not found or already paid.'];
    }

    // Insert payment record
    $uuid = generateUUID();
    $stmt = $conn->prepare("
        INSERT INTO payment (uuid, bill_id, user_id, amount_paid, payment_method)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("siids", $uuid, $bill_id, $user_id, $amount, $method);
    $stmt->execute();

    // Update bill status
    $stmt2 = $conn->prepare("UPDATE bill SET status = 'paid' WHERE id = ?");
    $stmt2->bind_param("i", $bill_id);

    if ($stmt2->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Payment failed. Please try again.'];
    }
}

// Toggle bill status (admin)
function toggleBillStatus($bill_id, $current_status) {
    global $conn;
    $new_status = $current_status === 'unpaid' ? 'paid' : 'unpaid';
    $stmt = $conn->prepare("UPDATE bill SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $bill_id);
    if ($stmt->execute()) {
        return ['success' => true, 'status' => $new_status];
    }
    return ['success' => false];
}

// Update rate
function updateRate($new_rate) {
    global $conn;
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'rate_per_kwh'");
    $stmt->bind_param("s", $new_rate);
    if ($stmt->execute()) {
        return ['success' => true];
    }
    return ['success' => false, 'error' => 'Failed to update rate.'];
}

// Update setting
function updateSetting($key, $value) {
    global $conn;
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->bind_param("ss", $value, $key);
    return $stmt->execute();
}

// Get report summary
function getReportSummary() {
    global $conn;
    $data = [];

    $data['total_users']   = $conn->query("SELECT COUNT(*) AS c FROM user")->fetch_assoc()['c'];
    $data['total_bills']   = $conn->query("SELECT COUNT(*) AS c FROM bill")->fetch_assoc()['c'];
    $data['total_paid']    = $conn->query("SELECT COUNT(*) AS c FROM bill WHERE status = 'paid'")->fetch_assoc()['c'];
    $data['total_unpaid']  = $conn->query("SELECT COUNT(*) AS c FROM bill WHERE status = 'unpaid'")->fetch_assoc()['c'];
    $data['total_revenue'] = $conn->query("SELECT SUM(amount_paid) AS s FROM payment")->fetch_assoc()['s'] ?? 0;
    $data['rate']          = getRate();

    return $data;
}
?>