<?php
// ================================================
// Reports
// public/admin/reports.php
// ================================================

define('REQUIRED_ROLE', 'admin');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';

$page_title = "Reports";
$report     = getReportSummary();

// Monthly revenue data
global $conn;
$monthly = $conn->query("
    SELECT
        DATE_FORMAT(payment_date, '%b %Y') AS month,
        SUM(amount_paid) AS total
    FROM payment
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY payment_date DESC
    LIMIT 6
");

// Highest Electricity Consumers (total per customer)
$top_consumers = $conn->query("
    SELECT 
        CONCAT(u.firstName, ' ', u.lastname) AS full_name,
        SUM(b.kwh_consumed) AS total_kwh,
        SUM(b.amount_due) AS total_amount
    FROM bill b
    JOIN user u ON b.user_id = u.id
    GROUP BY b.user_id, u.firstName, u.lastname
    ORDER BY total_kwh DESC
    LIMIT 5
");

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <div class="main-content flex-grow-1">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="page-content">

      <!-- Summary Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-4">
          <div class="card text-center p-3">
            <div class="text-primary mb-1">
              <i class="bi bi-people-fill" style="font-size:1.8rem;"></i>
            </div>
            <h4 class="fw-bold mb-0"><?php echo $report['total_users']; ?></h4>
            <small class="text-muted">Total Users</small>
          </div>
        </div>
        <div class="col-md-2 col-sm-4">
          <div class="card text-center p-3">
            <div class="text-success mb-1">
              <i class="bi bi-receipt" style="font-size:1.8rem;"></i>
            </div>
            <h4 class="fw-bold mb-0"><?php echo $report['total_bills']; ?></h4>
            <small class="text-muted">Total Bills</small>
          </div>
        </div>
        <div class="col-md-2 col-sm-4">
          <div class="card text-center p-3">
            <div class="text-success mb-1">
              <i class="bi bi-check-circle" style="font-size:1.8rem;"></i>
            </div>
            <h4 class="fw-bold mb-0"><?php echo $report['total_paid']; ?></h4>
            <small class="text-muted">Paid Bills</small>
          </div>
        </div>
        <div class="col-md-2 col-sm-4">
          <div class="card text-center p-3">
            <div class="text-warning mb-1">
              <i class="bi bi-exclamation-circle" style="font-size:1.8rem;"></i>
            </div>
            <h4 class="fw-bold mb-0"><?php echo $report['total_unpaid']; ?></h4>
            <small class="text-muted">Unpaid Bills</small>
          </div>
        </div>
        <div class="col-md-2 col-sm-4">
          <div class="card text-center p-3">
            <div class="text-purple mb-1">
              <i class="bi bi-cash-stack text-success" style="font-size:1.8rem;"></i>
            </div>
            <h4 class="fw-bold mb-0">₱<?php echo number_format($report['total_revenue'], 2); ?></h4>
            <small class="text-muted">Total Revenue</small>
          </div>
        </div>
        <div class="col-md-2 col-sm-4">
          <div class="card text-center p-3">
            <div class="text-info mb-1">
              <i class="bi bi-lightning-charge" style="font-size:1.8rem;"></i>
            </div>
            <h4 class="fw-bold mb-0">₱<?php echo number_format($report['rate'], 2); ?></h4>
            <small class="text-muted">Rate per kWh</small>
          </div>
        </div>
      </div>

      <!-- Monthly Revenue Table -->
      <div class="card">
        <div class="card-header">
          <i class="bi bi-bar-chart me-2"></i>Monthly Revenue
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>Month</th>
                  <th>Revenue Collected</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($monthly->num_rows > 0):
                  while ($row = $monthly->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $row['month']; ?></td>
                  <td class="fw-semibold text-success">
                    ₱<?php echo number_format($row['total'], 2); ?>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="2" class="text-center text-muted py-4">
                    No payment records yet.
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Highest Electricity Consumers -->
      <div class="card mt-4">
        <div class="card-header">
          <i class="bi bi-lightning-charge me-2"></i>Highest Electricity Consumers
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>Rank</th>
                  <th>Customer Name</th>
                  <th>Total Consumption (kWh)</th>
                  <th>Total Amount Billed</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($top_consumers && $top_consumers->num_rows > 0):
                  $rank = 1;
                  while ($row = $top_consumers->fetch_assoc()): ?>
                <tr>
                  <td><strong><?php echo $rank; ?></strong></td>
                  <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                  <td><?php echo number_format($row['total_kwh'], 2); ?> kWh</td>
                  <td class="fw-semibold text-success">
                    ₱<?php echo number_format($row['total_amount'], 2); ?>
                  </td>
                </tr>
                <?php $rank++; endwhile; else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    No data available.
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