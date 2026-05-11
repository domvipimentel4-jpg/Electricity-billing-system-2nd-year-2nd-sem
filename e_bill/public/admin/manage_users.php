<?php
// ================================================
// Manage Users
// public/admin/manage_users.php
// ================================================

define('REQUIRED_ROLE', 'admin');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/customer_controller.php';

$page_title = "Manage Users";
$success    = "";
$error      = "";

// Toggle user status
if (isset($_GET['toggle']) && isset($_GET['status'])) {
    $id         = intval($_GET['toggle']);
    $new_status = $_GET['status'] === 'active' ? 'inactive' : 'active';
    updateUserStatus($id, $new_status);
    $success = "User status updated to " . strtoupper($new_status) . ".";
}

// Archive user (soft delete)
if (isset($_GET['archive'])) {
    $id = intval($_GET['archive']);
    if (archiveUser($id)) {
        $success = "User archived successfully. Their data and bills are preserved.";
    } else {
        $error = "Failed to archive user.";
    }
}

$users = getAllUsers();

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
          <span><i class="bi bi-people me-2"></i>All Registered Users</span>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary"><?php echo $users->num_rows; ?> users</span>
            <a href="archived_users.php" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-archive me-1"></i>View Archived
            </a>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Contact</th>
                  <th>Date of Birth</th>
                  <th>Meter No.</th>
                  <th>Address</th>
                  <th>Bills</th>
                  <th>Unpaid</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($users->num_rows > 0):
                  $count = 1;
                  while ($row = $users->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $count++; ?></td>
                  <td class="fw-semibold">
                    <?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastname']); ?>
                  </td>
                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                  <td><?php echo htmlspecialchars($row['emailAddress']); ?></td>
                  <td><?php echo htmlspecialchars($row['contactNumber'] ?? 'N/A'); ?></td>
                  <td><?php echo $row['dateOfBirth'] ? date('M d, Y', strtotime($row['dateOfBirth'])) : 'N/A'; ?></td>
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
                  <td>
                    <?php if ($row['status'] === 'active'): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="?toggle=<?php echo $row['id']; ?>&status=<?php echo $row['status']; ?>"
                         class="btn btn-sm <?php echo $row['status'] === 'active' ? 'btn-warning' : 'btn-success'; ?>"
                         onclick="return confirm('Toggle user status?')">
                        <i class="bi <?php echo $row['status'] === 'active' ? 'bi-pause' : 'bi-play'; ?>"></i>
                      </a>
                      <a href="?archive=<?php echo $row['id']; ?>"
                         class="btn btn-sm btn-secondary"
                         onclick="return confirm('Archive this user? Their bills and history will be preserved.')">
                        <i class="bi bi-archive"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="12" class="text-center text-muted py-4">
                    No users registered yet.
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>