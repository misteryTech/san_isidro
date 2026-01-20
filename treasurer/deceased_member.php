<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// -------------------------
// Month & Year Filter
// -------------------------
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// -------------------------
// Total Deceased Members
// -------------------------
$totalStmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM deceased_benefit_applications
    WHERE status = 'Approved'
");
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalDeceased = $totalResult->fetch_assoc()['total'] ?? 0;

// -------------------------
// Monthly Deceased Members
// -------------------------
$monthlyStmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM deceased_benefit_applications
    WHERE status = 'Approved'
      AND MONTH(updated_at) = ?
      AND YEAR(updated_at) = ?
");
$monthlyStmt->bind_param("ii", $month, $year);
$monthlyStmt->execute();
$monthlyResult = $monthlyStmt->get_result();
$monthlyDeceased = $monthlyResult->fetch_assoc()['total'] ?? 0;

// -------------------------
// Fetch Approved Deceased List
// -------------------------
$listStmt = $conn->prepare("
    SELECT dba.*, ut.first_name, ut.last_name
    FROM deceased_benefit_applications AS dba
    LEFT JOIN user_table AS ut
        ON ut.osca_id = dba.osca_id
    WHERE dba.status = 'Approved'
      AND MONTH(dba.updated_at) = ?
      AND YEAR(dba.updated_at) = ?
    ORDER BY dba.updated_at DESC
");
$listStmt->bind_param("ii", $month, $year);
$listStmt->execute();
$result = $listStmt->get_result();
?>

<section class="section dashboard">

  <div class="row mb-4 align-items-stretch">

    <!-- Filter Column -->
    <div class="col-lg-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h5 class="card-title mb-3"><i class="bi bi-funnel"></i> Filter Records</h5>
          <form method="GET" class="d-flex flex-column gap-3">
            <div>
              <label class="form-label">Month</label>
              <select name="month" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                  <option value="<?= $m; ?>" <?= ($month == $m) ? 'selected' : ''; ?>>
                    <?= date('F', mktime(0,0,0,$m,1)); ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>

            <div>
              <label class="form-label">Year</label>
              <select name="year" class="form-select">
                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                  <option value="<?= $y; ?>" <?= ($year == $y) ? 'selected' : ''; ?>>
                    <?= $y; ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>

            <button class="btn btn-primary w-100">
              <i class="bi bi-filter"></i> Apply Filter
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Cards Column -->
    <div class="col-lg-9">
      <div class="row g-4">

        <!-- Total Deceased Card -->
        <div class="col-md-6">
          <div class="card shadow-lg border-0 h-100 fancy-card">
            <div class="card-body">
              <h5 class="card-title text-primary">
                <i class="bi bi-people me-2"></i>Total Deceased <span class="text-muted">| All Time</span>
              </h5>
              <div class="d-flex align-items-center mt-3">
                <div class="card-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                  <i class="bi bi-people"></i>
                </div>
                <div>
                  <h2 class="fw-bold mb-0"><?= $totalDeceased; ?></h2>
                  <small class="text-muted">Members</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Deceased Card -->
        <div class="col-md-6">
          <div class="card shadow-lg border-0 h-100 fancy-card">
            <div class="card-body">
              <h5 class="card-title text-success">
                <i class="bi bi-calendar-event me-2"></i>Monthly Deceased <span class="text-muted">| This Month</span>
              </h5>
              <div class="d-flex align-items-center mt-3">
                <div class="card-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                  <i class="bi bi-calendar-event"></i>
                </div>
                <div>
                  <h2 class="fw-bold mb-0"><?= $monthlyDeceased; ?></h2>
                  <small class="text-muted">Members</small>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- row for cards -->
    </div> <!-- col-lg-9 -->

  </div> <!-- row for filter + cards -->

  <!-- ================= DECEASED LIST ================= -->
  <div class="row mt-4">
    <div class="col-lg-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title">
            List of Deceased Persons (<?= date("F Y", strtotime("$year-$month-01")); ?>)
          </h5>
          <table class="table table-hover table-bordered datatable">
            <thead class="table-dark">
              <tr>
                <th>Name</th>
                <th>OSCA ID</th>
                <th>Date Approved</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                    <td><?= htmlspecialchars($row['updated_at'] ?? ''); ?></td>
                    <td>
                      <a href="view_deceased_profile.php?osca_id=<?= urlencode($row['osca_id']); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    No approved applications found for this month.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</section>



<?php
$listStmt->close();
$monthlyStmt->close();
$totalStmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/dashboard.js"></script>
