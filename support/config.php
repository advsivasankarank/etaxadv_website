<?php
// ==============================
// E Tax Advisors - Support Config
// ==============================

require_once dirname(__DIR__) . '/includes/runtime_config.php';

// DB Settings
// Update these on the live server with your actual cPanel MySQL values.
// Example live values shared by you:
// DB_NAME => etaxadv_support
// DB_USER => etaxadv_supportuser
// DB_PASS => your live password
$etaSupportDbConfig = eta_support_db_config();
defined('DB_HOST') || define('DB_HOST', $etaSupportDbConfig['host']);
defined('DB_NAME') || define('DB_NAME', $etaSupportDbConfig['name']);
defined('DB_USER') || define('DB_USER', $etaSupportDbConfig['user']);
defined('DB_PASS') || define('DB_PASS', $etaSupportDbConfig['pass']);

// Site
define('OFFICE_EMAIL', 'support@etaxadv.com');
define('BO_PASSWORD_HASH', '$2y$12$jUBtFMhu/WdedQsmwJLXbu/M2Zj8Fxt7ZoPSy5a.4sH6NmZ/gcSce');
define('ADMIN_PASSWORD_HASH', '$2y$12$ivdWha1Webf8ptxvOLbHtuEZo2mNg/ni3qN/hl2rub0iWYAgMVNSa');
define('FROM_EMAIL', 'support@etaxadv.com');
define('SITE_NAME', 'E Tax Advisors Private Limited');
define('SESSION_NAME', 'ETAX_SUPPORT');

function app_root_path(): string {
  static $root = null;

  if ($root !== null) {
    return $root;
  }

  $project_root = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__));
  $document_root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ($_SERVER['DOCUMENT_ROOT'] ?? ''));

  if ($document_root !== '' && str_starts_with($project_root, $document_root)) {
    $computed_root = substr($project_root, strlen($document_root));
    $computed_root = str_replace('\\', '/', $computed_root);
    $root = $computed_root === '' ? '' : rtrim($computed_root, '/');
  } else {
    $root = '';
  }

  return $root;
}

function app_href(string $path): string {
  return app_root_path() . $path;
}

// PDO
function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;
  if (!eta_support_db_is_configured()) {
    if (eta_is_production()) {
      throw new RuntimeException(eta_support_db_error_message());
    }
    throw new RuntimeException('Support database is not configured for this environment.');
  }
  $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
  $pdo = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
  return $pdo;
}

function h(?string $s): string {
  return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}


function make_ticket_id(): string {
  return "ETA-".date('Ymd')."-".strtoupper(substr(bin2hex(random_bytes(3)),0,6));
}

function send_mail_safe(string $to, string $subject, string $body): void {
  // Basic mail() for cPanel shared hosting
  $headers = "From: ".FROM_EMAIL."\r\n".
             "Reply-To: ".OFFICE_EMAIL."\r\n".
             "X-Mailer: PHP/" . phpversion();
  @mail($to, $subject, $body, $headers);
}
