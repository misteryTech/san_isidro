<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$osca_id = $_GET['osca_id'] ?? null;
?>

<section class="section">
    <div class="row">

    <?php if(!empty($_SESSION['success_message'])): ?>
<div class="alert alert-success">
    <?= $_SESSION['success_message']; ?>
</div>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if(!empty($_SESSION['error_message'])): ?>
<div class="alert alert-danger">
    <?= $_SESSION['error_message']; ?>
</div>
<?php unset($_SESSION['error_message']); endif; ?>



        <div class="col-lg-10 mx-auto">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Payment Release Request Information</h5>

                    <?php if ($osca_id): ?>

                        <?php
                        $stmt = $conn->prepare("
                            SELECT
                                -- Payment Release
                                prr.id AS prr_id,
                                prr.release_amount,
                                prr.released_method,
                                prr.requested_by,
                                prr.requested_at,
                                prr.status,

                                -- Deceased Benefit Application
                                dba.id AS dba_id,
                                dba.deceased_name,
                                dba.dob,
                                dba.date_of_death,
                                dba.claimant_name,
                                dba.relationship,
                                dba.contact,
                                dba.address,
                                dba.remarks,
                                dba.status AS db_status,
                                ut.chapter,
                                ut.account,
                                ut.id as user_id,

                                -- Requester
                                CONCAT(ut.first_name, ' ', ut.last_name) AS requester_name

                            FROM payment_release_requests prr
                            INNER JOIN deceased_benefit_applications dba
                                ON prr.osca_id = dba.osca_id
                            INNER JOIN user_table ut
                                ON prr.osca_id = ut.osca_id
                            WHERE prr.osca_id = ?
                            LIMIT 1
                        ");

                        $stmt->bind_param("s", $osca_id);
                        $stmt->execute();
                        $result = $stmt->get_result();


                        ?>

                        <?php if ($row = $result->fetch_assoc()): ?>
                            <h6 class="mt-3 text-primary">Deceased Information</h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <?= htmlspecialchars($row['requester_name']); ?></p>
                                </div>
                                   <div class="col-md-6">
                                    <p><strong>Chapter:</strong> <?= htmlspecialchars($row['chapter']); ?></p>
                                </div>

                                <div class="col-md-6">
                                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($row['dob']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Date of Death:</strong> <?= htmlspecialchars($row['date_of_death']); ?></p>
                                </div>

                                <div class="col-md-6">
                                    <p><strong>Account:</strong> <?= htmlspecialchars($row['account']); ?></p>
                                </div>


                            </div>

                                                            <h6 class="mt-4 text-primary">Claimant Information</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Claimant Name:</strong> <?= htmlspecialchars($row['claimant_name']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Relationship:</strong> <?= htmlspecialchars($row['relationship']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($row['address'])); ?></p>
                                        </div>

                                         <div class="col-md-6">
                                            <p><strong>Status

                                                   <span class="badge bg-<?=
                                                $row['db_status'] === 'Approved' ? 'success' :
                                                ($row['db_status'] === 'Rejected    ' ? 'warning' : 'danger');
                                            ?>">
                                                <?= ucfirst($row['db_status']); ?>
                                            </span>
                                            </p>


                                        </div>
                                    </div>


                            <!-- 💰 Payment Release Information -->
                           <h6 class="mt-4 text-primary">Payment Release Information</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Release Amount:</strong> ₱<?= number_format($row['release_amount'], 2); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Release Method:</strong> <?= htmlspecialchars($row['released_method']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Status:</strong>
                                            <span class="badge bg-<?=
                                                $row['status'] === 'Approved' ? 'success' :
                                                ($row['status'] === 'Pending' ? 'warning' : 'danger');
                                            ?>">
                                                <?= ucfirst($row['status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>


                                        <h6 class="mt-4 text-primary">Request Payment Approval Details</h6>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Requested By:</strong> Treasurer</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Requested At:</strong>
                                    <?= date('F d, Y h:i A', strtotime($row['requested_at'])); ?>
                                </p>
                            </div>

                            <?php if (!empty($row['remarks'])): ?>
                                <div class="col-md-12">
                                    <p><strong>Remarks:</strong> <?= htmlspecialchars($row['remarks']); ?></p>
                                </div>
                            <?php endif; ?>
                                </div>



                            <!-- 🔘 Actions -->
                            <div class="mt-4">
                                <a href="payment_request.php" class="btn btn-secondary btn-sm">
                                    Back
                                </a>

                                <button type="button" class="btn btn-info btn-sm" onclick="printCard()">
                                    Print
                                </button>

                                     <?php if ($row['status'] === 'Pending'): ?>
                                        <button type="button"
                                                class="btn btn-success btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approveModal">
                                            Approve
                                        </button>

                                        <a href="decline_request.php?osca_id=<?= urlencode($osca_id); ?>"
                                        class="btn btn-danger btn-sm">
                                            Decline
                                        </a>
                                    <?php endif; ?>


                            </div>

                        <?php else: ?>
                            <p class="text-muted">No record found for this OSCA ID.</p>
                        <?php endif; ?>

                    <?php else: ?>
                        <p class="text-muted">No OSCA ID provided.</p>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</section><!-- ✅ Approve Confirmation Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="approveForm" method="POST" action="transaction/php/approve_request.php">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p><strong>Release Amount:</strong> ₱<?= number_format($row['release_amount'], 2); ?></p>
                    <input type="hidden" name="osca_id" value="<?= htmlspecialchars($osca_id); ?>">
                    <input type="hidden" name="prr_id" value="<?= htmlspecialchars($row['prr_id']); ?>">
                    <input type="hidden" name="dba_id" value="<?= htmlspecialchars($row['dba_id']); ?>">
                    <input type="number" name="amount" value="<?= htmlspecialchars($row['release_amount']); ?>">
                    <p>Enter your password to confirm approval.</p>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password"
                               id="confirmPassword"
                               class="form-control"
                               required>
                    </div>

                    <div class="alert alert-danger d-none" id="passwordError">
                        Incorrect password.
                    </div>

                    <div class="alert alert-warning mb-0">
                        This action cannot be undone.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-success">
                        Yes, Approve
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>



<?php
$stmt?->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/view_payment_request.js"></script>
