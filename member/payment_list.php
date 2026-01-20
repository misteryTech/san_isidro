<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/connection.php';

$osca_id = $_SESSION['osca_id'];

/* ==============================
   FETCH PAYMENTS WITH DECEASED NAME
================================ */
$stmt = $conn->prepare("
    SELECT
        p.amount,
        p.payment_date,
        p.payment_method,

        u.first_name AS deceased_first_name,
        u.last_name  AS deceased_last_name

    FROM payments p

    INNER JOIN deceased_benefit_applications dba
        ON p.deceased_benefit_id = dba.id

    INNER JOIN user_table u
        ON dba.osca_id = u.osca_id

    WHERE p.osca_id = ?
      AND p.payment_status = 'completed'

    ORDER BY p.payment_date DESC
");

$stmt->bind_param("s", $osca_id);
$stmt->execute();
$result = $stmt->get_result();
$payments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ==============================
   TOTAL AMOUNT
================================ */
$totalAmount = 0;

ob_start();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Deceased Payment Records</h5>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Deceased Person</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php foreach ($payments as $payment):
                                    $totalAmount += $payment['amount'];
                                    $date = date('F d, Y', strtotime($payment['payment_date']));
                                ?>
                                    <tr>
                                        <td>₱<?= number_format($payment['amount'], 2) ?></td>
                                        <td><?= $date ?></td>
                                        <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                        <td>
                                            <?= htmlspecialchars(
                                                $payment['deceased_first_name'] . ' ' .
                                                $payment['deceased_last_name']
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No completed payments found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-3 text-end">
                        <strong>Total Amount Paid:</strong>
                        ₱<?= number_format($totalAmount, 2) ?>
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
