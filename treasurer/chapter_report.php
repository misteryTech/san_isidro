<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

// --- Fetch total payments per chapter ---
$chapterPaymentStmt = $conn->prepare("
    SELECT
        u.chapter,
        SUM(p.amount) AS total_paid
    FROM payments p
    LEFT JOIN user_table u
        ON p.osca_id COLLATE utf8mb4_unicode_ci = u.osca_id COLLATE utf8mb4_unicode_ci
    GROUP BY u.chapter
    ORDER BY total_paid DESC
");
$chapterPaymentStmt->execute();
$chapterPayments = $chapterPaymentStmt->get_result();

$chapterLabels = [];
$chapterData   = [];
$chapterRows   = []; // For table

while ($row = $chapterPayments->fetch_assoc()) {
    $chapterName = $row['chapter'] ?: 'Unknown';
    $totalPaid   = (float)$row['total_paid'];

    $chapterLabels[] = $chapterName;
    $chapterData[]   = $totalPaid;
    $chapterRows[]   = [
        'chapter' => $chapterName,
        'total_paid' => $totalPaid
    ];
}
?>

<div class="section section-dashboard">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Total Payments Per Chapter</h5>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Chapter</th>
                                    <th>Total Payments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($chapterRows as $index => $row): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($row['chapter']) ?></td>
                                        <td><?= number_format($row['total_paid'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <canvas id="chapterPaymentChart" height="100"></canvas>
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
const ctx = document.getElementById('chapterPaymentChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chapterLabels) ?>,
        datasets: [{
            label: 'Total Payments',
            data: <?= json_encode($chapterData) ?>,
            backgroundColor: '#f6c23e'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { ticks: { autoSkip: false, maxRotation: 90, minRotation: 45 } } // handles many chapters
        }
    }
});
</script>
