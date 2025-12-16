<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// Get month and year from GET parameters, fallback to current month/year
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Prepare SQL query
$stmt = $conn->prepare("
    SELECT dba.*, ut.*
    FROM deceased_benefit_applications AS dba
    LEFT JOIN user_table AS ut
        ON ut.osca_id = dba.osca_id
    WHERE dba.status = 'Approved'
      AND MONTH(dba.updated_at) = ?
      AND YEAR(dba.updated_at) = ?
");

$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of Deceased Person (<?= htmlspecialchars(date("F Y", strtotime("$year-$month-01"))) ?>)</h5>

                    <table class="table table-striped table-bordered datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>OSCA ID</th>
                                <th>Date Approved</th>
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
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                    <td><?= htmlspecialchars($row['updated_at'] ?? ''); ?></td>
                                    <td>
                                            <a href="view_deceased_profile.php?osca_id=<?= urlencode($row['osca_id']); ?>">
                                                <button class="btn btn-primary btn-sm">View</button>
                                            </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No approved applications found for this month.
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
