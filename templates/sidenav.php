<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">

    <!-- Dashboard link (visible to all) -->
    <li class="nav-item">
      <a class="nav-link <?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>"
         href="dashboard">
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
            <a href="member_list">
              <i class="bi bi-circle"></i>
              <span>Member List</span>
            </a>
          </li>

          <li>
            <a href="member_registration">
              <i class="bi bi-circle"></i>
              <span>Member Registration</span>
            </a>
          </li>
        </ul>

        <a class="nav-link collapsed" data-bs-target="#payment-list-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-currency-dollar"></i>
          <span>Payment</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="payment-list-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="payment_list">
              <i class="bi bi-circle"></i>
              <span>Payment List</span>
            </a>
          </li>

        </ul>

        <a class="nav-link collapsed" data-bs-target="#chapter-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-shop"></i>
          <span>Chapter</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="chapter-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="chapter_list">
              <i class="bi bi-circle"></i>
              <span>Chapter List</span>
            </a>
          </li>


        </ul>


      </li>
    <?php endif; ?>


    <!-- Staff-specific menu -->
    <?php if ($position === "president") : ?>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#members-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i>
          <span>Members</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="members-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">


          <li>
            <a href="associate_member">
              <i class="bi bi-circle"></i>
              <span>Associate Member</span>
            </a>
          </li>


          <li>
            <a href="member_request">
              <i class="bi bi-circle"></i>
              <span>Member Request</span>
            </a>
          </li>



          <li>
            <a href="regular_member">
              <i class="bi bi-circle"></i>
              <span>Regular Member</span>
            </a>
          </li>
          <li>
            <a href="deceased_member">
              <i class="bi bi-circle"></i>
              <span>Deceased Member</span>
            </a>
          </li>
        </ul>

           <a class="nav-link collapsed" data-bs-target="#staff-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person"></i>
          <span>Staff</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="staff-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="staff">
              <i class="bi bi-circle"></i>
              <span>Staff List</span>
            </a>
          </li>

        </ul>

         <a class="nav-link collapsed" data-bs-target="#payment-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-currency-dollar"></i>
          <span>Payment</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="payment-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <a href="payment_list">
              <i class="bi bi-circle"></i>
              <span>Payment List</span>
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
                  <a href="chapter_report" class="<?php echo ($current_page === 'chapter_report') ? 'active' : ''; ?>">
                      <i class="bi bi-circle"></i>
                      <span>Chapter Report</span>
                  </a>
          </li>


          <li>
            <a href="deceased_table">
              <i class="bi bi-circle"></i>
              <span>Deceased Request</span>
            </a>
          </li>


         <li>
            <a href="payment_request" class="<?php echo ($current_page === 'payment_request') ? 'active' : ''; ?>">
              <i class="bi bi-circle"></i>
              <span>Payment Release</span>
            </a>
         </li>


        </ul>

      </li>



    <?php endif; ?>
    <!-- Regular member menu -->
    <?php if ($position === "member" && $account === "Regular") : ?>
        <!-- <li class="nav-item">
          <a class="nav-link collapsed <?php echo ($current_page === 'deceased_list') ? 'active' : ''; ?>"
            data-bs-target="#staff-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person-x"></i>
            <span>Deceased Person</span>
            <i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="staff-nav" class="nav-content collapse <?php echo ($current_page === 'deceased_list') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
            <li>
              <a href="deceased_list" class="<?php echo ($current_page === 'deceased_list') ? 'active' : ''; ?>">
                <i class="bi bi-circle"></i>
                <span>List of Member</span>
              </a>
            </li>
          </ul> -->

          <a class="nav-link collapsed <?php echo ($current_page === 'payment_list') ? 'active' : ''; ?>"
            data-bs-target="#payment-nav" data-bs-toggle="collapse" href="#">
            <i class="bx bxs-dollar-circle"></i>
            <span>Payments</span>
            <i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="payment-nav" class="nav-content collapse <?php echo ($current_page === 'payment_list') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
            <li>
              <a href="payment_list" class="<?php echo ($current_page === 'payment_list') ? 'active' : ''; ?>">
                <i class="bi bi-circle"></i>
                <span>Mortuary Payment</span>
              </a>
            </li>
            <li>
              <a href="membership_payment" class="<?php echo ($current_page === 'membership_payment') ? 'active' : ''; ?>">
                <i class="bi bi-circle"></i>
                <span>Membership Payment</span>
              </a>
            </li>

          </ul>
        </li>

    <?php endif; ?>


    <!-- Regular member menu -->
    <?php if ($position === "treasurer" ) : ?>
              <li class="nav-item">
          <a class="nav-link collapsed <?php echo ($current_page === 'member_list' || $current_page === 'deceased_member') ? 'active' : ''; ?>"
            data-bs-target="#member-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-person"></i>
            <span>Member</span>
            <i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="member-nav" class="nav-content collapse <?php echo ($current_page === 'member_list' || $current_page === 'deceased_member') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
            <li>
              <a href="member_list" class="<?php echo ($current_page === 'member_list') ? 'active' : ''; ?>">
                <i class="bi bi-circle"></i>
                <span>Member List</span>
              </a>
            </li>



               <li>
                  <a href="deceased_member" class="<?php echo ($current_page === 'deceased_member') ? 'active' : ''; ?>">
                    <i class="bi bi-circle"></i>
                    <span>Deceased Member</span>
                  </a>
               </li>

          </ul>

        <a class="nav-link collapsed <?php echo ($current_page === 'payment_request' || $current_page === 'walkin_payment'  || $current_page === 'view_payment_details') ? 'active' : ''; ?>"
          data-bs-target="#payment-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-cash"></i>
            <span>Payment</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>


          <ul id="payment-nav" class="nav-content collapse <?php echo ($current_page === 'payment_request' || $current_page === 'walkin_payment' || $current_page === 'view_payment_details') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">

                    <li>
                        <a href="walkin_payment" class="<?php echo ($current_page === 'walkin_payment') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Walkin Payment</span>
                        </a>
                    </li>
                     <li>
                        <a href="membership_payment" class="<?php echo ($current_page === 'membership_payment') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Membership Payment</span>
                        </a>
                    </li>


          </ul>

        <a class="nav-link collapsed <?php echo ($current_page === 'transaction') ? 'active' : ''; ?>"
          data-bs-target="#transac-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-cash"></i>
            <span>Transaction</span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>


          <ul id="transac-nav" class="nav-content collapse <?php echo ($current_page === 'transaction_payment' || $current_page === 'walkin_payment' || $current_page === 'view_payment_details') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="transaction_payment" class="<?php echo ($current_page === 'transaction_payment') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Release Payment</span>
                        </a>
                    </li>

                        <li>
                        <a href="confirm_payment" class="<?php echo ($current_page === 'confirm_payment') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Confirm Payment</span>
                        </a>
                    </li>



          </ul>





          <a class="nav-link collapsed <?php echo ($current_page === 'transaction_reports' ) ? 'active' : ''; ?>"
          data-bs-target="#reports-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-folder"></i>
            <span>Reports</span>
            <i class="bi bi-chevron-down ms-auto"></i>
         </a>


          <ul id="reports-nav" class="nav-content collapse <?php echo ($current_page === 'transaction_reports' || $current_page === 'collection_reports') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="chapter_report" class="<?php echo ($current_page === 'chapter_report') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Chapter Report</span>
                        </a>
                    </li>

                    <li>
                        <a href="disburstment_list" class="<?php echo ($current_page === 'collection_reports') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Disburstment</span>
                        </a>
                    </li>


                    <li>
                        <a href="transaction_reports" class="<?php echo ($current_page === 'transaction_reports' || $current_page === 'collection_reports') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Transaction Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="collection_reports" class="<?php echo ($current_page === 'collection_reports') ? 'active' : ''; ?>">
                            <i class="bi bi-circle"></i>
                            <span>Collection Reports</span>
                        </a>
                    </li>



          </ul>


        </li>

    <?php endif; ?>

  </ul>
</aside><!-- End Sidebar -->
