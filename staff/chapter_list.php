<?php
session_start();
ob_start();
require_once __DIR__ . '/../database/connection.php';

/* Fetch all chapters */
$chapters = [];
$stmt = $conn->prepare("
    SELECT id, chapter_code, chapter_name, created_at
    FROM chapters
    ORDER BY chapter_name ASC
");
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

          <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#chapterModal">
            Add Chapter
          </button>

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
                  <td><?= $i + 1 ?></td>
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
                <td colspan="5" class="text-center text-muted">No chapters found</td>
              </tr>
            <?php endif; ?>
            </tbody>
          </table>

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

function editChapter(id, code, name) {
  document.getElementById('modalTitle').innerText = 'Edit Chapter';
  document.getElementById('chapter_id').value = id;
  document.getElementById('chapter_code').value = code;
  document.getElementById('chapter_name').value = name;
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
