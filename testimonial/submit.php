<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . app_href('/testimonial/'));
  exit;
}

if (!testimonial_verify_csrf($_POST['csrf_token'] ?? null)) {
  header('Location: ' . app_href('/testimonial/') . '?error=' . urlencode('Security validation failed. Please try again.'));
  exit;
}

if (testimonial_submission_too_fast()) {
  header('Location: ' . app_href('/testimonial/') . '?error=' . urlencode('Submission blocked by spam protection. Please try again after a moment.'));
  exit;
}

if (trim((string)($_POST['website'] ?? '')) !== '') {
  header('Location: ' . app_href('/testimonial/') . '?submitted=1');
  exit;
}

$name = testimonial_clean_text($_POST['name'] ?? '', 120);
$company = testimonial_clean_text($_POST['company_name'] ?? '', 160);
$city = testimonial_clean_text($_POST['city'] ?? '', 120);
$mobile = testimonial_clean_text($_POST['mobile'] ?? '', 20);
$email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL) ?: '';
$service = testimonial_clean_text($_POST['service_availed'] ?? '', 160);
$rating = (int)($_POST['rating'] ?? 0);
$publishPermission = ($_POST['publish_permission'] ?? '') === '1' ? 1 : 0;
$testimonial = testimonial_clean_multiline($_POST['testimonial'] ?? '', 2500);

if (
  $name === '' || $company === '' || $city === '' || $mobile === '' || $email === '' ||
  $service === '' || $testimonial === '' || $rating < 1 || $rating > 5
) {
  header('Location: ' . app_href('/testimonial/') . '?error=' . urlencode('Please complete all required fields correctly.'));
  exit;
}

$now = date('Y-m-d H:i:s');

$stmt = db()->prepare("
  INSERT INTO testimonials
  (created_at, approved_at, client_name, company_name, city, mobile, email, service_availed, rating, testimonial_text, publish_permission, status, admin_notes, is_spam, updated_at)
  VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NULL, 0, ?)
");

$stmt->execute([
  $now,
  $name,
  $company,
  $city,
  $mobile,
  $email,
  $service,
  $rating,
  $testimonial,
  $publishPermission,
  $now,
]);

$body = "New testimonial submission awaiting approval\n\n" .
        "Client: {$name}\n" .
        "Company: {$company}\n" .
        "City: {$city}\n" .
        "Mobile: {$mobile}\n" .
        "Email: {$email}\n" .
        "Service: {$service}\n" .
        "Rating: {$rating}/5\n" .
        "Publish Permission: " . ($publishPermission ? 'Yes' : 'No') . "\n\n" .
        "Testimonial:\n{$testimonial}\n";

send_mail_safe(OFFICE_EMAIL, 'New Testimonial Submission', $body);

header('Location: ' . app_href('/testimonial/') . '?submitted=1');
exit;
