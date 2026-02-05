<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

/**
 * Fetch approved applications for table
 */
$stmt = $conn->prepare("
    SELECT dba.*, ut.first_name, ut.last_name
    FROM deceased_benefit_applications AS dba
    LEFT JOIN user_table AS ut ON ut.osca_id = dba.osca_id
    WHERE dba.status = 'Approved'
      AND MONTH(dba.updated_at) = ?
      AND YEAR(dba.updated_at) = ?
");
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

/**
 * Fetch counts per date for calendar marks
 */
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
$calendarResult = $calendarStmt->get_result();

$calendarMarks = [];
while ($row = $calendarResult->fetch_assoc()) {
    $calendarMarks[$row['mark_date']] = $row['total'];
}
?>


<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">
    <?php
$firstDayOfMonth = strtotime("$year-$month-01");
$daysInMonth = date('t', $firstDayOfMonth);
$startDay = date('w', $firstDayOfMonth); // 0 (Sun) - 6 (Sat)
?>


<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title text-center">
            <?= date("F Y", $firstDayOfMonth); ?>
        </h5>

        <table class="table table-bordered text-center">
            <thead class="table-light">
                <tr>
                    <th>Sun</th><th>Mon</th><th>Tue</th>
                    <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <?php
                for ($i = 0; $i < $startDay; $i++) {
                    echo "<td></td>";
                }

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $hasData = isset($calendarMarks[$dateKey]);

                    $badge = $hasData
                        ? "<span class='badge bg-danger mt-1'>{$calendarMarks[$dateKey]}</span>"
                        : "";

                    echo "
                        <td class='".($hasData ? "bg-light" : "")."'>
                            <strong>$day</strong><br>
                            $badge
                        </td>
                    ";

                    if ((($day + $startDay) % 7) === 0) {
                        echo "</tr><tr>";
                    }
                }

                echo "</tr>";
                ?>
            </tbody>
        </table>

        <small class="text-muted">
            🔴 Badge indicates approved deceased records on that date.
        </small>
    </div>
</div>



            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List of Deceased Person (<?= htmlspecialchars(date("F Y", strtotime("$year-$month-01"))) ?>)</h5>

                    <table class="table table-striped table-bordered datatable">
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
                            <?php $counter = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $counter++;
                                    $modalId = "userModal" . $counter;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                    <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                    <td><?= htmlspecialchars($row['updated_at'] ?? ''); ?></td>
                                    <td>
                                            <a href="view_deceased_profile.php?osca_id=<?= urlencode($row['osca_id']); ?>">
                                                <button class="btn btn-primary btn-sm">View</button>
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
$stmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
<script src="transaction/js/dashboard.js"></script>
