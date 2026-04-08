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

$user = getUserById($user_id);

// -----------------------------------------------
// Handle profile picture update
// -----------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_picture') {
    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Please select an image to upload.";
    } else {
        $result = updateProfilePicture($user_id, $_FILES['profile_picture']);
        if ($result['success']) {
            // Update session so topbar reflects immediately
            $_SESSION['user_profile_picture'] = $result['filename'];
            $success = "Profile picture updated successfully!";
            $user    = getUserById($user_id);
        } else {
            $error = $result['error'];
        }
    }
}

// -----------------------------------------------
// Handle profile info update
// -----------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    if (empty($_POST['firstName']) || empty($_POST['lastname']) ||
        empty($_POST['email'])     || empty($_POST['street'])   ||
        empty($_POST['barangay'])  || empty($_POST['city'])) {
        $error = "Please fill in all required fields.";
    } else {
        $data = [
            'firstName'  => trim($_POST['firstName']),
            'middleName' => trim($_POST['middleName']),
            'lastname'   => trim($_POST['lastname']),
            'email'      => trim($_POST['email']),
            'contact'    => trim($_POST['contact']),
            'dateOfBirth'=> trim($_POST['dateOfBirth']),
            'street'     => trim($_POST['street']),
            'barangay'   => trim($_POST['barangay']),
            'city'       => trim($_POST['city']),
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

// -----------------------------------------------
// Handle password change
// -----------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

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
        $stmt   = $conn->prepare("UPDATE `user` SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $user_id);
        if ($stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password. Please try again.";
        }
    }
}

$pic_url = getProfilePictureUrl($user);

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
          <i class="bi bi-exclamation-circle me-2"></i>
          <?php echo htmlspecialchars($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">

        <!-- Left: Profile Picture + Edit Info -->
        <div class="col-md-8">

          <!-- ==============================
               Profile Picture Card
               ============================== -->
          <div class="card mb-4">
            <div class="card-header">
              <i class="bi bi-camera me-2"></i>Profile Picture
            </div>
            <div class="card-body p-4">
              <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_picture">
                <div class="d-flex align-items-center gap-4 flex-wrap">

                  <!-- Current / Preview image -->
                  <div style="position:relative;">
                    <img src="<?php echo $pic_url; ?>"
                         id="profilePreviewImg"
                         alt="Profile Picture"
                         style="width:110px;height:110px;border-radius:50%;
                                object-fit:cover;border:3px solid #dbeafe;">
                    <!-- Camera badge overlay -->
                    <label for="profile_picture_input"
                           style="position:absolute;bottom:0;right:0;
                                  background:#1a6fa3;border-radius:50%;
                                  width:32px;height:32px;display:flex;
                                  align-items:center;justify-content:center;
                                  cursor:pointer;border:2px solid #fff;"
                           title="Change photo">
                      <i class="bi bi-camera-fill text-white" style="font-size:0.85rem;"></i>
                    </label>
                  </div>

                  <!-- Upload controls -->
                  <div class="flex-grow-1">
                    <p class="fw-semibold mb-1">
                      <?php echo htmlspecialchars($user['firstName'] . ' ' . $user['lastname']); ?>
                    </p>
                    <p class="text-muted small mb-2">
                      JPG, PNG, GIF or WEBP &nbsp;·&nbsp; Max 2MB
                    </p>
                    <input type="file"
                           name="profile_picture"
                           id="profile_picture_input"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           class="d-none">
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                      <button type="button"
                              class="btn btn-outline-primary btn-sm"
                              onclick="document.getElementById('profile_picture_input').click()">
                        <i class="bi bi-upload me-1"></i>Choose Photo
                      </button>
                      <span id="chosenFileName" class="text-muted small">No file chosen</span>
                    </div>
                  </div>

                  <!-- Save button -->
                  <div>
                    <button type="submit" class="btn btn-primary">
                      <i class="bi bi-save me-1"></i>Save Photo
                    </button>
                  </div>

                </div>
              </form>
            </div>
          </div>

          <!-- ==============================
               Personal Info Card
               ============================== -->
          <div class="card">
            <div class="card-header">
              <i class="bi bi-person me-2"></i>Personal Information
            </div>
            <div class="card-body p-4">
              <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="row g-3">

                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">
                      First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="firstName" class="form-control"
                           value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">Middle Name</label>
                    <input type="text" name="middleName" class="form-control"
                           value="<?php echo htmlspecialchars($user['middleName'] ?? ''); ?>">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold small">
                      Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="lastname" class="form-control"
                           value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">
                      Email Address <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-envelope text-muted"></i>
                      </span>
                      <input type="email" name="email" class="form-control"
                             value="<?php echo htmlspecialchars($user['emailAddress']); ?>" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Contact Number</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-telephone text-muted"></i>
                      </span>
                      <input type="tel" name="contact" class="form-control"
                             placeholder="e.g. 09171234567"
                             value="<?php echo htmlspecialchars($user['contactNumber'] ?? ''); ?>">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Date of Birth</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-calendar text-muted"></i>
                      </span>
                      <input type="date" name="dateOfBirth" class="form-control"
                             value="<?php echo htmlspecialchars($user['dateOfBirth'] ?? ''); ?>"
                             max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">Meter Number</label>
                    <input type="text" class="form-control bg-light"
                           value="<?php echo htmlspecialchars($user['meter_number'] ?? 'N/A'); ?>" disabled>
                    <div class="form-text">Contact admin to change meter number</div>
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold small">
                      Street <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="street" class="form-control"
                           value="<?php echo htmlspecialchars($user['street']); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">
                      Barangay <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="barangay" class="form-control"
                           value="<?php echo htmlspecialchars($user['barangay']); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small">
                      City <span class="text-danger">*</span>
                    </label>
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

        <!-- Right: Account Info + Change Password -->
        <div class="col-md-4">

          <!-- Account Info Card -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="bi bi-person-badge me-2"></i>Account Info
            </div>
            <div class="card-body">
              <div class="text-center mb-3">
                <img src="<?php echo $pic_url; ?>"
                     alt="Profile"
                     style="width:80px;height:80px;border-radius:50%;
                            object-fit:cover;border:3px solid #dbeafe;">
              </div>
              <div class="mb-2">
                <small class="text-muted">Username</small>
                <p class="fw-semibold mb-0"><?php echo htmlspecialchars($user['username']); ?></p>
              </div>
              <div class="mb-2">
                <small class="text-muted">Contact Number</small>
                <p class="fw-semibold mb-0">
                  <?php if (!empty($user['contactNumber'])): ?>
                    <i class="bi bi-telephone text-success me-1"></i>
                    <?php echo htmlspecialchars($user['contactNumber']); ?>
                  <?php else: ?>
                    <span class="text-muted fst-italic small">Not set yet</span>
                  <?php endif; ?>
                </p>
              </div>
              <div class="mb-2">
                <small class="text-muted">Date of Birth</small>
                <p class="fw-semibold mb-0">
                  <?php if (!empty($user['dateOfBirth'])): ?>
                    <i class="bi bi-calendar text-primary me-1"></i>
                    <?php echo date('F d, Y', strtotime($user['dateOfBirth'])); ?>
                  <?php else: ?>
                    <span class="text-muted fst-italic small">Not set yet</span>
                  <?php endif; ?>
                </p>
              </div>
              <div class="mb-2">
                <small class="text-muted">Status</small>
                <p class="mb-0">
                  <span class="badge bg-success"><?php echo ucfirst($user['status'] ?? 'active'); ?></span>
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

          <!-- Change Password Card -->
          <div class="card">
            <div class="card-header">
              <i class="bi bi-shield-lock me-2"></i>Change Password
            </div>
            <div class="card-body">
              <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="mb-3">
                  <label class="form-label fw-semibold small">Current Password</label>
                  <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold small">New Password</label>
                  <input type="password" name="new_password" class="form-control"
                         placeholder="Min. 6 characters" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold small">Confirm New Password</label>
                  <input type="password" name="confirm_password" class="form-control" required>
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

<script>
// Live preview when choosing a new photo
const picInput     = document.getElementById('profile_picture_input');
const previewImg   = document.getElementById('profilePreviewImg');
const fileNameSpan = document.getElementById('chosenFileName');

picInput.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        fileNameSpan.textContent = this.files[0].name;
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>