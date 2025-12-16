<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// Fetch pending cashless payments
$stmt = $conn->prepare("
    SELECT *
    FROM payments
    WHERE payment_method = ?
      AND payment_status = ?
    ORDER BY payment_date DESC
");

$paymentMethod = 'cashless';
$paymentStatus = 'pending';

$stmt->bind_param("ss", $paymentMethod, $paymentStatus);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Cashless Payments</h5>

                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User ID</th>
                                        <th>Amount</th>
                                        <th>Reference No</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= htmlspecialchars($row['user_id']); ?></td>
                                            <td>₱<?= number_format($row['amount'], 2); ?></td>
                                            <td><?= htmlspecialchars($row['reference_no'] ?? 'N/A'); ?></td>
                                            <td><?= date('M d, Y h:i A', strtotime($row['payment_date'])); ?></td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    <?= ucfirst($row['payment_status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No pending cashless payments found.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
