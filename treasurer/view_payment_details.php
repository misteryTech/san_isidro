<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/*
|-----------------------------------------------------------
| VALIDATE USER ID
|-----------------------------------------------------------
*/
if (empty($_GET['user_id'])) {
    echo "<div class='alert alert-danger'>Invalid User ID.</div>";
    exit;
}

$user_id = (int) $_GET['user_id'];

/*
|-----------------------------------------------------------
| MONTH & YEAR FILTER
|-----------------------------------------------------------
*/
$month = isset($_GET['month']) ? (int) $_GET['month'] : date('m');
$year  = isset($_GET['year'])  ? (int) $_GET['year']  : date('Y');

/*
|-----------------------------------------------------------
| FETCH ALL COMPLETED PAYMENTS FOR USER
|-----------------------------------------------------------
*/
$paymentSql = "
    SELECT deceased_benefit_id
    FROM payments
    WHERE user_id = ?
      AND payment_status = 'completed'
";
$paymentStmt = $conn->prepare($paymentSql);
$paymentStmt->bind_param("i", $user_id);
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();

$paidApplications = [];
while ($p = $paymentResult->fetch_assoc()) {
    $paidApplications[] = $p['deceased_benefit_id'];
}

/*
|-----------------------------------------------------------
| FETCH APPROVED DECEASED APPLICATIONS
|-----------------------------------------------------------
*/
$appSql = "
    SELECT
        id,
        deceased_name,
        date_of_death,
        updated_at
    FROM deceased_benefit_applications
    WHERE status = 'approved'
      AND MONTH(updated_at) = ?
      AND YEAR(updated_at) = ?
    ORDER BY updated_at DESC
";

$appStmt = $conn->prepare($appSql);
$appStmt->bind_param("ii", $month, $year);
$appStmt->execute();
$appResult = $appStmt->get_result();

$paid = [];
$unpaid = [];

while ($row = $appResult->fetch_assoc()) {
    if (in_array($row['id'], $paidApplications)) {
        $paid[] = $row;
    } else {
        $unpaid[] = $row;
    }
}
?>

<section class="section">

<!-- ================= FILTER ================= -->
<div class="row mb-3">
    <div class="col-lg-6">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="user_id" value="<?= $user_id; ?>">

            <select name="month" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m; ?>" <?= ($month == $m) ? 'selected' : ''; ?>>
                        <?= date('F', mktime(0,0,0,$m,1)); ?>
                    </option>
                <?php endfor; ?>
            </select>

            <select name="year" class="form-select">
                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?= $y; ?>" <?= ($year == $y) ? 'selected' : ''; ?>>
                        <?= $y; ?>
                    </option>
                <?php endfor; ?>
            </select>

            <button class="btn btn-primary">
                <i class="bi bi-filter"></i> Filter
            </button>
        </form>
    </div>
</div>

<!-- ================= ALL APPLICATIONS ================= -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Approved Applications (<?= date('F Y', strtotime("$year-$month-01")); ?>)
                </h5>

                <?php if (!empty($paid) || !empty($unpaid)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Deceased Name</th>
                                <th>Date of Death</th>
                                <th>Approved Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach(array_merge($paid,$unpaid) as $row): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($row['deceased_name']); ?></td>
                                <td><?= date('M d, Y', strtotime($row['date_of_death'])); ?></td>
                                <td><?= date('M d, Y', strtotime($row['updated_at'])); ?></td>
                                <td>
                                    <?php if (in_array($row['id'], $paidApplications)): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-warning payBtn"
                                                data-user="<?= $user_id; ?>"
                                                data-deceased="<?= $row['id']; ?>"
                                                data-name="<?= htmlspecialchars($row['deceased_name']); ?>">
                                            Unpaid
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info">No approved applications found.</div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- ================= UNPAID ONLY ================= -->
<div class="row">
    <div class="col-lg-12">
        <div class="card border-danger">
            <div class="card-body">
                <h5 class="card-title text-danger">Unpaid Deceased Applications</h5>

                <?php if (!empty($unpaid)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Deceased Name</th>
                                <th>Date of Death</th>
                                <th>Approved Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach($unpaid as $row): ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($row['deceased_name']); ?></td>
                                <td><?= date('M d, Y', strtotime($row['date_of_death'])); ?></td>
                                <td><?= date('M d, Y', strtotime($row['updated_at'])); ?></td>
                                <td>
                                        <button class="btn btn-sm btn-warning walkinPayBtn"
                                                data-application="<?= $row['id']; ?>"
                                                data-user="<?= $user_id; ?>"
                                                data-name="<?= htmlspecialchars($row['deceased_name']); ?>">
                                            Walk-in Payment
                                        </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-success">All applications are fully paid 🎉</div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php
    include('payment_modal.php');
?>
</section>

<script src="transaction/js/payment_modal.js"></script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
