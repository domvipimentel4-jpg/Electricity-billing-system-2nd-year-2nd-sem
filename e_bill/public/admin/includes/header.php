<?php
// ================================================
// Admin Header
// public/admin/includes/header.php
// ================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($page_title) ? $page_title . ' — ' : ''; ?>Electricity Billing Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body { overflow-x: hidden; }
    .sidebar {
        min-height: 100vh;
        width: 250px;
        background: #1e293b;
        position: fixed;
        top: 0; left: 0;
        z-index: 100;
        transition: all 0.3s;
    }
    .sidebar .nav-link {
        color: #94a3b8;
        padding: 10px 20px;
        border-radius: 8px;
        margin: 2px 10px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background: #334155;
        color: #ffffff;
    }
    .sidebar .nav-link i {
        width: 20px;
        margin-right: 8px;
    }
    .sidebar-brand {
        padding: 20px;
        border-bottom: 1px solid #334155;
        color: #ffffff;
        font-weight: 700;
        font-size: 1rem;
    }
    .main-content {
        margin-left: 250px;
        min-height: 100vh;
        background: #f1f5f9;
    }
    .topbar {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 99;
    }
    .page-content { padding: 24px; }
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }
    .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600;
        padding: 16px 20px;
    }
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        color: #ffffff;
        border: none;
    }
    .table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        font-weight: 600;
    }
    .table tbody tr:hover { background: #f8fafc; }
    .badge { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; }
    .btn { border-radius: 8px; font-size: 0.875rem; }
    .btn-sm { padding: 4px 12px; font-size: 0.8rem; }
  </style>
</head>
<body>