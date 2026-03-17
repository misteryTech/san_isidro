<?php
session_start();
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* Search & Pagination config */
$search      = trim($_GET['search'] ?? '');
$perPage     = 10;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($currentPage - 1) * $perPage;

/* Total count for pagination (with search) */
$countStmt = $conn->prepare("
    SELECT COUNT(*) FROM chapters
    WHERE chapter_code LIKE ? OR chapter_name LIKE ?
");
$searchParam = "%$search%";
$countStmt->bind_param("ss", $searchParam, $searchParam);
$countStmt->execute();
$totalRows  = $countStmt->get_result()->fetch_row()[0];
$totalPages = ceil($totalRows / $perPage);

/* Clamp currentPage to valid range */
$currentPage = min($currentPage, max(1, $totalPages));
$offset      = ($currentPage - 1) * $perPage;

/* Fetch paginated + filtered chapters */
$chapters = [];
$stmt = $conn->prepare("
    SELECT id, chapter_code, chapter_name, created_at
    FROM chapters
    WHERE chapter_code LIKE ? OR chapter_name LIKE ?
    ORDER BY chapter_name ASC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ssii", $searchParam, $searchParam, $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $chapters[] = $row;
}
?>

<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">

          <h5 class="card-title">Chapters</h5>

          <!-- Toolbar -->
          <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">

            <!-- Search -->
            <form method="GET" class="d-flex gap-2" style="max-width: 400px; width: 100%;">
              <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search by code or name…"
                value="<?= htmlspecialchars($search) ?>"
              >
              <button type="submit" class="btn btn-outline-primary">Search</button>
              <?php if ($search): ?>
                <a href="?" class="btn btn-outline-secondary">Clear</a>
              <?php endif; ?>
            </form>

            <!-- Add Chapter -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#chapterModal"
                    onclick="resetModal()">
              Add Chapter
            </button>

          </div>

          <!-- Search result info -->
          <?php if ($search): ?>
            <p class="text-muted small mb-2">
              Showing results for: <strong><?= htmlspecialchars($search) ?></strong>
              — <?= $totalRows ?> record(s) found.
            </p>
          <?php endif; ?>

          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Chapter Code</th>
                <th>Chapter Name</th>
                <th>Date Created</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($chapters): ?>
              <?php foreach ($chapters as $i => $c): ?>
                <tr>
                  <td><?= $offset + $i + 1 ?></td>
                  <td><?= htmlspecialchars($c['chapter_code']) ?></td>
                  <td><?= htmlspecialchars($c['chapter_name']) ?></td>
                  <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                  <td>
                    <button class="btn btn-sm btn-warning"
                      onclick="editChapter(
                        '<?= $c['id'] ?>',
                        '<?= htmlspecialchars($c['chapter_code'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($c['chapter_name'], ENT_QUOTES) ?>'
                      )">
                      Edit
                    </button>
                    <button class="btn btn-sm btn-danger"
                      onclick="deleteChapter(<?= $c['id'] ?>)">
                      Delete
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-3">
                  <?= $search ? 'No chapters matched your search.' : 'No chapters found.' ?>
                </td>
              </tr>
            <?php endif; ?>
            </tbody>
          </table>

          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
          <nav aria-label="Chapter pagination">
            <ul class="pagination justify-content-end mb-0">

              <!-- Previous -->
              <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>">
                  &laquo; Prev
                </a>
              </li>

              <!-- Page numbers -->
              <?php
                $start = max(1, $currentPage - 2);
                $end   = min($totalPages, $currentPage + 2);
              ?>

              <?php if ($start > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=1&search=<?= urlencode($search) ?>">1</a>
                </li>
                <?php if ($start > 2): ?>
                  <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
              <?php endif; ?>

              <?php for ($p = $start; $p <= $end; $p++): ?>
                <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                  <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>"><?= $totalPages ?></a>
                </li>
              <?php endif; ?>

              <!-- Next -->
              <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>">
                  Next &raquo;
                </a>
              </li>

            </ul>
          </nav>

          <!-- Page info -->
          <p class="text-muted small mt-2 text-end">
            Showing <?= $totalRows > 0 ? $offset + 1 : 0 ?>–<?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?> chapters
          </p>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="chapterModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="chapterForm">
      <input type="hidden" name="id" id="chapter_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add Chapter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Chapter Code</label>
            <input type="text" class="form-control" name="chapter_code" id="chapter_code" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Chapter Name</label>
            <input type="text" class="form-control" name="chapter_name" id="chapter_name" required>
          </div>

          <div id="chapterMessage" class="alert d-none"></div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

<script>
const modal = new bootstrap.Modal(document.getElementById('chapterModal'));

function resetModal() {
  document.getElementById('modalTitle').innerText = 'Add Chapter';
  document.getElementById('chapter_id').value = '';
  document.getElementById('chapter_code').value = '';
  document.getElementById('chapter_name').value = '';
  document.getElementById('chapterMessage').classList.add('d-none');
}

function editChapter(id, code, name) {
  document.getElementById('modalTitle').innerText = 'Edit Chapter';
  document.getElementById('chapter_id').value = id;
  document.getElementById('chapter_code').value = code;
  document.getElementById('chapter_name').value = name;
  document.getElementById('chapterMessage').classList.add('d-none');
  modal.show();
}

function deleteChapter(id) {
  if (!confirm('Delete this chapter?')) return;

  fetch('transaction/php/chapter_handler.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=delete&id=' + id
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.status === 'success') location.reload();
  });
}

document.getElementById('chapterForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('action', 'save');

  fetch('transaction/php/chapter_handler.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    const msg = document.getElementById('chapterMessage');
    msg.classList.remove('d-none', 'alert-success', 'alert-danger');
    msg.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
    msg.textContent = data.message;

    if (data.status === 'success') {
      setTimeout(() => location.reload(), 600);
    }
  });
});
</script>