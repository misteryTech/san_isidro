<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/*-----------------------------------------------------------
| FETCH PENDING DECEASED BENEFIT APPLICATIONS
|-----------------------------------------------------------*/
$stmt = $conn->prepare("
    SELECT *
    FROM deceased_benefit_applications
    WHERE status = 'Approved'
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
          <h5 class="card-title">Pending Deceased Benefit Requests</h5>

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>OSCA ID</th>
                  <th>Deceased Name</th>
                  <th>Date of Death</th>
                  <th>Approved/Created Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                    <td><?= htmlspecialchars($row['deceased_name']); ?></td>
                    <td><?= htmlspecialchars(date('M d, Y', strtotime($row['date_of_death']))); ?></td>
                    <td><?= htmlspecialchars(date('M d, Y', strtotime($row['created_at']))); ?></td>
                    <td><span class="badge bg-success text-light"><?= htmlspecialchars($row['status']); ?></span></td>
                    <td>
                                                                      <?php
                                      // Check if this OSCA ID has a release request (any status, or just Pending)
                                      $releaseCheckStmt = $conn->prepare("
                                          SELECT 1
                                          FROM payment_release_requests
                                          WHERE osca_id = ?
                                          LIMIT 1
                                      ");
                                      $releaseCheckStmt->bind_param("s", $row['osca_id']);
                                      $releaseCheckStmt->execute();
                                      $releaseCheckStmt->store_result();
                                      $hasPendingRelease = $releaseCheckStmt->num_rows > 0;
                                      ?>

                                      <!-- Button -->
                                      <?php if ($hasPendingRelease): ?>
                                          <button class="btn btn-secondary btn-sm" disabled>
                                              Pending Release
                                          </button>
                                      <?php else: ?>
                                          <button class="btn btn-warning btn-sm"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#releaseModal<?= $row['id']; ?>">
                                              Release
                                          </button>

                                         <a class="btn btn-primary btn-sm" href="view_deceased_profile.php?osca_id=<?=  $row['osca_id']; ?>">Profile</a>
                                      <?php endif; ?>


                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
/*-----------------------------------------------------------
| MODALS FOR RELEASE
|-----------------------------------------------------------*/
$result->data_seek(0);
while ($row = $result->fetch_assoc()) :
?>
<div class="modal fade" id="releaseModal<?= $row['id']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <form class="releaseForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Release</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <p><strong>OSCA ID:</strong> <?= htmlspecialchars($row['osca_id']); ?></p>
          <p><strong>Deceased Name:</strong> <?= htmlspecialchars($row['deceased_name']); ?></p>
          <p><strong>Date of Death:</strong> <?= htmlspecialchars(date('M d, Y', strtotime($row['date_of_death']))); ?></p>

          <label>Amount to Release</label>
          <input type="number" class="form-control" name="release_amount" step="0.01" required>

          <label>Release Method</label>
          <select name="released_method" class="form-control" required>
            <option value="Cash">Cash</option>
            <option value="Bank Transfer">Bank Transfer</option>
          </select>

          <!-- Hidden IDs -->
          <input type="hidden" name="osca_id" value="<?= $row['osca_id']; ?>">
          <input type="hidden" name="dba_id" value="<?= $row['id']; ?>">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Request for Approval</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php endwhile; ?>

<script src="transaction/js/process_payment_request.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
