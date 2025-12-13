    <!-- Modal -->
                                <div class="modal fade" id="<?= $modalId; ?>" tabindex="-1" aria-labelledby="<?= $modalId; ?>Label" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">

                                                <h5 class="modal-title" id="<?= $modalId; ?>Label">
                                                    <?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?> - Details
                                                </h5>

                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">

                                                    <!-- LEFT COLUMN : MEMBER DETAILS -->
                                                    <div class="col-md-8">
                                                        <p><strong>OSCA ID:</strong> <?= htmlspecialchars($row['osca_id']); ?></p>
                                                        <p><strong>Chapter:</strong> <?= htmlspecialchars($row['chapter']); ?></p>
                                                        <p><strong>Birth Date:</strong> <?= htmlspecialchars($row['birth_date'] ?? ''); ?></p>
                                                        <p><strong>Civil Status:</strong> <?= htmlspecialchars($row['civil_status'] ?? ''); ?></p>
                                                        <p><strong>Address:</strong> <?= htmlspecialchars($row['place_birth'] ?? ''); ?></p>
                                                        <p><strong>Account Type:</strong> <?= htmlspecialchars($row['account'] ?? ''); ?></p>
                                                        <p><strong>Date Applied:</strong> <?= htmlspecialchars($row['date_added'] ?? ''); ?></p>
                                                    </div>

                                                    <!-- RIGHT COLUMN : VERIFICATION STATUS -->
                                                    <div class="col-md-4 text-end">
                                                        <h5 class="fw-bold">Verification Status</h5>

                                                        <span class="badge
                                                            <?= $row['status'] === 'Verified' ? 'bg-success' :
                                                            ($row['status'] === 'Declined' ? 'bg-danger' : 'bg-warning'); ?>">
                                                            <?= htmlspecialchars($row['status']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <hr>
                                                <!-- Membership-specific fields -->
                                                <h3 class="text-primary">Contact Person</h3>
                                                <p><strong>Name:</strong> <?= htmlspecialchars($row['cp_fullname'] ?? ''); ?></p>
                                                <p><strong>Relationship:</strong> <?= htmlspecialchars($row['cp_relationship'] ?? ''); ?></p>
                                                <p><strong>Mobile No.:</strong> <?= htmlspecialchars($row['cp_contact'] ?? ''); ?></p>
                                                <p><strong>Email:</strong> <?= htmlspecialchars($row['cp_email'] ?? ''); ?></p>
                                                <p><strong>Occupation:</strong> <?= htmlspecialchars($row['cp_occupation'] ?? ''); ?></p>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <!-- Accept Modal -->
                        <div class="modal fade" id="<?= $acceptModalId; ?>" tabindex="-1" aria-labelledby="<?= $acceptModalId; ?>Label" aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="acceptForm<?= $acceptModalId; ?>">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title" id="<?= $acceptModalId; ?>Label">Accept Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <input type="hidden" name="osca_id" value="<?= htmlspecialchars($row['osca_id']); ?>">
                                            <input type="text" name="membership_id" value="<?= htmlspecialchars($row['membership_id']); ?>">
                                            <input type="hidden" name="action" value="accept">

                                                <div class="mb-3">
                                                    <label class="form-label">Remarks</label>
                                                    <textarea class="form-control" name="remarks" rows="3"></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Confirm Accept</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Decline Modal -->
                            <div class="modal fade" id="<?= $declineModalId; ?>" tabindex="-1" aria-labelledby="<?= $declineModalId; ?>Label" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="declineForm<?= $declineModalId; ?>">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="<?= $declineModalId; ?>Label">Decline Request</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">

                                            <input type="hidden" name="osca_id" value="<?= htmlspecialchars($row['osca_id']); ?>">
                                            <input type="text" name="membership_id" value="<?= htmlspecialchars($row['membership_id']); ?>">
                                            <input type="hidden" name="action" value="decline">
                                                    <div class="mb-3">
                                                    <label class="form-label">Remarks</label>
                                                    <textarea class="form-control" name="remarks" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Confirm Decline</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
