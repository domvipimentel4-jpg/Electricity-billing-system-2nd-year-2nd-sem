<?php
// ================================================
// User Dashboard
// public/user/dashboard.php
// ================================================

define('REQUIRED_ROLE', 'user');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

$page_title = "My Dashboard";
$user_id    = $_SESSION['user_id'];

// Get user's bill summary
global $conn;
$summary = $conn->prepare("
    SELECT
        COUNT(*) AS total_bills,
        SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) AS unpaid_count,
        SUM(CASE WHEN status = 'paid'   THEN 1 ELSE 0 END) AS paid_count,
        SUM(CASE WHEN status = 'unpaid' THEN amount_due ELSE 0 END) AS total_due
    FROM bill WHERE user_id = ?
");
$summary->bind_param("i", $user_id);
$summary->execute();
$stats = $summary->get_result()->fetch_assoc();

// Get recent bills
$recent = $conn->prepare("
    SELECT * FROM bill
    WHERE user_id = ?
    ORDER BY billing_date DESC
    LIMIT 5
");
$recent->bind_param("i", $user_id);
$recent->execute();
$recent_bills = $recent->get_result();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <div class="main-content flex-grow-1">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="page-content">

      <!-- Welcome Banner -->
      <div class="alert border-0 mb-4"
           style="background: linear-gradient(135deg,#0f4c75,#1a6fa3);color:#fff;border-radius:12px;">
        <div class="d-flex align-items-center gap-3">
          <i class="bi bi-person-circle" style="font-size:2.5rem;opacity:0.8;"></i>
          <div>
            <h5 class="fw-bold mb-0">
              Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!
            </h5>
            <small class="opacity-75">
              <?php echo date('l, F d, Y'); ?>
            </small>
          </div>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
          <div class="card text-center p-3">
            <i class="bi bi-receipt text-primary mb-2" style="font-size:2rem;"></i>
            <h3 class="fw-bold mb-0"><?php echo $stats['total_bills'] ?? 0; ?></h3>
            <small class="text-muted">Total Bills</small>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card text-center p-3">
            <i class="bi bi-exclamation-circle text-warning mb-2" style="font-size:2rem;"></i>
            <h3 class="fw-bold mb-0"><?php echo $stats['unpaid_count'] ?? 0; ?></h3>
            <small class="text-muted">Unpaid Bills</small>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card text-center p-3">
            <i class="bi bi-check-circle text-success mb-2" style="font-size:2rem;"></i>
            <h3 class="fw-bold mb-0"><?php echo $stats['paid_count'] ?? 0; ?></h3>
            <small class="text-muted">Paid Bills</small>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card text-center p-3">
            <i class="bi bi-cash text-danger mb-2" style="font-size:2rem;"></i>
            <h3 class="fw-bold mb-0">
              ₱<?php echo number_format($stats['total_due'] ?? 0, 2); ?>
            </h3>
            <small class="text-muted">Total Due</small>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <a href="my_bills" class="btn btn-primary w-100 py-3">
            <i class="bi bi-receipt d-block mb-1" style="font-size:1.5rem;"></i>
            View My Bills
          </a>
        </div>
        <div class="col-md-4">
          <a href="pay_bill" class="btn btn-success w-100 py-3">
            <i class="bi bi-credit-card d-block mb-1" style="font-size:1.5rem;"></i>
            Pay a Bill
          </a>
        </div>
        <div class="col-md-4">
          <a href="profile" class="btn btn-outline-secondary w-100 py-3">
            <i class="bi bi-person d-block mb-1" style="font-size:1.5rem;"></i>
            My Profile
          </a>
        </div>
      </div>

      <!-- Recent Bills -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clock-history me-2"></i>Recent Bills</span>
          <a href="my_bills" class="btn btn-sm btn-outline-primary">View All</a>
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
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($recent_bills->num_rows > 0):
                  while ($row = $recent_bills->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $row['billing_date']; ?></td>
                  <td><?php echo $row['kwh_consumed']; ?></td>
                  <td class="fw-semibold">₱<?php echo number_format($row['amount_due'], 2); ?></td>
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
                      <a href="pay_bill?bill_id=<?php echo $row['id']; ?>"
                         class="btn btn-sm btn-success">
                        <i class="bi bi-credit-card"></i> Pay
                      </a>
                    <?php else: ?>
                      <span class="text-muted small">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    No bills yet. Your bills will appear here once added.
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