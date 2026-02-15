<?php
session_start();
ob_start(); // Capture page content

require_once __DIR__ . '/../database/connection.php';

?>

<section class="section">
   <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

          <div class="card mb-3">
            <div class="card-body">
              <div class="pt-4 pb-2 text-center">
                <h5 class="card-title pb-0 fs-4">Create an Account</h5>
                <p class="small">Enter your personal details to create account</p>
              </div>

              <form id="registerForm" class="row g-3 needs-validation" novalidate>
                <div id="responseBox" class="mt-3 text-success"></div>

                <!-- OSCA ID and Chapter -->
                <div class="col-md-6">
                  <label for="oscaId" class="form-label">OSCA ID No.</label>
                  <input type="text" name="osca_id" class="form-control" id="oscaId" required>
                  <div class="invalid-feedback">Please enter your OSCA ID!</div>
                </div>

                  <div class="col-md-6">
                <label for="chapter" class="form-label">Chapter</label>
                <select name="chapter" class="form-select" id="chapter" required>
                  <option value="">Loading chapters...</option>
                </select>
                <div class="invalid-feedback">Please select Chapter!</div>
                 </div>


                <!-- Name Fields -->
                <div class="col-md-4">
                  <label for="firstName" class="form-label">First Name</label>
                  <input type="text" name="first_name" class="form-control" id="firstName" required>
                  <div class="invalid-feedback">Please enter your first name!</div>
                </div>

                <div class="col-md-4">
                  <label for="middleName" class="form-label">Middle Name</label>
                  <input type="text" name="middle_name" class="form-control" id="middleName" required>
                  <div class="invalid-feedback">Please enter your middle name!</div>
                </div>

                <div class="col-md-4">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input type="text" name="last_name" class="form-control" id="lastName" required>
                  <div class="invalid-feedback">Please enter your last name!</div>
                </div>

                <!-- Birth & Civil Status -->
                <div class="col-md-6">
                  <label for="birthDate" class="form-label">Birth Date</label>
                  <input type="date" name="birth_date" class="form-control" id="birthDate" required>
                  <div class="invalid-feedback">Please enter your birth date!</div>
                </div>

                <div class="col-md-6">
                  <label for="civilStatus" class="form-label">Civil Status</label>
                  <select name="civil_status" class="form-select" id="civilStatus" required>
                    <option value="" selected disabled>Choose...</option>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="separated">Separated</option>
                    <option value="divorced">Divorced</option>
                  </select>
                  <div class="invalid-feedback">Please select your civil status!</div>
                </div>

                <!-- Place of Birth -->
                <div class="col-md-12">
                  <label for="placeBirth" class="form-label">Place of Birth</label>
                  <input type="text" name="place_birth" class="form-control" id="placeBirth" required>
                  <div class="invalid-feedback">Please enter your place of birth!</div>
                </div>

                <!-- Pension Info -->
                <div class="col-md-6">
                  <label for="pensioner" class="form-label">Pensioner</label>
                  <select name="pensioner" class="form-select" id="pensioner" required>
                    <option value="">Choose...</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                  </select>
                  <div class="invalid-feedback">Please select pensioner status!</div>
                </div>

                <div class="col-md-6">
                  <label for="pensionDetails" class="form-label">Pension Details</label>
                  <input type="text" name="pension_details" class="form-control" id="pensionDetails">
                </div>

                <!-- Contact & Password -->
                 <!-- Contact & Password -->
                <div class="col-md-6">
                  <label for="mobileno" class="form-label">Mobile No. (Guardian / Contact Person)</label>
                  <input
                    type="text"
                    name="mobileno"
                    class="form-control"
                    id="mobileno"
                    maxlength="11"
                    pattern="\d{11}"
                    required>
                  <div class="invalid-feedback">Please enter an 11-digit mobile number.</div>
                </div>


                <div class="col-md-6">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" id="password" required>
                  <div class="invalid-feedback">Please enter your password!</div>
                </div>

                <!-- Submit -->
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Create Account</button>
                </div>

              </form>
            </div>
          </div>

        </div>
      </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/member_registration.js"></script>