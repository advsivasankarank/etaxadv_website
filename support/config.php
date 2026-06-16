<?php
// ==============================
// E Tax Advisors - Support Config
// ==============================

// DB Settings
// Update these on the live server with your actual cPanel MySQL values.
// Example live values shared by you:
// DB_NAME => etaxadv_support
// DB_USER => etaxadv_supportuser
// DB_PASS => your live password
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// Site
define('OFFICE_EMAIL', 'support@etaxadv.com');
define('BO_PASSWORD_HASH', '$2y$12$.BJZOIRskpMif/WIi6uBh./dkQR95zoXjt3n4ZUse0FSoK8/eE3.2');
define('ADMIN_PASSWORD_HASH', '$2y$12$AaLmIOxfBo7KDsrz2f4qJOjr/GBkn.2xw3xanv7HGWLzV3vPp.UFm');
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

if (!function_exists('clean_input')) {
  function clean_input(string $value, int $maxlen = 0): string {
    $val = strip_tags(trim($value));
    return $maxlen > 0 ? mb_substr($val, 0, $maxlen, 'UTF-8') : $val;
  }
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
