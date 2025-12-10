<?php
// ALWAYS load config first
require_once __DIR__ . '/../config.php';

// Include layout parts
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/topnav.php';
include __DIR__ . '/../templates/sidenav.php';
?>

<!-- ======= Main Content ======= -->
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Member Dashboard</h1>
  </div>

  <section class="section dashboard">
    <div class="row">

      <h2>Welcome, Member!</h2>
      <p>Your dashboard content goes here...</p>

    </div>
  </section>

</main>
<!-- End #main -->

<?php include __DIR__ . '/../templates/footer.php'; ?>
