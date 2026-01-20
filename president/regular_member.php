<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$stmt = $conn->prepare("
  SELECT * FROM user_table AS ut WHERE account = 'Regular'
");

$stmt->execute();
$result = $stmt->get_result();

// PREPARE CHECKER STATEMENT
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Regular Members</h5>

                    <table class="table table-striped table-bordered datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>OSCA ID</th>
                                <th>Date Applied</th>

                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                    <td><?= htmlspecialchars($row['date_registration'] ?? ''); ?></td>

                                </tr>

                                <?php include("transaction_modal.php"); ?>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <!-- ✅ TABLE-ONLY MESSAGE -->
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No Regular Member Found.
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
