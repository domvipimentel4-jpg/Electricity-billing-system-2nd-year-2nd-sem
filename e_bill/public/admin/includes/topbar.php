<?php
// ================================================
// Admin Topbar
// public/admin/includes/topbar.php
// ================================================
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
    <span class="text-muted small">
      <i class="bi bi-person-circle me-1"></i>
      <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
    </span>
  </div>
</div>

<script>
(function () {
  const sidebar   = document.getElementById('adminSidebar');
  const content   = document.querySelector('.main-content');
  const toggleBtn = document.getElementById('sidebarToggle');
  const overlay   = document.getElementById('sidebarOverlay');
  const isMobile  = () => window.innerWidth <= 768;

  // Sidebar starts collapsed by default (already has class in HTML)
  // On desktop, check if user previously opened it
  if (!isMobile() && localStorage.getItem('adminSidebarOpen') === 'true') {
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
      localStorage.setItem('adminSidebarOpen', !isCollapsed);
    }
  });

  // Smooth navigation — no hard flash when clicking menu links
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