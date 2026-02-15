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

  .login-section {
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
</style>

<div class="bg-logo-wrapper"></div>

<main>
  <section class="login-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

          <div class="d-flex justify-content-center py-4">
            <a href="index.html" class="logo d-flex align-items-center w-auto">
              <span>Barangay San Isidro</span>
            </a>
          </div>

          <div class="card mb-3 w-100">
            <div class="card-body">

              <div class="pt-4 pb-2">
                <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                <p class="text-center small">Enter your username & password to login</p>
              </div>

              <form id="loginProcess" class="row g-3 needs-validation" novalidate>
                <div id="responseBox" class="mt-3"></div>

                <div class="col-12">
                  <label for="osca_id" class="form-label">OSCA ID Number</label>
                  <div class="input-group has-validation">
                    <span class="input-group-text">#</span>
                    <input type="text" name="osca_id" class="form-control" id="osca_id" required>
                    <div class="invalid-feedback">Please enter your OSCA ID.</div>
                  </div>
                </div>

                <div class="col-12">
                  <label for="yourPassword" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" name="password" class="form-control" id="yourPassword" required>
                    <span class="input-group-text" id="togglePassword" style="cursor:pointer">
                      <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>
                  </div>
                  <div class="invalid-feedback">Please enter your password!</div>
                </div>

                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                  </div>
                </div>

                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Login</button>
                </div>

                <div class="col-12">
                  <p class="small mb-0">Don't have an account? <a href="register">Create an account</a></p>
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
<script src="assets/js/login.js"></script>