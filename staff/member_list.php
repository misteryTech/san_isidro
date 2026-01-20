<?php
session_start();
ob_start(); // Capture page content

require_once __DIR__ . '/../database/connection.php';

// Fetch users from database securely
$users = [];
$query = "SELECT * FROM user_table";

if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
}

$conn->close();
?>

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Members</h5>
          <!-- <p>
            Add lightweight datatables to your project using the
            <a href="https://github.com/fiduswriter/Simple-DataTables" target="_blank">Simple DataTables</a> library.
            Just add <code>.datatable</code> class name to any table you wish to convert to a datatable.
            Check <a href="https://fiduswriter.github.io/simple-datatables/demos/" target="_blank">more examples</a>.
          </p> -->

          <!-- Table with stripped rows -->
          <table class="table datatable">
            <thead>
              <tr>
                <th >OSCA ID No.</th>
                <th><b>Name</b></th>
                <th><b>Chapter</b></th>
                <th>Status</th>
                <th>Date Registration</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['osca_id']); ?></td>
                <td><?php echo htmlspecialchars($user['chapter']); ?></td>
                <td><?php echo htmlspecialchars($user['account']); ?></td>
                <td><?php echo htmlspecialchars($user['date_registration']); ?></td>
                <td>
                  <a href="view_user.php?id=<?= urlencode($user['id']); ?>"
                    class="btn btn-sm btn-primary">
                    View
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <!-- End Table -->

        </div>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
