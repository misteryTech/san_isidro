<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// Validate osca_id from URL
if (!isset($_GET['osca_id']) || empty($_GET['osca_id'])) {
    echo "<div class='alert alert-danger'>Invalid OSCA ID.</div>";
    exit;
}
$osca_id = $_GET['osca_id'];

// Fetch user info securely
$stmt = $conn->prepare("SELECT * FROM user_table WHERE osca_id = ? LIMIT 1");
$stmt->bind_param("s", $osca_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>No user record found.</div>";
    exit;
}

$user = $result->fetch_assoc();

// Fetch deceased_benefit_id for this user
$stmtBenefit = $conn->prepare("SELECT id AS deceased_benefit_id FROM deceased_benefit_applications WHERE osca_id = ? LIMIT 1");
$stmtBenefit->bind_param("s", $osca_id);
$stmtBenefit->execute();
$benefitResult = $stmtBenefit->get_result();

if ($benefitResult->num_rows === 0) {
    echo "<div class='alert alert-warning'>No deceased benefit record found.</div>";
    exit;
}

$benefit = $benefitResult->fetch_assoc();
$deceased_benefit_id = $benefit['deceased_benefit_id'];

// Fetch Paid Users
$stmtPaid = $conn->prepare("
    SELECT u.osca_id, CONCAT(u.first_name,' ',u.last_name) AS fullname, p.amount, p.payment_date
    FROM payments p
    INNER JOIN user_table u ON u.id = p.user_id
    WHERE p.user_id = ? AND p.deceased_benefit_id = ? AND p.payment_status = 'completed'
");
$stmtPaid->bind_param("si", $osca_id, $deceased_benefit_id);
$stmtPaid->execute();
$result_paid = $stmtPaid->get_result();

// Fetch Unpaid Users
$stmtUnpaid = $conn->prepare("
    SELECT u.osca_id, CONCAT(u.first_name,' ',u.last_name) AS fullname
    FROM user_table u
    INNER JOIN deceased_benefit_applications dba ON dba.osca_id = u.osca_id
    WHERE u.osca_id = ? AND dba.id = ?
      AND NOT EXISTS (
        SELECT 1 FROM payments p
        WHERE p.user_id = u.id
          AND p.deceased_benefit_id = dba.id
          AND p.payment_status = 'completed'
      )
");
$stmtUnpaid->bind_param("si", $osca_id, $deceased_benefit_id);
$stmtUnpaid->execute();
$result_unpaid = $stmtUnpaid->get_result();
?>

<section class="section">
    <div class="row">
        <!-- User Profile Card -->
        <div class="col-lg-12 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-primary mb-0">Deceased Profile Information</h4>
                        <button class="btn btn-outline-secondary btn-sm" onclick="history.back()">
                            <i class="bi bi-arrow-left"></i> Back
                        </button>
                    </div>

                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <small class="text-muted">Full Name</small>
                            <div class="fw-bold fs-5"><?= htmlspecialchars($user['first_name'].' '.$user['last_name']); ?></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Chapter</small>
                            <div class="fw-bold fs-5"><?= htmlspecialchars($user['chapter'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">OSCA ID</small>
                            <div class="fw-bold fs-5"><?= htmlspecialchars($user['osca_id']); ?></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Status</small>
                            <div class="fw-bold fs-5"><?= htmlspecialchars($user['status']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paid / Unpaid Tables -->
        <div class="col-lg-12 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <!-- PAID USERS -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-success mb-3">
                                        <i class="bi bi-check-circle"></i> Paid Users
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead class="table-success">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>OSCA ID</th>
                                                    <th>Amount</th>
                                                    <th>Date Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result_paid->num_rows > 0): ?>
                                                    <?php $i = 1; while($row = $result_paid->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= $i++; ?></td>
                                                            <td><?= htmlspecialchars($row['fullname']); ?></td>
                                                            <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                                            <td>₱<?= number_format($row['amount'],2); ?></td>
                                                            <td><?= date('M d, Y', strtotime($row['payment_date'])); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">No paid users found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- UNPAID USERS -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-danger mb-3">
                                        <i class="bi bi-x-circle"></i> Unpaid Users
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead class="table-danger">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>OSCA ID</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result_unpaid->num_rows > 0): ?>
                                                    <?php $i = 1; while($row = $result_unpaid->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= $i++; ?></td>
                                                            <td><?= htmlspecialchars($row['fullname']); ?></td>
                                                            <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                                            <td><span class="badge bg-danger">Unpaid</span></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No unpaid users found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- row -->
                </div>
            </div>
        </div>

    </div> <!-- main row -->
</section>

<?php
$stmt->close();
$stmtBenefit->close();
$stmtPaid->close();
$stmtUnpaid->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
