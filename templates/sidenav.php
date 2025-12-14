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
              <span>Associate</span>
            </a>
          </li>
        </ul>
      </li>
    <?php endif; ?>


    <!-- Staff-specific menu -->
    <?php if ($position === "president") : ?>
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
              <span>Associate Member</span>
            </a>
          </li>

          <li>
            <a href="staff/">
              <i class="bi bi-circle"></i>
              <span>Regular Member</span>
            </a>
          </li>


          <li>
            <a href="staff/">
              <i class="bi bi-circle"></i>
              <span>Cancelled Member</span>
            </a>
          </li>



        </ul>


        <a class="nav-link collapsed" data-bs-target="#deceased-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder"></i>
          <span>Transaction</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="deceased-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">


          <li>
            <a href="deceased_table.php">
              <i class="bi bi-circle"></i>
              <span>Deceased Request</span>
            </a>
          </li>
        </ul>


      </li>
    <?php endif; ?>
    <!-- Regular member menu -->
    <?php if ($position === "member" && $account === "Regular") : ?>
        <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#staff-nav" data-bs-toggle="collapse" href="#">
         <i class="bi bi-person-x"></i>
          <span>Deceased Person</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="staff-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="deceased_list.php">
              <i class="bi bi-circle"></i>
              <span>List of Member</span>
            </a>
          </li>


        </ul>




      </li>

    <?php endif; ?>

  </ul>
</aside><!-- End Sidebar -->
