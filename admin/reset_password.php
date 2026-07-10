<?php
require_once __DIR__ . '/_auth.php';

enq_auth_session_start();

$token = trim((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$user = enq_auth_find_user_by_token($token);
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if ($password !== $confirm) {
        $message = 'Passwords do not match.';
        $messageType = 'err';
    } else {
        $error = enq_auth_complete_password_setup($token, $password);
        if ($error === null) {
            $message = 'Password set successfully. You can now sign in.';
            $messageType = 'ok';
            $user = null;
        } else {
            $message = $error;
            $messageType = 'err';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Set Password | E Tax Advisors</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(app_href('/assets/css/style.css')) ?>">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:var(--gray-50);margin:0;padding:20px;">
  <div class="login-card" style="width:100%;max-width:440px;background:var(--white);border-radius:var(--radius-md);padding:48px 40px 40px;box-shadow:var(--shadow-md);text-align:center;">
    <img src="<?= htmlspecialchars(app_href('/assets/img/logo.png')) ?>" alt="E Tax Advisors logo" style="width:56px;height:56px;border-radius:12px;margin:0 auto 16px;">
    <h1 style="margin:0 0 6px;font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--navy);">Set Your Password</h1>
    <p style="margin:0 0 24px;color:var(--gray-600);font-size:14px;">Create a secure password for the enquiries follow-up dashboard.</p>
    <?php if ($message): ?>
      <div class="alert <?= $messageType === 'ok' ? 'ok' : 'err' ?>" style="margin-bottom:20px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($user): ?>
      <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="field" style="text-align:left;margin-bottom:16px;">
          <label for="password">New Password</label>
          <input class="input" id="password" name="password" type="password" required minlength="10" autofocus>
        </div>
        <div class="field" style="text-align:left;margin-bottom:16px;">
          <label for="confirm_password">Confirm Password</label>
          <input class="input" id="confirm_password" name="confirm_password" type="password" required minlength="10">
        </div>
        <p style="margin:0 0 16px;font-size:11px;color:var(--gray-400);">Minimum 10 characters, with uppercase, lowercase, number and special character.</p>
        <button class="btn btn-primary btn-lg" style="width:100%;" type="submit">Save Password</button>
      </form>
    <?php else: ?>
      <a class="btn btn-outline btn-lg" href="forgot_password.php" style="width:100%;">Request New Link</a>
    <?php endif; ?>

    <div style="margin-top:20px;font-size:13px;">
      <a href="login.php" style="color:var(--gray-400);">&larr; Back to login</a>
    </div>
  </div>
</body>
</html>
