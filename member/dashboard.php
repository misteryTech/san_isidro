<?php
session_start();
ob_start(); // Capture page content
require_once __DIR__ . '/../database/connection.php';

$osca_id = $_SESSION['osca_id'] ?? null;
$currentYear = date('Y');

if (!$osca_id) {
    // If no osca_id in session, redirect to login or show error
    header("Location: login.php");
    exit;
}

// Function to check if a record exists
function isPaid($conn, $table, $osca_id, $yearColumn = null, $year = null) {
    if ($yearColumn && $year) {
        $sql = "SELECT 1 FROM $table WHERE osca_id = ? AND YEAR($yearColumn) = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $osca_id, $year);
    } else {
        $sql = "SELECT 1 FROM $table WHERE osca_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $osca_id);
    }
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Check payment statuses
$membershipPaid = isPaid($conn, "membership_fees", $osca_id);
$monthlyPaid    = isPaid($conn, "monthly_dues", $osca_id, "month", $currentYear);
$regionalPaid   = isPaid($conn, "regional_fees", $osca_id);

// Redirect if regional fees are unpaid

// Optional: Check pending membership (you already have this logic)
$stmt = $conn->prepare("SELECT status FROM membership_table WHERE osca_id = ? LIMIT 1");
$stmt->bind_param("s", $osca_id);
$stmt->execute();
$stmt->store_result();
$hasPending = $stmt->num_rows > 0;

?>


<section class="section">
        <div class="row">
        <!-- Membership Upgrade Announcement -->
        <?php if ($_SESSION['account'] !== "Regular") : ?>
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h3 class="card-title fw-bold">Upgrade to Regular Membership</h3>
                        <p class="mb-3">
                            As part of our community growth, we are now offering all Associate Members
                            the opportunity to upgrade into <strong>Regular Members</strong>.
                        </p>

                        <div class="alert alert-primary">
                            Enjoy more benefits, full voting rights, and exclusive member privileges.
                        </div>

                                                        <?php if (!$hasPending): ?>
                                        <a href="member_upgrade.php" class="btn btn-primary btn-lg mt-2">
                                            Upgrade Now
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark mt-3">
                                            Membership Request Pending
                                        </span>
                                    <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Carousel Section -->



<div class="col-lg-7">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title">Your Payment Status</h5>
            <ul class="list-group mb-3">
                <li class="list-group-item">
                    Membership Fee:
                    <span class="badge bg-<?php echo $membershipPaid ? 'success' : 'danger'; ?>">
                        <?php echo $membershipPaid ? 'Paid' : 'Unpaid'; ?>
                    </span>
                </li>
                <li class="list-group-item">
                    Monthly Dues (<?php echo $currentYear; ?>):
                    <span class="badge bg-<?php echo $monthlyPaid ? 'success' : 'danger'; ?>">
                        <?php echo $monthlyPaid ? 'Paid' : 'Unpaid'; ?>
                    </span>
                </li>
                <li class="list-group-item">
                    Regional Fees:
                    <span class="badge bg-<?php echo $regionalPaid ? 'success' : 'danger'; ?>">
                        <?php echo $regionalPaid ? 'Paid' : 'Redirecting...'; ?>
                    </span>
                </li>
            </ul>

            <!-- Existing Carousel -->
            <h5 class="card-title">Announcements Carousel</h5>
            <p class="text-muted">Stay updated with the latest events, programs, and announcements.</p>

            <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../assets/img/slides-1.jpg" class="d-block w-100" alt="Slide 1">
                    </div>
                    <div class="carousel-item">
                        <img src="../assets/img/slides-2.jpg" class="d-block w-100" alt="Slide 2">
                    </div>
                    <div class="carousel-item">
                        <img src="../assets/img/slides-3.jpg" class="d-block w-100" alt="Slide 3">
                    </div>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>
</div>


        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">
                        Monthly Published List of Deceased Beneficiaries
                    </h5>

                    <p class="text-muted small mb-3">
                        Official record of approved deceased benefit applications for the current month.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="approvedTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name of Deceased</th>
                                    <th>OSCA ID</th>
                                    <th>Month Published</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JavaScript will inject rows here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/display_deceased.js"></script>