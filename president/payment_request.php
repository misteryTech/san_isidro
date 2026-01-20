<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* ===============================
   FETCH PAYMENT RELEASE REQUESTS
================================ */
$sql = "
    SELECT
        prr.osca_id AS prr_osca_id,
        prr.release_amount,
        prr.released_method,
        prr.requested_by,
        prr.requested_at,
        prr.status,
        CONCAT(ut.first_name, ' ', ut.last_name) AS requester_name
    FROM payment_release_requests prr
    INNER JOIN user_table ut
        ON prr.osca_id = ut.osca_id
    ORDER BY prr.requested_at DESC
";


$stmt   = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Payment Release Requests</h5>

                    <table class="table table-striped table-bordered datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>Deceased Benefit ID</th>
                                <th>Release Amount</th>
                                <th>Release Method</th>
                                <th>Requested By</th>
                                <th>Requested At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $counter++;
                                    $viewModalId   = "viewModal" . $counter;
                                    $acceptModalId = "acceptModal" . $counter;
                                    $declineModalId = "declineModal" . $counter;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['prr_osca_id']); ?></td>
                                    <td>₱<?= number_format($row['release_amount'], 2); ?></td>
                                    <td><?= htmlspecialchars($row['released_method']); ?></td>
                                    <td><h4>Treasurer</h4></td>
                                    <td><?= htmlspecialchars(date('F d, Y h:i A', strtotime($row['requested_at']))); ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status'] === 'Approved' ? 'success' : ($row['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?= ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                            <a href="view_payment_request.php?osca_id=<?= urlencode($row['prr_osca_id']); ?>"
                                                class="btn btn-primary btn-sm">
                                                View
                                            </a>
                                    </td>
                                </tr>

                                <?php include("transaction_modal.php"); ?>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No payment release requests found.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</section>

<?php
$stmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/dashboard.js"></script>
