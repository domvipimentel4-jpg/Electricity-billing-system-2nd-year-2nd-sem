<?php
// ================================================
// Customer Controller
// app/controller/customer_controller.php
// ================================================

require_once __DIR__ . '/../config/config.php';

// Get all users
function getAllUsers() {
    global $conn;
    return $conn->query("
        SELECT u.*,
               COUNT(b.id) AS total_bills,
               SUM(CASE WHEN b.status = 'unpaid' THEN b.amount_due ELSE 0 END) AS total_unpaid
        FROM user u
        LEFT JOIN bill b ON u.id = b.user_id
        GROUP BY u.id
        ORDER BY u.dateCreated DESC
    ");
}

// Get single user by ID
function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Update user status (active/inactive)
function updateUserStatus($id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

// Update user profile
function updateUserProfile($id, $data) {
    global $conn;
    $stmt = $conn->prepare("
        UPDATE user SET
        firstName = ?, middleName = ?, lastname = ?,
        emailAddress = ?, contact_number = ?, date_of_birth = ?, street = ?, barangay = ?, city = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssssssssi",
        $data['firstName'],
        $data['middleName'],
        $data['lastname'],
        $data['email'],
        $data['contact_number'],
        $data['date_of_birth'],
        $data['street'],
        $data['barangay'],
        $data['city'],
        $id
    );
    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Failed to update profile.'];
    }
}

// Delete user
function deleteUser($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Get users dropdown
function getUsersDropdown() {
    global $conn;
    return $conn->query("
        SELECT id, firstName, lastname, meter_number
        FROM user
        ORDER BY firstName ASC
    ");
}
?>