<?php
session_start();
include('templates/header.php');
?>

<main id="main" class="main">

<div class="container">
<section class="section">

<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card">
<div class="card-body">

<h5 class="card-title text-center">
    Deceased Regular Member Benefit Application
</h5>
<p class="text-center text-muted">
    Please complete the form and upload the required documents.
</p>

<form
      enctype="multipart/form-data"
      class="row g-3">

<!-- ================== DECEASED MEMBER INFO ================== -->
<h6 class="mt-3">Deceased Member Information</h6>

<div class="col-md-6">
    <label class="form-label">OSCA ID Number</label>
    <input type="text" name="osca_id" class="form-control" required>
</div>

<div class="col-md-6">
    <label class="form-label">Full Name</label>
    <input type="text" name="deceased_name" class="form-control" required>
</div>

<div class="col-md-6">
    <label class="form-label">Date of Birth</label>
    <input type="date" name="dob" class="form-control" required>
</div>

<div class="col-md-6">
    <label class="form-label">Date of Death</label>
    <input type="date" name="date_of_death" class="form-control" required>
</div>

<!-- ================== CLAIMANT INFO ================== -->
<h6 class="mt-4">Claimant Information</h6>

<div class="col-md-6">
    <label class="form-label">Claimant Full Name</label>
    <input type="text" name="claimant_name" class="form-control" required>
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
</div>

<div class="col-md-6">
    <label class="form-label">Contact Number</label>
    <input type="text" name="contact" class="form-control" required>
</div>

<div class="col-md-6">
    <label class="form-label">Address</label>
    <input type="text" name="address" class="form-control" required>
</div>

<!-- ================== DOCUMENT UPLOAD ================== -->
<h6 class="mt-4">Required Documents</h6>

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

<!-- ================== SUBMIT ================== -->
<div class="col-12 text-center">
    <button type="submit" class="btn btn-primary">
        Submit Application
    </button>
</div>

</form>

</div>
</div>

</div>
</div>

</section>
</div>

</main>

<?php include('templates/footer.php'); ?>
  <script src="assets/js/deceased_application.js"></script>