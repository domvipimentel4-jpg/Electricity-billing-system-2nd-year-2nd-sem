<?php
// ================================================
// Add Bill
// public/admin/add_bill.php
// ================================================

define('REQUIRED_ROLE', 'admin');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

$page_title = "Add Bill";
$success    = "";
$error      = "";
$rate       = getRate();

// Fresh direct query for dropdown
$users_query = $conn->query("SELECT id, firstName, lastname, meter_number FROM user ORDER BY firstName ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id      = isset($_POST['user_id'])     ? intval($_POST['user_id'])    : 0;
    $kwh          = isset($_POST['kwh_consumed']) ? trim($_POST['kwh_consumed']) : '';
    $billing_date = isset($_POST['billing_date']) ? trim($_POST['billing_date']) : '';

    if ($user_id === 0) {
        $error = "Please select a customer.";
    } elseif ($kwh === '' || !is_numeric($kwh) || floatval($kwh) <= 0) {
        $error = "Please enter a valid kWh amount greater than 0.";
    } elseif ($billing_date === '') {
        $error = "Please select a billing date.";
    } else {
        $result = addBill($user_id, floatval($kwh), $billing_date);
        if ($result['success']) {
            $success = "Bill added successfully! Amount due: <strong>₱" .
                       number_format($result['amount'], 2) .
                       "</strong> — Due date: <strong>" .
                       $result['due_date'] . "</strong>";
        } else {
            $error = $result['error'];
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
      <div class="row justify-content-center">
        <div class="col-md-7">
          <div class="card">
            <div class="card-header">
              <i class="bi bi-plus-circle me-2"></i>Add New Bill
              </div>
              <div class="card-body p-4">

                <?php if ($success): ?>
                  <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                  </div>
                  <a href="add_bill" class="btn btn-primary me-2">
                    <i class="bi bi-plus me-1"></i>Add Another Bill
                  </a>
                  <a href="view_bills" class="btn btn-outline-secondary">
                    <i class="bi bi-receipt me-1"></i>View All Bills
                  </a>

              <?php else: ?>

                <?php if ($error): ?>
                  <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                  </div>
                <?php endif; ?>

                <?php if ($users_query && $users_query->num_rows > 0): ?>
                <form method="POST">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">
                      Select Customer <span class="text-danger">*</span>
                    </label>
                    <select name="user_id" class="form-select" required>
                      <option value="">— Choose a customer —</option>
                      <?php while ($u = $users_query->fetch_assoc()): ?>
                        <option value="<?php echo intval($u['id']); ?>"
                          <?php echo (isset($_POST['user_id']) && intval($_POST['user_id']) == intval($u['id'])) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($u['firstName'] . ' ' . $u['lastname']); ?>
                          (Meter: <?php echo htmlspecialchars($u['meter_number'] ?? 'N/A'); ?>)
                        </option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">
                      Billing Date <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           name="billing_date"
                           class="form-control"
                           value="<?php echo isset($_POST['billing_date']) ? htmlspecialchars($_POST['billing_date']) : date('Y-m-d'); ?>"
                           required>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold">
                      kWh Consumed <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           name="kwh_consumed"
                           id="kwh"
                           class="form-control"
                           step="0.01"
                           min="0.01"
                           value="<?php echo isset($_POST['kwh_consumed']) ? htmlspecialchars($_POST['kwh_consumed']) : ''; ?>"
                           placeholder="Enter kWh consumed"
                           required>
                    <div class="form-text">
                      Current rate:
                      <strong>₱<?php echo number_format($rate, 2); ?></strong> per kWh
                    </div>
                  </div>

                  <div class="alert alert-info d-none" id="preview">
                    <i class="bi bi-calculator me-2"></i>
                    Estimated bill: <strong id="preview-amount">₱0.00</strong>
                  </div>

                  <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle me-2"></i>Save Bill
                  </button>
                </form>

                <?php else: ?>
                  <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No users found. Ask users to register first.
                  </div>
                <?php endif; ?>

              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
const rate = <?php echo floatval($rate); ?>;

document.getElementById('kwh')?.addEventListener('input', function () {
    const kwh     = parseFloat(this.value) || 0;
    const amount  = kwh * rate;
    const preview = document.getElementById('preview');
    const el      = document.getElementById('preview-amount');

    if (kwh > 0) {
        el.textContent = '₱' + amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
});
</script>