<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'electricity_db2');
define('DB_PORT', 3307);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Updated base URL to match your actual folder path
define('BASE_URL',  'http://localhost/Electricity-billing-system-2nd-year-2nd-sem/public/');
define('ADMIN_URL', 'http://localhost/Electricity-billing-system-2nd-year-2nd-sem/public/admin/');
define('USER_URL',  'http://localhost/Electricity-billing-system-2nd-year-2nd-sem/public/user/');
?>
