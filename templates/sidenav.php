<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard link (visible to all) -->
    <li class="nav-item">
      <a class="nav-link <?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>"
         href="dashboard.php">
        <i class="bi bi-house"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Staff-specific menu -->
    <?php if ($position === "staff") : ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#staff-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i>
          <span>Members</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="staff-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="staff/">
              <i class="bi bi-circle"></i>
              <span>Member Request</span>
            </a>
          </li>
        </ul>
      </li>
    <?php endif; ?>

    <!-- Regular member menu -->
    <?php if ($position === "member" && $account === "Regular") : ?>
      <li class="nav-item">
        <a class="nav-link" href="member_profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li>
    <?php endif; ?>

  </ul>
</aside><!-- End Sidebar -->
