<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// Fetch distinct months and years from approved applications for filter
$filterStmt = $conn->prepare("
    SELECT DISTINCT
        YEAR(updated_at) AS year,
        MONTH(updated_at) AS month
    FROM deceased_benefit_applications
    WHERE status = 'Approved'
    ORDER BY year DESC, month DESC
");
$filterStmt->execute();
$filterResult = $filterStmt->get_result();
$availableMonths = [];
$availableYears = [];
while ($row = $filterResult->fetch_assoc()) {
    if (!in_array($row['month'], $availableMonths)) $availableMonths[] = $row['month'];
    if (!in_array($row['year'], $availableYears)) $availableYears[] = $row['year'];
}

// Get selected month/year from GET parameters, fallback to latest available
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : ($availableMonths[0] ?? date('n'));
$selectedYear  = isset($_GET['year']) ? (int)$_GET['year'] : ($availableYears[0] ?? date('Y'));

// Fetch approved applications with total completed payment for the selected month/year
$stmt = $conn->prepare("
    SELECT
        dba.*,
        ut.*,
        IFNULL(SUM(p.amount), 0) AS total_payment
    FROM deceased_benefit_applications AS dba
    LEFT JOIN user_table AS ut ON ut.osca_id = dba.osca_id
    LEFT JOIN payments AS p ON p.deceased_benefit_id = dba.id AND p.payment_status = 'completed'
    WHERE dba.status = 'Approved'
      AND MONTH(dba.updated_at) = ?
      AND YEAR(dba.updated_at) = ?
    GROUP BY dba.id
    ORDER BY dba.updated_at DESC
");
$stmt->bind_param("ii", $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

// Calculate monthly total
$monthlyTotalStmt = $conn->prepare("
    SELECT IFNULL(SUM(p.amount), 0) AS monthly_total
    FROM deceased_benefit_applications AS dba
    LEFT JOIN payments AS p ON p.deceased_benefit_id = dba.id AND p.payment_status = 'completed'
    WHERE dba.status = 'Approved'
      AND MONTH(dba.updated_at) = ?
      AND YEAR(dba.updated_at) = ?
");
$monthlyTotalStmt->bind_param("ii", $selectedMonth, $selectedYear);
$monthlyTotalStmt->execute();
$monthlyTotal = $monthlyTotalStmt->get_result()->fetch_assoc()['monthly_total'] ?? 0;

// Calculate yearly total
$grandTotalStmt = $conn->prepare("
    SELECT IFNULL(SUM(p.amount), 0) AS grand_total
    FROM deceased_benefit_applications AS dba
    LEFT JOIN payments AS p ON p.deceased_benefit_id = dba.id AND p.payment_status = 'completed'
    WHERE dba.status = 'Approved'
      AND YEAR(dba.updated_at) = ?
");
$grandTotalStmt->bind_param("i", $selectedYear);
$grandTotalStmt->execute();
$grandTotal = $grandTotalStmt->get_result()->fetch_assoc()['grand_total'] ?? 0;
?>

<!-- Print Styles (only affect printing, not web layout) -->
<style>
@media print {
    body * { visibility: hidden; }
    #printableArea, #printableArea * { visibility: visible; }
    #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #ddd; }
}
</style>

<section class="section" id="printableArea">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body mt-3">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Deceased Persons Payment Report</h5>
                        <button class="btn btn-success btn-sm no-print" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>

                    <!-- Month/Year Filter -->
                    <form method="GET" class="mb-3 d-flex align-items-center gap-2 no-print">
                        <label for="monthFilter" class="form-label mb-0">Month:</label>
                        <select name="month" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($availableMonths as $m): ?>
                                <?php $monthName = date("F", mktime(0,0,0,$m,1)); ?>
                                <option value="<?= $m ?>" <?= ($m == $selectedMonth) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($monthName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="yearFilter" class="form-label mb-0">Year:</label>
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($availableYears as $y): ?>
                                <option value="<?= $y ?>" <?= ($y == $selectedYear) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($y) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>

                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>OSCA ID</th>
                                <th>Date Approved</th>
                                <th>Total Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php $counter++; ?>
                                <tr>
                                    <td><?= $counter ?></td>
                                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']) ?></td>
                                   <td>
                                    <?= htmlspecialchars(
                                            !empty($row['updated_at'])
                                            ? date("F j, Y g:i A", strtotime($row['updated_at']))
                                            : ''
                                        ); ?>
                                    </td>
                                    <td><?= number_format($row['total_payment'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr class="table-secondary">
                                <td colspan="4" class="text-end"><strong>Grand Total (Month <?= date("F", mktime(0,0,0,$selectedMonth,1)) ?> <?= $selectedYear ?>):</strong></td>
                                <td><strong><?= number_format($monthlyTotal, 2) ?></strong></td>
                            </tr>
                            <tr class="table-secondary">
                                <td colspan="4" class="text-end"><strong>Grand Total (Year <?= $selectedYear ?>):</strong></td>
                                <td><strong><?= number_format($grandTotal, 2) ?></strong></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No approved applications found for this month/year.
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
$stmt->close();
$monthlyTotalStmt->close();
$grandTotalStmt->close();
$filterStmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
