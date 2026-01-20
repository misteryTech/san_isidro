<?php
ob_start();
session_start();
require_once __DIR__ . '/../database/connection.php';

/* ===============================
   GET USER REGISTRATION DATE
================================ */
$userRegistrationDate = $_SESSION['date_registration'] ?? null;

if (!$userRegistrationDate) {
    die('User registration date not found in session.');
}

/* ===============================
   FETCH DECEASED APPLICATIONS
   FROM USER REGISTRATION ONWARDS
================================ */
$sql = "
    SELECT
        id,
        osca_id,
        status,
        updated_at
    FROM deceased_benefit_applications
    WHERE DATE(updated_at) >= ?
    ORDER BY updated_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userRegistrationDate);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card">
                <div class="card-body">

                    <h5 class="card-title">
                        Deceased Benefit Applications (From Registration Date)
                    </h5>

                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>OSCA ID</th>
                                <th>Status</th>
                                <th>Date Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php $counter++; ?>

                                <tr>
                                    <!-- OSCA ID -->
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>

                                    <!-- Status -->
                                    <td>
                                        <span class="badge
                                            <?= $row['status'] === 'Approved' ? 'bg-success' :
                                               ($row['status'] === 'Pending' ? 'bg-warning text-dark' : 'bg-secondary'); ?>">
                                            <?= htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>

                                    <!-- Date Updated -->
                                    <td>
                                        <?= !empty($row['updated_at'])
                                            ? date("F j, Y g:i A", strtotime($row['updated_at']))
                                            : '—'; ?>
                                    </td>

                                    <!-- Action -->
                                    <td>
                                        <button
                                            class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewModal<?= $counter; ?>">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>

                                <?php include 'payment_modal.php'; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
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
