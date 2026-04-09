<?php
// ================================================
// Configuration
// app/config/config.php
// ================================================

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'electricity_db2');
define('DB_PORT', 3307);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

define('BASE_URL',  'http://localhost/pit/e_bill/public/');
define('ADMIN_URL', 'http://localhost/pit/e_bill/public/admin/');
define('USER_URL',  'http://localhost/pit/e_bill/public/user/');

// -----------------------------------------------
// Upload paths — points to the original app/uploads/
// folder from your project structure
// -----------------------------------------------
// Filesystem path for saving files (used by PHP)
define('UPLOADS_PATH', __DIR__ . '/../uploads/');

// Web-accessible URL for displaying files in browser
define('UPLOADS_URL',  'http://localhost/pit/e_bill/app/uploads/');
?>