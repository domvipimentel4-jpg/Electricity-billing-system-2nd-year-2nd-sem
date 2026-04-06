<?php
// ================================================
// User Topbar
// public/user/includes/topbar.php
// ================================================
?>
<div class="topbar">
  <div>
    <h6 class="mb-0 fw-bold text-dark">
      <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
    </h6>
    <small class="text-muted">
      <?php echo date('l, F d, Y'); ?>
    </small>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span class="text-muted small">
      <i class="bi bi-person-circle me-1"></i>
      <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
    </span>
    <a href="<?php echo BASE_URL; ?>index.php?logout=1"
       class="btn btn-outline-danger btn-sm">
      <i class="bi bi-box-arrow-left"></i> Logout
    </a>
  </div>
</div>