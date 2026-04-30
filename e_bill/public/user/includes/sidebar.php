<?php
// ================================================
// User Sidebar
// public/user/includes/sidebar.php
// ================================================
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar collapsed d-flex flex-column" id="userSidebar">
  <div class="sidebar-brand">
    <i class="bi bi-lightning-charge-fill text-warning"></i>
    <span class="sidebar-brand-text">My Account</span>
  </div>
  <div class="mt-3 flex-grow-1">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="dashboard" class="nav-link <?php echo $current == 'dashboard' ? 'active' : ''; ?>" title="Dashboard">
          <i class="bi bi-house"></i> <span class="nav-label">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="my_bills" class="nav-link <?php echo $current == 'my_bills' ? 'active' : ''; ?>" title="My Bills">
          <i class="bi bi-receipt"></i> <span class="nav-label">My Bills</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="pay_bill" class="nav-link <?php echo $current == 'pay_bill' ? 'active' : ''; ?>" title="Pay Bill">
          <i class="bi bi-credit-card"></i> <span class="nav-label">Pay Bill</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="profile" class="nav-link <?php echo $current == 'profile' ? 'active' : ''; ?>" title="My Profile">
          <i class="bi bi-person"></i> <span class="nav-label">My Profile</span>
        </a>
      </li>
    </ul>
  </div>
  <div class="p-3" style="border-top: 1px solid #1a6fa3;">
    <a href="<?php echo BASE_URL; ?>index?logout=1" class="nav-link text-danger logout-link d-flex align-items-center" title="Logout">
      <i class="bi bi-box-arrow-left" style="width:20px; margin-right:8px; flex-shrink:0;"></i>
      <span class="logout-label">Logout</span>
    </a>
  </div>
</div>