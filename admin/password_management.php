<?php
require_once __DIR__ . '/_auth.php';

$currentUser = enq_auth_require_admin();

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $submitted_token = $_POST['csrf_token'] ?? '';

  if (!hash_equals($csrf_token, $submitted_token)) {
    $message = 'Unauthorised access.';
    $message_type = 'err';
  } else {
    $action = $_POST['action'] ?? '';

    if ($action === 'change_admin') {
      $current_pwd  = $_POST['current_password'] ?? '';
      $new_pwd      = $_POST['new_password'] ?? '';
      $confirm_pwd  = $_POST['confirm_password'] ?? '';

      if ($new_pwd !== $confirm_pwd) {
        $message = 'Passwords do not match.';
        $message_type = 'err';
      } else {
        $err = enq_auth_change_password((string) $currentUser['id'], (string) $current_pwd, (string) $new_pwd);
        if ($err === null) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
          $message = 'Admin password updated successfully.';
          $message_type = 'ok';
        } else {
          $message = $err;
          $message_type = 'err';
        }
      }
    } elseif ($action === 'reset_bo') {
      $target_email = trim((string) ($_POST['reset_email'] ?? ''));
      if ($target_email === '') {
        $message = 'Select a user email.';
        $message_type = 'err';
      } else {
        enq_auth_send_reset_link($target_email);
        $message = 'If the user exists and is active, a password setup/reset email has been sent.';
        $message_type = 'ok';
      }
    } elseif ($action === 'create_user') {
      $new_name = trim((string) ($_POST['new_user_name'] ?? ''));
      $new_email = trim((string) ($_POST['new_user_email'] ?? ''));
      $new_role = trim((string) ($_POST['new_user_role'] ?? 'bo'));

      $err = enq_auth_create_user($new_name, $new_email, $new_role);
      if ($err === null) {
        $message = 'User created successfully. A set-password email has been sent.';
        $message_type = 'ok';
      } else {
        $message = $err;
        $message_type = 'err';
      }
    } else {
      $message = 'Invalid action.';
      $message_type = 'err';
    }
  }
}

$page_title = 'Password Management';
require_once __DIR__ . '/../includes/header.php';
?>

<main id="main-content">

<section class="section">
  <div class="container">

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
      <div>
        <h1 style="margin:0;font-family:var(--font-display);font-size:28px;font-weight:700;color:var(--navy);">Password Management</h1>
        <p style="margin:4px 0 0;color:var(--gray-600);font-size:14px;">Admin only</p>
      </div>
      <div style="display:flex;gap:8px;">
        <a class="btn btn-outline" href="enquiries.php">Back to Workflow</a>
        <a class="btn btn-outline" href="enquiries.php?logout=1">Logout</a>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="alert <?= $message_type === 'ok' ? 'ok' : 'err' ?>" style="margin-bottom:24px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

      <div class="card" style="padding:32px;">
        <h3 style="margin:0 0 6px;font-family:var(--font-display);font-size:18px;font-weight:700;color:var(--navy);">Change Admin Password</h3>
        <p style="margin:0 0 20px;color:var(--gray-600);font-size:13px;">Update your own admin login password for <?= htmlspecialchars((string) ($currentUser['email'] ?? '')) ?>.</p>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
          <input type="hidden" name="action" value="change_admin">
          <div class="field" style="margin-bottom:14px;">
            <label for="cp1">Current Admin Password</label>
            <input class="input" id="cp1" name="current_password" type="password" required autocomplete="current-password">
          </div>
          <div class="field" style="margin-bottom:14px;">
            <label for="np1">New Admin Password</label>
            <input class="input" id="np1" name="new_password" type="password" required autocomplete="new-password" minlength="10">
          </div>
          <div class="field" style="margin-bottom:16px;">
            <label for="cp2">Confirm New Admin Password</label>
            <input class="input" id="cp2" name="confirm_password" type="password" required autocomplete="new-password" minlength="10">
          </div>
          <p style="margin:0 0 14px;font-size:11px;color:var(--gray-400);">Min 10 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character.</p>
          <button class="btn btn-primary" type="submit">Update Admin Password</button>
        </form>
      </div>

      <div class="card" style="padding:32px;">
        <h3 style="margin:0 0 6px;font-family:var(--font-display);font-size:18px;font-weight:700;color:var(--navy);">Send Password Reset Link</h3>
        <p style="margin:0 0 20px;color:var(--gray-600);font-size:13px;">Send a secure password setup/reset email to an active enquiries user. Only Admin can perform this action.</p>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
          <input type="hidden" name="action" value="reset_bo">
          <div class="field" style="margin-bottom:14px;">
            <label for="reset_email">User Email</label>
            <select class="input" id="reset_email" name="reset_email" required>
              <option value="">Select a user</option>
<?php foreach (enq_auth_load_users() as $user): ?>
<?php if (($user['status'] ?? '') === 'active'): ?>
              <option value="<?= htmlspecialchars((string) ($user['email'] ?? '')) ?>"><?= htmlspecialchars((string) ($user['email'] ?? '')) ?> (<?= htmlspecialchars((string) ($user['role'] ?? 'user')) ?>)</option>
<?php endif; ?>
<?php endforeach; ?>
            </select>
          </div>
          <p style="margin:0 0 14px;font-size:11px;color:var(--gray-400);">The email is sent from support@etaxadv.com with a secure time-limited link.</p>
          <button class="btn btn-gold" type="submit">Send Reset Link</button>
        </form>
      </div>

    </div>

    <div class="card" style="padding:32px;margin-top:24px;">
      <h3 style="margin:0 0 6px;font-family:var(--font-display);font-size:18px;font-weight:700;color:var(--navy);">Create User</h3>
      <p style="margin:0 0 20px;color:var(--gray-600);font-size:13px;">Create a new enquiries follow-up user and send a first-time set-password link from support@etaxadv.com.</p>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="action" value="create_user">
        <div style="display:grid;grid-template-columns:1fr 1fr 180px auto;gap:14px;align-items:end;">
          <div class="field">
            <label for="new_user_name">Name</label>
            <input class="input" id="new_user_name" name="new_user_name" required>
          </div>
          <div class="field">
            <label for="new_user_email">Email</label>
            <input class="input" id="new_user_email" name="new_user_email" type="email" required>
          </div>
          <div class="field">
            <label for="new_user_role">Role</label>
            <select class="input" id="new_user_role" name="new_user_role">
              <option value="bo">Back Office</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button class="btn btn-primary" type="submit">Create User</button>
        </div>
      </form>
    </div>
  </div>
</section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
