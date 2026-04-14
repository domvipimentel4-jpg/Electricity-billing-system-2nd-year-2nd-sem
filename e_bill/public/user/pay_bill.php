<?php
// ================================================
// Pay Bill
// public/user/pay_bill.php
// ================================================

define('REQUIRED_ROLE', 'user');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

$page_title = "Pay Bill";
$user_id    = $_SESSION['user_id'];
$success    = "";
$error      = "";
$bill       = null;
$receipt_uuid = null;

// Load specific bill if ID provided
if (isset($_GET['bill_id'])) {
    $bill = getBillById(intval($_GET['bill_id']));
    if (!$bill || $bill['status'] === 'paid') {
        $bill  = null;
        $error = "Bill not found or already paid.";
    }
}

// Process payment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bill_id = intval($_POST['bill_id']);
    $amount  = floatval($_POST['amount_due']);
    $method  = trim($_POST['payment_method']);

    $result = payBill($bill_id, $user_id, $amount, $method);
    if ($result['success']) {
        $success      = "Payment successful! Your bill has been marked as paid.";
        $receipt_uuid = $result['payment_uuid'] ?? null;
        $bill         = null;
    } else {
        $error = $result['error'];
    }
}

// Get all unpaid bills for this user
global $conn;
$unpaid = $conn->prepare("SELECT * FROM bill WHERE user_id = ? AND status = 'unpaid' ORDER BY billing_date DESC");
$unpaid->bind_param("i", $user_id);
$unpaid->execute();
$unpaid_bills = $unpaid->get_result();

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
            <div class="alert alert-success d-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill fs-4"></i>
              <div>
                <strong>Payment Successful!</strong><br>
                <?php echo $success; ?>
              </div>
            </div>
            <?php if ($receipt_uuid): ?>
              <a href="download_receipt.php?uuid=<?php echo urlencode($receipt_uuid); ?>" class="btn btn-primary me-2">
                <i class="bi bi-download me-1"></i>Download Receipt
              </a>
            <?php endif; ?>
            <a href="my_bills" class="btn btn-outline-secondary me-2">View My Bills</a>
            <a href="dashboard" class="btn btn-outline-secondary">Dashboard</a>
          <?php else: ?>

            <?php if ($error): ?>
              <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
              </div>
            <?php endif; ?>

            <?php if ($bill): ?>
              <!-- Pay specific bill -->
              <div class="card mb-3">
                <div class="card-header">
                  <i class="bi bi-receipt me-2"></i>Bill Details
                </div>
                <div class="card-body">
                  <div class="row g-2 mb-3">
                    <div class="col-6">
                      <small class="text-muted">Billing Date</small>
                      <p class="fw-semibold mb-0"><?php echo $bill['billing_date']; ?></p>
                    </div>
                    <div class="col-6">
                      <small class="text-muted">Due Date</small>
                      <p class="fw-semibold mb-0"><?php echo $bill['due_date']; ?></p>
                    </div>
                    <div class="col-6">
                      <small class="text-muted">kWh Consumed</small>
                      <p class="fw-semibold mb-0"><?php echo $bill['kwh_consumed']; ?> kWh</p>
                    </div>
                    <div class="col-6">
                      <small class="text-muted">Amount Due</small>
                      <p class="fw-bold text-danger fs-5 mb-0">
                        ₱<?php echo number_format($bill['amount_due'], 2); ?>
                      </p>
                    </div>
                  </div>

                  <form method="POST">
                    <input type="hidden" name="bill_id" value="<?php echo $bill['id']; ?>">
                    <input type="hidden" name="amount_due" value="<?php echo $bill['amount_due']; ?>">
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                      <select name="payment_method" class="form-select" required>
                        <option value="">— Select payment method —</option>
                        <option value="cash">
                          <i class="bi bi-cash"></i> Cash
                        </option>
                        <option value="gcash">GCash</option>
                        <option value="maya">Maya</option>
                        <option value="bank">Bank Transfer</option>
                      </select>
                    </div>
                    <div class="alert alert-info">
                      <i class="bi bi-info-circle me-2"></i>
                      You are about to pay
                      <strong>₱<?php echo number_format($bill['amount_due'], 2); ?></strong>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                      Confirm Payment — ₱<?php echo number_format($bill['amount_due'], 2); ?>
                    </button>
                  </form>
                </div>
              </div>

            <?php else: ?>
              <!-- Show list of unpaid bills -->
              <div class="card">
                <div class="card-header">
                  <i class="bi bi-credit-card me-2"></i>Select a Bill to Pay
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th>Billing Date</th>
                          <th>kWh</th>
                          <th>Amount Due</th>
                          <th>Due Date</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($unpaid_bills->num_rows > 0):
                          while ($row = $unpaid_bills->fetch_assoc()): ?>
                        <tr>
                          <td><?php echo $row['billing_date']; ?></td>
                          <td><?php echo $row['kwh_consumed']; ?> kWh</td>
                          <td class="fw-semibold text-danger">
                            ₱<?php echo number_format($row['amount_due'], 2); ?>
                          </td>
                          <td><?php echo $row['due_date']; ?></td>
                          <td>
                            <a href="pay_bill?bill_id=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-success">
                              <i class="bi bi-credit-card me-1"></i>Pay Now
                            </a>
                          </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                          <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-check-circle text-success" style="font-size:2rem;"></i>
                            <p class="mt-2">No unpaid bills. You're all caught up!</p>
                          </td>
                        </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            <?php endif; ?>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>