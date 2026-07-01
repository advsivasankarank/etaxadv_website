<?php
require_once __DIR__ . '/../support/config.php';

session_name('ENQUIRIES_ADMIN');
session_start();

if (!empty($_SESSION['enq_auth'])) {
  header('Location: enquiries.php');
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pwd = $_POST['password'] ?? '';
  if (password_verify($pwd, ADMIN_PASSWORD_HASH)) {
    session_regenerate_id(true);
    $_SESSION['enq_auth'] = true;
    $_SESSION['enq_role'] = 'admin';
    $_SESSION['enq_time'] = time();
    header('Location: enquiries.php');
    exit;
  }
  if (password_verify($pwd, BO_PASSWORD_HASH)) {
    session_regenerate_id(true);
    $_SESSION['enq_auth'] = true;
    $_SESSION['enq_role'] = 'bo';
    $_SESSION['enq_time'] = time();
    header('Location: enquiries.php');
    exit;
  }
  $error = 'Invalid password.';
}

$page_title = 'Admin Login | E Tax Advisors';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(app_href('/assets/css/style.css')) ?>">
  <style>
    html, body { height: 100%; }
    body { display:flex;align-items:center;justify-content:center;background:var(--gray-50);margin:0;padding:20px; }
    .login-card { width:100%;max-width:400px;background:var(--white);border-radius:var(--radius-md);padding:48px 40px 40px;box-shadow:var(--shadow-md);text-align:center; }
    .login-card img { width:56px;height:56px;border-radius:12px;margin:0 auto 16px; }
    .login-card h1 { margin:0 0 4px;font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--navy); }
    .login-card p { margin:0 0 28px;color:var(--gray-600);font-size:14px; }
    .login-card .field { text-align:left;margin-bottom:20px; }
    .login-card .btn { width:100%; }
    .login-card .alert { margin-bottom:20px; }
    .login-back { margin-top:20px;font-size:13px; }
    .login-back a { color:var(--gray-400); }
    .login-back a:hover { color:var(--navy); }
  </style>
</head>
<body>
  <div class="login-card">
    <img src="<?= htmlspecialchars(app_href('/assets/img/logo.png')) ?>" alt="E Tax Advisors logo">
    <h1>Admin Login</h1>
    <p>Back office enquiries dashboard</p>
    <?php if ($error): ?>
      <div class="alert err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="field">
        <label for="pwd">Password</label>
        <input class="input" id="pwd" name="password" type="password" required autofocus>
      </div>
      <button class="btn btn-primary btn-lg" type="submit">Login</button>
    </form>
    <div class="login-back"><a href="<?= htmlspecialchars(app_href('/index.php')) ?>">&larr; Back to website</a></div>
  </div>
</body>
</html>
