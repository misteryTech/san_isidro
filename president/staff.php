<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$stmt = $conn->prepare("
    SELECT *
    FROM user_table
    WHERE position != 'member'
");
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section">
  <div class="row">
    <div class="col-lg-12 mx-auto">

      <div class="card">
        <div class="card-body">

          <!-- HEADER -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Staff Member</h5>
            <button
              type="button"
              class="btn btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#registerModal"
            >
              <i class="bi bi-person-plus"></i> Register Staff Member
            </button>
          </div>

          <!-- TABLE -->
          <table class="table table-striped table-bordered datatable">
            <thead class="table-dark">
              <tr>
                <th>Name</th>
                <th>OSCA ID</th>
                <th>Position</th>
                <th>Date Applied</th>
                <th>Status</th>
              </tr>
            </thead>

            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($row['osca_id']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td><?= htmlspecialchars($row['date_registration'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    No Staff Member Member Found.
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

<!-- ===========================
     REGISTER MODAL
=========================== -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

                <form id="registerForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Register Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>


                                        <?php
                    // Database connection (use your existing connection file if already included)


                    $chapters = [];
                    $query = "SELECT id, chapter_name FROM chapters ORDER BY chapter_name ASC";
                    $result = mysqli_query($conn, $query);

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $chapters[] = $row;
                        }
                    }
                    ?>
                      <div class="col-md-6">
                        <label class="form-label">OSCA ID</label>
                    <select name="chapter" class="form-select" required>
                        <option value="">Select Chapter</option>
                        <?php foreach ($chapters as $chapter): ?>
                            <option value="<?= $chapter['id']; ?>">
                                <?= htmlspecialchars($chapter['chapter_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">OSCA ID</label>
                        <input type="text" name="osca_id" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Birth Date</label>
                        <input type="date" name="birth_date" class="form-control">
                    </div>
                        <div class="col-md-6">
                        <label class="form-label">Civil Status</label>
                        <select name="civil_status" class="form-select" required>
                            <option value="">Select status</option>
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="widowed">Widowed</option>
                            <option value="separated">Separated</option>
                            <option value="divorced">Divorced</option>
                        </select>
                        </div>

                    <div class="col-md-6">
                        <label class="form-label">Place of Birth</label>
                        <input type="text" name="place_birth" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Pensioner</label>
                        <select name="pensioner" class="form-select">
                        <option value="">Select</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Pension Details</label>
                        <input type="text" name="pension_details" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Registration</label>
                        <input type="datetime-local" name="date_registration" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-select" required>
                        <option value="">Select position</option>
                        <option value="encoder">Encoder</option>
                        <option value="treasurer">Treasurer</option>
                        <option value="president">President</option>
                        <option value="staff">Staff</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Account Type</label>
                        <select name="account" class="form-select">
                        <option value="">Select type</option>
                        <option value="Associate">Associate</option>
                        <option value="Regular">Regular</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                        <option value="">Select status</option>
                       <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <input type="mobileno" name="mobileno" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    </div>

                    <div id="registerResponse" class="mt-3"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Register
                    </button>
                </div>
                </form>

    </div>
  </div>
</div>

<?php
$stmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

<script src="transaction/js/staff_registration.js"></script>
