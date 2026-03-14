<?php
session_start();

$osca_id = $_SESSION['osca_id'] ?? '';
ob_start(); // Capture page content
?>

<div class="pagetitle">
  <h1>Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
      <li class="breadcrumb-item">Members</li>
      <li class="breadcrumb-item active">Profile</li>
    </ol>
  </nav>
</div>

<section class="section profile">
  <div class="row">
    <!-- Left Column: Profile Card -->
    <div class="col-xl-4">
     <?php
      include(__DIR__ . '/../database/connection.php');

      // Try fetching membership first
      $stmt = $conn->prepare("
          SELECT ut.*, mt.*
          FROM user_table AS ut
          LEFT JOIN membership_table AS mt
              ON ut.osca_id = mt.osca_id
          WHERE ut.osca_id = ?
      ");
      $stmt->bind_param("s", $osca_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
          $member = $result->fetch_assoc();

          // User Table Data (always exists)
          $member_firstname        = $member['first_name'] ?? '';
          $member_lastname         = $member['last_name'] ?? '';
          $member_account          = $member['account'] ?? '';
          $member_mobileno            = $member['mobileno'] ?? '';
          $member_birthdate        = $member['birth_date'] ?? '';
          $member_civil_status     = $member['civil_status'] ?? '';
          $member_address          = $member['place_birth'] ?? '';

          // Membership Table Data (may not exist)
          $member_chapter          = $member['chapter'] ?? '';
          $member_pensioner        = $member['pensioner'] ?? '';
          $member_pensioner_details= $member['pension_details'] ?? '';
          $cp_firstname             = $member['cp_firstname'] ?? '';
          $cp_lastname              = $member['cp_lastname'] ?? '';
          $cp_contact              = $member['cp_contact'] ?? '';
          $cp_email                = $member['cp_email'] ?? '';
          $cp_relationship         = $member['cp_relationship'] ?? '';
          $cp_occupation           = $member['cp_occupation'] ?? '';
          $date_added              = $member['date_added'] ?? '';
      } else {
          // Only user_table exists
          $stmt2 = $conn->prepare("SELECT * FROM user_table WHERE osca_id = ?");
          $stmt2->bind_param("s", $osca_id);
          $stmt2->execute();
          $result2 = $stmt2->get_result();

          if ($result2->num_rows > 0) {
              $member = $result2->fetch_assoc();

              $member_firstname        = $member['first_name'] ?? '';
              $member_lastname         = $member['last_name'] ?? '';
              $member_account          = $member['account'] ?? '';
              $member_mobileno            = $member['mobileno'] ?? '';
              $member_birthdate        = $member['birth_date'] ?? '';
              $member_civil_status     = $member['civil_status'] ?? '';
              $member_address          = $member['place_birth'] ?? '';

              // Membership defaults
              $member_chapter          = '';
              $member_pensioner        = '';
              $member_pensioner_details= '';
              $cp_fullname             = '';
              $cp_contact              = '';
              $cp_email                = '';
              $cp_relationship         = '';
              $cp_occupation           = '';
              $date_added              = '';
          } else {
              echo "<div class='alert alert-warning'>No user found with that OSCA ID.</div>";
          }

          $stmt2->close();
      }



            // Prepare chapters for dropdown
            $user_chapter = $member_chapter;
            $chapters_result = mysqli_query($conn, "SELECT chapter_code, chapter_name FROM chapters ORDER BY chapter_name ASC");
            $chapters = [];
            if ($chapters_result) {
                while ($row = mysqli_fetch_assoc($chapters_result)) {
                    $chapters[] = $row;
                }
            }
      $stmt->close();
      $conn->close();
      ?>

      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
          <h1><?= htmlspecialchars($osca_id); ?></h1>
          <h2><?= htmlspecialchars($member_firstname . ' ' . $member_lastname); ?></h2>
          <h3><?= htmlspecialchars($member_account) ?> Member</h3>
        </div>
      </div>
    </div>

    <!-- Right Column: Tabs -->
    <div class="col-xl-8">
      <div class="card">
        <div class="card-body pt-3">
          <!-- Tabs -->
          <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview" type="button" role="tab">Overview</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit" type="button" role="tab">Edit Profile</button>
            </li>

             <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-pass" type="button" role="tab">Change Password</button>
            </li>

          </ul>

          <div class="tab-content pt-2">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
              <h5 class="card-title">Profile Details</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Full Name</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_firstname . ' ' . $member_lastname); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Birth Date</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_birthdate); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Civil  Status</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_civil_status); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Address</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_address); ?></div>
              </div>
              <hr>
              <h5 class="card-title">Pension Details</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Pensioner</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_pensioner); ?></div>
              </div>
               <div class="row">
                <div class="col-lg-3 col-md-4 label">Pension Details</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_pensioner_details); ?></div>
              </div>

              <?php if ($member_account ==="Associate") : ?>
               <hr>
              <h5 class="card-title">Contact Person information</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Fullname</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_firstname . ' ' . $cp_lastname); ?></div>
              </div>
               <div class="row">
                <div class="col-lg-3 col-md-4 label">Relationship</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_relationship); ?></div>
              </div>

                 <div class="row">
                <div class="col-lg-3 col-md-4 label">Contact</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_contact); ?></div>
              </div>


                 <div class="row">
                <div class="col-lg-3 col-md-4 label">Email</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_email); ?></div>
              </div>

                 <div class="row">
                <div class="col-lg-3 col-md-4 label">Occupation</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_occupation); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Date Applied</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($date_added); ?></div>
              </div>

              <?php endif; ?>


            </div>

            <!-- Edit Profile Tab -->


                              <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">
                      <form id="updateProfile" class="row g-3 needs-validation" novalidate>
                         <div id="responseBox" class="mt-3 text-success"></div>

                          <!-- OSCA ID and Chapter -->
                          <div class="row">
                              <div class="col-md-6">
                                  <label for="oscaId" class="form-label">OSCA ID No.</label>
                                  <input type="text" value="<?= htmlspecialchars($osca_id) ?>" name="osca_id" class="form-control" id="oscaId" required>
                                  <div class="invalid-feedback">Please enter your OSCA ID!</div>
                              </div>

                              <div class="col-md-6">
                                  <label for="chapter" class="form-label">Chapter</label>
                                  <select name="chapter" class="form-select" id="chapter" required>
                                      <?php foreach ($chapters as $chapter):
                                          $selected = ($chapter['chapter_code'] == $member_chapter) ? "selected" : "";
                                      ?>
                                          <option value="<?= htmlspecialchars($chapter['chapter_code'], ENT_QUOTES) ?>" <?= $selected ?>>
                                              <?= htmlspecialchars($chapter['chapter_name']) ?>
                                          </option>
                                      <?php endforeach; ?>
                                  </select>
                                  <div class="invalid-feedback">Please select Chapter!</div>
                              </div>
                          </div>

                          <!-- Personal Info -->
                          <div class="row mt-2">
                              <div class="col-md-6">
                                  <label for="firstName" class="form-label">First Name</label>
                                  <input type="text" name="first_name" value="<?= htmlspecialchars($member_firstname) ?>" class="form-control" id="firstName" required>
                                  <div class="invalid-feedback">Please enter your first name!</div>
                              </div>
                              <div class="col-md-6">
                                  <label for="lastName" class="form-label">Last Name</label>
                                  <input type="text" name="last_name" value="<?= htmlspecialchars($member_lastname) ?>" class="form-control" id="lastName" required>
                                  <div class="invalid-feedback">Please enter your last name!</div>
                              </div>
                          </div>

                          <div class="row mt-2">
                              <div class="col-md-6">
                                  <label for="birthDate" class="form-label">Birth Date</label>
                                  <input type="date" name="birth_date" value="<?= htmlspecialchars($member_birthdate) ?>" class="form-control" id="birthDate" required>
                                  <div class="invalid-feedback">Please enter your birth date!</div>
                              </div>
                              <div class="col-md-6">
                                  <label for="placeBirth" class="form-label">Place of Birth</label>
                                  <input type="text" name="place_birth" value="<?= htmlspecialchars($member_address) ?>" class="form-control" id="placeBirth" required>
                                  <div class="invalid-feedback">Please enter your place of birth!</div>
                              </div>
                          </div>

                          <div class="col-md-6 mt-2">
                              <label for="civilStatus" class="form-label">Civil Status</label>
                              <input type="text" name="civil_status" value="<?= htmlspecialchars($member_civil_status) ?>" class="form-control" id="civilStatus" required>
                              <div class="invalid-feedback">Please enter your civil status!</div>
                          </div>

                               <div class="col-md-6 mt-2">
                              <label for="mobileNo" class="form-label">Mobile Number</label>
                              <input type="text" name="mobileno" value="<?= htmlspecialchars($member_mobileno) ?>" class="form-control" id="mobileno" required>
                              <div class="invalid-feedback">Please enter your mobile number!</div>
                          </div>


                          <!-- Pension Info -->
                          <div class="row mt-3">
                              <div class="col-md-6">
                                  <label for="pensioner" class="form-label">Pensioner</label>
                                  <select name="pensioner" class="form-select" id="pensioner" required>
                                      <option value="Yes" <?= ($member_pensioner === "Yes") ? "selected" : "" ?>>Yes</option>
                                      <option value="No" <?= ($member_pensioner === "No") ? "selected" : "" ?>>No</option>
                                  </select>
                                  <div class="invalid-feedback">Please select pensioner status!</div>
                              </div>
                              <div class="col-md-6">
                                  <label for="pensionDetails" class="form-label">Pension Details</label>
                                  <input type="text" name="pension_details" value="<?= htmlspecialchars($member_pensioner_details) ?>" class="form-control" id="pensionDetails">
                              </div>
                          </div>

                          <!-- Contact Info -->
                          <?php if ($member_account === "Associate"): ?>
                          <hr>
                          <h5>Contact Person Information</h5>
                          <div class="row mt-2">
                              <div class="col-md-6">
                                  <label for="cpFullname" class="form-label">Full Name</label>
                                  <input type="text" name="cp_fullname" value="<?= htmlspecialchars($cp_firstname . ' ' . $cp_lastname) ?>" class="form-control" id="cpFullname">
                              </div>
                              <div class="col-md-6">
                                  <label for="cpRelationship" class="form-label">Relationship</label>
                                  <input type="text" name="cp_relationship" value="<?= htmlspecialchars($cp_relationship) ?>" class="form-control" id="cpRelationship">
                              </div>
                          </div>
                          <div class="row mt-2">
                              <div class="col-md-6">
                                  <label for="cpContact" class="form-label">Contact</label>
                                  <input type="text" name="cp_contact" value="<?= htmlspecialchars($cp_contact) ?>" class="form-control" id="cpContact">
                              </div>
                              <div class="col-md-6">
                                  <label for="cpEmail" class="form-label">Email</label>
                                  <input type="email" name="cp_email" value="<?= htmlspecialchars($cp_email) ?>" class="form-control" id="cpEmail">
                              </div>
                          </div>
                          <div class="col-md-6 mt-2">
                              <label for="cpOccupation" class="form-label">Occupation</label>
                              <input type="text" name="cp_occupation" value="<?= htmlspecialchars($cp_occupation) ?>" class="form-control" id="cpOccupation">
                          </div>
                          <?php endif; ?>
                            <input type="hidden" name="member_account" value="<?= htmlspecialchars($member_account) ?>">
                          <div class="col-12 mt-3">
                              <button class="btn btn-primary w-100" type="submit">Update Account</button>
                          </div>
                      </form>
                  </div>



            <div class="tab-pane fade profile-change-pass pt-3" id="profile-change-pass" role="tabpanel">
                              <form id="updatePassword" class="row g-3 needs-validation" novalidate>
                      <div id="responseBoxes" class="mt-3 text-success"></div>

                      <div class="row">
                      <!-- Old Password -->

                      <div class="col-md-6">
                        <label for="old_pass" class="form-label">Old Password</label>
                        <input type="password" name="old_pass" class="form-control" id="old_pass" required>
                        <div class="invalid-feedback">Please enter your old password.</div>
                      </div>
                     </div>
                      <!-- New Password -->
                      <div class="col-md-6">
                        <label for="new_pass" class="form-label">New Password</label>
                        <input type="password" name="new_pass" class="form-control" id="new_pass" required>
                        <div class="invalid-feedback">Please enter your new password.</div>
                      </div>

                      <!-- Retype Password -->
                      <div class="col-md-6">
                        <label for="retype_pass" class="form-label">Retype New Password</label>
                        <input type="password" name="retype_pass" class="form-control" id="retype_pass" required>
                        <div class="invalid-feedback">Please retype your new password.</div>
                      </div>

                      <!-- Optional: Submit button -->
                      <div class="col-12">
                        <button class="btn btn-primary" type="submit">Update Password</button>
                      </div>
                    </form>

          </div>



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

  <script src="transaction/js/updateProfile.js"></script>