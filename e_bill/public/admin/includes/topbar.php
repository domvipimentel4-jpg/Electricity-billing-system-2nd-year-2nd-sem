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
  <div class="d-flex align-items-center gap-2">
    <!-- Dark mode toggle -->
    <button class="dark-toggle-btn" id="darkToggle" title="Toggle Dark Mode">
      <i class="bi bi-moon-fill" id="darkIcon"></i>
    </button>
    <span class="text-muted small ms-1">
      <i class="bi bi-person-circle me-1"></i>
      <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
    </span>
  </div>
</div>

<script>
(function () {
  const sidebar    = document.getElementById('adminSidebar');
  const content    = document.querySelector('.main-content');
  const toggleBtn  = document.getElementById('sidebarToggle');
  const overlay    = document.getElementById('sidebarOverlay');
  const darkBtn    = document.getElementById('darkToggle');
  const darkIcon   = document.getElementById('darkIcon');
  const isMobile   = () => window.innerWidth <= 768;
  const DARK_KEY   = 'adminDarkMode';

  // ── Sidebar restore (no animation) ──
  if (!isMobile() && localStorage.getItem('adminSidebarOpen') === 'true') {
    content.style.transition = 'none';
    sidebar.classList.remove('collapsed');
    content.classList.add('sidebar-open');
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        content.style.transition = '';
      });
    });
  }

  // ── Sidebar toggle ──
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

  overlay.addEventListener('click', function () {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
  });

  // ── Dark mode icon sync ──
  function syncIcon() {
    const isDark = document.body.classList.contains('dark-mode');
    darkIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    darkBtn.title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
  }

  // ── Dark mode toggle ──
  darkBtn.addEventListener('click', function () {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem(DARK_KEY, isDark);
    syncIcon();
  });

  // Set correct icon on load
  syncIcon();
})();
</script>