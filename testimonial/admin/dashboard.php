<?php
require __DIR__ . '/_auth.php';
require_testimonial_admin();

$ok = '';
$err = '';
$csrf = testimonial_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!testimonial_verify_csrf($_POST['csrf_token'] ?? null)) {
    $err = 'Security validation failed.';
  } else {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
      $err = 'Invalid testimonial selected.';
    } elseif ($action === 'approve') {
      db()->prepare("UPDATE testimonials SET status='approved', approved_at=NOW(), updated_at=NOW() WHERE id=?")->execute([$id]);
      $ok = 'Testimonial approved.';
    } elseif ($action === 'reject') {
      db()->prepare("UPDATE testimonials SET status='rejected', updated_at=NOW() WHERE id=?")->execute([$id]);
      $ok = 'Testimonial rejected.';
    } elseif ($action === 'delete') {
      db()->prepare("DELETE FROM testimonials WHERE id=?")->execute([$id]);
      $ok = 'Testimonial deleted.';
    }
  }
}

$q = trim($_GET['q'] ?? '');
$ratingFilter = (int)($_GET['rating'] ?? 0);
$statusFilter = trim($_GET['status'] ?? '');

$where = [];
$params = [];

if ($q !== '') {
  $where[] = "(client_name LIKE ? OR company_name LIKE ? OR city LIKE ? OR service_availed LIKE ? OR testimonial_text LIKE ?)";
  $like = '%' . $q . '%';
  $params = array_merge($params, [$like, $like, $like, $like, $like]);
}

if ($ratingFilter >= 1 && $ratingFilter <= 5) {
  $where[] = "rating = ?";
  $params[] = $ratingFilter;
}

if (in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
  $where[] = "status = ?";
  $params[] = $statusFilter;
}

$sql = "SELECT * FROM testimonials";
if ($where) {
  $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY created_at DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$widget = db()->query("
  SELECT
    COUNT(*) AS total_reviews,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS pending_reviews,
    SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) AS approved_reviews,
    COALESCE(AVG(CASE WHEN status='approved' AND publish_permission=1 THEN rating END), 0) AS average_rating
  FROM testimonials
")->fetch() ?: ['total_reviews' => 0, 'pending_reviews' => 0, 'approved_reviews' => 0, 'average_rating' => 0];

$page_title = "Testimonial Management";
require __DIR__ . '/../../support/_layout_header.php';
?>

<div class="card">
  <h3>Testimonial Management</h3>
  <p class="note">
    Logged in as <b><?= h($_SESSION['admin_username'] ?? 'admin') ?></b> |
    <a href="<?= h(app_href('/testimonial/')) ?>" target="_blank"><b>View Public Testimonials</b></a> |
    <a href="<?= h(app_href('/testimonial/admin/logout.php')) ?>"><b>Logout</b></a>
  </p>

  <?php if ($ok): ?><div class="alert ok"><?= h($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert err"><?= h($err) ?></div><?php endif; ?>
</div>

<div class="stats-grid" style="margin-top:16px;">
  <div class="stat-card"><strong><?= h((string)$widget['total_reviews']) ?></strong><span>Total Testimonials</span></div>
  <div class="stat-card"><strong><?= h(number_format((float)$widget['average_rating'], 1)) ?></strong><span>Average Rating</span></div>
  <div class="stat-card"><strong><?= h((string)$widget['pending_reviews']) ?></strong><span>Pending Approval</span></div>
  <div class="stat-card"><strong><?= h((string)$widget['approved_reviews']) ?></strong><span>Approved Reviews</span></div>
</div>

<div class="card" style="margin-top:16px;">
  <form class="form-grid" method="get">
    <div class="field">
      <label for="search_q">Search</label>
      <input class="input" id="search_q" name="q" value="<?= h($q) ?>" placeholder="Client, company, city, service or testimonial text" />
    </div>
    <div class="field">
      <label for="filter_rating">Filter by Rating</label>
      <select class="input" id="filter_rating" name="rating">
        <option value="0">All Ratings</option>
        <?php for ($r = 5; $r >= 1; $r--): ?>
          <option value="<?= $r ?>" <?= $ratingFilter === $r ? 'selected' : '' ?>><?= $r ?> Star<?= $r > 1 ? 's' : '' ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="field">
      <label for="filter_status">Status</label>
      <select class="input" id="filter_status" name="status">
        <option value="">All Statuses</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>
    <div class="field">
      <label>&nbsp;</label>
      <button class="btn btn-primary" type="submit">Apply Filters</button>
    </div>
  </form>
</div>

<div class="card" style="margin-top:16px;">
  <table class="table">
    <tr>
      <th>Client</th>
      <th>Rating</th>
      <th>Status</th>
      <th>Publish</th>
      <th>Service</th>
      <th>Submitted</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($items as $item): ?>
      <tr>
        <td>
          <b><?= h($item['client_name']) ?></b><br/>
          <span class="note"><?= h($item['company_name']) ?>, <?= h($item['city']) ?></span>
        </td>
        <td><?= h(testimonial_issue_stars((int)$item['rating'])) ?></td>
        <td><?= h(ucfirst($item['status'])) ?></td>
        <td><?= (int)$item['publish_permission'] === 1 ? 'Yes' : 'No' ?></td>
        <td><?= h($item['service_availed']) ?></td>
        <td><?= h($item['created_at']) ?></td>
        <td>
          <div class="admin-action-stack">
            <a class="btn btn-outline btn-sm" href="<?= h(app_href('/testimonial/admin/edit.php')) ?>?id=<?= (int)$item['id'] ?>">Edit</a>
            <form method="post">
              <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>" />
              <input type="hidden" name="id" value="<?= (int)$item['id'] ?>" />
              <button class="btn btn-primary btn-sm" type="submit" name="action" value="approve">Approve</button>
            </form>
            <form method="post">
              <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>" />
              <input type="hidden" name="id" value="<?= (int)$item['id'] ?>" />
              <button class="btn btn-outline btn-sm" type="submit" name="action" value="reject">Reject</button>
            </form>
            <form method="post" onsubmit="return confirm('Delete this testimonial?');">
              <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>" />
              <input type="hidden" name="id" value="<?= (int)$item['id'] ?>" />
              <button class="btn btn-outline btn-sm" type="submit" name="action" value="delete">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="7"><span class="note"><?= nl2br(h($item['testimonial_text'])) ?></span></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php require __DIR__ . '/../../support/_layout_footer.php'; ?>
