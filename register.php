<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['position'] === 'Admin') {
        header("Location: admin/dashboard.php");
        exit;
    } else {
        header("Location: member/dashboard.php");
        exit;
    }
}

include('templates/header.php');
?>

<style>
  body, html {
    height: 100%;
    margin: 0;
  }

  .bg-logo-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
  }

  .bg-logo-wrapper::before {
    content: "";
    position: absolute;
    inset: 0;
    background: url('assets/img/san_isidro.png') no-repeat center center;
    background-size: cover;
    filter: blur(8px) brightness(0.4);
    z-index: -1;
  }

  .register-section {
    position: relative;
    z-index: 1;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .card {
    background-color: rgba(255, 255, 255, 0.95);
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
  }

  .logo span {
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
  }

  .form-label {
    font-weight: 500;
  }

  .form-check-label a {
    text-decoration: underline;
  }
</style>

<div class="bg-logo-wrapper"></div>

<main>
  <section class="register-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

          <div class="text-center py-4">
            <a href="index.html" class="logo d-flex align-items-center justify-content-center w-auto">
              <span>Member Registration</span>
            </a>
          </div>

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
                    <option value="">Choose...</option>
                    <option value="Chapter1">Chapter 1</option>
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
                <div class="col-md-6">
                  <label for="email" class="form-label">Email (Guardian / Contact Person)</label>
                  <input type="email" name="email" class="form-control" id="email" required>
                  <div class="invalid-feedback">Please enter a valid email address!</div>
                </div>

                <div class="col-md-6">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" id="password" required>
                  <div class="invalid-feedback">Please enter your password!</div>
                </div>

                <!-- Terms -->
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                    <label class="form-check-label" for="acceptTerms">
                      I agree and accept the <a href="#">terms and conditions</a>
                    </label>
                    <div class="invalid-feedback">You must agree before submitting.</div>
                  </div>
                </div>

                <!-- Submit -->
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Create Account</button>
                </div>

                <!-- Login Link -->
                <div class="col-12 text-center">
                  <p class="small mb-0">Already have an account? <a href="login.php">Log in</a></p>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</main>

<?php include('templates/footer.php'); ?>
<script src="assets/js/source.js"></script>