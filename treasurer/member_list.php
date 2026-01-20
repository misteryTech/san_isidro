<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* =======================
   Pagination Settings
======================= */
$limit = 10; // rows per page
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* =======================
   Search
======================= */
$search = $_GET['search'] ?? '';
$searchLike = "%" . $search . "%";

/* =======================
   Count Total Rows
======================= */
$countSql = "
  SELECT COUNT(*) AS total
  FROM user_table
  WHERE position = 'member'
  AND (
    osca_id LIKE ?
    OR first_name LIKE ?
    OR last_name LIKE ?
    OR chapter LIKE ?
  )
";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("ssss", $searchLike, $searchLike, $searchLike, $searchLike);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

/* =======================
   Fetch Paginated Data
======================= */
$dataSql = "
  SELECT *
  FROM user_table
  WHERE position = 'member'
  AND (
    osca_id LIKE ?
    OR first_name LIKE ?
    OR last_name LIKE ?
    OR chapter LIKE ?
  )
  ORDER BY last_name ASC
  LIMIT ? OFFSET ?
";
$dataStmt = $conn->prepare($dataSql);
$dataStmt->bind_param(
  "ssssii",
  $searchLike,
  $searchLike,
  $searchLike,
  $searchLike,
  $limit,
  $offset
);
$dataStmt->execute();
$result = $dataStmt->get_result();
?>

<div class="section section-dashboard">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">

          <h5 class="card-title">Member List</h5>

          <!-- 🔍 Search -->
          <form method="GET" class="mb-3">
            <div class="input-group">
              <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search member..."
                value="<?= htmlspecialchars($search); ?>"
              >
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>OSCA ID</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Chapter</th>
                  <th>Account</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['osca_id']); ?></td>
                      <td><?= htmlspecialchars($row['first_name']); ?></td>
                      <td><?= htmlspecialchars($row['last_name']); ?></td>
                      <td><?= htmlspecialchars($row['chapter']); ?></td>
                      <td><?= htmlspecialchars($row['account']); ?></td>
                    <td>
                      <?php
                        $status = strtolower($row['status']);

                        switch ($status) {
                          case 'active':
                            $badge = 'success';
                            break;
                          case 'pending':
                            $badge = 'warning';
                            break;
                          case 'suspended':
                            $badge = 'danger';
                            break;
                          case 'inactive':
                            $badge = 'secondary';
                            break;
                          default:
                            $badge = 'primary';
                        }
                      ?>
                      <span class="badge bg-<?= $badge; ?>">
                        <?= htmlspecialchars(ucfirst($row['status'])); ?>
                      </span>
                    </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center">No records found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- 📄 Pagination -->
          <?php if ($totalPages > 1): ?>
            <nav>
              <ul class="pagination justify-content-center mt-3">

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                  <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link"
                       href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>">
                      <?= $i; ?>
                    </a>
                  </li>
                <?php endfor; ?>

              </ul>
            </nav>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
