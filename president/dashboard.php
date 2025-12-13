<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$stmt = $conn->prepare("
    SELECT mt.*, ut.first_name, ut.last_name, ut.birth_date, ut.civil_status,
           ut.place_birth, ut.account, ut.date_registration, ut.chapter,
           mt.id AS membership_id
    FROM membership_table AS mt
    LEFT JOIN user_table AS ut
        ON mt.osca_id = ut.osca_id
    WHERE mt.status = 'Pending'
");
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Member Request to Regular</h5>

                    <table class="table table-striped table-bordered datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>OSCA ID</th>
                                <th>Date Applied</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $counter++;
                                    $modalId = "userModal" . $counter;
                                    $acceptModalId = "acceptModal" . $counter;
                                    $declineModalId = "declineModal" . $counter;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                    <td><?= htmlspecialchars($row['date_registration'] ?? ''); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#<?= $modalId; ?>">View</button>
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#<?= $acceptModalId; ?>">Accept</button>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#<?= $declineModalId; ?>">Decline</button>
                                    </td>
                                </tr>

                                <?php include("transaction_modal.php"); ?>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <!-- ✅ TABLE-ONLY MESSAGE -->
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No membership request found.
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
