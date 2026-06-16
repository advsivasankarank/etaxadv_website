<?php
require_once __DIR__ . '/../support/config.php';
require_once __DIR__ . '/security.php';

function contact_ensure_schema(): void {
  static $ready = false;
  if ($ready) return;
  try {
    db()->exec("
      CREATE TABLE IF NOT EXISTS enquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        enquiry_date DATETIME NOT NULL,
        name VARCHAR(120) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        email VARCHAR(190) NOT NULL,
        organisation VARCHAR(190) DEFAULT NULL,
        service VARCHAR(190) NOT NULL,
        consultation_mode VARCHAR(50) DEFAULT NULL,
        preferred_date VARCHAR(20) DEFAULT NULL,
        preferred_time VARCHAR(190) DEFAULT NULL,
        message TEXT NOT NULL,
        source_page VARCHAR(190) NOT NULL DEFAULT '',
        ip_address VARCHAR(45) NOT NULL DEFAULT '',
        status ENUM('new','contacted','converted','closed') NOT NULL DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    $ready = true;
  } catch (Throwable $e) {
    error_log('Contact schema error: ' . $e->getMessage());
  }
}

function contact_register_form(): void {
  if (session_status() === PHP_SESSION_NONE) {
    session_name('ETAX_SESSION');
    session_start();
  }
  $_SESSION['_form_time'] = time();
}

function contact_submission_too_fast(int $minSeconds = 3): bool {
  if (session_status() === PHP_SESSION_NONE) {
    session_name('ETAX_SESSION');
    session_start();
  }
  $started = (int)($_SESSION['_form_time'] ?? 0);
  return $started > 0 && (time() - $started) < $minSeconds;
}

function contact_process_submission(): array {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return ['success' => false, 'error' => 'Invalid request method.'];
  }

  if (!verify_csrf($_POST['_csrf'] ?? null)) {
    return ['success' => false, 'error' => 'Security validation failed. Please reload the page and try again.'];
  }

  if (contact_submission_too_fast()) {
    return ['success' => false, 'error' => 'Please wait a moment before submitting again.'];
  }

  if (!rate_limit_check('contact_form', 10)) {
    return ['success' => false, 'error' => 'Too many submissions. Please try again later.'];
  }

  $name = clean_input($_POST['name'] ?? '', 120);
  $mobile = clean_input($_POST['mobile'] ?? '', 20);
  $email = clean_input($_POST['email'] ?? '', 190);
  $organisation = clean_input($_POST['organisation'] ?? '', 190);
  $service = clean_input($_POST['service'] ?? '', 190);
  $consultation_mode = clean_input($_POST['consultation_mode'] ?? '', 50);
  $preferred_date = clean_input($_POST['preferred_date'] ?? '', 20);
  $preferred_time = clean_input($_POST['preferred_time'] ?? '', 190);
  $msg = clean_multiline($_POST['message'] ?? '', 5000);

  $errors = [];
  if ($name === '') $errors[] = 'Name is required.';
  if ($mobile === '' || !validate_mobile($mobile)) $errors[] = 'Valid mobile number is required.';
  if ($email === '' || !validate_email($email)) $errors[] = 'Valid email address is required.';
  if ($service === '') $errors[] = 'Service field is required.';
  if ($consultation_mode === '') $errors[] = 'Consultation mode is required.';
  if ($msg === '') $errors[] = 'Message is required.';

  if (!empty($errors)) {
    return ['success' => false, 'error' => implode(' ', $errors)];
  }

  $source = clean_input($_POST['source_page'] ?? $_SERVER['HTTP_REFERER'] ?? '', 190);
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';

  contact_ensure_schema();

  try {
    $stmt = db()->prepare("
      INSERT INTO enquiries (enquiry_date, name, mobile, email, organisation, service, consultation_mode, preferred_date, preferred_time, message, source_page, ip_address, status)
      VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')
    ");
    $stmt->execute([
      $name, $mobile, $email,
      $organisation !== '' ? $organisation : null,
      $service,
      $consultation_mode !== '' ? $consultation_mode : null,
      $preferred_date !== '' ? $preferred_date : null,
      $preferred_time !== '' ? $preferred_time : null,
      $msg, $source, $ip
    ]);

    $subject = "New Enquiry from etaxadv.com - " . $service;
    $body = "New Enquiry Received\n\n"
      . "Name: {$name}\n"
      . "Mobile: {$mobile}\n"
      . "Email: {$email}\n"
      . "Organisation: " . ($organisation ?: '-') . "\n"
      . "Service: {$service}\n"
      . "Consultation Mode: {$consultation_mode}\n"
      . "Preferred Date: " . ($preferred_date ?: '-') . "\n"
      . "Preferred Time: " . ($preferred_time ?: '-') . "\n"
      . "Message:\n{$msg}\n\n"
      . "Source: {$source}\n"
      . "IP: {$ip}\n";
    send_mail_safe(OFFICE_EMAIL, $subject, $body);

    return ['success' => true, 'message' => 'Thank you! Your consultation request has been received. We will review and contact you shortly.'];
  } catch (Throwable $e) {
    error_log('Contact insert error: ' . $e->getMessage());
    return ['success' => false, 'error' => 'Something went wrong. Please try again or call us directly.'];
  }
}

function contact_render_success(string $message): string {
  return '<div class="alert ok" role="alert">'
    . htmlspecialchars($message)
    . '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" class="btn btn-sm btn-outline" style="margin-top:10px;display:inline-block;">Submit another enquiry</a>'
    . '</div>';
}

function contact_render_error(string $error): string {
  return '<div class="alert err" role="alert">' . htmlspecialchars($error) . '</div>';
}
