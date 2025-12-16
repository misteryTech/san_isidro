<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';

/*
|--------------------------------------------------------------------------|
| SEARCH QUERY
|--------------------------------------------------------------------------|
*/
$search = $_GET['search'] ?? '';

/*
|--------------------------------------------------------------------------|
| FETCH USERS + PAYMENT COUNTS
|--------------------------------------------------------------------------|
*/
$params = [];
$types = '';

$userSql = "
    SELECT
        u.id AS user_id,
        u.osca_id,
        u.first_name,
        u.last_name,
        u.chapter,
        COUNT(dba.id) AS total_applications,
        SUM(CASE WHEN p.payment_status = 'pending' THEN 1 ELSE 0 END) AS pending_payments,
        SUM(CASE WHEN p.payment_status = 'completed' THEN 1 ELSE 0 END) AS completed_payments
    FROM user_table u
    LEFT JOIN deceased_benefit_applications dba
        ON dba.osca_id = u.osca_id
        AND dba.status = 'approved'
    LEFT JOIN payments p
        ON p.deceased_benefit_id = dba.id
";

// Add search filter if needed
if (!empty($search)) {
    $userSql .= " WHERE u.osca_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?";
    $searchParam = "%{$search}%";
    $params = [$searchParam, $searchParam, $searchParam];
    $types = "sss";
}

// GROUP BY must come after WHERE
$userSql .= " GROUP BY u.id, u.osca_id, u.first_name, u.last_name, u.chapter
              ORDER BY u.last_name ASC, u.first_name ASC";

$userStmt = $conn->prepare($userSql);
if (!empty($search)) {
    $userStmt->bind_param($types, ...$params);
}

$userStmt->execute();
$users = $userStmt->get_result();
?>

<section class="section">

    <!-- CHAPTER HEADER -->
    <div class="row mb-3">
        <div class="col-lg-12">
            <h4 class="fw-bold text-primary">Chapter: User Management</h4>
        </div>
    </div>

    <!-- SEARCH BAR -->
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

    <!-- USERS LIST -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-secondary">
                <div class="card-body">

                    <h5 class="card-title text-secondary">User List</h5>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>OSCA ID</th>
                                    <th>Name</th>
                                    <th>Chapter</th>
                                    <th>Pending</th>
                                    <th>Completed</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $u = 1; while ($user = $users->fetch_assoc()):
                                    $modalId = 'viewUserModal' . $u;
                                ?>
                                    <tr>
                                        <td><?= $u; ?></td>
                                        <td><?= htmlspecialchars($user['osca_id']); ?></td>
                                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?= htmlspecialchars($user['chapter']); ?></td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                <?= $user['pending_payments'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?= $user['completed_payments'] ?? 0; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#<?= $modalId; ?>">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- VIEW USER MODAL -->
                                    <div class="modal fade" id="<?= $modalId; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">User Payment Summary</h5>
                                                    <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p><strong>OSCA ID:</strong> <?= htmlspecialchars($user['osca_id']); ?></p>
                                                    <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                                    <p><strong>Chapter:</strong> <?= htmlspecialchars($user['chapter']); ?></p>

                                                    <hr>

                                                    <div class="d-flex justify-content-between">
                                                        <span>Pending Payments</span>
                                                        <span class="badge bg-warning text-dark">
                                                            <?= $user['pending_payments'] ?? 0; ?>
                                                        </span>
                                                    </div>

                                                    <div class="d-flex justify-content-between mt-2">
                                                        <span>Completed Payments</span>
                                                        <span class="badge bg-success">
                                                            <?= $user['completed_payments'] ?? 0; ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                    <a href="view_payment_details.php?user_id=<?= urlencode($user['user_id']); ?>"
                                                       class="btn btn-primary btn-sm">
                                                        View Payment Details
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php $u++; endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($users->num_rows === 0): ?>
                        <div class="alert alert-info mt-3">No users found.</div>
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
