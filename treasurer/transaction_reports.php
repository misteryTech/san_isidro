<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$userStmt = $conn->prepare("
    SELECT
        osca_id,
        first_name,
        last_name,
        chapter,
        account,
        status,
        date_registration
    FROM user_table
    WHERE position = 'member'
      AND date_registration IS NOT NULL
    ORDER BY date_registration ASC
");

$userStmt->execute();
$result = $userStmt->get_result();

$chartStmt = $conn->prepare("
    SELECT
        MONTH(date_registration) AS month_num,
        DATE_FORMAT(date_registration, '%M') AS month_name,
        COUNT(*) AS total_members
    FROM user_table
    WHERE position = 'member'
      AND date_registration IS NOT NULL
    GROUP BY month_num, month_name
    ORDER BY month_num ASC
");

$chartStmt->execute();
$chartResult = $chartStmt->get_result();

$labels = [];
$data   = [];

while ($row = $chartResult->fetch_assoc()) {
    $labels[] = $row['month_name'];   // January, February, etc.
    $data[]   = (int)$row['total_members'];
}

/* Chart: Associate vs Regular members */
$typeStmt = $conn->prepare("
    SELECT
        account,
        COUNT(*) AS total
    FROM user_table
    WHERE position = 'member'
      AND date_registration IS NOT NULL
    GROUP BY account
");
$typeStmt->execute();
$typeResult = $typeStmt->get_result();

$typeLabels = [];
$typeData   = [];

while ($row = $typeResult->fetch_assoc()) {
    $typeLabels[] = $row['account']; // Associate / Regular
    $typeData[]   = (int)$row['total'];
}
?>
<div class="section section-dashboard">
  <!-- Charts Row -->
  <div class="row mb-4">
    <div class="col-12 col-md-8 mb-3 mb-md-0">
      <div class="card h-20">
        <div class="card-body text-center">
          <h5 class="card-title mb-3">Registered Members Per Month</h5>
          <canvas id="registrationChart" height="120"></canvas>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card h-20">
        <div class="card-body text-center">
          <h5 class="card-title mb-3">Associate vs Regular Members</h5>
          <canvas id="memberTypeChart" height="40"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Member List Row -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Member List</h5>

          <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th>OSCA ID</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Chapter</th>
                  <th>Account</th>
                  <th>Registered On</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                  <tr>
                    <td><?= htmlspecialchars($row['osca_id']) ?></td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['chapter']) ?></td>
                    <td><?= htmlspecialchars($row['account']) ?></td>
                    <td><?= htmlspecialchars(date('F m, Y', strtotime($row['date_registration']))) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>


<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

<script>
const ctx = document.getElementById('registrationChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Registered Members',
            data: <?= json_encode($data) ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});


const typeCtx = document.getElementById('memberTypeChart');

new Chart(typeCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($typeLabels) ?>,
        datasets: [{
            data: <?= json_encode($typeData) ?>
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

</script>
