<?php
// ================================================
// Admin Sidebar
// public/admin/includes/sidebar.php
// ================================================
$current = pathinfo(basename($_SERVER["PHP_SELF"]), PATHINFO_FILENAME);
?>
<div class="sidebar collapsed d-flex flex-column" id="adminSidebar">
  <div class="sidebar-brand">
    <i class="bi bi-lightning-charge-fill text-warning"></i>
    <span class="sidebar-brand-text">Electricity Billing</span>
  </div>
  <div class="mt-3 flex-grow-1">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="dashboard" class="nav-link <?php echo $current == 'dashboard' ? 'active' : ''; ?>" title="Dashboard">
          <i class="bi bi-speedometer2"></i> <span class="nav-label">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="manage_users" class="nav-link <?php echo $current == 'manage_users' ? 'active' : ''; ?>" title="Manage Users">
          <i class="bi bi-people"></i> <span class="nav-label">Manage Users</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="archived_users" class="nav-link <?php echo $current == 'archived_users' ? 'active' : ''; ?>" title="Archived Users">
          <i class="bi bi-archive"></i> <span class="nav-label">Archived Users</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="add_bill" class="nav-link <?php echo $current == 'add_bill' ? 'active' : ''; ?>" title="Add Bill">
          <i class="bi bi-plus-circle"></i> <span class="nav-label">Add Bill</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="view_bills" class="nav-link <?php echo $current == 'view_bills' ? 'active' : ''; ?>" title="View Bills">
          <i class="bi bi-receipt"></i> <span class="nav-label">View Bills</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="reports" class="nav-link <?php echo $current == 'reports' ? 'active' : ''; ?>" title="Reports">
          <i class="bi bi-bar-chart"></i> <span class="nav-label">Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="settings" class="nav-link <?php echo $current == 'settings' ? 'active' : ''; ?>" title="Settings">
          <i class="bi bi-gear"></i> <span class="nav-label">Settings</span>
        </a>
      </li>
    </ul>
  </div>
  <div class="p-3" style="border-top: 1px solid #334155;">
    <a href="<?php echo BASE_URL; ?>index.php?logout=1" class="nav-link text-danger logout-link d-flex align-items-center" title="Logout">
      <i class="bi bi-box-arrow-left" style="width:20px; margin-right:8px; flex-shrink:0;"></i>
      <span class="logout-label">Logout</span>
    </a>
  </div>
</div>