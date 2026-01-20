<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/*
|--------------------------------------------------------------------------
| SEARCH QUERY
|--------------------------------------------------------------------------
*/
$search = $_GET['search'] ?? '';

$params = [];
$types  = '';

/*
|--------------------------------------------------------------------------
| FETCH GLOBAL APPROVED DECEASED COUNT
|--------------------------------------------------------------------------
*/
$countSql = "
    SELECT COUNT(*) AS total_approved
    FROM deceased_benefit_applications
    WHERE status = 'approved'
";
$countStmt = $conn->prepare($countSql);
$countStmt->execute();
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();

$totalApproved = $countRow['total_approved'] ?? 0;

/*
|--------------------------------------------------------------------------
| FETCH USERS (NO PER-USER COUNT)
|--------------------------------------------------------------------------
*/
$userSql = "
    SELECT
        u.id AS user_id,
        u.osca_id,
        u.first_name,
        u.last_name,
        u.chapter
    FROM user_table u WHERE account = 'Regular' AND status = 'Active'
";

if (!empty($search)) {
    $userSql .= " WHERE u.osca_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?";
    $searchParam = "%{$search}%";
    $params = [$searchParam, $searchParam, $searchParam];
    $types  = "sss";
}

$userSql .= " ORDER BY u.last_name ASC, u.first_name ASC";

$userStmt = $conn->prepare($userSql);
if (!empty($search)) {
    $userStmt->bind_param($types, ...$params);
}
$userStmt->execute();
$users = $userStmt->get_result();
?>

<section class="section">

    <!-- HEADER -->
    <div class="row mb-3">
        <div class="col-lg-12">
            <h4 class="fw-bold text-primary">Walkin Payments</h4>
        </div>
    </div>

    <!-- SEARCH -->
    <div class="row mb-3">
        <div class="col-lg-6">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2"
                       placeholder="Search OSCA ID or name..."
                       value="<?= htmlspecialchars($search); ?>">
                <button class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-secondary">
                <div class="card-body">

                    <h5 class="card-title text-secondary">User List</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>OSCA ID</th>
                                    <th>Name</th>
                                    <th>Chapter</th>

                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                while ($row = $users->fetch_assoc()):
                                    $modalId = 'deceasedModal' . $i;
                                ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= htmlspecialchars($row['osca_id']); ?></td>
                                        <td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
                                        <td><?= htmlspecialchars($row['chapter']); ?></td>

                                        <td>
                                            <button class="btn btn-sm btn-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#<?= $modalId; ?>">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- MODAL -->
                                    <div class="modal fade" id="<?= $modalId; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Walkin Payment</h5>
                                                    <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p><strong>OSCA ID:</strong> <?= htmlspecialchars($row['osca_id']); ?></p>
                                                    <p><strong>Name:</strong> <?= htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></p>
                                                    <p><strong>Chapter:</strong> <?= htmlspecialchars($row['chapter']); ?></p>
                                                    <hr>

                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                   <a href="view_payment_details.php?user_id=<?= urlencode($row['user_id']); ?>"
                                                    class="btn btn-primary btn-sm">
                                                        View Deceased List
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                <?php
                                $i++;
                                endwhile;
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($users->num_rows === 0): ?>
                        <div class="alert alert-info mt-3">No records found.</div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
