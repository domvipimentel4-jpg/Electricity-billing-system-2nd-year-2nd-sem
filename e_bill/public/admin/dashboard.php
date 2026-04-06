<?php
// ================================================
// Admin Dashboard
// public/admin/dashboard.php
// ================================================

define('REQUIRED_ROLE', 'admin');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/middleware/auth_middleware.php';
require_once __DIR__ . '/../../app/controller/bill_controller.php';
require_once __DIR__ . '/../../app/controller/customer_controller.php';

$page_title = "Dashboard";
$report     = getReportSummary();

require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

  <div class="main-content flex-grow-1">
    <?php require_once __DIR__ . '/includes/topbar.php'; ?>

    <div class="page-content">

      <!-- Stat Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: linear-gradient(135deg,#2563eb,#1e40af);">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="small opacity-75 mb-1">Total Users</div>
                <h3 class="fw-bold mb-0"><?php echo $report['total_users']; ?></h3>
              </div>
              <i class="bi bi-people-fill" style="font-size:2rem;opacity:0.4;"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: linear-gradient(135deg,#059669,#047857);">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="small opacity-75 mb-1">Total Bills</div>
                <h3 class="fw-bold mb-0"><?php echo $report['total_bills']; ?></h3>
              </div>
              <i class="bi bi-receipt" style="font-size:2rem;opacity:0.4;"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: linear-gradient(135deg,#d97706,#b45309);">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="small opacity-75 mb-1">Unpaid Bills</div>
                <h3 class="fw-bold mb-0"><?php echo $report['total_unpaid']; ?></h3>
              </div>
              <i class="bi bi-exclamation-circle" style="font-size:2rem;opacity:0.4;"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="stat-card" style="background: linear-gradient(135deg,#7c3aed,#6d28d9);">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="small opacity-75 mb-1">Total Revenue</div>
                <h3 class="fw-bold mb-0">₱<?php echo number_format($report['total_revenue'], 2); ?></h3>
              </div>
              <i class="bi bi-cash-stack" style="font-size:2rem;opacity:0.4;"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <i class="bi bi-grid me-2"></i>Quick Actions
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                  <a href="manage_users.php" class="btn btn-primary w-100 py-3">
                    <i class="bi bi-people d-block mb-1" style="font-size:1.5rem;"></i>
                    Manage Users
                  </a>
                </div>
                <div class="col-md-3 col-sm-6">
                  <a href="add_bill.php" class="btn btn-success w-100 py-3">
                    <i class="bi bi-plus-circle d-block mb-1" style="font-size:1.5rem;"></i>
                    Add Bill
                  </a>
                </div>
                <div class="col-md-3 col-sm-6">
                  <a href="view_bills.php" class="btn btn-warning w-100 py-3">
                    <i class="bi bi-receipt d-block mb-1" style="font-size:1.5rem;"></i>
                    View Bills
                  </a>
                </div>
                <div class="col-md-3 col-sm-6">
                  <a href="reports.php" class="btn btn-info w-100 py-3 text-white">
                    <i class="bi bi-bar-chart d-block mb-1" style="font-size:1.5rem;"></i>
                    Reports
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Bills -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clock-history me-2"></i>Recent Bills</span>
          <a href="view_bills.php" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th>Meter No.</th>
                  <th>kWh</th>
                  <th>Amount</th>
                  <th>Due Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                global $conn;
                $recent = $conn->query("
                    SELECT b.*, CONCAT(u.firstName,' ',u.lastname) AS full_name,
                           u.meter_number
                    FROM bill b
                    JOIN user u ON b.user_id = u.id
                    ORDER BY b.dateCreated DESC
                    LIMIT 5
                ");
                if ($recent->num_rows > 0):
                    while ($row = $recent->fetch_assoc()):
                ?>
                <tr>
                  <td class="fw-semibold"><?php echo htmlspecialchars($row['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($row['meter_number']); ?></td>
                  <td><?php echo $row['kwh_consumed']; ?></td>
                  <td>₱<?php echo number_format($row['amount_due'], 2); ?></td>
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
                </tr>
                <?php endwhile; else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    No bills found. <a href="add_bill.php">Add one now</a>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div><!-- end page-content -->
  </div><!-- end main-content -->
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>