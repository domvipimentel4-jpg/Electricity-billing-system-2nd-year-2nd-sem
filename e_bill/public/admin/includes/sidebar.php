<?php
// ================================================
// Admin Sidebar
// public/admin/includes/sidebar.php
// ================================================
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar d-flex flex-column">
  <div class="sidebar-brand">
    <i class="bi bi-lightning-charge-fill text-warning"></i>
    Electricity Billing
  </div>
  <div class="mt-3 flex-grow-1">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="dashboard" class="nav-link <?php echo $current == 'dashboard' ? 'active' : ''; ?>">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="manage_users" class="nav-link <?php echo $current == 'manage_users' ? 'active' : ''; ?>">
          <i class="bi bi-people"></i> Manage Users
        </a>
      </li>
      <li class="nav-item">
        <a href="add_bill" class="nav-link <?php echo $current == 'add_bill' ? 'active' : ''; ?>">
          <i class="bi bi-plus-circle"></i> Add Bill
        </a>
      </li>
      <li class="nav-item">
        <a href="view_bills" class="nav-link <?php echo $current == 'view_bills.' ? 'active' : ''; ?>">
          <i class="bi bi-receipt"></i> View Bills
        </a>
      </li>
      <li class="nav-item">
        <a href="reports" class="nav-link <?php echo $current == 'reports' ? 'active' : ''; ?>">
          <i class="bi bi-bar-chart"></i> Reports
        </a>
      </li>
      <li class="nav-item">
        <a href="settings" class="nav-link <?php echo $current == 'settings' ? 'active' : ''; ?>">
          <i class="bi bi-gear"></i> Settings
        </a>
      </li>
    </ul>
  </div>
  <div class="p-3" style="border-top: 1px solid #334155;">
    <a href="<?php echo BASE_URL; ?>index.php?logout=1" class="nav-link text-danger">
      <i class="bi bi-box-arrow-left"></i> Logout
    </a>
  </div>
</div>