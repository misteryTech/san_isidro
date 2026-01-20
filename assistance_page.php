<?php
session_start();
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

  .application-section {
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
    border-radius: 8px;
  }

  .card-title {
    font-weight: 600;
    color: #333;
  }

  h6 {
    font-weight: 600;
    color: #444;
    border-bottom: 1px solid #ddd;
    padding-bottom: 6px;
    margin-bottom: 12px;
  }

  .form-label {
    font-weight: 500;
  }

  .btn-primary {
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: 500;
  }
</style>

<div class="bg-logo-wrapper"></div>

<main>
  <section class="application-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 mx-auto">

          <div class="card shadow-sm">
            <div class="card-body">

              <!-- Title -->
              <h5 class="card-title text-center">
                Deceased Regular Member Benefit Application
              </h5>
              <p class="text-center text-muted mb-4">
                Please complete the form and upload the required documents.
              </p>

              <!-- Form -->
              <form enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>

                <!-- Deceased Member Info -->
                <h6 class="mt-3 text-center">Deceased Member Information</h6>

                <div class="col-md-6">
                  <label class="form-label">OSCA ID Number</label>
                  <input type="text" name="osca_id" class="form-control" required>
                  <div class="invalid-feedback">Please enter OSCA ID.</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="deceased_name" class="form-control" required>
                  <div class="invalid-feedback">Please enter full name.</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" name="dob" class="form-control" required>
                  <div class="invalid-feedback">Please enter date of birth.</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Date of Death</label>
                  <input type="date" name="date_of_death" class="form-control" required>
                  <div class="invalid-feedback">Please enter date of death.</div>
                </div>

                <!-- Claimant Info -->
                <h6 class="mt-4 text-center">Claimant Information</h6>

                <div class="col-md-6">
                  <label class="form-label">Claimant Full Name</label>
                  <input type="text" name="claimant_name" class="form-control" required>
                  <div class="invalid-feedback">Please enter claimant name.</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Relationship to Deceased</label>
                  <select name="relationship" class="form-select" required>
                    <option value="">Select</option>
                    <option>Spouse</option>
                    <option>Child</option>
                    <option>Sibling</option>
                    <option>Relative</option>
                  </select>
                  <div class="invalid-feedback">Please select relationship.</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Contact Number</label>
                  <input type="text" name="contact" class="form-control" required>
                  <div class="invalid-feedback">Please enter contact number.</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Address</label>
                  <input type="text" name="address" class="form-control" required>
                  <div class="invalid-feedback">Please enter address.</div>
                </div>

                <!-- Document Upload -->
                <h6 class="mt-4 text-center">Required Documents</h6>

                <div class="col-12">
                  <label class="form-label">Death Certificate</label>
                  <input type="file" name="death_certificate" class="form-control" required>
                </div>

                <div class="col-12">
                  <label class="form-label">OSCA ID (Deceased)</label>
                  <input type="file" name="osca_id_file" class="form-control" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Claimant Valid ID</label>
                  <input type="file" name="claimant_id" class="form-control" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Barangay Clearance / Certificate</label>
                  <input type="file" name="barangay_clearance" class="form-control">
                </div>

                <!-- Submit -->
                <div class="col-12 text-center mt-3">
                  <button type="submit" class="btn btn-primary btn-lg">
                    Submit Application
                  </button>

                  <a href="login.php" class="btn btn-danger btn-lg">Back to Login</a>
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
<script src="assets/js/deceased_application.js"></script>