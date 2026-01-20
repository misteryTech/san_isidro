<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* Fetch disbursements */
$stmt = $conn->prepare("
    SELECT
        id,
        prr_id,
        dba_id,
        osca_id,
        approved_by,
        amount,
        created_at,
        released,
        released_by,
        released_date
    FROM disbursements
    ORDER BY created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="section section-dashboard">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Disbursement List</h5>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Batch ID</th>
                  <th>PRR ID</th>
                  <th>OSCA ID</th>
                  <th>DBA ID</th>
                  <th>Approved By</th>
                  <th>Amount</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['id']); ?></td>
                      <td><?= htmlspecialchars($row['prr_id']); ?></td>
                      <td><?= htmlspecialchars($row['osca_id']); ?></td>
                      <td><?= htmlspecialchars($row['dba_id']); ?></td>
                      <td><?= htmlspecialchars($row['approved_by']); ?></td>
                      <td>₱<?= number_format($row['amount'], 2); ?></td>
                      <td><?= date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                      <td>
                        <?php if ((int)$row['released'] === 0): ?>
                          <button
                            class="btn btn-sm btn-success release-btn"
                            data-id="<?= $row['id']; ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#releaseModal"
                          >
                            Release
                          </button>
                        <?php else: ?>
                          <span class="text-success fw-semibold">
                            Successful Transaction
                          </span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center">No disbursements found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- RELEASE CONFIRMATION MODAL -->
<div class="modal fade" id="releaseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="transaction/php/release_disburstment.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Release</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="disbursement_id" id="releaseDisbursementId">
          <p class="mb-0">
            Are you sure you want to <strong>release this disbursement</strong>?
          </p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-success">
            Yes, Release
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.release-btn').forEach(button => {
    button.addEventListener('click', function () {
      document.getElementById('releaseDisbursementId').value = this.dataset.id;
    });
  });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
