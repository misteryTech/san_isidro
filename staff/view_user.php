<?php
session_start();
ob_start();

require_once __DIR__ . '/../database/connection.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo '<div class="alert alert-danger">Invalid user ID.</div>';
    exit;
}

// Fetch single user securely
$stmt = $conn->prepare("SELECT * FROM user_table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="alert alert-warning">User not found.</div>';
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<section class="section">
    <div class="row">
        <div class="col-lg-8 mx-auto">

            <div class="card">
                <div class="card-body">

                    <h5 class="card-title">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                    </h5>
                    <h4 class="card-text"><b>OSCA ID No.<?= htmlspecialchars($user['osca_id']) ?></h4>

                    <!-- Profile Details -->
                    <h6 class="text-muted mb-3">Profile Details</h6>

                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Birth Date</div>
                        <div class="col-md-8"><?= htmlspecialchars($user['birth_date']); ?></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Civil Status</div>
                        <div class="col-md-8"><?= htmlspecialchars($user['civil_status']); ?></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Address</div>
                        <div class="col-md-8"><?= htmlspecialchars($user['place_birth']); ?></div>
                    </div>

                    <hr>

                    <!-- Account Info -->
                    <h6 class="text-muted mb-3">Account Information</h6>

                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Account Type</div>
                        <div class="col-md-8"><?= htmlspecialchars($user['account']); ?></div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Date Applied</div>
                        <div class="col-md-8"><?= htmlspecialchars($user['date_registration']); ?></div>
                    </div>

                    <hr>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <a href="dashboard.php" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>


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
