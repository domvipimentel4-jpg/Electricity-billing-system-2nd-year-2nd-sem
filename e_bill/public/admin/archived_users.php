<?php
// ================================================
// Archived Users
// public/admin/archived_users.php
// ================================================

define('REQUIRED_ROLE', 'admin');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/customer_controller.php';

$page_title = "Archived Users";
$success    = "";
$error      = "";

// Restore user
if (isset($_GET['restore'])) {
    $id = intval($_GET['restore']);
    if (restoreUser($id)) {
        $success = "User restored successfully and set back to Active.";
    } else {
        $error = "Failed to restore user.";
    }
}

$users = getArchivedUsers();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <div class="main-content flex-grow-1">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="page-content">

      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-archive me-2"></i>Archived Users</span>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary"><?php echo $users->num_rows; ?> archived</span>
            <a href="manage_users.php" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-people me-1"></i>Back to Active Users
            </a>
          </div>
        </div>

        <?php if ($users->num_rows === 0): ?>
          <div class="card-body text-center text-muted py-5">
            <i class="bi bi-archive display-4 d-block mb-3 opacity-25"></i>
            No archived users yet.
          </div>
        <?php else: ?>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Meter No.</th>
                  <th>Address</th>
                  <th>Total Bills</th>
                  <th>Unpaid Balance</th>
                  <th>Archived On</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $count = 1;
                  while ($row = $users->fetch_assoc()): ?>
                <tr class="table-secondary">
                  <td><?php echo $count++; ?></td>
                  <td class="fw-semibold text-muted">
                    <?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastname']); ?>
                  </td>
                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                  <td><?php echo htmlspecialchars($row['emailAddress']); ?></td>
                  <td><?php echo htmlspecialchars($row['meter_number'] ?? 'N/A'); ?></td>
                  <td class="small">
                    <?php echo htmlspecialchars($row['barangay'] . ', ' . $row['city']); ?>
                  </td>
                  <td><?php echo $row['total_bills']; ?></td>
                  <td>
                    <?php if ($row['total_unpaid'] > 0): ?>
                      <span class="text-danger fw-semibold">
                        ₱<?php echo number_format($row['total_unpaid'], 2); ?>
                      </span>
                    <?php else: ?>
                      <span class="text-success">₱0.00</span>
                    <?php endif; ?>
                  </td>
                  <td class="small text-muted">
                    <?php echo $row['archived_at'] ? date('M d, Y g:i A', strtotime($row['archived_at'])) : 'N/A'; ?>
                  </td>
                  <td>
                    <a href="?restore=<?php echo $row['id']; ?>"
                       class="btn btn-sm btn-success"
                       onclick="return confirm('Restore this user? They will be set back to Active.')">
                      <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                    </a>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>