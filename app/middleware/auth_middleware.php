<?php
// ================================================
// Authentication Middleware
// app/middleware/auth_middleware.php
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check which type of user is required
// Usage: define('REQUIRED_ROLE', 'admin') or 'user' before including this file

$required_role = defined('REQUIRED_ROLE') ? REQUIRED_ROLE : 'admin';

if ($required_role === 'admin') {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
} elseif ($required_role === 'user') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}
?>