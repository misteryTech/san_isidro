<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* ===========================
   TOTAL USERS COUNT
   =========================== */
$userCountStmt = $conn->prepare("
    SELECT COUNT(*) AS total_users
    FROM user_table
");
$userCountStmt->execute();
$userResult = $userCountStmt->get_result();
$totalUsers = $userResult->fetch_assoc()['total_users'];

/* ===========================
   PENDING MEMBERSHIP COUNT
   =========================== */
$pendingStmt = $conn->prepare("
    SELECT COUNT(*) AS total_pending
    FROM membership_table
    WHERE status = 'Pending'
");
$pendingStmt->execute();
$pendingResult = $pendingStmt->get_result();
$totalPending = $pendingResult->fetch_assoc()['total_pending'];

/* ===========================
   DECEASED MEMBERS REQUEST COUNT
   =========================== */
$deceasedStmt = $conn->prepare("
    SELECT COUNT(*) AS total_deceased_requests
    FROM deceased_benefit_applications
    WHERE status = 'Pending'
");
$deceasedStmt->execute();
$deceasedResult = $deceasedStmt->get_result();
$totalDeceasedRequests = $deceasedResult->fetch_assoc()['total_deceased_requests'];

/* ===========================
   PENDING PAYMENTS COUNT
   =========================== */
$paymentStmt = $conn->prepare("
    SELECT COUNT(*) AS total_pending_payments
    FROM payments
    WHERE payment_status = 'Pending'
");
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();
$totalPendingPayments = $paymentResult->fetch_assoc()['total_pending_payments'];
?>

<section class="section dashboard">
    <div class="row">

        <!-- Total Users Card -->
        <div class="col-xxl-3 col-md-3">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Users <span>| Total</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?= number_format($totalUsers); ?></h6>
                            <span class="text-muted small pt-2">Registered users</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Members Card -->
        <div class="col-xxl-3 col-md-3">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Members <span>| Pending</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?= number_format($totalPending); ?></h6>
                            <span class="text-muted small pt-2">Pending membership requests</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deceased Members Requests Card -->
        <div class="col-xxl-3 col-md-3">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Deceased <span>| Requests</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-heartbreak"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?= number_format($totalDeceasedRequests); ?></h6>
                            <span class="text-muted small pt-2">Pending deceased requests</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Payments Card -->
        <div class="col-xxl-3 col-md-3">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title">Payments <span>| Pending</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="ps-3">
                            <h6><?= number_format($totalPendingPayments); ?></h6>
                            <span class="text-muted small pt-2">Pending payments</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<div class="row">

   <div class="col-8">
    <div class="card banner-card mb-3">
        <img
            src="/path/to/your/banner.jpg"
            class="card-img-top"
            alt="Dashboard Banner"
            style="object-fit: cover; height: 200px; width: 100%; border-radius: .5rem;">
        <div class="card-body text-center">
            <h5 class="card-title text-white">Welcome to the Dashboard</h5>
            <p class="card-text text-white-50">Here you can see the latest updates and stats.</p>
        </div>
    </div>
</div>


    <div class="col-4">
        <div class="card recent-deceased-list-card">
            <div class="card-body">
                <h5 class="card-title">Recently Deceased Today</h5>
                <ul class="list-group list-group-flush">
                    <?php
                    // Fetch recently deceased today
                    $today = date('Y-m-d'); // Current date

                    $recentDeceasedStmt = $conn->prepare("
                        SELECT ut.first_name, ut.last_name, d.updated_at
                        FROM deceased_benefit_applications AS d
                        LEFT JOIN user_table AS ut ON d.osca_id = ut.osca_id
                        WHERE DATE(d.updated_at) = ?
                        ORDER BY d.updated_at DESC
                    ");
                    $recentDeceasedStmt->bind_param("s", $today);
                    $recentDeceasedStmt->execute();
                    $recentDeceasedResult = $recentDeceasedStmt->get_result();

                    if($recentDeceasedResult->num_rows > 0):
                        while($row = $recentDeceasedResult->fetch_assoc()):
                    ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                <span class="badge bg-danger rounded-pill">
                                    <?= date('F j, Y', strtotime($row['updated_at'])); ?>
                                </span>
                            </li>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <li class="list-group-item text-center text-muted">
                            No deceased members reported today.
                        </li>
                    <?php
                    endif;


                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>


</section>

<?php
    $recentDeceasedStmt->close();
$userCountStmt->close();
$pendingStmt->close();
$deceasedStmt->close();
$paymentStmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/dashboard.js"></script>
