<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// Get filter inputs
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// SQL query for disbursement data
$stmt = $conn->prepare("
    SELECT
        dba.*,
        ut.first_name,
        ut.last_name,
        dt.amount AS disbursed_amount,
        dt.released,
        dt.released_date
    FROM deceased_benefit_applications AS dba
    LEFT JOIN user_table AS ut ON ut.osca_id = dba.osca_id
    LEFT JOIN disbursements AS dt ON dt.dba_id = dba.id
    WHERE dba.status = 'Approved'
      AND YEAR(dba.updated_at) = ?
      AND MONTH(dba.updated_at) = ?
    ORDER BY dt.released_date DESC
");

$stmt->bind_param("ii", $year, $month);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for table and chart
$disbursements = [];
$monthly_totals = array_fill(1, 12, 0); // 12 months

while ($row = $result->fetch_assoc()) {
    $disbursements[] = $row;
    if (!empty($row['disbursed_amount']) && !empty($row['released_date'])) {
        $month_num = (int)date('n', strtotime($row['released_date']));
        $monthly_totals[$month_num] += (float)$row['disbursed_amount'];
    }
}

$stmt->close();
$conn->close();
?>

<section class="section">
    <div class="row mb-4">
        <div class="col-lg-12">
            <!-- Filter Form -->
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label for="month" class="form-label">Month</label>
                    <select name="month" id="month" class="form-select">
                        <?php
                        for ($m=1; $m<=12; $m++) {
                            $selected = ($m === $month) ? 'selected' : '';
                            echo "<option value='$m' $selected>".date('F', mktime(0,0,0,$m,1))."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">Year</label>
                    <input type="number" name="year" id="year" class="form-control" value="<?= htmlspecialchars($year) ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <button type="button" onclick="window.print();" class="btn btn-secondary">Print</button>
                </div>
            </form>

            <!-- Chart -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Monthly Disbursement Chart - <?= htmlspecialchars($year) ?></h5>
                    <canvas id="disbursementChart" height="100"></canvas>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Disbursement List (<?= htmlspecialchars(date("F Y", strtotime("$year-$month-01"))) ?>)</h5>
                    <table class="table table-striped table-bordered datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>OSCA ID</th>
                                <th>Date Approved</th>
                                <th>Disbursed Amount</th>
                                <th>Date Disbursed</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($disbursements) > 0): ?>
                            <?php foreach ($disbursements as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                    <td><?= htmlspecialchars($row['updated_at'] ?? ''); ?></td>
                                    <td><?= isset($row['disbursed_amount']) ? number_format($row['disbursed_amount'], 2) : '-' ?></td>
                                    <td><?= !empty($row['released_date']) ? htmlspecialchars($row['released_date']) : '-' ?></td>
                                    <td><?= !empty($row['released']) && $row['released'] === '1' ? 'Released' : 'Pending' ?></td>
                                    <td>
                                        <a href="view_deceased_profile.php?osca_id=<?= urlencode($row['osca_id']); ?>">
                                            <button class="btn btn-primary btn-sm">View</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No disbursements found for this month.
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
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('disbursementChart').getContext('2d');
    const disbursementChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            datasets: [{
                label: 'Total Disbursement (₱)',
                data: <?= json_encode(array_values($monthly_totals)); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
