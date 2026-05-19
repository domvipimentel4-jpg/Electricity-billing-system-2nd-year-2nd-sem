<?php
// ================================================
// Download Receipt
// public/user/download_receipt.php
// ================================================

define('REQUIRED_ROLE', 'user');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

if (!isset($_GET['uuid']) || empty(trim($_GET['uuid']))) {
    http_response_code(400);
    echo 'Receipt identifier is missing.';
    exit;
}

$uuid = trim($_GET['uuid']);
$payment = getPaymentByUuid($uuid);

if (!$payment || $payment['user_id'] !== $_SESSION['user_id']) {
    http_response_code(404);
    echo 'Receipt not found.';
    exit;
}

$bill = getBillById($payment['bill_id']);
if (!$bill) {
    http_response_code(404);
    echo 'The requested bill could not be loaded.';
    exit;
}

$customer_name = trim($payment['firstName'] . ' ' . $payment['lastname']);
$receipt_lines = [];
$receipt_lines[] = '========== Electricity Bill Payment Receipt ==========';
$receipt_lines[] = 'Receipt Number: ' . $payment['uuid'];
$receipt_lines[] = 'Payment Date: ' . date('Y-m-d H:i:s', strtotime($payment['payment_date']));
$receipt_lines[] = 'Payment Method: ' . ucfirst($payment['payment_method']);
$receipt_lines[] = '';
$receipt_lines[] = 'Customer Name: ' . $customer_name;
$receipt_lines[] = 'Meter Number: ' . $payment['meter_number'];
$receipt_lines[] = 'Email Address: ' . $payment['emailAddress'];
$receipt_lines[] = '';
$receipt_lines[] = 'Billing Date: ' . $bill['billing_date'];
$receipt_lines[] = 'Due Date: ' . $bill['due_date'];
$receipt_lines[] = 'kWh Consumed: ' . $bill['kwh_consumed'] . ' kWh';
$receipt_lines[] = 'Currency: Philippine Peso (PHP)';
$receipt_lines[] = 'Amount Paid: PHP ' . number_format($payment['amount_paid'], 2);
$receipt_lines[] = 'Bill Status: ' . ucfirst($bill['status']);
$receipt_lines[] = '';
$receipt_lines[] = 'Thank you for your payment.';
$receipt_lines[] = '=======================================================';

$filename = 'receipt-' . $payment['uuid'] . '.txt';

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Transfer-Encoding: binary');

echo implode(PHP_EOL, $receipt_lines);
exit;
