<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// Fetch pending cashless payments that are not paid via walk-in
$stmt = $conn->prepare("
    SELECT p.*, u.first_name, u.last_name, u.osca_id, d.deceased_name
    FROM payments p
    INNER JOIN user_table u ON u.id = p.user_id
    INNER JOIN deceased_benefit_applications d ON d.id = p.deceased_benefit_id
    WHERE p.payment_method = ?
      AND p.payment_status = ?
      AND p.deceased_benefit_id NOT IN (
          SELECT deceased_benefit_id
          FROM payments
          WHERE payment_method = 'walkin'
            AND payment_status = 'completed'
      )
    ORDER BY p.payment_date DESC
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
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>OSCA ID</th>
                                        <th>Deceased</th>
                                        <th>Amount</th>
                                        <th>Reference No</th>
                                        <th>Payment Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                            <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                            <td><?= htmlspecialchars($row['deceased_name']); ?></td>
                                            <td>₱<?= number_format($row['amount'], 2); ?></td>
                                            <td><?= htmlspecialchars($row['reference_no'] ?? 'N/A'); ?></td>
                                            <td><?= date('M d, Y h:i A', strtotime($row['payment_date'])); ?></td>

                                            <td>
                                                <button class="btn btn-sm btn-primary cashlessViewBtn"
                                                    data-payment='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>'>
                                                    View
                                                </button>

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
        include("cashless_modal.php");
        include("cashless_confirmation_modal.php");
?>
<script src="transaction/js/cashless_table.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
