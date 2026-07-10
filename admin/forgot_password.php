<?php
require_once __DIR__ . '/_auth.php';

enq_auth_session_start();

if (!empty($_SESSION['enq_auth'])) {
    header('Location: enquiries.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    if ($email !== '') {
        enq_auth_send_reset_link($email);
    }
    $message = 'If the email exists in the system, a password setup or reset link has been sent.';
    $messageType = 'ok';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password | E Tax Advisors</title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(app_href('/assets/css/style.css')) ?>">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:var(--gray-50);margin:0;padding:20px;">
  <div class="login-card" style="width:100%;max-width:420px;background:var(--white);border-radius:var(--radius-md);padding:48px 40px 40px;box-shadow:var(--shadow-md);text-align:center;">
    <img src="<?= htmlspecialchars(app_href('/assets/img/logo.png')) ?>" alt="E Tax Advisors logo" style="width:56px;height:56px;border-radius:12px;margin:0 auto 16px;">
    <h1 style="margin:0 0 6px;font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--navy);">Set or Reset Password</h1>
    <p style="margin:0 0 24px;color:var(--gray-600);font-size:14px;">Enter your registered email address. We will send a secure link from support@etaxadv.com.</p>
    <?php if ($message): ?>
      <div class="alert <?= $messageType === 'ok' ? 'ok' : 'err' ?>" style="margin-bottom:20px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="field" style="text-align:left;margin-bottom:20px;">
        <label for="email">Email</label>
        <input class="input" id="email" name="email" type="email" required autofocus>
      </div>
      <button class="btn btn-primary btn-lg" style="width:100%;" type="submit">Send Link</button>
    </form>
    <div style="margin-top:20px;font-size:13px;">
      <a href="login.php" style="color:var(--gray-400);">&larr; Back to login</a>
    </div>
  </div>
</body>
</html>
