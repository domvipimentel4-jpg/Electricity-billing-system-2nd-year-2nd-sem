<?php
// ================================================
// User Topbar
// public/user/includes/topbar.php
// ================================================
if (!empty($_SESSION['user_profile_picture'])) {
    $_topbar_pic = UPLOADS_URL . 'profile_pictures/' . htmlspecialchars($_SESSION['user_profile_picture']);
} else {
    $_topbar_name = urlencode($_SESSION['user_name'] ?? 'User');
    $_topbar_pic  = 'https://ui-avatars.com/api/?name=' . $_topbar_name . '&background=1a6fa3&color=fff&size=64&bold=true';
}
?>
<div class="topbar">
  <div class="d-flex align-items-center">
    <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
      <i class="bi bi-list"></i>
    </button>
    <div>
      <h6 class="mb-0 fw-bold text-dark">
        <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
      </h6>
      <small class="text-muted"><?php echo date('l, F d, Y'); ?></small>
    </div>
  </div>
  <div class="d-flex align-items-center gap-3">
    <a href="profile" class="d-flex align-items-center gap-2 text-decoration-none text-muted small" title="My Profile">
      <img src="<?php echo $_topbar_pic; ?>" alt="Profile"
           style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid #dbeafe;flex-shrink:0;">
      <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
    </a>
  </div>
</div>

<script>
(function () {
  const sidebar   = document.getElementById('userSidebar');
  const content   = document.querySelector('.main-content');
  const toggleBtn = document.getElementById('sidebarToggle');
  const overlay   = document.getElementById('sidebarOverlay');
  const isMobile  = () => window.innerWidth <= 768;

  // Sidebar starts collapsed by default
  // Restore if user previously opened it
  if (!isMobile() && localStorage.getItem('userSidebarOpen') === 'true') {
    sidebar.classList.remove('collapsed');
    content.classList.add('sidebar-open');
  }

  toggleBtn.addEventListener('click', function () {
    if (isMobile()) {
      sidebar.classList.toggle('mobile-open');
      overlay.classList.toggle('active');
    } else {
      const isCollapsed = sidebar.classList.toggle('collapsed');
      content.classList.toggle('sidebar-open', !isCollapsed);
      localStorage.setItem('userSidebarOpen', !isCollapsed);
    }
  });

  // Smooth navigation when clicking menu links
  document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (!href || href === '#') return;
      e.preventDefault();
      document.querySelector('.main-content').style.opacity = '0';
      document.querySelector('.main-content').style.transition = 'opacity 0.2s ease';
      setTimeout(function() { window.location.href = href; }, 200);
    });
  });

  overlay.addEventListener('click', function () {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
  });
})();
</script>