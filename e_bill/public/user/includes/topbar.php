<?php
// ================================================
// User Topbar
// public/user/includes/topbar.php
// ================================================

// Build profile picture URL for topbar
$_topbar_pic = '';
if (!empty($_SESSION['user_profile_picture'])) {
    $_topbar_pic = BASE_URL . '../uploads/profile_pictures/' . htmlspecialchars($_SESSION['user_profile_picture']);
} else {
    // Auto-generated letter avatar fallback
    $_topbar_name = urlencode($_SESSION['user_name'] ?? 'User');
    $_topbar_pic  = 'https://ui-avatars.com/api/?name=' . $_topbar_name . '&background=1a6fa3&color=fff&size=64&bold=true';
}
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
    <!-- Profile picture + name -->
    <a href="profile.php"
       class="d-flex align-items-center gap-2 text-decoration-none text-muted small"
       title="My Profile">
      <img src="<?php echo $_topbar_pic; ?>"
           alt="Profile"
           style="width:34px;height:34px;border-radius:50%;
                  object-fit:cover;border:2px solid #dbeafe;">
      <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
    </a>
    <a href="<?php echo BASE_URL; ?>index.php?logout=1"
       class="btn btn-outline-danger btn-sm">
      <i class="bi bi-box-arrow-left"></i> Logout
    </a>
  </div>
</div>