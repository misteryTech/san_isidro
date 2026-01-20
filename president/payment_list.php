<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$query = "
SELECT
    u.osca_id,
    u.status AS user_status,
    u.date_registration,

    -- total approved deceased benefits *after user registration*
    COUNT(dba.id) AS total_deceased_benefits,

    -- total payments made by user
    COALESCE(SUM(p.payment_count), 0) AS total_payments,

    -- missed payments
    COUNT(dba.id) - COALESCE(SUM(p.payment_count), 0) AS total_missed_payments

FROM user_table u

-- join approved deceased benefits after user registration
LEFT JOIN deceased_benefit_applications dba
    ON dba.status = 'APPROVED'
    AND dba.updated_at >= u.date_registration

-- join payments per user
LEFT JOIN (
    SELECT osca_id COLLATE utf8mb4_unicode_ci AS osca_id, deceased_benefit_id, COUNT(*) AS payment_count
    FROM payments
    GROUP BY osca_id, deceased_benefit_id
) p
    ON p.osca_id = u.osca_id COLLATE utf8mb4_unicode_ci
    AND p.deceased_benefit_id = dba.id

GROUP BY u.osca_id, u.date_registration
ORDER BY total_missed_payments DESC
";

$result = mysqli_query($conn, $query);
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Payment Details</h5>
                    <table class="table table-striped table-bordered datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>OSCA ID</th>
                                <th>Status</th>
                                <th>Registration Date</th>
                                <th>Total Approved Benefits</th>
                                <th>Payments Made</th>
                                <th>Total Missed Payments</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr data-osca-id="<?= htmlspecialchars($row['osca_id']) ?>">
                                    <td><?= htmlspecialchars($row['osca_id']) ?></td>
                                    <td><?= htmlspecialchars($row['user_status']) ?></td>
                                    <td><?= htmlspecialchars($row['date_registration']) ?></td>
                                    <td><?= (int)$row['total_deceased_benefits'] ?></td>
                                    <td><?= (int)$row['total_payments'] ?></td>
                                    <td><?= (int)$row['total_missed_payments'] ?></td>
                                    <td>
                                        <?php if ($row['total_missed_payments'] >= 3): ?>
                                            <span class="badge bg-danger">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Good Standing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- Open one shared modal -->
                                        <button type="button" class="btn btn-warning set-inactive-btn"
                                                data-osca-id="<?= htmlspecialchars($row['osca_id']) ?>"
                                                data-bs-toggle="modal" data-bs-target="#inactiveModal">
                                            Set Inactive
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No payment records found.
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

<!-- One shared modal -->
<div class="modal fade" id="inactiveModal" tabindex="-1" aria-labelledby="inactiveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inactiveModalLabel">Set User Inactive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to set <strong id="modalOscaId"></strong> as INACTIVE?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmInactiveBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/payment.js"></script>
