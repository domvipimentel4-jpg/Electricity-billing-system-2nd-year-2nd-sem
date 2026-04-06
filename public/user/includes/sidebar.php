<?php
// ================================================
// User Sidebar
// public/user/includes/sidebar.php
// ================================================
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar d-flex flex-column">
  <div class="sidebar-brand">
    <i class="bi bi-lightning-charge-fill text-warning"></i>
    My Account
  </div>
  <div class="mt-3 flex-grow-1">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="dashboard.php" class="nav-link <?php echo $current == 'dashboard.php' ? 'active' : ''; ?>">
          <i class="bi bi-house"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="my_bills.php" class="nav-link <?php echo $current == 'my_bills.php' ? 'active' : ''; ?>">
          <i class="bi bi-receipt"></i> My Bills
        </a>
      </li>
      <li class="nav-item">
        <a href="pay_bill.php" class="nav-link <?php echo $current == 'pay_bill.php' ? 'active' : ''; ?>">
          <i class="bi bi-credit-card"></i> Pay Bill
        </a>
      </li>
      <li class="nav-item">
        <a href="profile.php" class="nav-link <?php echo $current == 'profile.php' ? 'active' : ''; ?>">
          <i class="bi bi-person"></i> My Profile
        </a>
      </li>
    </ul>
  </div>
  <div class="p-3" style="border-top: 1px solid #1a6fa3;">
    <a href="<?php echo BASE_URL; ?>index.php?logout=1" class="nav-link text-danger">
      <i class="bi bi-box-arrow-left"></i> Logout
    </a>
  </div>
</div>