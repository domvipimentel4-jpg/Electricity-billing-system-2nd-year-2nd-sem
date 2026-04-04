<?php
// ================================================
// User Registration Page
// public/register.php
// ================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/controller/auth_controller.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$success = "";
$error   = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate passwords match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = "Passwords do not match.";
    } elseif (strlen($_POST['password']) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (empty($_POST['firstName']) || empty($_POST['lastname']) ||
              empty($_POST['username']) || empty($_POST['email']) ||
              empty($_POST['meter_number']) || empty($_POST['contact_number']) ||
              empty($_POST['date_of_birth']) || empty($_POST['street']) ||
              empty($_POST['barangay']) || empty($_POST['city'])) {
        $error = "Please fill in all required fields.";
    } else {
        $data = [
            'firstName'      => trim($_POST['firstName']),
            'middleName'     => trim($_POST['middleName']),
            'lastname'       => trim($_POST['lastname']),
            'email'          => trim($_POST['email']),
            'username'       => trim($_POST['username']),
            'password'       => $_POST['password'],
            'meter_number'   => trim($_POST['meter_number']),
            'contact_number' => trim($_POST['contact_number']),
            'date_of_birth'  => trim($_POST['date_of_birth']),
            'street'         => trim($_POST['street']),
            'barangay'       => trim($_POST['barangay']),
            'city'           => trim($_POST['city']),
        ];

        $result = registerUser($data);
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
    }
    .btn-register:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(37,99,235,0.4);
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card register-card">
        <div class="card-body">

          <div class="text-center mb-4">
            <i class="bi bi-lightning-charge-fill text-warning"
               style="font-size: 2.5rem;"></i>
            <h4 class="fw-bold text-dark mb-1 mt-2">Create an Account</h4>
            <p class="text-muted small">Register to view and pay your electricity bills</p>
          </div>

          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill"></i>
              <?php echo $success; ?>
              <a href="index.php" class="ms-auto btn btn-success btn-sm">Login Now</a>
            </div>
          <?php endif; ?>

          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-circle-fill"></i>
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>

          <form method="POST">

            <div class="section-title">
              <i class="bi bi-person me-1"></i> Personal Information
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                <input type="text" name="firstName" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['firstName'] ?? ''); ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Middle Name</label>
                <input type="text" name="middleName" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['middleName'] ?? ''); ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="lastname" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Contact Number <span class="text-danger">*</span></label>
                <input type="tel" name="contact_number" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['contact_number'] ?? ''); ?>"
                       placeholder="e.g. +639171234567" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" name="date_of_birth" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Meter Number <span class="text-danger">*</span></label>
                <input type="text" name="meter_number" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['meter_number'] ?? ''); ?>"
                       placeholder="e.g. MTR-001" required>
              </div>
            </div>

            <div class="section-title">
              <i class="bi bi-geo-alt me-1"></i> Address
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Street <span class="text-danger">*</span></label>
                <input type="text" name="street" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['street'] ?? ''); ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Barangay <span class="text-danger">*</span></label>
                <input type="text" name="barangay" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['barangay'] ?? ''); ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">City <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
              </div>
            </div>

            <div class="section-title">
              <i class="bi bi-shield-lock me-1"></i> Account Credentials
            </div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 characters" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" required>
              </div>
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-register">
                <i class="bi bi-person-plus me-2"></i>Create Account
              </button>
            </div>

            <p class="text-center text-muted small mt-3 mb-0">
              Already have an account?
              <a href="index.php" class="text-primary fw-semibold">Sign in here</a>
            </p>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>