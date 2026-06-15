<?php
require __DIR__ . '/_auth.php';
require_testimonial_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
  header('Location: /testimonial/admin/dashboard.php');
  exit;
}

$stmt = db()->prepare("SELECT * FROM testimonials WHERE id=? LIMIT 1");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
  header('Location: /testimonial/admin/dashboard.php');
  exit;
}

$ok = '';
$err = '';
$csrf = testimonial_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!testimonial_verify_csrf($_POST['csrf_token'] ?? null)) {
    $err = 'Security validation failed.';
  } else {
    $clientName = testimonial_clean_text($_POST['client_name'] ?? '', 120);
    $company = testimonial_clean_text($_POST['company_name'] ?? '', 160);
    $city = testimonial_clean_text($_POST['city'] ?? '', 120);
    $mobile = testimonial_clean_text($_POST['mobile'] ?? '', 20);
    $email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL) ?: '';
    $service = testimonial_clean_text($_POST['service_availed'] ?? '', 160);
    $rating = (int)($_POST['rating'] ?? 0);
    $publish = ($_POST['publish_permission'] ?? '') === '1' ? 1 : 0;
    $status = trim($_POST['status'] ?? 'pending');
    $text = testimonial_clean_multiline($_POST['testimonial_text'] ?? '', 2500);

    if (
      $clientName === '' || $company === '' || $city === '' || $mobile === '' || $email === '' ||
      $service === '' || $text === '' || $rating < 1 || $rating > 5 ||
      !in_array($status, ['pending', 'approved', 'rejected'], true)
    ) {
      $err = 'Please complete all fields correctly.';
    } else {
      $approvedAt = $status === 'approved' ? date('Y-m-d H:i:s') : null;
      $sql = "UPDATE testimonials SET client_name=?, company_name=?, city=?, mobile=?, email=?, service_availed=?, rating=?, testimonial_text=?, publish_permission=?, status=?, updated_at=NOW()";
      $params = [$clientName, $company, $city, $mobile, $email, $service, $rating, $text, $publish, $status];

      if ($approvedAt !== null && empty($item['approved_at'])) {
        $sql .= ", approved_at=?";
        $params[] = $approvedAt;
      }

      $sql .= " WHERE id=?";
      $params[] = $id;

      db()->prepare($sql)->execute($params);
      $ok = 'Testimonial updated.';

      $stmt->execute([$id]);
      $item = $stmt->fetch();
    }
  }
}

$page_title = "Edit Testimonial";
require __DIR__ . '/../../support/_layout_header.php';
?>

<div class="card">
  <h3>Edit Testimonial</h3>
  <p class="note"><a href="/testimonial/admin/dashboard.php"><b>Back to Dashboard</b></a></p>
  <?php if ($ok): ?><div class="alert ok"><?= h($ok) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert err"><?= h($err) ?></div><?php endif; ?>

  <form method="post">
    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>" />
    <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>" />
    <div class="form-grid">
      <div class="field">
        <label for="e_client_name">Name</label>
        <input class="input" id="e_client_name" name="client_name" value="<?= h($item['client_name']) ?>" required />
      </div>
      <div class="field">
        <label for="e_company_name">Company Name</label>
        <input class="input" id="e_company_name" name="company_name" value="<?= h($item['company_name']) ?>" required />
      </div>
      <div class="field">
        <label for="e_city">City</label>
        <input class="input" id="e_city" name="city" value="<?= h($item['city']) ?>" required />
      </div>
      <div class="field">
        <label for="e_mobile">Mobile</label>
        <input class="input" id="e_mobile" name="mobile" value="<?= h($item['mobile']) ?>" required />
      </div>
      <div class="field">
        <label for="e_email">Email</label>
        <input class="input" id="e_email" name="email" type="email" value="<?= h($item['email']) ?>" required />
      </div>
      <div class="field">
        <label for="e_service">Service Availed</label>
        <input class="input" id="e_service" name="service_availed" value="<?= h($item['service_availed']) ?>" required />
      </div>
      <div class="field">
        <label for="e_rating">Rating</label>
        <select class="input" id="e_rating" name="rating" required>
          <?php for ($r = 1; $r <= 5; $r++): ?>
            <option value="<?= $r ?>" <?= (int)$item['rating'] === $r ? 'selected' : '' ?>><?= $r ?> Star<?= $r > 1 ? 's' : '' ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="field">
        <label for="e_publish">Publish Permission</label>
        <select class="input" id="e_publish" name="publish_permission" required>
          <option value="1" <?= (int)$item['publish_permission'] === 1 ? 'selected' : '' ?>>Yes</option>
          <option value="0" <?= (int)$item['publish_permission'] === 0 ? 'selected' : '' ?>>No</option>
        </select>
      </div>
      <div class="field full-span">
        <label for="e_status">Status</label>
        <select class="input" id="e_status" name="status" required>
          <option value="pending" <?= $item['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="approved" <?= $item['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
          <option value="rejected" <?= $item['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
      </div>
      <div class="field full-span">
        <label for="e_testimonial_text">Testimonial</label>
        <textarea class="input" id="e_testimonial_text" name="testimonial_text" required><?= h($item['testimonial_text']) ?></textarea>
      </div>
      <div class="field full-span">
        <button class="btn btn-primary" type="submit">Save Changes</button>
      </div>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../../support/_layout_footer.php'; ?>
