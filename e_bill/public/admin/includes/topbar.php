<?php
// ================================================
// Admin Topbar
// public/admin/includes/topbar.php
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
      <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
    </span>
  </div>
</div>