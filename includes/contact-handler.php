<?php
require_once __DIR__ . '/../support/config.php';
require_once __DIR__ . '/security.php';

define('ENQUIRIES_FILE', __DIR__ . '/../support/data/enquiries.json');

function contact_ensure_storage(): string {
  $dir = dirname(ENQUIRIES_FILE);
  if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
  }
  if (!file_exists(ENQUIRIES_FILE)) {
    @file_put_contents(ENQUIRIES_FILE, '[]', LOCK_EX);
  }
  return ENQUIRIES_FILE;
}

function contact_load_enquiries(): array {
  $file = contact_ensure_storage();
  $fh = @fopen($file, 'r');
  if (!$fh) return [];
  flock($fh, LOCK_SH);
  $contents = stream_get_contents($fh);
  fclose($fh);
  $data = json_decode($contents, true);
  return is_array($data) ? $data : [];
}

function contact_save_enquiries(array $enquiries): void {
  $file = contact_ensure_storage();
  $fh = @fopen($file, 'w');
  if (!$fh) return;
  flock($fh, LOCK_EX);
  fwrite($fh, json_encode($enquiries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  fclose($fh);
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

  try {
    $enquiries = contact_load_enquiries();
    $enquiries[] = [
      'id' => count($enquiries) + 1,
      'enquiry_date' => date('Y-m-d H:i:s'),
      'name' => $name,
      'mobile' => $mobile,
      'email' => $email,
      'organisation' => $organisation ?: null,
      'service' => $service,
      'consultation_mode' => $consultation_mode ?: null,
      'preferred_date' => $preferred_date ?: null,
      'preferred_time' => $preferred_time ?: null,
      'message' => $msg,
      'source_page' => $source,
      'ip_address' => $ip,
      'status' => 'new',
    ];
    contact_save_enquiries($enquiries);

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
    error_log('Contact save error: ' . $e->getMessage());
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
