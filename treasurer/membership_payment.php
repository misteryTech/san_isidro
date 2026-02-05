<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* =======================
   Pagination Settings
======================= */
$limit = 10;
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
  AND status = 'active'
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
  AND status = 'active'
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

<style>
  .alert {
  opacity: 1;
  transition: opacity 1s ease;
}

</style>
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



          <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] === 'success') {
                    echo "<div style='color: green; font-weight: bold;'>Payment successful! 🎉</div>";
                } elseif ($_GET['status'] === 'error') {
                    echo "<div style='color: red; font-weight: bold;'>Something went wrong. Please try again.</div>";
                }
            }
            ?>




          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>OSCA ID</th>
                  <th>Name</th>
                  <th>Chapter</th>
                  <th>Account</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                      $status = strtolower($row['status']);
                      $badge = match ($status) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        'inactive' => 'secondary',
                        default => 'primary'
                      };
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($row['osca_id']); ?></td>
                      <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                      <td><?= htmlspecialchars($row['chapter']); ?></td>
                      <td><?= htmlspecialchars($row['account']); ?></td>
                      <td>
                        <span class="badge bg-<?= $badge; ?>">
                          <?= ucfirst($row['status']); ?>
                        </span>
                      </td>
                      <td>
                       <button
                        class="btn btn-sm btn-primary payBtn"
                        data-bs-toggle="modal"
                        data-bs-target="#paymentModal"
                        data-osca="<?= htmlspecialchars($row['osca_id']); ?>"
                        data-name="<?= htmlspecialchars($row['first_name'].' '.$row['last_name']); ?>"
                      >
                        Pay
                      </button>

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
                    <a class="page-link" href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>">
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

<!-- =======================
     PAYMENT MODAL
======================= -->
<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form action="transaction/php/process_payment.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="osca_id" id="osca_id">
          <input type="hidden" name="payment_type" id="payment_type">


          <div class="mb-2">
            <label class="form-label">Member</label>
            <input type="text" id="member_name" class="form-control" readonly>
          </div>

          <div class="mb-2">
            <label for="receipt">Receipt No.</label>
            <input type="text" name="receipt" id="receipt" class="form-control" required>
          </div>
          <!-- Payment type cards -->
          <label class="form-label mt-3">Select Fee</label>
          <div class="row g-2">

            <div class="col-4">
              <div class="card payment-card text-center p-2"
                   data-type="membership" data-amount="30">
                <strong>Membership</strong>
                <small>30</small>
              </div>
            </div>

            <div class="col-4">
              <div class="card payment-card text-center p-2"
                   data-type="monthly" data-amount="100">
                <strong>Monthly</strong>
                <small>₱100</small>
              </div>
            </div>

            <div class="col-4">
              <div class="card payment-card text-center p-2"
                   data-type="regional" data-amount="300">
                <strong>Regional</strong>
                <small>₱300</small>
              </div>
            </div>

          </div>

          <!-- Monthly only -->
          <div class="mt-3 d-none" id="monthField">
            <label class="form-label">Month</label>
            <input type="month" name="month" class="form-control">
          </div>

          <!-- Regional only -->
          <div class="mt-3 d-none" id="regionField">
            <label class="form-label">Region</label>
            <input type="text" name="region" class="form-control">
          </div>

          <div class="mt-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" readonly required>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success w-100">Confirm Payment</button>
        </div>
      </form>

    </div>
  </div>
</div>


<!-- =======================
     MODAL SCRIPT
======================= -->
<script>document.addEventListener('DOMContentLoaded', () => {
  // Fade out alert after 3 seconds
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      alert.style.transition = "opacity 1s ease"; // smooth fade
      alert.style.opacity = "0";                  // fade out
      setTimeout(() => alert.remove(), 1000);     // remove after fade
    }
  }, 3000);

  // Handle pay button clicks
  document.querySelectorAll('.payBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      osca_id.value = btn.dataset.osca;
      member_name.value = btn.dataset.name;

      payment_type.value = '';
      amount.value = '';

      monthField.classList.add('d-none');
      regionField.classList.add('d-none');

      document.querySelectorAll('.payment-card')
        .forEach(c => c.classList.remove('border-primary'));
    });
  });

  // Handle payment card selection
  document.querySelectorAll('.payment-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.payment-card')
        .forEach(c => c.classList.remove('border-primary'));

      card.classList.add('border', 'border-primary');

      payment_type.value = card.dataset.type;
      amount.value = card.dataset.amount;

      monthField.classList.add('d-none');
      regionField.classList.add('d-none');

      if (card.dataset.type === 'monthly') {
        monthField.classList.remove('d-none');
      }

      if (card.dataset.type === 'regional') {
        regionField.classList.remove('d-none');
      }
    });
  });
});
</script>


<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
