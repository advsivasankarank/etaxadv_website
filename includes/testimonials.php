<?php
require_once __DIR__ . '/../support/config.php';

function testimonial_db_available(): bool {
  static $available = null;

  if ($available !== null) {
    return $available;
  }

  try {
    db();
    $available = true;
  } catch (Throwable $e) {
    $available = false;
  }

  return $available;
}

function testimonial_ensure_schema(): void {
  static $ready = false;

  if ($ready || !testimonial_db_available()) {
    return;
  }

  try {
    db()->exec("
      CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        created_at DATETIME NOT NULL,
        approved_at DATETIME NULL,
        client_name VARCHAR(120) NOT NULL,
        company_name VARCHAR(160) NOT NULL,
        city VARCHAR(120) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        email VARCHAR(190) NOT NULL,
        service_availed VARCHAR(160) NOT NULL,
        rating TINYINT NOT NULL,
        testimonial_text TEXT NOT NULL,
        publish_permission TINYINT(1) NOT NULL DEFAULT 0,
        status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        admin_notes TEXT NULL,
        is_spam TINYINT(1) NOT NULL DEFAULT 0,
        updated_at DATETIME NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $ready = true;
  } catch (Throwable $e) {
    $ready = false;
  }
}

testimonial_ensure_schema();

function testimonial_issue_stars(int $rating): string {
  $rating = max(1, min(5, $rating));
  return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}

function testimonial_csrf_token(): string {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(SESSION_NAME);
    session_start();
  }

  if (empty($_SESSION['testimonial_csrf'])) {
    $_SESSION['testimonial_csrf'] = bin2hex(random_bytes(24));
  }

  return $_SESSION['testimonial_csrf'];
}

function testimonial_verify_csrf(?string $token): bool {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(SESSION_NAME);
    session_start();
  }

  return is_string($token)
    && !empty($_SESSION['testimonial_csrf'])
    && hash_equals($_SESSION['testimonial_csrf'], $token);
}

function testimonial_register_form_render(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(SESSION_NAME);
    session_start();
  }

  $_SESSION['testimonial_form_time'] = time();
}

function testimonial_submission_too_fast(int $minimumSeconds = 3): bool {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(SESSION_NAME);
    session_start();
  }

  $started = (int)($_SESSION['testimonial_form_time'] ?? 0);
  return $started > 0 && (time() - $started) < $minimumSeconds;
}

function testimonial_clean_text(?string $value, int $maxLength, bool $allowNewLines = false): string {
  $value = trim((string)$value);
  $value = preg_replace('/\s+/u', $allowNewLines ? ' ' : ' ', $value) ?? '';
  $value = strip_tags($value);
  if (function_exists('mb_substr')) {
    return mb_substr($value, 0, $maxLength);
  }
  return substr($value, 0, $maxLength);
}

function testimonial_clean_multiline(?string $value, int $maxLength): string {
  $value = trim((string)$value);
  $value = str_replace(["\r\n", "\r"], "\n", $value);
  $value = preg_replace("/[ \t]+/", ' ', $value) ?? '';
  $value = preg_replace("/\n{3,}/", "\n\n", $value) ?? '';
  $value = strip_tags($value);
  if (function_exists('mb_substr')) {
    return mb_substr($value, 0, $maxLength);
  }
  return substr($value, 0, $maxLength);
}

function testimonial_get_summary(): array {
  if (!testimonial_db_available()) {
    return ['total_reviews' => 0, 'average_rating' => 0.0];
  }

  try {
    $stmt = db()->query("
      SELECT
        COUNT(*) AS total_reviews,
        COALESCE(AVG(rating), 0) AS average_rating
      FROM testimonials
      WHERE status = 'approved' AND publish_permission = 1
    ");
    $row = $stmt->fetch() ?: ['total_reviews' => 0, 'average_rating' => 0];
    return [
      'total_reviews' => (int)$row['total_reviews'],
      'average_rating' => round((float)$row['average_rating'], 1),
    ];
  } catch (Throwable $e) {
    return ['total_reviews' => 0, 'average_rating' => 0.0];
  }
}

function testimonial_get_featured(int $limit = 6): array {
  if (!testimonial_db_available()) {
    return [];
  }

  try {
    $stmt = db()->prepare("
      SELECT id, client_name, company_name, city, service_availed, rating, testimonial_text, created_at
      FROM testimonials
      WHERE status = 'approved' AND publish_permission = 1
      ORDER BY COALESCE(approved_at, created_at) DESC, created_at DESC
      LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  } catch (Throwable $e) {
    return [];
  }
}
