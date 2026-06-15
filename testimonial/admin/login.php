<?php
require __DIR__ . '/../config.php';
session_name(SESSION_NAME);
session_start();

$page_title = "Testimonial Admin Login";
$err = '';
$csrf = testimonial_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!testimonial_verify_csrf($_POST['csrf_token'] ?? null)) {
    $err = "Security validation failed.";
  } else {
  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';

  $stmt = db()->prepare("SELECT * FROM admin_users WHERE username=? LIMIT 1");
  $stmt->execute([$u]);
  $user = $stmt->fetch();

  if ($user && password_verify($p, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int)$user['id'];
    $_SESSION['admin_username'] = $user['username'];
    header('Location: /testimonial/admin/dashboard.php');
    exit;
  }

  $err = "Invalid credentials.";
  }
}

require __DIR__ . '/../../support/_layout_header.php';
?>

<div class="card" style="max-width:520px;margin:0 auto;">
  <h3>Testimonial Admin Login</h3>
  <p class="note">Use the existing admin credentials to review and moderate testimonials.</p>
  <?php if ($err): ?><div class="alert err"><?= h($err) ?></div><?php endif; ?>

  <form class="form-grid" method="post" style="grid-template-columns:1fr;">
    <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>" />
    <div class="field">
      <label for="admin_user">Username</label>
      <input class="input" id="admin_user" name="username" required />
    </div>
    <div class="field">
      <label for="admin_pass">Password</label>
      <input class="input" id="admin_pass" name="password" type="password" required />
    </div>
    <div class="field">
      <button class="btn btn-primary" type="submit">Login</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../../support/_layout_footer.php'; ?>
