<?php
require_once __DIR__ . '/runtime_config.php';

function send_security_headers(): void {
  if (headers_sent()) return;
  $scriptSrc = ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net'];
  $styleSrc = ["'self'", "'unsafe-inline'", 'https://cdn.jsdelivr.net', 'https://fonts.googleapis.com'];
  $fontSrc = ["'self'", 'https://fonts.gstatic.com'];
  $imgSrc = ["'self'", 'data:', 'https://www.etaxadv.com'];
  $connectSrc = ["'self'"];

  if (eta_google_analytics_id() !== null) {
    $scriptSrc[] = 'https://www.googletagmanager.com';
    $connectSrc[] = 'https://www.google-analytics.com';
    $connectSrc[] = 'https://www.googletagmanager.com';
  }

  $csp = [
    "default-src 'self'",
    'script-src ' . implode(' ', array_unique($scriptSrc)),
    'style-src ' . implode(' ', array_unique($styleSrc)),
    'font-src ' . implode(' ', array_unique($fontSrc)),
    'img-src ' . implode(' ', array_unique($imgSrc)),
    'connect-src ' . implode(' ', array_unique($connectSrc)),
  ];
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: SAMEORIGIN');
  header('X-XSS-Protection: 1; mode=block');
  header('Referrer-Policy: strict-origin-when-cross-origin');
  header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
  header('Content-Security-Policy: ' . implode('; ', $csp));
  if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    return;
  }
  header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

function csrf_token(): string {
  if (session_status() === PHP_SESSION_NONE) {
    session_name('ETAX_SESSION');
    session_start();
  }
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['_csrf'];
}

function csrf_field(): string {
  return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

function verify_csrf(?string $token): bool {
  if (session_status() === PHP_SESSION_NONE) {
    session_name('ETAX_SESSION');
    session_start();
  }
  return is_string($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function clean_input(string $value, int $maxLength = 1000): string {
  $value = trim($value);
  $value = strip_tags($value);
  $value = preg_replace('/\s+/u', ' ', $value) ?? '';
  return mb_substr($value, 0, $maxLength);
}

function clean_multiline(string $value, int $maxLength = 5000): string {
  $value = trim($value);
  $value = strip_tags($value);
  $value = str_replace(["\r\n", "\r"], "\n", $value);
  $value = preg_replace("/[ \t]+/", ' ', $value) ?? '';
  $value = preg_replace("/\n{3,}/", "\n\n", $value) ?? '';
  return mb_substr($value, 0, $maxLength);
}

function validate_mobile(string $mobile): bool {
  return preg_match('/^[0-9+\-\s()]{7,20}$/', $mobile) === 1;
}

function validate_email(string $email): bool {
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function rate_limit_check(string $action, int $maxPerHour = 5): bool {
  $fileResult = file_rate_limit_check($action, $maxPerHour);
  if ($fileResult !== null) {
    return $fileResult;
  }
  if (session_status() === PHP_SESSION_NONE) {
    session_name('ETAX_SESSION');
    session_start();
  }
  $key = '_rl_' . $action;
  $now = time();
  $window = 3600;
  $attempts = $_SESSION[$key] ?? [];
  $attempts = array_filter($attempts, fn($t) => $t > ($now - $window));
  if (count($attempts) >= $maxPerHour) {
    return false;
  }
  $attempts[] = $now;
  $_SESSION[$key] = $attempts;
  return true;
}

function security_storage_root(): string {
  return dirname(__DIR__) . '/storage/security';
}

function security_rate_limit_root(): string {
  return security_storage_root() . '/rate_limits';
}

function security_rate_limit_subject(): string {
  foreach (['email', 'username', 'user', 'login', 'mobile'] as $field) {
    $value = $_POST[$field] ?? $_REQUEST[$field] ?? null;
    if (!is_string($value)) {
      continue;
    }
    $value = strtolower(trim($value));
    if ($value !== '') {
      return $value;
    }
  }
  return '';
}

function security_request_ip(): string {
  foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'] as $header) {
    $raw = $_SERVER[$header] ?? '';
    if (!is_string($raw) || trim($raw) === '') {
      continue;
    }
    $candidate = trim(explode(',', $raw)[0]);
    if (filter_var($candidate, FILTER_VALIDATE_IP)) {
      return $candidate;
    }
  }
  return '0.0.0.0';
}

function security_rate_limit_file(string $action, string $ip, string $subjectHash): string {
  $bucket = hash('sha256', strtolower($action) . '|' . $ip . '|' . $subjectHash);
  return security_rate_limit_root() . '/' . $bucket . '.json';
}

function security_rate_limit_cleanup(string $root, int $window, int $now): void {
  foreach (glob($root . '/*.json') ?: [] as $file) {
    if (!is_file($file)) {
      continue;
    }
    $mtime = @filemtime($file);
    if ($mtime !== false && $mtime < ($now - ($window * 2))) {
      @unlink($file);
    }
  }
}

function file_rate_limit_check(string $action, int $maxPerHour = 5): ?bool {
  $root = security_rate_limit_root();
  if (!is_dir($root) && !@mkdir($root, 0775, true) && !is_dir($root)) {
    return null;
  }

  $ip = security_request_ip();
  $subject = security_rate_limit_subject();
  $subjectHash = $subject === '' ? 'anonymous' : hash('sha256', $subject);
  $file = security_rate_limit_file($action, $ip, $subjectHash);
  $window = 3600;
  $now = time();

  $handle = @fopen($file, 'c+');
  if ($handle === false) {
    return null;
  }

  try {
    if (!flock($handle, LOCK_EX)) {
      fclose($handle);
      return null;
    }

    $contents = stream_get_contents($handle);
    $payload = json_decode(is_string($contents) ? $contents : '', true);
    $attempts = is_array($payload['attempts'] ?? null) ? $payload['attempts'] : [];
    $attempts = array_values(array_filter($attempts, static fn($timestamp): bool => is_int($timestamp) && $timestamp > ($now - $window)));

    if (count($attempts) >= $maxPerHour) {
      ftruncate($handle, 0);
      rewind($handle);
      fwrite($handle, json_encode(['attempts' => $attempts], JSON_UNESCAPED_UNICODE));
      fflush($handle);
      flock($handle, LOCK_UN);
      fclose($handle);
      return false;
    }

    $attempts[] = $now;
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode(['attempts' => $attempts], JSON_UNESCAPED_UNICODE));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
  } catch (Throwable) {
    @fclose($handle);
    return null;
  }

  if (random_int(1, 25) === 1) {
    security_rate_limit_cleanup($root, $window, $now);
  }

  return true;
}

function json_response(array $data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function redirect_safe(string $url): void {
  header('Location: ' . $url);
  exit;
}
