<?php
// ================================================
// User Profile
// public/user/profile.php
// ================================================

define('REQUIRED_ROLE', 'user');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/customer_controller.php';

$page_title = "My Profile";
$user_id    = $_SESSION['user_id'];
$success    = "";
$error      = "";

// Get current user data
$user = getUserById($user_id);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    if (empty($_POST['firstName']) || empty($_POST['lastname']) ||
        empty($_POST['email']) || empty($_POST['contact_number']) ||
        empty($_POST['date_of_birth']) || empty($_POST['street']) ||
        empty($_POST['barangay']) || empty($_POST['city'])) {
        $error = "Please fill in all required fields.";
    } else {
        $data = [
            'firstName'      => trim($_POST['firstName']),
            'middleName'     => trim($_POST['middleName']),
            'lastname'       => trim($_POST['lastname']),
            'email'          => trim($_POST['email']),
            'contact_number' => trim($_POST['contact_number']),
            'date_of_birth'  => trim($_POST['date_of_birth']),
            'street'         => trim($_POST['street']),
            'barangay'       => trim($_POST['barangay']),
            'city'           => trim($_POST['city']),
        ];
        $result = updateUserProfile($user_id, $data);
        if ($result['success']) {
            $success = "Profile updated successfully!";
            $user    = getUserById($user_id);
        } else {
            $error = $result['error'];
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current  = $_POST['current_password'];
    $new      = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($current) || empty($new) || empty($confirm)) {
        $error = "Please fill in all password fields.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } elseif (strlen($new) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect.";
    } else {
        global $conn;
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt   = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $user_id);
        if ($stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password. Please try again.";
        }
    }
}

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
          <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">

        <!-- Profile Info -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <i class="bi bi-person me-2"></i>Personal Information
            </div>
            <div class="card-body p-4">
              <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="firstName" class="form-control"
                           value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">Middle Name</label>
                    <input type="text" name="middleName" class="form-control"
                           value="<?php echo htmlspecialchars($user['middleName'] ?? ''); ?>">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="lastname" class="form-control"
                           value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                           value="<?php echo htmlspecialchars($user['emailAddress']); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Contact Number <span class="text-danger">*</span></label>
                    <input type="tel" name="contact_number" class="form-control"
                           value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>"
                           placeholder="e.g. +639171234567" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" class="form-control"
                           value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Meter Number</label>
                    <input type="text" class="form-control"
                           value="<?php echo htmlspecialchars($user['meter_number'] ?? 'N/A'); ?>" disabled>
                    <div class="form-text">Contact admin to change meter number</div>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold small">Street <span class="text-danger">*</span></label>
                    <input type="text" name="street" class="form-control"
                           value="<?php echo htmlspecialchars($user['street']); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Barangay <span class="text-danger">*</span></label>
                    <input type="text" name="barangay" class="form-control"
                           value="<?php echo htmlspecialchars($user['barangay']); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">City <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control"
                           value="<?php echo htmlspecialchars($user['city']); ?>" required>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                  <i class="bi bi-save me-2"></i>Save Changes
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Account Info + Change Password -->
        <div class="col-md-4">
          <!-- Account summary -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="bi bi-person-badge me-2"></i>Account Info
            </div>
            <div class="card-body">
              <div class="mb-2">
                <small class="text-muted">Username</small>
                <p class="fw-semibold mb-0">
                  <?php echo htmlspecialchars($user['username']); ?>
                </p>
              </div>
              <div class="mb-2">
                <small class="text-muted">Status</small>
                <p class="mb-0">
                  <span class="badge bg-success">
                    <?php echo ucfirst($user['status']); ?>
                  </span>
                </p>
              </div>
              <div>
                <small class="text-muted">Member Since</small>
                <p class="fw-semibold mb-0">
                  <?php echo date('F d, Y', strtotime($user['dateCreated'])); ?>
                </p>
              </div>
            </div>
          </div>

          <!-- Change Password -->
          <div class="card">
            <div class="card-header">
              <i class="bi bi-shield-lock me-2"></i>Change Password
            </div>
            <div class="card-body">
              <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="mb-3">
                  <label class="form-label fw-semibold small">Current Password</label>
                  <input type="password" name="current_password"
                         class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold small">New Password</label>
                  <input type="password" name="new_password"
                         class="form-control" placeholder="Min. 6 characters" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold small">Confirm New Password</label>
                  <input type="password" name="confirm_password"
                         class="form-control" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">
                  <i class="bi bi-key me-2"></i>Change Password
                </button>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>