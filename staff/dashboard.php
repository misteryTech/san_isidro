<?php
session_start();
ob_start(); // Capture page content

require_once __DIR__ . '/../database/connection.php';

?>

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Members</h5>


        </div>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
