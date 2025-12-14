
<!-- ================= VIEW DETAILS MODAL ================= -->
<div class="modal fade" id="<?= $viewModal; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <?= htmlspecialchars($row['deceased_name']); ?> – Deceased Benefit Application
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <p><strong>OSCA ID:</strong> <?= htmlspecialchars($row['osca_id']); ?></p>
                        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($row['dob']); ?></p>
                        <p><strong>Date of Death:</strong> <?= htmlspecialchars($row['date_of_death']); ?></p>
                        <p><strong>Date Applied:</strong> <?= htmlspecialchars($row['created_at']); ?></p>
                    </div>

                    <div class="col-md-4 text-end">
                        <h6>Status</h6>
                        <span class="badge
                            <?= $row['status'] === 'Approved' ? 'bg-success' :
                               ($row['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning'); ?>">
                            <?= htmlspecialchars($row['status']); ?>
                        </span>
                    </div>
                </div>

                <hr>

                <h6 class="text-primary">Claimant Information</h6>
                <p><strong>Name:</strong> <?= htmlspecialchars($row['claimant_name']); ?></p>
                <p><strong>Relationship:</strong> <?= htmlspecialchars($row['relationship']); ?></p>
                <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact']); ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($row['address']); ?></p>

                <hr>

                <h6 class="text-primary">Submitted Documents</h6>

                <ul class="list-group">
                    <li class="list-group-item">
                        Death Certificate
                    <a href="../assets/<?= htmlspecialchars($row['death_certificate']); ?>"
                    target="_blank"
                    class="btn btn-sm btn-outline-primary float-end">
                    View
                    </a>


                    </li>

                    <li class="list-group-item">
                        OSCA ID (Deceased)
                        <a href="../<?= $row['osca_id_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary float-end">View</a>
                    </li>

                    <li class="list-group-item">
                        Claimant Valid ID
                        <a href="../<?= $row['claimant_id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary float-end">View</a>
                    </li>

                    <?php if (!empty($row['barangay_clearance'])): ?>
                        <li class="list-group-item">
                            Barangay Clearance
                            <a href="../<?= $row['barangay_clearance']; ?>" target="_blank" class="btn btn-sm btn-outline-primary float-end">View</a>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- ================= APPROVE MODAL ================= -->
<div class="modal fade" id="<?= $approveModal; ?>" tabindex="-1">
    <div class="modal-dialog">
        <form id="approveForm" class="approveForm">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Approve Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <input type="hidden" name="action" value="accept">

                    <div class="mb-3">
                        <label class="form-label">Remarks (Optional)</label>
                        <textarea class="form-control" name="remarks" rows="3"></textarea>
                    </div>

                    <p class="text-muted">
                        This action will approve the benefit application.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Confirm Approval</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- ================= REJECT MODAL ================= -->
<div class="modal fade" id="<?= $rejectModal; ?>" tabindex="-1">
    <div class="modal-dialog">
        <form id="rejectForm" class="rejectForm">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <input type="hidden" name="action" value="decline">

                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" name="remarks" rows="3" required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </div>
        </form>
    </div>
</div>
