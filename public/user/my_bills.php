<?php
// ================================================
// My Bills
// public/user/my_bills.php
// ================================================

define('REQUIRED_ROLE', 'user');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

$page_title = "My Bills";
$user_id    = $_SESSION['user_id'];
$bills      = getBillsByUser($user_id);

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <div class="main-content flex-grow-1">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="page-content">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-receipt me-2"></i>My Bills</span>
          <span class="badge bg-primary"><?php echo $bills->num_rows; ?> bills</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Billing Date</th>
                  <th>kWh Consumed</th>
                  <th>Amount Due</th>
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
                  <td><?php echo $row['billing_date']; ?></td>
                  <td><?php echo $row['kwh_consumed']; ?> kWh</td>
                  <td class="fw-semibold">₱<?php echo number_format($row['amount_due'], 2); ?></td>
                  <td>
                    <?php
                    $due   = new DateTime($row['due_date']);
                    $today = new DateTime();
                    $diff  = $today->diff($due)->days;
                    $late  = $today > $due && $row['status'] == 'unpaid';
                    echo $row['due_date'];
                    if ($late) {
                        echo ' <span class="badge bg-danger ms-1">Overdue</span>';
                    } elseif ($row['status'] == 'unpaid' && $diff <= 7) {
                        echo ' <span class="badge bg-warning text-dark ms-1">Due soon</span>';
                    }
                    ?>
                  </td>
                  <td>
                    <?php if ($row['status'] == 'paid'): ?>
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Paid
                      </span>
                    <?php elseif ($late): ?>
                      <span class="badge bg-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>Overdue
                      </span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>Unpaid
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($row['status'] == 'unpaid'): ?>
                      <a href="pay_bill.php?bill_id=<?php echo $row['id']; ?>"
                         class="btn btn-sm btn-success">
                        <i class="bi bi-credit-card me-1"></i>Pay Now
                      </a>
                    <?php else: ?>
                      <span class="text-muted small">
                        <i class="bi bi-check-circle text-success"></i> Paid
                      </span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-receipt" style="font-size:2rem;opacity:0.3;"></i>
                    <p class="mt-2">No bills found yet.</p>
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