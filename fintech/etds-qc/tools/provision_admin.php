<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
  fwrite(STDERR, "This utility can only be run from the command line.\n");
  exit(1);
}

require_once dirname(__DIR__) . '/bootstrap_runtime.php';

function provision_usage(): void {
  $usage = <<<TXT
Usage:
  php fintech/etds-qc/tools/provision_admin.php --email=admin@example.com [--current-email=legacy@example.com] [--name="System Administrator"] [--role=super_admin] [--password="<provided-at-runtime>"]

Notes:
  - If --password is omitted, the script prompts securely at runtime.
  - Use --current-email when replacing an existing admin record with a new email.
  - Passwords are never printed back to the terminal.

TXT;
  fwrite(STDOUT, $usage);
}

function provision_prompt(string $message): string {
  fwrite(STDOUT, $message);
  $line = fgets(STDIN);
  return trim((string) $line);
}

function provision_prompt_hidden(string $message): string {
  if (PHP_OS_FAMILY === 'Windows') {
    $ps = '$p = Read-Host "' . addslashes($message) . '" -AsSecureString;'
      . '$b = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($p);'
      . '[Runtime.InteropServices.Marshal]::PtrToStringAuto($b)';
    $command = 'powershell -NoProfile -NonInteractive -Command ' . escapeshellarg($ps);
    $value = shell_exec($command);
    if (is_string($value)) {
      fwrite(STDOUT, PHP_EOL);
      return trim($value);
    }
  }

  if (DIRECTORY_SEPARATOR !== '\\') {
    fwrite(STDOUT, $message);
    $sttyMode = shell_exec('stty -g');
    shell_exec('stty -echo');
    $line = fgets(STDIN);
    if (is_string($sttyMode) && trim($sttyMode) !== '') {
      shell_exec('stty ' . trim($sttyMode));
    } else {
      shell_exec('stty echo');
    }
    fwrite(STDOUT, PHP_EOL);
    return trim((string) $line);
  }

  return provision_prompt($message);
}

function provision_normalize_email(string $email): string {
  return strtolower(trim($email));
}

function provision_validate_email(string $email): bool {
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function provision_password_errors(string $password): array {
  $errors = [];
  if (strlen($password) < 12) {
    $errors[] = 'Password must be at least 12 characters.';
  }
  if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Password must include at least one uppercase letter.';
  }
  if (!preg_match('/[a-z]/', $password)) {
    $errors[] = 'Password must include at least one lowercase letter.';
  }
  if (!preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must include at least one number.';
  }
  if (!preg_match('/[^A-Za-z0-9]/', $password)) {
    $errors[] = 'Password must include at least one special character.';
  }
  return $errors;
}

function provision_next_user_id(array $users): string {
  $max = 0;
  foreach ($users as $user) {
    if (preg_match('/^USR-(\d{4})$/', (string) ($user['id'] ?? ''), $matches) === 1) {
      $max = max($max, (int) $matches[1]);
    }
  }
  return sprintf('USR-%04d', $max + 1);
}

function provision_find_user_index(array $users, string $email): ?int {
  foreach ($users as $index => $user) {
    if (provision_normalize_email((string) ($user['email'] ?? '')) === $email) {
      return $index;
    }
  }
  return null;
}

function provision_legacy_admin_detected(array $users): bool {
  foreach ($users as $user) {
    if (
      provision_normalize_email((string) ($user['email'] ?? '')) === 'admin@etaxadv.local'
      && (string) ($user['password_hash'] ?? '') === '$2y$12$Kp.UmGs91Th5LFsLsAuEgO6KlFxW8kt8xkK8HvPk5a676gCi1ZaSa'
    ) {
      return true;
    }
  }
  return false;
}

$options = getopt('', ['email:', 'current-email::', 'name::', 'role::', 'password::', 'help']);
if ($options === false || array_key_exists('help', $options)) {
  provision_usage();
  exit(0);
}

$email = provision_normalize_email((string) ($options['email'] ?? ''));
if ($email === '') {
  $email = provision_normalize_email(provision_prompt('Admin email: '));
}
if (!provision_validate_email($email)) {
  fwrite(STDERR, "A valid admin email is required.\n");
  exit(1);
}

$currentEmail = provision_normalize_email((string) ($options['current-email'] ?? $email));
$name = trim((string) ($options['name'] ?? 'System Administrator'));
$role = trim((string) ($options['role'] ?? 'super_admin'));
if ($name === '') {
  $name = 'System Administrator';
}
if ($role === '') {
  $role = 'super_admin';
}

$password = (string) ($options['password'] ?? '');
if ($password === '') {
  $password = provision_prompt_hidden('Admin password: ');
}
$confirmPassword = '';
if (!array_key_exists('password', $options)) {
  $confirmPassword = provision_prompt_hidden('Confirm password: ');
  if (!hash_equals($password, $confirmPassword)) {
    fwrite(STDERR, "Password confirmation does not match.\n");
    exit(1);
  }
}

$passwordErrors = provision_password_errors($password);
if ($passwordErrors !== []) {
  foreach ($passwordErrors as $error) {
    fwrite(STDERR, $error . PHP_EOL);
  }
  exit(1);
}

etds_qc_bootstrap();
$users = etds_qc_users();
$legacyDetected = provision_legacy_admin_detected($users);
$targetIndex = provision_find_user_index($users, $currentEmail);
$duplicateIndex = provision_find_user_index($users, $email);

if ($targetIndex !== null && $duplicateIndex !== null && $duplicateIndex !== $targetIndex) {
  fwrite(STDERR, "Another user already exists with the target email address.\n");
  exit(1);
}

if ($targetIndex === null && $duplicateIndex !== null) {
  $targetIndex = $duplicateIndex;
}

if ($targetIndex === null) {
  $users[] = [
    'id' => provision_next_user_id($users),
    'name' => $name,
    'email' => $email,
    'role' => $role,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'status' => 'active',
    'created_on' => etds_qc_now(),
    'updated_on' => etds_qc_now(),
  ];
  $action = 'created';
} else {
  $existing = $users[$targetIndex];
  $users[$targetIndex] = [
    'id' => (string) ($existing['id'] ?? provision_next_user_id($users)),
    'name' => $name,
    'email' => $email,
    'role' => $role,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'status' => 'active',
    'created_on' => (string) ($existing['created_on'] ?? etds_qc_now()),
    'updated_on' => etds_qc_now(),
  ];
  $action = 'updated';
}

etds_qc_write_json(ETDS_QC_USERS_FILE, array_values($users));
if (etds_qc_has_active_users()) {
  etds_qc_clear_provisioning_required();
}

fwrite(STDOUT, 'Admin user ' . $action . ' successfully for ' . $email . '.' . PHP_EOL);
if ($legacyDetected) {
  fwrite(STDOUT, "Legacy seeded admin record was detected in existing users.\n");
}
