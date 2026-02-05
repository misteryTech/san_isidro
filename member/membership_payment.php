<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/connection.php';

$osca_id = $_SESSION['osca_id'] ?? null;

if (!$osca_id) {
    header("Location: login.php");
    exit;
}

// Function to fetch records
function fetchRecords($conn, $table, $osca_id) {
    $sql = "SELECT * FROM $table WHERE osca_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $osca_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all payments
$membership = fetchRecords($conn, "membership_fees", $osca_id);
$monthly_dues = fetchRecords($conn, "monthly_dues", $osca_id); // Removed year filter
$regional = fetchRecords($conn, "regional_fees", $osca_id);
?>

<section class="section">
    <div class="row">
        <!-- Membership Card -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Membership Fee</h5>
                    <?php if ($membership->num_rows > 0): ?>
                        <span class="badge bg-success fs-6">Already Paid</span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6">Not Paid</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Monthly Dues Table -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Monthly Dues</h5>
                    <?php if ($monthly_dues->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $monthly_dues->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('F', strtotime($row['month'])); ?></td>
                                        <td><?php echo date('Y', strtotime($row['month'])); ?></td>
                                        <td><?php echo number_format($row['amount'], 2); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['month'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No monthly dues paid yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Regional Fees Side by Side -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Regional Fees</h5>
                    <?php if ($regional->num_rows > 0): ?>
                        <span class="badge bg-success fs-6">Paid</span>
                        <ul class="list-group list-group-flush mt-2">
                            <?php while ($row = $regional->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($row['description'] ?? 'Regional Fee'); ?> -
                                    <?php echo number_format($row['amount'], 2); ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <span class="badge bg-danger fs-6">Not Paid</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Optional: Another card next to Regional Fees -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title">Other Regional Info</h5>
                    <p class="text-muted">You can place additional info or links here.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
