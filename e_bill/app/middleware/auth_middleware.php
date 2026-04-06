<?php
// ================================================
// Authentication Middleware
// app/middleware/auth_middleware.php
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$required_role = defined('REQUIRED_ROLE') ? REQUIRED_ROLE : 'admin';

if ($required_role === 'admin') {
    // Not logged in at all — redirect to login
    if (!isset($_SESSION['admin_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
    // Logged in as user trying to access admin — redirect to user dashboard
    if (isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
        header("Location: " . BASE_URL . "user/dashboard.php");
        exit();
    }

} elseif ($required_role === 'user') {
    // Not logged in at all — redirect to login
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
    // Logged in as admin trying to access user pages — redirect to admin dashboard
    if (isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "admin/dashboard.php");
        exit();
    }
}
?>