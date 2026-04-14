<?php
// ================================================
// Settings
// public/admin/settings.php
// ================================================

define('REQUIRED_ROLE', 'admin');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

$page_title   = "Settings";
$success      = "";
$error        = "";

$rate         = getRate();
$due_days     = getSetting('due_days') ?? 30;
$system_name  = getSetting('system_name') ?? 'Electricity Billing System';
$city         = getSetting('city') ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_rate    = trim($_POST['rate_per_kwh']);
    $new_due     = trim($_POST['due_days']);
    $new_name    = trim($_POST['system_name']);
    $new_city    = trim($_POST['city']);

    if (empty($new_rate) || !is_numeric($new_rate) || $new_rate <= 0) {
        $error = "Rate must be a valid positive number.";
    } elseif (empty($new_due) || !is_numeric($new_due) || $new_due <= 0) {
        $error = "Due days must be a valid positive number.";
    } else {
        updateRate($new_rate);
        updateSetting('due_days',     $new_due);
        updateSetting('system_name',  $new_name);
        updateSetting('city',         $new_city);

        $rate        = $new_rate;
        $due_days    = $new_due;
        $system_name = $new_name;
        $city        = $new_city;
        $success     = "Settings updated successfully!";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <div class="main-content flex-grow-1">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="page-content">
      <div class="row justify-content-center">
        <div class="col-md-7">

          <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
              <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if ($error): ?>
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-circle me-2"></i>
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>

          <div class="card">
            <div class="card-header">
              <i class="bi bi-gear me-2"></i>System Settings
            </div>
            <div class="card-body p-4">
              <form method="POST">

                <h6 class="text-muted text-uppercase small fw-bold mb-3">
                  Billing Configuration
                </h6>
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Rate per kWh (₱) <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-lightning-charge"></i></span>
                      <input type="number" name="rate_per_kwh" class="form-control"
                             step="0.01" min="0.01"
                             value="<?php echo htmlspecialchars($rate); ?>" required>
                      <span class="input-group-text">per kWh</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Payment Due Days <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                      <input type="number" name="due_days" class="form-control"
                             min="1"
                             value="<?php echo htmlspecialchars($due_days); ?>" required>
                      <span class="input-group-text">days after billing</span>
                    </div>
                  </div>
                </div>

                <h6 class="text-muted text-uppercase small fw-bold mb-3">
                  System Information
                </h6>
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">System Name</label>
                    <input type="text" name="system_name" class="form-control"
                           value="<?php echo htmlspecialchars($system_name); ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">City</label>
                    <input type="text" name="city" class="form-control"
                           value="<?php echo htmlspecialchars($city); ?>">
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                  Submit
                </button>
              </form>
            </div>
          </div>

          <!-- Info card -->
          <div class="card mt-3">
            <div class="card-header">
              <i class="bi bi-info-circle me-2"></i>How Settings Work
            </div>
            <div class="card-body">
              <ul class="small text-muted mb-0">
                <li class="mb-1">Rate per kWh applies to all <strong>new bills</strong> — existing bills are not affected</li>
                <li class="mb-1">Due days sets how many days after billing date the payment is due</li>
                <li>To update an existing bill's amount, use the <strong>View Bills</strong> page</li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>