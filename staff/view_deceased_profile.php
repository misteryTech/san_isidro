<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* =======================
   VALIDATE OSCA ID
======================= */
if (empty($_GET['osca_id'])) {
    echo "<div class='alert alert-danger'>Invalid OSCA ID.</div>";
    exit;
}
$osca_id = $_GET['osca_id'];

/* =======================
   FETCH USER INFO
======================= */
$userStmt = $conn->prepare("
    SELECT osca_id, first_name, last_name, chapter, status
    FROM user_table
    WHERE osca_id = ?
    LIMIT 1
");
$userStmt->bind_param("s", $osca_id);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    echo "<div class='alert alert-warning'>No user record found.</div>";
    exit;
}
$user = $userResult->fetch_assoc();

/* =======================
   FETCH TOTAL PAYMENTS
======================= */
$paymentStmt = $conn->prepare("
    SELECT
        COUNT(*) AS total_payments,
        COALESCE(SUM(amount), 0) AS total_amount
    FROM payments
    WHERE osca_id = ?
");
$paymentStmt->bind_param("s", $osca_id);
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();
$payment = $paymentResult->fetch_assoc();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <!-- USER PROFILE -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="text-primary mb-0">Member Payment Profile</h4>
                        <button class="btn btn-outline-secondary btn-sm" onclick="history.back()">
                            <i class="bi bi-arrow-left"></i> Back
                        </button>
                    </div>

                    <div class="row text-center">
                        <div class="col-md-3">
                            <small class="text-muted">Full Name</small>
                            <div class="fw-bold">
                                <?= htmlspecialchars($user['first_name'].' '.$user['last_name']); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Chapter</small>
                            <div class="fw-bold"><?= htmlspecialchars($user['chapter'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">OSCA ID</small>
                            <div class="fw-bold"><?= htmlspecialchars($user['osca_id']); ?></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Status</small>
                            <div class="fw-bold"><?= htmlspecialchars($user['status']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAYMENT SUMMARY -->
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Payment Summary</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Total Payments Made</small>
                            <div class="fs-4 fw-bold">
                                <?= (int)$payment['total_payments']; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Total Amount Paid</small>
                            <div class="fs-4 fw-bold text-success">
                                ₱<?= number_format($payment['total_amount'], 2); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php
$userStmt->close();
$paymentStmt->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
