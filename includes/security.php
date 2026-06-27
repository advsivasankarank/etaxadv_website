<?php
function send_security_headers(): void {
  if (headers_sent()) return;
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: SAMEORIGIN');
  header('X-XSS-Protection: 1; mode=block');
  header('Referrer-Policy: strict-origin-when-cross-origin');
  header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
  header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'");
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
