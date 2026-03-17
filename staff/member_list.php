<?php
session_start();
ob_start();

require_once __DIR__ . '/../database/connection.php';

// Helper function to convert name to camel case
function toCamelCase($name) {
    return implode(' ', array_map('ucfirst', array_map('strtolower', explode(' ', $name))));
}

// Fetch users joined with chapter table
$users = [];
$query = "
    SELECT u.*, c.chapter_name
    FROM user_table u
    LEFT JOIN chapters c ON u.chapter = c.chapter_code
";

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

          <table class="table datatable">
            <thead>
              <tr>
                <th>OSCA ID No.</th>
                <th>Name</th>
                <th>Chapter</th>
                <th>Status</th>
                <th>Date Registration</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['osca_id']); ?></td>
                <td><?php
                  $fullName = $user['first_name'] . ' '
                            . ($user['middle_name'] ? $user['middle_name'][0] . '. ' : '')
                            . $user['last_name'];
                  echo htmlspecialchars(toCamelCase($fullName));
                ?></td>
                <td><?php echo htmlspecialchars(toCamelCase($user['chapter_name'] ?? 'N/A')); ?></td>
                <td><?php echo htmlspecialchars($user['account'] ?? ''); ?></td>
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

        </div>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>