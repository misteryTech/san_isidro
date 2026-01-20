<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// MAIN QUERY
$stmt = $conn->prepare("
    SELECT *
    FROM deceased_benefit_applications
    WHERE status = 'Pending'
    ORDER BY created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();

// PREPARED STATEMENT FOR REGISTRATION CHECK
$verifyStmt = $conn->prepare("
    SELECT osca_id
    FROM user_table
    WHERE osca_id = ?
    LIMIT 1
");
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
                                <th>Registration Status</th>
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

                                // CHECK IF REGISTERED
                                $verifyStmt->bind_param("s", $row['osca_id']);
                                $verifyStmt->execute();
                                $isRegistered = $verifyStmt->get_result()->num_rows > 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['deceased_name']); ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                    <td><?= htmlspecialchars($row['claimant_name']); ?></td>

                                    <td>
                                        <?php if ($isRegistered): ?>
                                            <span class="badge bg-success">Registered</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Not Registered</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($row['date_of_death']); ?></td>
                                    <td><?= htmlspecialchars($row['created_at']); ?></td>

                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#<?= $viewModal; ?>">
                                            View
                                        </button>

                                        <?php if ($isRegistered): ?>
                                            <button class="btn btn-success btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#<?= $approveModal; ?>">
                                                Approve
                                            </button>
                                        <?php endif; ?>

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
                                <td colspan="7" class="text-center text-muted py-4">
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
$verifyStmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

<script src="transaction/js/deceased_modal.js"></script>
