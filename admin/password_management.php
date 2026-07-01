<?php
require_once __DIR__ . '/../support/config.php';

session_name('ENQUIRIES_ADMIN');
session_start();

if (empty($_SESSION['enq_auth']) || ($_SESSION['enq_role'] ?? '') !== 'admin') {
  header('Location: login.php');
  exit;
}

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];
$message = '';
$message_type = '';

function validate_password_strength(string $p): ?string {
  if (strlen($p) < 10) return 'Password must be at least 10 characters.';
  if (!preg_match('/[A-Z]/', $p)) return 'Password must contain at least one uppercase letter.';
  if (!preg_match('/[a-z]/', $p)) return 'Password must contain at least one lowercase letter.';
  if (!preg_match('/[0-9]/', $p)) return 'Password must contain at least one number.';
  if (!preg_match('/[^A-Za-z0-9]/', $p)) return 'Password must contain at least one special character.';
  return null;
}

function update_config_hash(string $constant_name, string $new_hash): bool {
  $config_path = __DIR__ . '/../support/config.php';
  if (!is_writable($config_path)) return false;

  $fp = fopen($config_path, 'c+');
  if (!$fp || !flock($fp, LOCK_EX)) {
    if ($fp) fclose($fp);
    return false;
  }

  $content = stream_get_contents($fp);
  if ($content === false) {
    flock($fp, LOCK_UN);
    fclose($fp);
    return false;
  }

  $quoted = preg_quote($constant_name, '/');
  $pattern = "/(define\s*\(\s*'{$quoted}'\s*,\s*')[^']*(')/";
  $replacement = "\${1}{$new_hash}\${2}";

  $new_content = preg_replace($pattern, $replacement, $content, 1);

  if ($new_content === null || $new_content === $content) {
    flock($fp, LOCK_UN);
    fclose($fp);
    return false;
  }

  ftruncate($fp, 0);
  rewind($fp);
  fwrite($fp, $new_content);
  fflush($fp);
  flock($fp, LOCK_UN);
  fclose($fp);

  if (function_exists('opcache_invalidate')) {
    opcache_invalidate($config_path, true);
  }

  return true;
}

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

      if (!password_verify($current_pwd, ADMIN_PASSWORD_HASH)) {
        $message = 'Invalid current password.';
        $message_type = 'err';
      } elseif ($new_pwd !== $confirm_pwd) {
        $message = 'Passwords do not match.';
        $message_type = 'err';
      } elseif ($err = validate_password_strength($new_pwd)) {
        $message = $err;
        $message_type = 'err';
      } else {
        $new_hash = password_hash($new_pwd, PASSWORD_DEFAULT);
        if (update_config_hash('ADMIN_PASSWORD_HASH', $new_hash)) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
          $message = 'Admin password updated successfully.';
          $message_type = 'ok';
        } else {
          $message = 'Failed to update configuration. Check file permissions.';
          $message_type = 'err';
        }
      }
    } elseif ($action === 'reset_bo') {
      $current_admin_pwd = $_POST['current_admin_password'] ?? '';
      $new_bo_pwd        = $_POST['new_bo_password'] ?? '';
      $confirm_bo_pwd    = $_POST['confirm_bo_password'] ?? '';

      if (!password_verify($current_admin_pwd, ADMIN_PASSWORD_HASH)) {
        $message = 'Invalid current Admin password.';
        $message_type = 'err';
      } elseif ($new_bo_pwd !== $confirm_bo_pwd) {
        $message = 'Passwords do not match.';
        $message_type = 'err';
      } elseif ($err = validate_password_strength($new_bo_pwd)) {
        $message = $err;
        $message_type = 'err';
      } else {
        $new_hash = password_hash($new_bo_pwd, PASSWORD_DEFAULT);
        if (update_config_hash('BO_PASSWORD_HASH', $new_hash)) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
          $message = 'BO password reset successfully.';
          $message_type = 'ok';
        } else {
          $message = 'Failed to update configuration. Check file permissions.';
          $message_type = 'err';
        }
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
        <p style="margin:0 0 20px;color:var(--gray-600);font-size:13px;">Update your own admin login password.</p>
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
        <h3 style="margin:0 0 6px;font-family:var(--font-display);font-size:18px;font-weight:700;color:var(--navy);">Reset BO Password</h3>
        <p style="margin:0 0 20px;color:var(--gray-600);font-size:13px;">Reset the Back Office login password. Only Admin can perform this action.</p>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
          <input type="hidden" name="action" value="reset_bo">
          <div class="field" style="margin-bottom:14px;">
            <label for="acp1">Your Admin Password</label>
            <input class="input" id="acp1" name="current_admin_password" type="password" required autocomplete="current-password">
          </div>
          <div class="field" style="margin-bottom:14px;">
            <label for="bop1">New BO Password</label>
            <input class="input" id="bop1" name="new_bo_password" type="password" required autocomplete="new-password" minlength="10">
          </div>
          <div class="field" style="margin-bottom:16px;">
            <label for="bop2">Confirm New BO Password</label>
            <input class="input" id="bop2" name="confirm_bo_password" type="password" required autocomplete="new-password" minlength="10">
          </div>
          <p style="margin:0 0 14px;font-size:11px;color:var(--gray-400);">Min 10 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character.</p>
          <button class="btn btn-gold" type="submit">Reset BO Password</button>
        </form>
      </div>

    </div>
  </div>
</section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
