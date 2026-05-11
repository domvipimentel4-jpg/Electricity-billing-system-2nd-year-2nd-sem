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
    /* ── CSS Variables — Light Mode defaults ── */
    :root {
      --sidebar-bg:        #1e293b;
      --sidebar-border:    #334155;
      --sidebar-link:      #94a3b8;
      --sidebar-hover-bg:  #2d3f55;
      --sidebar-hover-txt: #e2e8f0;
      --sidebar-active-bg: #334155;
      --sidebar-active-txt:#ffffff;
      --sidebar-accent:    #3b82f6;
      --sidebar-brand-txt: #ffffff;

      --topbar-bg:         #ffffff;
      --topbar-border:     #e2e8f0;
      --topbar-shadow:     rgba(0,0,0,0.04);
      --topbar-txt:        #1e293b;
      --topbar-muted:      #64748b;

      --page-bg:           #f1f5f9;
      --card-bg:           #ffffff;
      --card-border:       #f1f5f9;
      --card-shadow:       rgba(0,0,0,0.06);
      --card-shadow-hover: rgba(0,0,0,0.1);

      --table-head-bg:     #f8fafc;
      --table-head-txt:    #475569;
      --table-head-border: #e2e8f0;
      --table-hover-bg:    #f8fafc;

      --body-txt:          #1e293b;
      --muted-txt:         #64748b;

      --toggle-btn-txt:    #475569;
      --toggle-btn-hover:  #f1f5f9;
      --toggle-btn-active: #e2e8f0;
      --toggle-btn-htxt:   #1e293b;
    }

    /* ── Dark Mode overrides ── */
    body.dark-mode {
      --sidebar-bg:        #0f172a;
      --sidebar-border:    #1e293b;
      --sidebar-link:      #64748b;
      --sidebar-hover-bg:  #1e293b;
      --sidebar-hover-txt: #cbd5e1;
      --sidebar-active-bg: #1e293b;
      --sidebar-active-txt:#e2e8f0;
      --sidebar-accent:    #60a5fa;
      --sidebar-brand-txt: #e2e8f0;

      --topbar-bg:         #1e293b;
      --topbar-border:     #334155;
      --topbar-shadow:     rgba(0,0,0,0.2);
      --topbar-txt:        #e2e8f0;
      --topbar-muted:      #94a3b8;

      --page-bg:           #0f172a;
      --card-bg:           #1e293b;
      --card-border:       #334155;
      --card-shadow:       rgba(0,0,0,0.2);
      --card-shadow-hover: rgba(0,0,0,0.35);

      --table-head-bg:     #0f172a;
      --table-head-txt:    #94a3b8;
      --table-head-border: #334155;
      --table-hover-bg:    #263148;

      --body-txt:          #e2e8f0;
      --muted-txt:         #94a3b8;

      --toggle-btn-txt:    #94a3b8;
      --toggle-btn-hover:  #334155;
      --toggle-btn-active: #475569;
      --toggle-btn-htxt:   #e2e8f0;
    }

    /* ── Base ── */
    body {
      overflow-x: hidden;
      background: var(--page-bg);
      color: var(--body-txt);
      transition: background 0.3s ease, color 0.3s ease;
    }

    /* ─────────────────────────────────────────
       SIDEBAR
    ───────────────────────────────────────── */
    .sidebar {
        min-height: 100vh;
        width: 250px;
        background: var(--sidebar-bg);
        position: fixed;
        top: 0; left: 0;
        z-index: 1000;
        overflow: hidden;
        transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    background 0.3s ease;
    }
    .sidebar:not(.collapsed) {
        box-shadow: 4px 0 24px rgba(0,0,0,0.25);
    }
    .sidebar.collapsed { width: 60px; }

    .sidebar-brand {
        padding: 20px;
        border-bottom: 1px solid var(--sidebar-border);
        color: var(--sidebar-brand-txt);
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        white-space: nowrap;
        min-height: 64px;
        transition: border-color 0.3s ease;
    }
    .sidebar-brand i {
        flex-shrink: 0;
        margin-right: 10px;
        font-size: 1.2rem;
        transition: margin 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar.collapsed .sidebar-brand { justify-content: center; padding: 20px 10px; }
    .sidebar.collapsed .sidebar-brand i { margin-right: 0; }

    .sidebar .nav-link {
        color: var(--sidebar-link);
        padding: 10px 16px;
        border-radius: 8px;
        margin: 2px 10px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        white-space: nowrap;
        position: relative;
        transition: background 0.2s ease, color 0.2s ease,
                    padding 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    margin 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar .nav-link i {
        width: 20px; font-size: 1rem; flex-shrink: 0;
        margin-right: 10px;
        transition: margin 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar.collapsed .nav-link { justify-content: center; padding: 10px 0; margin: 2px 6px; }
    .sidebar.collapsed .nav-link i { margin-right: 0; }

    .sidebar .nav-link:hover { background: var(--sidebar-hover-bg); color: var(--sidebar-hover-txt); }
    .sidebar .nav-link.active {
        background: var(--sidebar-active-bg);
        color: var(--sidebar-active-txt);
        box-shadow: inset 3px 0 0 var(--sidebar-accent);
    }

    .sidebar .nav-label,
    .sidebar .sidebar-brand-text,
    .sidebar .logout-label {
        opacity: 1; max-width: 160px; overflow: hidden;
        display: inline-block; white-space: nowrap;
        transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                    max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar.collapsed .nav-label,
    .sidebar.collapsed .sidebar-brand-text,
    .sidebar.collapsed .logout-label { opacity: 0; max-width: 0; }

    .logout-link {
        display: flex; align-items: center;
        transition: background 0.2s ease, color 0.2s ease,
                    padding 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .logout-link i { width: 20px; flex-shrink: 0; margin-right: 10px;
        transition: margin 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
    .sidebar.collapsed .logout-link { justify-content: center; padding: 10px 0; }
    .sidebar.collapsed .logout-link i { margin-right: 0; }

    /* ─────────────────────────────────────────
       MAIN CONTENT
    ───────────────────────────────────────── */
    .main-content {
        margin-left: 60px;
        min-height: 100vh;
        background: var(--page-bg);
        transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                    background 0.3s ease;
    }
    .main-content.sidebar-open { margin-left: 250px; }

    /* ─────────────────────────────────────────
       TOPBAR
    ───────────────────────────────────────── */
    .topbar {
        background: var(--topbar-bg);
        border-bottom: 1px solid var(--topbar-border);
        padding: 12px 24px;
        display: flex; align-items: center;
        justify-content: space-between;
        position: sticky; top: 0; z-index: 99;
        box-shadow: 0 1px 4px var(--topbar-shadow);
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    .topbar h6 { color: var(--topbar-txt) !important; }
    .topbar .text-muted { color: var(--topbar-muted) !important; }

    /* ── Toggle button (hamburger) ── */
    .sidebar-toggle-btn {
        background: none; border: none;
        font-size: 1.3rem; color: var(--toggle-btn-txt);
        cursor: pointer; padding: 6px 9px; border-radius: 8px;
        margin-right: 12px;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }
    .sidebar-toggle-btn:hover { background: var(--toggle-btn-hover); color: var(--toggle-btn-htxt); }
    .sidebar-toggle-btn:active { transform: scale(0.88); background: var(--toggle-btn-active); }

    /* ── Dark mode toggle button ── */
    .dark-toggle-btn {
        background: none; border: none;
        font-size: 1.15rem; color: var(--toggle-btn-txt);
        cursor: pointer; padding: 6px 9px; border-radius: 8px;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        position: relative;
    }
    .dark-toggle-btn:hover { background: var(--toggle-btn-hover); color: var(--toggle-btn-htxt); }
    .dark-toggle-btn:active { transform: scale(0.88); }

    /* ── Mobile overlay ── */
    .sidebar-overlay {
        visibility: hidden; opacity: 0;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 999;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .sidebar-overlay.active { visibility: visible; opacity: 1; }

    @media (max-width: 768px) {
        .sidebar {
            width: 250px; transform: translateX(-100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar.mobile-open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,0.3); }
        .main-content { margin-left: 0 !important; }
    }

    /* ─────────────────────────────────────────
       PAGE & COMPONENTS
    ───────────────────────────────────────── */
    .page-content { padding: 24px; }

    .card {
        border: 1px solid var(--card-border) !important;
        border-radius: 12px;
        background: var(--card-bg) !important;
        box-shadow: 0 2px 10px var(--card-shadow);
        transition: box-shadow 0.2s ease, background 0.3s ease, border-color 0.3s ease;
    }
    .card:hover { box-shadow: 0 4px 20px var(--card-shadow-hover); }

    .card-header {
        background: var(--card-bg) !important;
        border-bottom: 1px solid var(--card-border) !important;
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600; padding: 16px 20px;
        color: var(--body-txt);
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    .stat-card { border-radius: 12px; padding: 20px; color: #ffffff; border: none !important; }

    .table thead th {
        background: var(--table-head-bg) !important;
        color: var(--table-head-txt) !important;
        font-size: 0.78rem; text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--table-head-border) !important;
        font-weight: 600;
        transition: background 0.3s ease, color 0.3s ease;
    }
    .table tbody tr { transition: background 0.15s ease; }
    .table tbody tr:hover { background: var(--table-hover-bg) !important; }
    .table tbody td { color: var(--body-txt); border-color: var(--table-head-border) !important; }

    /* Dark mode Bootstrap overrides */
    body.dark-mode .text-dark   { color: var(--body-txt) !important; }
    body.dark-mode .text-muted  { color: var(--muted-txt) !important; }
    body.dark-mode .form-control,
    body.dark-mode .form-select {
        background: #0f172a; color: #e2e8f0;
        border-color: #334155;
    }
    body.dark-mode .form-control:focus,
    body.dark-mode .form-select:focus {
        background: #1e293b; color: #e2e8f0;
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96,165,250,0.15);
    }
    body.dark-mode .input-group-text {
        background: #1e293b; color: #94a3b8;
        border-color: #334155;
    }
    body.dark-mode .alert-info   { background: #1e3a5f; border-color: #2563eb; color: #bfdbfe; }
    body.dark-mode .alert-success{ background: #14532d; border-color: #16a34a; color: #bbf7d0; }
    body.dark-mode .alert-danger { background: #450a0a; border-color: #dc2626; color: #fecaca; }
    body.dark-mode .alert-warning{ background: #451a03; border-color: #d97706; color: #fde68a; }
    body.dark-mode .modal-content{ background: #1e293b; color: #e2e8f0; }
    body.dark-mode .dropdown-menu{ background: #1e293b; border-color: #334155; }
    body.dark-mode .dropdown-item{ color: #e2e8f0; }
    body.dark-mode .dropdown-item:hover { background: #334155; }

    /* ── Dark mode — Text & UI fixes ── */
    body.dark-mode h1, body.dark-mode h2, body.dark-mode h3,
    body.dark-mode h4, body.dark-mode h5, body.dark-mode h6,
    body.dark-mode p,  body.dark-mode span, body.dark-mode small,
    body.dark-mode label, body.dark-mode td, body.dark-mode th {
        color: var(--body-txt);
    }
    body.dark-mode .badge.bg-success  { color: #fff !important; }
    body.dark-mode .badge.bg-danger   { color: #fff !important; }
    body.dark-mode .badge.bg-warning  { color: #1e293b !important; }
    body.dark-mode .badge.bg-primary  { color: #fff !important; }
    body.dark-mode .badge.bg-secondary{ color: #fff !important; }
    body.dark-mode .stat-card  { color: #ffffff !important; }
    body.dark-mode .stat-card *{ color: #ffffff !important; }
    body.dark-mode .btn-outline-secondary {
        color: #94a3b8 !important; border-color: #475569 !important; background: transparent;
    }
    body.dark-mode .btn-outline-secondary:hover {
        background: #334155 !important; color: #e2e8f0 !important; border-color: #64748b !important;
    }
    body.dark-mode .btn-outline-primary {
        color: #60a5fa !important; border-color: #3b82f6 !important;
    }
    body.dark-mode .btn-outline-primary:hover {
        background: #1e3a5f !important; color: #bfdbfe !important;
    }
    body.dark-mode .btn-outline-danger {
        color: #f87171 !important; border-color: #ef4444 !important;
    }
    body.dark-mode .btn-outline-danger:hover {
        background: #450a0a !important; color: #fecaca !important;
    }
    body.dark-mode .table {
        --bs-table-bg: transparent;
        --bs-table-border-color: #334155;
        color: var(--body-txt);
    }
    body.dark-mode .table-hover tbody tr:hover td {
        background: #263148 !important; color: #e2e8f0 !important;
    }
    body.dark-mode .table tbody tr td { border-color: #1e293b !important; }
    body.dark-mode .fw-semibold,
    body.dark-mode .fw-bold { color: var(--body-txt); }
    body.dark-mode .text-success { color: #4ade80 !important; }
    body.dark-mode .text-danger  { color: #f87171 !important; }
    body.dark-mode .text-warning { color: #fbbf24 !important; }
    body.dark-mode .text-info    { color: #38bdf8 !important; }
    body.dark-mode .text-primary { color: #60a5fa !important; }
    body.dark-mode .form-text    { color: #64748b !important; }
    body.dark-mode .form-control:disabled,
    body.dark-mode .bg-light.form-control {
        background: #0f172a !important; color: #475569 !important; border-color: #334155 !important;
    }
    body.dark-mode .modal-header { border-color: #334155 !important; }
    body.dark-mode .modal-footer { border-color: #334155 !important; }
    body.dark-mode .card ul li,
    body.dark-mode .card .small { color: #94a3b8 !important; }
    body.dark-mode .input-group-text i { color: #64748b; }
    body.dark-mode .bg-light { background: #1e293b !important; }

    .badge { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; }
    .btn { border-radius: 8px; font-size: 0.875rem; transition: all 0.2s ease; }
    .btn:active { transform: scale(0.96); }
    .btn-sm { padding: 4px 12px; font-size: 0.8rem; }
  </style>

  <script>
    // Apply dark mode BEFORE render to prevent flash
    (function() {
      if (localStorage.getItem('adminDarkMode') === 'true') {
        document.documentElement.classList.add('dark-mode-pending');
      }
    })();
  </script>
  <style>
    /* Prevent flash on load */
    .dark-mode-pending { background: #0f172a !important; }
  </style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
  // Move dark mode class from html to body immediately
  if (document.documentElement.classList.contains('dark-mode-pending')) {
    document.documentElement.classList.remove('dark-mode-pending');
    document.body.classList.add('dark-mode');
  }
</script>