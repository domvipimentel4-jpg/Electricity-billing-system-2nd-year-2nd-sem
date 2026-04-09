<?php
// ================================================
// User Registration Page
// public/register.php
// ================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/controller/auth_controller.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$success = "";
$error   = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = "Passwords do not match.";
    } elseif (strlen($_POST['password']) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (
        empty($_POST['firstName'])    ||
        empty($_POST['lastname'])     ||
        empty($_POST['email'])        ||
        empty($_POST['contact'])      ||
        empty($_POST['dateOfBirth'])  ||
        empty($_POST['meter_number']) ||
        empty($_POST['username'])     ||
        empty($_POST['street'])       ||
        empty($_POST['barangay'])     ||
        empty($_POST['city'])
    ) {
        $error = "Please fill in all required fields.";
    } else {
        $data = [
            'firstName'    => trim($_POST['firstName']),
            'middleName'   => trim($_POST['middleName']),
            'lastname'     => trim($_POST['lastname']),
            'email'        => trim($_POST['email']),
            'contact'      => trim($_POST['contact']),
            'dateOfBirth'  => trim($_POST['dateOfBirth']),
            'username'     => trim($_POST['username']),
            'password'     => $_POST['password'],
            'meter_number' => trim($_POST['meter_number']),
            'street'       => trim($_POST['street']),
            'barangay'     => trim($_POST['barangay']),
            'city'         => trim($_POST['city']),
        ];

        // Pass profile picture file if uploaded
        $file   = isset($_FILES['profile_picture']) ? $_FILES['profile_picture'] : null;
        $result = registerUser($data, $file);

        if ($result['success']) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register — Electricity Billing System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        padding: 40px 0;
    }
    .register-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        overflow: hidden;
    }
    .register-card::before {
        content: '';
        display: block;
        height: 5px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
    }
    .register-card .card-body { padding: 2.5rem; }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        background: #f8fafc;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        background: #fff;
    }
    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #3b82f6;
        margin: 20px 0 12px;
        padding-bottom: 6px;
        border-bottom: 2px solid #dbeafe;
    }
    .btn-register {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border: none;
        border-radius: 8px;
        padding: 11px;
        font-weight: 600;
        color: #fff;
        width: 100%;
        transition: all 0.2s;
    }
    .btn-register:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(37,99,235,0.4);
    }
    .input-group-text {
        background: #f1f5f9;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
    }

    /* Profile picture preview */
    .avatar-upload-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    .avatar-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #dbeafe;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .avatar-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #dbeafe;
        border: 3px dashed #93c5fd;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.7rem;
        color: #3b82f6;
        font-weight: 600;
        text-align: center;
        gap: 2px;
    }
    .avatar-placeholder:hover {
        background: #bfdbfe;
        border-color: #3b82f6;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card register-card">
        <div class="card-body">

          <div class="text-center mb-4">
            <i class="bi bi-lightning-charge-fill text-warning" style="font-size: 2.5rem;"></i>
            <h4 class="fw-bold text-dark mb-1 mt-2">Create an Account</h4>
            <p class="text-muted small">Register to view and pay your electricity bills</p>
          </div>

          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill fs-5"></i>
              <div>
                <?php echo $success; ?>
                <a href="index" class="ms-2 btn btn-success btn-sm">Login Now</a>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-circle-fill fs-5"></i>
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>

          <!-- enctype required for file uploads -->
          <form method="POST" enctype="multipart/form-data">

            <!-- Profile Picture -->
            <div class="section-title">
              <i class="bi bi-camera me-1"></i> Profile Picture
            </div>
            <div class="row">
              <div class="col-12">
                <div class="avatar-upload-wrapper">
                  <!-- Preview circle -->
                  <div id="avatarPreviewWrap">
                    <div class="avatar-placeholder" id="avatarPlaceholder" onclick="document.getElementById('profile_picture').click()">
                      <i class="bi bi-camera" style="font-size:1.5rem;"></i>
                      <span>Click to upload</span>
                    </div>
                    <div class="avatar-preview d-none" id="avatarPreview">
                      <img src="" alt="Preview" id="previewImg">
                    </div>
                  </div>
                  <input type="file" name="profile_picture" id="profile_picture"
                         accept="image/jpeg,image/png,image/gif,image/webp"
                         class="d-none">
                  <div class="text-center">
                    <small class="text-muted">Optional · JPG, PNG, GIF, WEBP · Max 2MB</small><br>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-1"
                            onclick="document.getElementById('profile_picture').click()">
                      <i class="bi bi-upload me-1"></i>Choose Photo
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-1 d-none"
                            id="removePhoto">
                      <i class="bi bi-x me-1"></i>Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Personal Information -->
            <div class="section-title">
              <i class="bi bi-person me-1"></i> Personal Information
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  First Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="firstName" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['firstName'] ?? ''); ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Middle Name</label>
                <input type="text" name="middleName" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['middleName'] ?? ''); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  Last Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="lastname" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">
                  Email Address <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                  <input type="email" name="email" class="form-control"
                         placeholder="e.g. juan@gmail.com"
                         value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">
                  Contact Number <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-telephone text-muted"></i></span>
                  <input type="tel" name="contact" class="form-control"
                         placeholder="e.g. 09171234567"
                         value="<?php echo htmlspecialchars($_POST['contact'] ?? ''); ?>" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">
                  Date of Birth <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-calendar text-muted"></i></span>
                  <input type="date" name="dateOfBirth" class="form-control"
                         value="<?php echo htmlspecialchars($_POST['dateOfBirth'] ?? ''); ?>"
                         max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" required>
                </div>
                <div class="form-text">Must be 18 years or older</div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">
                  Meter Number <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lightning text-muted"></i></span>
                  <input type="text" name="meter_number" class="form-control"
                         placeholder="e.g. MTR-001"
                         value="<?php echo htmlspecialchars($_POST['meter_number'] ?? ''); ?>" required>
                </div>
              </div>
            </div>

            <!-- Address -->
            <div class="section-title">
              <i class="bi bi-geo-alt me-1"></i> Address
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  Street <span class="text-danger">*</span>
                </label>
                <input type="text" name="street" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['street'] ?? ''); ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  Barangay <span class="text-danger">*</span>
                </label>
                <input type="text" name="barangay" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['barangay'] ?? ''); ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  City <span class="text-danger">*</span>
                </label>
                <input type="text" name="city" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
              </div>
            </div>

            <!-- Account Credentials -->
            <div class="section-title">
              <i class="bi bi-shield-lock me-1"></i> Account Credentials
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  Username <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person text-muted"></i></span>
                  <input type="text" name="username" class="form-control"
                         value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                  <input type="password" name="password" id="password"
                         class="form-control border-end-0"
                         placeholder="Min. 6 characters" required>
                  <button type="button" class="toggle-pwd input-group-text" data-target="password" style="cursor:pointer;">
                    <i class="bi bi-eye-slash"></i>
                  </button>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">
                  Confirm Password <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                  <input type="password" name="confirm_password" id="confirm_password"
                         class="form-control border-end-0" required>
                  <button type="button" class="toggle-pwd input-group-text" data-target="confirm_password" style="cursor:pointer;">
                    <i class="bi bi-eye-slash"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-register">
                <i class="bi bi-person-plus me-2"></i>Create Account
              </button>
            </div>

            <p class="text-center text-muted small mt-3 mb-0">
              Already have an account?
              <a href="index" class="text-primary fw-semibold">Sign in here</a>
            </p>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Profile picture live preview
const fileInput      = document.getElementById('profile_picture');
const placeholder    = document.getElementById('avatarPlaceholder');
const previewWrap    = document.getElementById('avatarPreview');
const previewImg     = document.getElementById('previewImg');
const removeBtn      = document.getElementById('removePhoto');

fileInput.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            placeholder.classList.add('d-none');
            previewWrap.classList.remove('d-none');
            removeBtn.classList.remove('d-none');
        };
        reader.readAsDataURL(this.files[0]);
    }
});

removeBtn.addEventListener('click', function () {
    fileInput.value   = '';
    previewImg.src    = '';
    previewWrap.classList.add('d-none');
    placeholder.classList.remove('d-none');
    removeBtn.classList.add('d-none');
});

// Toggle password visibility
document.querySelectorAll('.toggle-pwd').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const input    = document.getElementById(this.getAttribute('data-target'));
        const icon     = this.querySelector('i');
        const isHidden = input.type === 'password';
        input.type     = isHidden ? 'text' : 'password';
        icon.className = isHidden ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
});
</script>

</body>
</html>