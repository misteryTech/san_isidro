<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$stmt = $conn->prepare("
    SELECT *
    FROM deceased_benefit_applications
    WHERE status = 'Pending'
    ORDER BY created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Deceased Regular Member Benefit Applications
                    </h5>

                    <table class="table table-striped table-bordered datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>Deceased Name</th>
                                <th>OSCA ID</th>
                                <th>Claimant</th>
                                <th>Date of Death</th>
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
                                    $viewModal   = "viewModal" . $counter;
                                    $approveModal = "approveModal" . $counter;
                                    $rejectModal  = "rejectModal" . $counter;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['deceased_name']); ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                    <td><?= htmlspecialchars($row['claimant_name']); ?></td>
                                    <td><?= htmlspecialchars($row['date_of_death']); ?></td>
                                    <td><?= htmlspecialchars($row['created_at']); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?= $viewModal; ?>">
                                            View
                                        </button>

                                        <button class="btn btn-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?= $approveModal; ?>">
                                            Approve
                                        </button>

                                        <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?= $rejectModal; ?>">
                                            Reject
                                        </button>
                                    </td>
                                </tr>

                                <?php include("deceased_modal.php"); ?>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No deceased benefit applications found.
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

<script src="transaction/js/deceased_modal.js"></script>
