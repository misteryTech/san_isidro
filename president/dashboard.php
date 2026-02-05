<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* ===========================
   DATE CONTROLS (CALENDAR)
   =========================== */
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

/* ===========================
   TOTAL USERS COUNT
   =========================== */
$userCountStmt = $conn->prepare("SELECT COUNT(*) AS total FROM user_table");
$userCountStmt->execute();
$totalUsers = $userCountStmt->get_result()->fetch_assoc()['total'];

/* ===========================
   PENDING MEMBERSHIP COUNT
   =========================== */
$pendingStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM membership_table
    WHERE status = 'Pending'
");
$pendingStmt->execute();
$totalPending = $pendingStmt->get_result()->fetch_assoc()['total'];

/* ===========================
   DECEASED REQUEST COUNT
   =========================== */
$deceasedStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM deceased_benefit_applications
    WHERE status = 'Pending'
");
$deceasedStmt->execute();
$totalDeceasedRequests = $deceasedStmt->get_result()->fetch_assoc()['total'];

/* ===========================
   PENDING PAYMENTS COUNT
   =========================== */
$paymentStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM payments
    WHERE payment_status = 'Pending'
");
$paymentStmt->execute();
$totalPendingPayments = $paymentStmt->get_result()->fetch_assoc()['total'];

/* ===========================
   CALENDAR MARKS (Approved)
   =========================== */
$calendarStmt = $conn->prepare("
    SELECT DATE(updated_at) AS mark_date, COUNT(*) AS total
    FROM deceased_benefit_applications
    WHERE status = 'Approved'
      AND MONTH(updated_at) = ?
      AND YEAR(updated_at) = ?
    GROUP BY DATE(updated_at)
");
$calendarStmt->bind_param("ii", $month, $year);
$calendarStmt->execute();

$calendarMarks = [];
$calendarResult = $calendarStmt->get_result();
while ($row = $calendarResult->fetch_assoc()) {
    $calendarMarks[$row['mark_date']] = $row['total'];
}
?>

<section class="section dashboard">

<!-- =======================
     TOP STAT CARDS
     ======================= -->
<div class="row">

<?php
$stats = [
    ['Users', 'Total', $totalUsers, 'bi-people'],
    ['Members', 'Pending', $totalPending, 'bi-hourglass-split'],
    ['Deceased', 'Requests', $totalDeceasedRequests, 'bi-heartbreak'],
    ['Payments', 'Pending', $totalPendingPayments, 'bi-cash-stack']
];

foreach ($stats as [$title, $label, $value, $icon]):
?>
<div class="col-xxl-3 col-md-3">
    <div class="card info-card">
        <div class="card-body">
            <h5 class="card-title"><?= $title ?> <span>| <?= $label ?></span></h5>
            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi <?= $icon ?>"></i>
                </div>
                <div class="ps-3">
                    <h6><?= number_format($value) ?></h6>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

</div>

<!-- =======================
     BANNER + CALENDAR
     ======================= -->
<div class="row">

<!-- Banner -->
<div class="col-8">
    <div class="card position-relative mb-3">
        <img src="/path/to/your/banner.jpg"
             class="card-img-top"
             style="height:200px; object-fit:cover;">
        <div class="card-img-overlay d-flex flex-column justify-content-center text-center"
             style="background:rgba(0,0,0,.45)">
            <h5 class="text-white">Welcome to the Dashboard</h5>
            <p class="text-white-50">Latest updates and statistics</p>
        </div>
    </div>
</div>

<!-- Calendar -->
<div class="col-4">
<div class="card mb-3">
<div class="card-body p-2">

<h6 class="text-center mb-2">
<?= date("F Y", strtotime("$year-$month-01")) ?>
</h6>

<table class="table table-bordered text-center small mb-0">
<thead class="table-light">
<tr>
<th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th>
</tr>
</thead>
<tbody>
<tr>
<?php
$firstDay = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDay);
$startDay = date('w', $firstDay);

for ($i = 0; $i < $startDay; $i++) echo "<td></td>";

for ($day = 1; $day <= $daysInMonth; $day++) {
    $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
    $hasData = isset($calendarMarks[$dateKey]);

    echo "<td class='".($hasData ? "bg-light" : "")."'>";
    echo "<strong>$day</strong>";

    if ($hasData) {
        echo "<br><span class='badge bg-danger'>{$calendarMarks[$dateKey]}</span>";
    }
    echo "</td>";

    if ((($day + $startDay) % 7) === 0) echo "</tr><tr>";
}
?>
</tr>
</tbody>
</table>

<small class="text-muted d-block text-center mt-1">
🔴 Approved (based on <code>updated_at</code>)
</small>

</div>
</div>
</div>

</div>

<!-- =======================
     RECENT DECEASED TODAY
     ======================= -->
<div class="row">
<div class="col-4">
<div class="card">
<div class="card-body">
<h5 class="card-title">Recently Deceased Today</h5>

<ul class="list-group list-group-flush">
<?php
$today = date('Y-m-d');
$recentStmt = $conn->prepare("
    SELECT ut.first_name, ut.last_name, d.updated_at
    FROM deceased_benefit_applications d
    LEFT JOIN user_table ut ON ut.osca_id = d.osca_id
    WHERE DATE(d.updated_at) = ?
    ORDER BY d.updated_at DESC
");
$recentStmt->bind_param("s", $today);
$recentStmt->execute();
$result = $recentStmt->get_result();

if ($result->num_rows):
while ($row = $result->fetch_assoc()):
?>
<li class="list-group-item d-flex justify-content-between">
<?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?>
<span class="badge bg-danger"><?= date('M d, Y', strtotime($row['updated_at'])) ?></span>
</li>
<?php endwhile; else: ?>
<li class="list-group-item text-muted text-center">
No deceased members reported today.
</li>
<?php endif; ?>
</ul>

</div>
</div>
</div>
</div>

</section>

<?php
$userCountStmt->close();
$pendingStmt->close();
$deceasedStmt->close();
$paymentStmt->close();
$calendarStmt->close();
$recentStmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
