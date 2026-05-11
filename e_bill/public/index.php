<?php
// ================================================
// Login Page
// public/index.php
// ================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/controller/auth_controller.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Handle logout
if (isset($_GET['logout'])) {
    logoutUser();
}

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $result = loginUser($username, $password);
        if ($result['success']) {
            if ($result['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
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
  <title>Login — Electricity Billing System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        overflow: hidden;
        width: 100%;
        max-width: 420px;
    }
    .login-card::before {
        content: '';
        display: block;
        height: 5px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
    }
    .login-card .card-body { padding: 2.5rem; }
    .form-control {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        background: #f8fafc;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        background: #fff;
    }
    .btn-login {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border: none;
        border-radius: 8px;
        padding: 11px;
        font-weight: 600;
        color: #fff;
        width: 100%;
    }
    .btn-login:hover {
        background: linear-gradient(135deg, #1e40af, #2563eb);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(37,99,235,0.4);
    }
    #togglePassword {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-left: none;
        border-radius: 0 8px 8px 0;
        padding: 0 14px;
        cursor: pointer;
        color: #64748b;
        transition: all 0.2s;
    }
    #togglePassword:hover {
        background: #e2e8f0;
        color: #334155;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card login-card">
        <div class="card-body">

          <div class="text-center mb-4">
            <div class="mb-2">
              <i class="bi bi-lightning-charge-fill text-warning"
                 style="font-size: 2.5rem;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Electricity Billing</h4>
            <p class="text-muted small">Sign in to your account</p>
          </div>

          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
              <i class="bi bi-exclamation-circle-fill"></i>
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label class="form-label fw-semibold text-secondary small">Username</label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                  <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" name="username" class="form-control border-start-0"
                       placeholder="Enter your username" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold text-secondary small">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                  <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" id="password"
                       class="form-control border-start-0 border-end-0"
                       placeholder="Enter your password" required>
                <button type="button" id="togglePassword">
                  <i class="bi bi-eye-slash" id="eyeIcon"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-login mb-3">
              <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>

            <p class="text-center text-muted small mb-0">
              Don't have an account?
              <a href="register" class="text-primary fw-semibold">Register here</a>
            </p>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
const toggleBtn     = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const eyeIcon       = document.getElementById('eyeIcon');

toggleBtn.addEventListener('click', function () {
    const isHidden     = passwordInput.type === 'password';
    passwordInput.type = isHidden ? 'text' : 'password';
    eyeIcon.className  = isHidden ? 'bi bi-eye' : 'bi bi-eye-slash';
});
</script>

</body>
</html>