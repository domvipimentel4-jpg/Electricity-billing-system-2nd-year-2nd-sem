<?php
// ================================================
// View Bills
// public/admin/view_bills.php
// ================================================

define('REQUIRED_ROLE', 'admin');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

$page_title = "View Bills";
$success    = "";

// Toggle bill status
if (isset($_GET['toggle']) && isset($_GET['status'])) {
    $bill_id = intval($_GET['toggle']);
    $result  = toggleBillStatus($bill_id, $_GET['status']);
    if ($result['success']) {
        $success = "Bill status updated to " . strtoupper($result['status']) . ".";
    }
}

$bills = getAllBills();

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

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-receipt me-2"></i>All Bills</span>
          <a href="add_bill" class="btn btn-sm btn-primary">
            <i class="bi bi-plus me-1"></i>Add Bill
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Customer</th>
                  <th>Meter No.</th>
                  <th>kWh</th>
                  <th>Amount Due</th>
                  <th>Billing Date</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($bills->num_rows > 0):
                  $count = 1;
                  while ($row = $bills->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $count++; ?></td>
                  <td class="fw-semibold"><?php echo htmlspecialchars($row['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($row['meter_number']); ?></td>
                  <td><?php echo $row['kwh_consumed']; ?></td>
                  <td>₱<?php echo number_format($row['amount_due'], 2); ?></td>
                  <td><?php echo $row['billing_date']; ?></td>
                  <td><?php echo $row['due_date']; ?></td>
                  <td>
                    <?php if ($row['status'] == 'paid'): ?>
                      <span class="badge bg-success">Paid</span>
                    <?php elseif ($row['status'] == 'overdue'): ?>
                      <span class="badge bg-danger">Overdue</span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark">Unpaid</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($row['status'] == 'unpaid'): ?>
                      <a href="?toggle=<?php echo $row['id']; ?>&status=unpaid"
                         class="btn btn-sm btn-success"
                         onclick="return confirm('Mark as PAID?')">
                        <i class="bi bi-check"></i> Mark Paid
                      </a>
                    <?php elseif ($row['status'] == 'paid'): ?>
                      <a href="?toggle=<?php echo $row['id']; ?>&status=paid"
                         class="btn btn-sm btn-warning"
                         onclick="return confirm('Mark as UNPAID?')">
                        <i class="bi bi-arrow-counterclockwise"></i> Unpaid
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="9" class="text-center text-muted py-4">
                    No bills found. <a href="add_bill.php">Add one now</a>.
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