<?php
declare(strict_types=1);

const ETDS_QC_SESSION_NAME = 'ETDS_QC_SESSION';
const ETDS_QC_STORAGE_ROOT = __DIR__ . '/../../storage/etds-qc';
const ETDS_QC_CASES_ROOT = ETDS_QC_STORAGE_ROOT . '/cases';
const ETDS_QC_DOCUMENTS_ROOT = ETDS_QC_STORAGE_ROOT . '/documents';
const ETDS_QC_UPLOADS_ROOT = ETDS_QC_STORAGE_ROOT . '/uploads';
const ETDS_QC_MASTERS_ROOT = ETDS_QC_STORAGE_ROOT . '/masters';
const ETDS_QC_RULES_ROOT = ETDS_QC_STORAGE_ROOT . '/rules';
const ETDS_QC_USERS_ROOT = ETDS_QC_STORAGE_ROOT . '/users';
const ETDS_QC_LOGS_ROOT = ETDS_QC_STORAGE_ROOT . '/logs';
const ETDS_QC_AUDIT_ROOT = ETDS_QC_STORAGE_ROOT . '/audit';
const ETDS_QC_SETTINGS_ROOT = ETDS_QC_STORAGE_ROOT . '/settings';
const ETDS_QC_LEGACY_SESSIONS_ROOT = ETDS_QC_STORAGE_ROOT . '/sessions';
const ETDS_QC_SESSIONS_ROOT = ETDS_QC_CASES_ROOT;
const ETDS_QC_USERS_FILE = ETDS_QC_USERS_ROOT . '/users.json';
const ETDS_QC_CONFIG_FILE = ETDS_QC_SETTINGS_ROOT . '/config.json';
const ETDS_QC_COUNTER_FILE = ETDS_QC_SETTINGS_ROOT . '/counters.json';
const ETDS_QC_MAX_UPLOAD_BYTES = 15 * 1024 * 1024;

if (session_status() === PHP_SESSION_NONE) {
  session_name(ETDS_QC_SESSION_NAME);
  $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => $isSecure,
    'samesite' => 'Lax',
  ]);
  ini_set('session.use_strict_mode', '1');
  session_start();
}

if (ini_get('error_log') === '' || ini_get('error_log') === false) {
  $errorLogFile = ETDS_QC_STORAGE_ROOT . '/php-error.log';
  @ini_set('error_log', $errorLogFile);
  @ini_set('log_errors', '1');
}

if ((int) ini_get('session.gc_maxlifetime') < 3600) {
  @ini_set('session.gc_maxlifetime', '3600');
}

require_once dirname(__DIR__, 2) . '/includes/security.php';
require_once dirname(__DIR__, 2) . '/app/engines/rule_registry.php';
require_once dirname(__DIR__, 2) . '/app/engines/rule_executor.php';
require_once dirname(__DIR__, 2) . '/app/engines/rule_engine.php';
require_once dirname(__DIR__, 2) . '/app/engines/validation_engine.php';
require_once dirname(__DIR__, 2) . '/app/engines/doctor_diagnosis.php';
require_once dirname(__DIR__, 2) . '/app/engines/doctor_priority.php';
require_once dirname(__DIR__, 2) . '/app/engines/doctor_score.php';
require_once dirname(__DIR__, 2) . '/app/engines/doctor_prescription.php';
require_once dirname(__DIR__, 2) . '/app/engines/doctor_engine.php';
require_once dirname(__DIR__, 2) . '/app/engines/challan_reconciliation.php';
require_once dirname(__DIR__, 2) . '/app/engines/deductee_reconciliation.php';
require_once dirname(__DIR__, 2) . '/app/engines/salary_reconciliation.php';
require_once dirname(__DIR__, 2) . '/app/engines/quarter_reconciliation.php';
require_once dirname(__DIR__, 2) . '/app/engines/financial_health.php';
require_once dirname(__DIR__, 2) . '/app/engines/reconciliation_engine.php';

$etdsQcProjectRoot = str_replace('\\', '/', realpath(dirname(__DIR__, 2)) ?: dirname(__DIR__, 2));
$etdsQcDocumentRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ($_SERVER['DOCUMENT_ROOT'] ?? ''));
$etdsQcSiteRoot = '';
if ($etdsQcDocumentRoot !== '' && str_starts_with($etdsQcProjectRoot, $etdsQcDocumentRoot)) {
  $computedRoot = substr($etdsQcProjectRoot, strlen($etdsQcDocumentRoot));
  $computedRoot = str_replace('\\', '/', $computedRoot);
  $etdsQcSiteRoot = $computedRoot === '' ? '' : rtrim($computedRoot, '/');
}

if (!function_exists('app_href')) {
  function app_href(string $path): string {
    global $etdsQcSiteRoot;
    return ($etdsQcSiteRoot !== '' ? $etdsQcSiteRoot : '') . $path;
  }
}

if (!function_exists('site_href')) {
  function site_href(string $path): string {
    return app_href($path);
  }
}

function etds_qc_storage_directories(): array {
  return [
    ETDS_QC_STORAGE_ROOT,
    ETDS_QC_CASES_ROOT,
    ETDS_QC_DOCUMENTS_ROOT,
    ETDS_QC_UPLOADS_ROOT,
    ETDS_QC_MASTERS_ROOT,
    ETDS_QC_RULES_ROOT,
    ETDS_QC_USERS_ROOT,
    ETDS_QC_LOGS_ROOT,
    ETDS_QC_AUDIT_ROOT,
    ETDS_QC_SETTINGS_ROOT,
  ];
}

function etds_qc_default_masters(): array {
  return [
    'sections' => [
      ['code' => '192', 'label' => 'Salary'],
      ['code' => '194C', 'label' => 'Contractor'],
      ['code' => '194H', 'label' => 'Commission'],
      ['code' => '194J', 'label' => 'Professional Fees'],
    ],
    'nature_of_payment' => [
      ['code' => 'SAL', 'label' => 'Salary Payment'],
      ['code' => 'CON', 'label' => 'Contract Payment'],
      ['code' => 'PRO', 'label' => 'Professional Payment'],
    ],
    'states' => [
      ['code' => 'TN', 'label' => 'Tamil Nadu'],
      ['code' => 'KA', 'label' => 'Karnataka'],
      ['code' => 'KL', 'label' => 'Kerala'],
      ['code' => 'MH', 'label' => 'Maharashtra'],
    ],
    'banks' => [
      ['code' => 'SBI', 'label' => 'State Bank of India'],
      ['code' => 'IOB', 'label' => 'Indian Overseas Bank'],
      ['code' => 'HDFC', 'label' => 'HDFC Bank'],
      ['code' => 'ICICI', 'label' => 'ICICI Bank'],
    ],
    'document_types' => [
      ['code' => 'excel', 'label' => 'Excel'],
      ['code' => 'pdf', 'label' => 'PDF'],
      ['code' => 'scanned_pdf', 'label' => 'Scanned PDF'],
      ['code' => 'image', 'label' => 'Images'],
      ['code' => 'zip', 'label' => 'ZIP'],
    ],
    'validation_rules' => [
      ['code' => 'PAN_FORMAT', 'label' => 'PAN format validation', 'phase' => 'v3'],
      ['code' => 'TAN_FORMAT', 'label' => 'TAN format validation', 'phase' => 'v2'],
      ['code' => 'DATE_FORMAT', 'label' => 'Date format validation', 'phase' => 'v3'],
    ],
    'financial_years' => [
      ['code' => '2026-27', 'label' => '2026-27'],
      ['code' => '2025-26', 'label' => '2025-26'],
      ['code' => '2024-25', 'label' => '2024-25'],
    ],
    'quarters' => [
      ['code' => 'Q1', 'label' => 'Q1'],
      ['code' => 'Q2', 'label' => 'Q2'],
      ['code' => 'Q3', 'label' => 'Q3'],
      ['code' => 'Q4', 'label' => 'Q4'],
    ],
  ];
}

function etds_qc_master_file(string $name): string {
  return ETDS_QC_MASTERS_ROOT . '/' . $name . '.json';
}

function etds_qc_bootstrap_masters(): void {
  foreach (etds_qc_default_masters() as $name => $payload) {
    $file = etds_qc_master_file($name);
    if (!is_file($file)) {
      etds_qc_write_json($file, $payload);
    }
  }
}

function etds_qc_master(string $name, array $default = []): array {
  $payload = etds_qc_load_json(etds_qc_master_file($name), $default);
  return is_array($payload) ? $payload : $default;
}

function etds_qc_case_status_catalog(): array {
  return [
    'draft' => ['label' => 'Draft', 'progress' => 5],
    'documents_received' => ['label' => 'Documents Received', 'progress' => 20],
    'extraction_running' => ['label' => 'Extraction Running', 'progress' => 35],
    'validation_running' => ['label' => 'Validation Running', 'progress' => 50],
    'reconciliation_pending' => ['label' => 'Reconciliation Pending', 'progress' => 65],
    'qc_in_progress' => ['label' => 'QC In Progress', 'progress' => 80],
    'qc_completed' => ['label' => 'QC Completed', 'progress' => 90],
    'ready_for_return_preparation' => ['label' => 'Ready for Return Preparation', 'progress' => 100],
    'archived' => ['label' => 'Archived', 'progress' => 100],
    'deleted' => ['label' => 'Deleted', 'progress' => 0],
  ];
}

function etds_qc_case_status_meta(string $status): array {
  $catalog = etds_qc_case_status_catalog();
  return $catalog[$status] ?? $catalog['draft'];
}

function etds_qc_default_case_documents(): array {
  return ['documents' => [], 'source_columns' => [], 'records' => [], 'summary' => ['document_count' => 0]];
}

function etds_qc_default_extraction(): array {
  return [
    'summary' => [
      'documents_processed' => 0,
      'documents_pending_review' => 0,
      'fields_extracted' => 0,
      'fields_missing' => 0,
      'overall_confidence' => 0,
      'documents_failed' => 0,
      'status' => 'pending',
    ],
    'documents' => [],
    'source_columns' => [],
    'records' => [],
    'phase' => 'phase_3',
  ];
}

function etds_qc_default_ocr(): array {
  return [
    'documents' => [],
    'summary' => [
      'documents_processed' => 0,
      'documents_pending' => 0,
      'pages_processed' => 0,
      'status' => 'pending',
    ],
  ];
}

function etds_qc_default_payments(): array {
  return ['payments' => [], 'summary' => ['total_records' => 0]];
}

function etds_qc_default_validation(): array {
  $payload = function_exists('etds_validation_output_schema')
    ? etds_validation_output_schema()
    : ['summary' => ['total_records' => 0, 'quality_score' => 0, 'critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0, 'information' => 0, 'total_findings' => 0, 'ready_status' => false, 'last_validated_on' => null], 'findings' => []];
  $payload['phase'] = 'validation_rules_engine';
  return $payload;
}

function etds_qc_default_reconciliation(): array {
  $payload = function_exists('etds_reconciliation_output_schema')
    ? etds_reconciliation_output_schema()
    : ['summary' => ['challan_score' => 0, 'deductee_score' => 0, 'salary_score' => 0, 'quarter_score' => 0, 'financial_health_score' => 0, 'reconciliation_score' => 0, 'ready_status' => false, 'total_issues' => 0, 'difference' => 0.0, 'balance' => 0.0, 'last_reconciled_on' => null], 'challan_reconciliation' => ['summary' => [], 'rows' => [], 'issues' => []], 'deductee_reconciliation' => ['summary' => [], 'rows' => [], 'issues' => []], 'salary_reconciliation' => ['summary' => [], 'issues' => []], 'quarter_reconciliation' => ['summary' => [], 'issues' => []], 'document_reconciliation' => ['summary' => []], 'issues' => [], 'exceptions' => []];
  $payload['phase'] = 'enterprise_reconciliation_engine';
  return $payload;
}

function etds_qc_default_qc(): array {
  return ['status' => 'not_started', 'certificate' => null, 'exports' => []];
}

function etds_qc_default_corrections(): array {
  return ['history' => [], 'cell_states' => []];
}

function etds_qc_default_doctor(): array {
  $payload = function_exists('etds_doctor_output_schema')
    ? etds_doctor_output_schema()
    : ['summary' => ['top_priority' => 'Information', 'top_diagnosis' => 'Diagnosis Pending', 'expected_improvement' => '0 -> 0', 'estimated_time_minutes' => 0, 'readiness' => 'Not Ready', 'last_generated_on' => null], 'diagnosis' => [], 'priority' => [], 'prescription' => [], 'health_scores' => ['extraction_score' => 0, 'validation_score' => 0, 'completeness_score' => 0, 'consistency_score' => 0, 'overall_data_health_score' => 0, 'methodology' => []], 'recommendations' => [], 'readiness' => ['status' => 'Not Ready', 'reason' => 'Validation findings are not available yet.']];
  $payload['phase'] = 'doctor_intelligence_engine';
  return $payload;
}

function etds_qc_case_file_name(string $logicalName): string {
  return match ($logicalName) {
    'session.json', 'case.json' => 'case.json',
    'client.json' => 'client.json',
    'deductor.json' => 'deductor.json',
    'documents.json', 'source_data.json' => 'documents.json',
    'deductees.json' => 'deductees.json',
    'challans.json' => 'challans.json',
    'salary.json' => 'salary.json',
    'payments.json' => 'payments.json',
    'extraction.json' => 'extraction.json',
    'ocr.json' => 'ocr.json',
    'validation.json', 'validated_data.json' => 'validation.json',
    'corrections.json' => 'corrections.json',
    'doctor.json' => 'doctor.json',
    'reconciliation.json' => 'reconciliation.json',
    'qc.json' => 'qc.json',
    'audit.json', 'audit/audit-log.json' => 'audit.json',
    default => $logicalName,
  };
}

function etds_qc_normalize_case_id(string $caseId): string {
  $caseId = strtoupper(trim($caseId));
  if (preg_match('/^ETD-\d{4}-\d{6}$/', $caseId)) {
    return $caseId;
  }
  if (preg_match('/^QC-(\d{4})-(\d{4})$/', $caseId, $matches)) {
    return sprintf('ETD-%s-%06d', $matches[1], (int) $matches[2]);
  }
  return $caseId;
}

function etds_qc_legacy_case_root(string $caseId): string {
  return ETDS_QC_LEGACY_SESSIONS_ROOT . '/' . $caseId;
}

function etds_qc_case_root(string $caseId): string {
  return ETDS_QC_CASES_ROOT . '/' . etds_qc_normalize_case_id($caseId);
}

function etds_qc_existing_case_sequence(string $year): int {
  $max = 0;
  foreach (glob(ETDS_QC_CASES_ROOT . '/ETD-' . $year . '-*', GLOB_ONLYDIR) ?: [] as $directory) {
    if (preg_match('/ETD-' . preg_quote($year, '/') . '-(\d{6})$/', basename($directory), $matches) === 1) {
      $max = max($max, (int) $matches[1]);
    }
  }
  return $max;
}

function etds_qc_next_counter(string $key, string $scope = 'global'): int {
  $counters = etds_qc_load_json(ETDS_QC_COUNTER_FILE, []);
  if (!is_array($counters)) {
    $counters = [];
  }
  $current = (int) ($counters[$key][$scope] ?? 0);
  if ($key === 'case_seq' && preg_match('/^\d{4}$/', $scope) === 1) {
    $current = max($current, etds_qc_existing_case_sequence($scope));
  }
  $next = $current + 1;
  $counters[$key][$scope] = $next;
  etds_qc_write_json(ETDS_QC_COUNTER_FILE, $counters);
  return $next;
}

function etds_qc_case_timeline_from_audit(string $caseId): array {
  $events = etds_qc_load_json(etds_qc_session_file($caseId, 'audit.json'), []);
  if (!is_array($events)) {
    return [];
  }
  $timeline = [];
  foreach (array_slice(array_reverse($events), 0, 8) as $event) {
    $timestamp = (string) ($event['timestamp'] ?? '');
    $timeLabel = '';
    try {
      $timeLabel = (new DateTimeImmutable($timestamp))->setTimezone(new DateTimeZone('Asia/Calcutta'))->format('h:i A');
    } catch (Throwable) {
      $timeLabel = '--:--';
    }
    $timeline[] = [
      'time' => $timeLabel,
      'label' => (string) ($event['event'] ?? ($event['action'] ?? 'Case activity')),
      'tone' => 'good',
    ];
  }
  return $timeline;
}

function etds_qc_migrate_legacy_structure(): void {
  if (!is_dir(ETDS_QC_LEGACY_SESSIONS_ROOT)) {
    return;
  }
  foreach (glob(ETDS_QC_LEGACY_SESSIONS_ROOT . '/QC-*', GLOB_ONLYDIR) ?: [] as $legacyDirectory) {
    $legacyId = basename($legacyDirectory);
    $caseId = etds_qc_normalize_case_id($legacyId);
    $targetDirectory = etds_qc_case_root($caseId);
    if (!is_dir($targetDirectory)) {
      mkdir($targetDirectory, 0775, true);
    }

    $legacySession = etds_qc_load_json($legacyDirectory . '/session.json', []);
    if (is_array($legacySession) && $legacySession !== []) {
      $legacySession['legacy_session_id'] = $legacyId;
      $legacySession['session_id'] = $caseId;
      $legacySession['case_id'] = $caseId;
      $legacySession['status'] = in_array((string) ($legacySession['status'] ?? ''), ['purged', 'downloaded'], true) ? 'ready_for_return_preparation' : ((string) ($legacySession['status'] ?? 'draft'));
      etds_qc_write_json($targetDirectory . '/case.json', $legacySession);
      etds_qc_write_json($targetDirectory . '/client.json', [
        'client_name' => $legacySession['client_name'] ?? '',
        'client_code' => '',
        'tan' => $legacySession['tan'] ?? '',
        'pan' => '',
        'address' => '',
        'contact_person' => '',
        'mobile' => '',
        'email' => '',
        'financial_year' => $legacySession['financial_year'] ?? '',
        'quarter' => $legacySession['quarter'] ?? '',
        'entity_type' => '',
      ]);
      etds_qc_write_json($targetDirectory . '/deductor.json', [
        'tan' => $legacySession['tan'] ?? '',
        'pan' => '',
        'entity_type' => '',
      ]);
    }

    $map = [
      'source_data.json' => 'documents.json',
      'validated_data.json' => 'validation.json',
      'reconciliation.json' => 'reconciliation.json',
      'challans.json' => 'challans.json',
    ];
    foreach ($map as $from => $to) {
      $source = $legacyDirectory . '/' . $from;
      if (is_file($source) && !is_file($targetDirectory . '/' . $to)) {
        copy($source, $targetDirectory . '/' . $to);
      }
    }

    if (is_file($legacyDirectory . '/audit/audit-log.json') && !is_file($targetDirectory . '/audit.json')) {
      copy($legacyDirectory . '/audit/audit-log.json', $targetDirectory . '/audit.json');
    }
    if (is_dir($legacyDirectory . '/uploads/original') && !is_dir($targetDirectory . '/uploads')) {
      mkdir($targetDirectory . '/uploads', 0775, true);
      foreach (glob($legacyDirectory . '/uploads/original/*') ?: [] as $item) {
        if (is_file($item)) {
          copy($item, $targetDirectory . '/uploads/' . basename($item));
        }
      }
    }
    if (is_dir($legacyDirectory . '/output') && !is_dir($targetDirectory . '/exports')) {
      mkdir($targetDirectory . '/exports', 0775, true);
      foreach (glob($legacyDirectory . '/output/*') ?: [] as $item) {
        if (is_file($item)) {
          copy($item, $targetDirectory . '/exports/' . basename($item));
        }
      }
    }
  }
}

function etds_qc_bootstrap(): void {
  foreach (etds_qc_storage_directories() as $directory) {
    if (!is_dir($directory)) {
      mkdir($directory, 0775, true);
    }
  }

  if (!is_file(ETDS_QC_USERS_FILE)) {
    etds_qc_write_json(ETDS_QC_USERS_FILE, [[
      'id' => 'USR-0001',
      'name' => 'System Administrator',
      'email' => 'admin@etaxadv.local',
      'role' => 'super_admin',
      'password_hash' => '$2y$12$Kp.UmGs91Th5LFsLsAuEgO6KlFxW8kt8xkK8HvPk5a676gCi1ZaSa',
      'status' => 'active',
      'created_on' => etds_qc_now(),
      'updated_on' => etds_qc_now(),
    ]]);
  }

  if (!is_file(ETDS_QC_CONFIG_FILE)) {
    etds_qc_write_json(ETDS_QC_CONFIG_FILE, [
      'quality_threshold' => 100,
      'purge_after_days' => 7,
      'allowed_extensions' => ['xlsx', 'xls', 'csv', 'pdf', 'png', 'jpg', 'jpeg', 'zip'],
    ]);
  }

  if (!is_file(ETDS_QC_COUNTER_FILE)) {
    etds_qc_write_json(ETDS_QC_COUNTER_FILE, ['case_seq' => [], 'audit_seq' => []]);
  }

  etds_qc_bootstrap_masters();
  if (function_exists('etds_rule_registry_bootstrap')) {
    etds_rule_registry_bootstrap();
  }
  etds_qc_migrate_legacy_structure();
  etds_qc_run_auto_purge();
}

function etds_qc_now(): string {
  return (new DateTimeImmutable('now', new DateTimeZone('Asia/Calcutta')))->format(DateTimeInterface::ATOM);
}

function etds_qc_client_ip(): string {
  $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
  foreach ($headers as $header) {
    if (!empty($_SERVER[$header])) {
      $ip = explode(',', (string) $_SERVER[$header])[0];
      $ip = trim($ip);
      if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
      }
    }
  }
  return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function etds_qc_h(?string $value): string {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function etds_qc_load_json(string $file, mixed $default = []): mixed {
  if (!is_file($file)) {
    return $default;
  }
  $raw = file_get_contents($file);
  if ($raw === false || trim($raw) === '') {
    return $default;
  }
  $decoded = json_decode($raw, true);
  return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
}

function etds_qc_write_json(string $file, mixed $payload): void {
  $directory = dirname($file);
  if (!is_dir($directory)) {
    mkdir($directory, 0775, true);
  }
  $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  if (!is_string($json)) {
    throw new RuntimeException('Failed to encode JSON for ' . $file);
  }
  if (file_put_contents($file, $json . PHP_EOL, LOCK_EX) === false) {
    throw new RuntimeException('Failed to write JSON file: ' . $file);
  }
}

function etds_qc_detect_mime_type(string $path): string {
  if (function_exists('mime_content_type')) {
    $detected = @mime_content_type($path);
    if (is_string($detected) && $detected !== '') {
      return $detected;
    }
  }
  if (class_exists('finfo')) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $detected = $finfo->file($path);
    if (is_string($detected) && $detected !== '') {
      return $detected;
    }
  }
  return 'application/octet-stream';
}

function etds_qc_config(): array {
  $config = etds_qc_load_json(ETDS_QC_CONFIG_FILE, []);
  return is_array($config) ? $config : [];
}

function etds_qc_users(): array {
  $users = etds_qc_load_json(ETDS_QC_USERS_FILE, []);
  return is_array($users) ? $users : [];
}

function etds_qc_current_user(): ?array {
  $userId = $_SESSION['etds_qc_user_id'] ?? null;
  if (!is_string($userId) || $userId === '') {
    return null;
  }
  foreach (etds_qc_users() as $user) {
    if (($user['id'] ?? '') === $userId && ($user['status'] ?? '') === 'active') {
      return $user;
    }
  }
  return null;
}

function etds_qc_require_auth(): void {
  if (etds_qc_current_user() !== null) {
    return;
  }
  header('Location: ' . site_href('/fintech/etds-qc/?view=login'));
  exit;
}

function etds_qc_login(string $email, string $password): bool {
  foreach (etds_qc_users() as $user) {
    if (strcasecmp((string) ($user['email'] ?? ''), $email) !== 0) {
      continue;
    }
    if (!password_verify($password, (string) ($user['password_hash'] ?? ''))) {
      continue;
    }
    session_regenerate_id(true);
    $_SESSION['etds_qc_user_id'] = $user['id'];
    $_SESSION['etds_qc_login_at'] = time();
    return true;
  }
  return false;
}

function etds_qc_logout(): void {
  unset($_SESSION['etds_qc_user_id'], $_SESSION['etds_qc_login_at']);
  session_regenerate_id(true);
}

function etds_qc_tan_valid(string $tan): bool {
  return preg_match('/^[A-Z]{4}[0-9]{5}[A-Z]$/', strtoupper($tan)) === 1;
}

function etds_qc_return_type_map(string $returnType): array {
  $returnType = strtoupper(trim($returnType));
  $map = [
    '24Q'   => ['old_form' => '24Q', 'new_form' => '138', 'form_label' => 'Form 138 / 24Q — Salary TDS', 'form_nature' => 'salary_tds'],
    '26Q'   => ['old_form' => '26Q', 'new_form' => '140', 'form_label' => 'Form 140 / 26Q — Non-salary TDS', 'form_nature' => 'non_salary_tds'],
    '27Q'   => ['old_form' => '27Q', 'new_form' => '144', 'form_label' => 'Form 144 / 27Q — Non-resident TDS', 'form_nature' => 'non_resident_tds'],
    '27EQ'  => ['old_form' => '27EQ', 'new_form' => '143', 'form_label' => 'Form 143 / 27EQ — TCS', 'form_nature' => 'tcs'],
    '138'   => ['old_form' => '24Q', 'new_form' => '138', 'form_label' => 'Form 138 / 24Q — Salary TDS', 'form_nature' => 'salary_tds'],
    '140'   => ['old_form' => '26Q', 'new_form' => '140', 'form_label' => 'Form 140 / 26Q — Non-salary TDS', 'form_nature' => 'non_salary_tds'],
    '144'   => ['old_form' => '27Q', 'new_form' => '144', 'form_label' => 'Form 144 / 27Q — Non-resident TDS', 'form_nature' => 'non_resident_tds'],
    '143'   => ['old_form' => '27EQ', 'new_form' => '143', 'form_label' => 'Form 143 / 27EQ — TCS', 'form_nature' => 'tcs'],
  ];
  return $map[$returnType] ?? ['old_form' => $returnType, 'new_form' => '', 'form_label' => $returnType, 'form_nature' => 'unknown'];
}

function etds_qc_return_type_label(string $returnType): string {
  return etds_qc_return_type_map($returnType)['form_label'];
}

function etds_qc_return_type_canonical(string $returnType): string {
  return etds_qc_return_type_map($returnType)['old_form'];
}

function etds_qc_session_dir(string $sessionId): string {
  return etds_qc_case_root($sessionId);
}

function etds_qc_session_file(string $sessionId, string $fileName): string {
  $normalized = etds_qc_case_file_name($fileName);
  if (str_starts_with($fileName, 'uploads/original/')) {
    return etds_qc_session_dir($sessionId) . '/uploads/' . basename($fileName);
  }
  if (str_starts_with($fileName, 'output/')) {
    return etds_qc_session_dir($sessionId) . '/exports/' . basename($fileName);
  }
  return etds_qc_session_dir($sessionId) . '/' . $normalized;
}

function etds_qc_ensure_session_structure(string $sessionId): void {
  foreach (['', 'uploads', 'exports', 'logs'] as $path) {
    $target = $path === '' ? etds_qc_session_dir($sessionId) : etds_qc_session_file($sessionId, $path);
    if (!is_dir($target)) {
      mkdir($target, 0775, true);
    }
  }
  foreach ([
    'case.json' => [],
    'client.json' => [],
    'deductor.json' => [],
    'documents.json' => etds_qc_default_case_documents(),
    'deductees.json' => ['deductees' => []],
    'challans.json' => ['challans' => []],
    'salary.json' => ['rows' => []],
    'payments.json' => etds_qc_default_payments(),
    'extraction.json' => etds_qc_default_extraction(),
    'ocr.json' => etds_qc_default_ocr(),
    'validation.json' => etds_qc_default_validation(),
    'corrections.json' => etds_qc_default_corrections(),
    'doctor.json' => etds_qc_default_doctor(),
    'reconciliation.json' => etds_qc_default_reconciliation(),
    'qc.json' => etds_qc_default_qc(),
    'audit.json' => [],
  ] as $file => $defaultPayload) {
    $path = etds_qc_session_file($sessionId, $file);
    if (!is_file($path)) {
      etds_qc_write_json($path, $defaultPayload);
    }
  }
}

function etds_qc_workspace_sync_case_data(string $sessionId): void {
  $documents = etds_qc_load_json(etds_qc_session_file($sessionId, 'documents.json'), etds_qc_default_case_documents());
  $records = is_array($documents['records'] ?? null) ? $documents['records'] : [];
  $deducteesFile = etds_qc_session_file($sessionId, 'deductees.json');
  $deducteesPayload = etds_qc_load_json($deducteesFile, ['deductees' => []]);
  $deductees = is_array($deducteesPayload['deductees'] ?? null) ? $deducteesPayload['deductees'] : [];

  if ($deductees === [] && $records !== []) {
    $deductees = array_map(static function (array $record): array {
      return [
        'deductee_id' => (string) ($record['record_id'] ?? ('DED-' . substr(bin2hex(random_bytes(3)), 0, 6))),
        'record_id' => (string) ($record['record_id'] ?? ''),
        'source_file_id' => (string) ($record['source_file_id'] ?? ''),
        'row_number' => (int) ($record['row_number'] ?? 0),
        'deductee_name' => (string) ($record['deductee_name'] ?? ''),
        'pan' => (string) ($record['pan'] ?? ''),
        'tds_amount' => (string) ($record['tds_amount'] ?? ''),
        'invoice_number' => (string) ($record['invoice_number'] ?? ''),
        'challan_reference' => (string) ($record['challan_reference'] ?? ''),
        'deduction_date' => (string) ($record['deduction_date'] ?? ''),
      ];
    }, $records);
    etds_qc_write_json($deducteesFile, ['deductees' => $deductees, 'summary' => ['total_records' => count($deductees)]]);
  }

  foreach ([
    'salary.json' => ['rows' => [], 'summary' => ['total_records' => 0]],
    'payments.json' => etds_qc_default_payments(),
    'challans.json' => ['challans' => [], 'summary' => ['total_records' => 0]],
  ] as $file => $defaultPayload) {
    $path = etds_qc_session_file($sessionId, $file);
    if (!is_file($path)) {
      etds_qc_write_json($path, $defaultPayload);
    }
  }
}

function etds_qc_workspace_cell_key(string $sheet, string $recordId, string $field): string {
  return $sheet . '::' . $recordId . '::' . $field;
}

function etds_qc_workspace_corrections(string $sessionId): array {
  $payload = etds_qc_load_json(etds_qc_session_file($sessionId, 'corrections.json'), etds_qc_default_corrections());
  return is_array($payload) ? $payload : etds_qc_default_corrections();
}

function etds_qc_workspace_sheet_catalog(): array {
  return [
    'deductor' => ['label' => 'Deductor', 'id_field' => 'deductor_id', 'fields' => ['client_name', 'tan', 'pan', 'financial_year', 'quarter', 'entity_type']],
    'deductees' => ['label' => 'Deductees', 'id_field' => 'deductee_id', 'fields' => ['deductee_name', 'pan', 'tds_amount', 'deduction_date', 'invoice_number', 'challan_reference']],
    'challans' => ['label' => 'Challans', 'id_field' => 'challan_id', 'fields' => ['challan_reference', 'bsr_code', 'deposit_date', 'section_code', 'total_available', 'allocated_total', 'balance_total']],
    'salary' => ['label' => 'Salary', 'id_field' => 'salary_id', 'fields' => ['employee_name', 'pan', 'amount', 'deduction_date', 'section_code']],
    'payments' => ['label' => 'Payments', 'id_field' => 'payment_id', 'fields' => ['voucher_number', 'party_name', 'pan', 'amount', 'payment_date', 'section_code']],
  ];
}

function etds_qc_workspace_infer_field_from_issue(array $issue): string {
  if (($issue['field'] ?? '') !== '') {
    return (string) $issue['field'];
  }
  $rule = strtolower((string) ($issue['rule_name'] ?? ''));
  $message = strtolower((string) ($issue['message'] ?? ''));
  return match (true) {
    str_contains($rule, 'pan') || str_contains($message, 'pan') => 'pan',
    str_contains($rule, 'challan') || str_contains($message, 'challan') => 'challan_reference',
    str_contains($rule, 'section') || str_contains($message, 'section') => 'section_code',
    str_contains($rule, 'date') || str_contains($message, 'date') => 'deduction_date',
    str_contains($rule, 'amount') || str_contains($message, 'amount') => 'tds_amount',
    str_contains($rule, 'name') || str_contains($message, 'name') => 'deductee_name',
    default => '_row',
  };
}

function etds_qc_workspace_issue_lookup(string $sessionId): array {
  $validation = etds_qc_load_json(etds_qc_session_file($sessionId, 'validation.json'), etds_qc_default_validation());
  $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation());
  $lookup = [];

  if (is_array($validation['findings'] ?? null) && $validation['findings'] !== []) {
    foreach ($validation['findings'] as $finding) {
      $recordId = (string) ($finding['record_reference'] ?? '');
      $field = etds_qc_workspace_infer_field_from_issue($finding);
      $lookup['deductees'][$recordId][$field][] = [
        'issue_id' => (string) ($finding['finding_id'] ?? $finding['rule_id'] ?? ''),
        'severity' => (string) ($finding['severity'] ?? 'Information'),
        'message' => (string) ($finding['message'] ?? 'Validation finding'),
        'suggested_action' => (string) ($finding['suggested_action'] ?? ''),
        'status' => (string) ($finding['status'] ?? 'open'),
        'reason' => (string) ($finding['rule_name'] ?? ''),
      ];
    }
    return $lookup;
  }

  foreach ((array) ($validation['records'] ?? []) as $record) {
    $recordId = (string) ($record['record_id'] ?? '');
    foreach ((array) ($record['issues'] ?? []) as $issue) {
      $field = (string) ($issue['field'] ?? '_row');
      $lookup['deductees'][$recordId][$field][] = [
        'issue_id' => (string) ($issue['issue_id'] ?? ''),
        'severity' => ucfirst((string) ($issue['severity'] ?? 'warning')),
        'message' => (string) ($issue['message'] ?? 'Validation issue'),
        'suggested_action' => (string) ($issue['suggested_correction'] ?? ''),
        'status' => (string) ($issue['resolution_status'] ?? 'open'),
        'reason' => (string) ($issue['type'] ?? ''),
      ];
    }
  }

  foreach ((array) ($reconciliation['issues'] ?? $reconciliation['exceptions'] ?? []) as $issue) {
    if (!is_array($issue)) {
      continue;
    }
    $module = (string) ($issue['module'] ?? '');
    $recordId = (string) ($issue['record_reference'] ?? $issue['record_id'] ?? '');
    $field = (string) ($issue['field'] ?? '_row');
    $sheet = match ($module) {
      'challan' => 'challans',
      'salary' => 'salary',
      'quarter', 'deductee' => 'deductees',
      default => 'deductees',
    };
    if ($recordId === '') {
      continue;
    }
    $lookup[$sheet][$recordId][$field][] = [
      'issue_id' => (string) ($issue['issue_id'] ?? ''),
      'severity' => (string) ($issue['severity'] ?? 'Medium'),
      'message' => (string) ($issue['message'] ?? 'Reconciliation issue'),
      'suggested_action' => (string) ($issue['suggested_action'] ?? ''),
      'status' => (string) ($issue['status'] ?? 'open'),
      'reason' => 'reconciliation',
    ];
  }

  return $lookup;
}

function etds_qc_workspace_suggestion_for_field(string $sheet, array $record, string $field, array $issues, array $context = []): ?array {
  if ($sheet !== 'deductees' || $issues === []) {
    return null;
  }
  $primary = $issues[0];
  $message = strtolower((string) ($primary['message'] ?? ''));
  $current = trim((string) ($record[$field] ?? ''));

  if ($field === 'pan' && $current !== '') {
    $normalized = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $current) ?? $current);
    if ($normalized !== $current) {
      return ['value' => $normalized, 'reason' => 'Normalize PAN formatting to uppercase alphanumeric value.'];
    }
  }

  if ($field === 'deduction_date' && $current !== '') {
    $timestamp = strtotime($current);
    if ($timestamp !== false) {
      $normalized = date('Y-m-d', $timestamp);
      if ($normalized !== $current) {
        return ['value' => $normalized, 'reason' => 'Normalize deduction date to YYYY-MM-DD format.'];
      }
    }
  }

  if ($field === 'challan_reference' && $current === '') {
    $challans = is_array($context['challans'] ?? null) ? $context['challans'] : [];
    if (count($challans) === 1 && trim((string) ($challans[0]['challan_reference'] ?? '')) !== '') {
      return ['value' => (string) $challans[0]['challan_reference'], 'reason' => 'Single challan detected in the case, so it can be suggested for the missing mapping.'];
    }
  }

  if ($field === 'deductee_name' && $current !== '') {
    $normalized = preg_replace('/\s+/', ' ', trim($current)) ?? trim($current);
    if ($normalized !== $current) {
      return ['value' => $normalized, 'reason' => 'Trim extra spaces in the deductee name.'];
    }
  }

  if ($field === 'tds_amount' && $current !== '') {
    $normalized = str_replace(',', '', $current);
    if (is_numeric($normalized) && $normalized !== $current) {
      return ['value' => $normalized, 'reason' => 'Normalize amount formatting to a numeric value.'];
    }
  }

  if (str_contains($message, 'missing') || str_contains($message, 'invalid')) {
    return ['value' => $current, 'reason' => (string) ($primary['suggested_action'] ?? 'Review the record manually.')];
  }

  return null;
}

function etds_qc_workspace_records(string $sessionId): array {
  etds_qc_workspace_sync_case_data($sessionId);
  $catalog = etds_qc_workspace_sheet_catalog();
  $case = etds_qc_find_session($sessionId) ?? [];
  $deductor = etds_qc_load_json(etds_qc_session_file($sessionId, 'deductor.json'), []);
  $deducteesPayload = etds_qc_load_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => []]);
  $challansPayload = etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]);
  $salaryPayload = etds_qc_load_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => []]);
  $paymentsPayload = etds_qc_load_json(etds_qc_session_file($sessionId, 'payments.json'), ['payments' => []]);
  $issues = etds_qc_workspace_issue_lookup($sessionId);
  $corrections = etds_qc_workspace_corrections($sessionId);
  $cellStates = is_array($corrections['cell_states'] ?? null) ? $corrections['cell_states'] : [];
  $context = ['challans' => is_array($challansPayload['challans'] ?? null) ? $challansPayload['challans'] : []];

  $sheets = [
    'deductor' => [[
      'deductor_id' => 'DEDUCTOR-0001',
      'client_name' => (string) ($case['client_name'] ?? ''),
      'tan' => (string) ($deductor['tan'] ?? $case['tan'] ?? ''),
      'pan' => (string) ($deductor['pan'] ?? $case['pan'] ?? ''),
      'financial_year' => (string) ($case['financial_year'] ?? ''),
      'quarter' => (string) ($case['quarter'] ?? ''),
      'entity_type' => (string) ($deductor['entity_type'] ?? $case['entity_type'] ?? ''),
    ]],
    'deductees' => is_array($deducteesPayload['deductees'] ?? null) ? $deducteesPayload['deductees'] : [],
    'challans' => is_array($challansPayload['challans'] ?? null) ? $challansPayload['challans'] : [],
    'salary' => is_array($salaryPayload['rows'] ?? null) ? $salaryPayload['rows'] : [],
    'payments' => is_array($paymentsPayload['payments'] ?? null) ? $paymentsPayload['payments'] : [],
  ];

  $payload = [];
  foreach ($catalog as $sheetKey => $meta) {
    $rows = [];
    foreach (($sheets[$sheetKey] ?? []) as $index => $row) {
      if (!is_array($row)) {
        continue;
      }
      $recordIdField = (string) $meta['id_field'];
      $recordId = (string) ($row[$recordIdField] ?? $row['record_id'] ?? strtoupper(substr($sheetKey, 0, 3)) . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT));
      $row[$recordIdField] = $recordId;
      $rowIssues = $issues[$sheetKey][$recordId] ?? [];
      $row['_issues'] = $rowIssues;
      $row['_cell_status'] = [];
      $row['_suggestions'] = [];
      foreach ($meta['fields'] as $field) {
        $cellKey = etds_qc_workspace_cell_key($sheetKey, $recordId, $field);
        $state = is_array($cellStates[$cellKey] ?? null) ? $cellStates[$cellKey] : [];
        $fieldIssues = $rowIssues[$field] ?? [];
        $suggestion = etds_qc_workspace_suggestion_for_field($sheetKey, $row, $field, $fieldIssues, $context);
        $status = 'valid';
        if (($state['mode'] ?? '') === 'manual_override') {
          $status = 'manual_override';
        } elseif (($state['mode'] ?? '') === 'ai_suggested') {
          $status = 'corrected';
        } elseif ($suggestion !== null) {
          $status = 'ai_suggested';
        } elseif ($fieldIssues !== []) {
          $severity = strtolower((string) ($fieldIssues[0]['severity'] ?? 'warning'));
          $status = in_array($severity, ['critical', 'high', 'error'], true) ? 'error' : 'warning';
        }
        $row['_cell_status'][$field] = $status;
        if ($suggestion !== null) {
          $row['_suggestions'][$field] = $suggestion;
        }
      }
      $rows[] = $row;
    }
    $payload[$sheetKey] = ['meta' => $meta, 'rows' => $rows];
  }

  return $payload;
}

function etds_qc_workspace_update_row(array &$rows, string $idField, string $recordId, callable $updater): ?array {
  foreach ($rows as $index => $row) {
    if (!is_array($row)) {
      continue;
    }
    $currentId = (string) ($row[$idField] ?? $row['record_id'] ?? '');
    if ($currentId !== $recordId) {
      continue;
    }
    $updated = $updater($row);
    if (is_array($updated)) {
      $rows[$index] = $updated;
      return $updated;
    }
  }
  return null;
}

function etds_qc_workspace_write_sheet(string $sessionId, string $sheet, array $rows): void {
  if ($sheet === 'deductor') {
    $row = $rows[0] ?? [];
    $deductor = [
      'tan' => (string) ($row['tan'] ?? ''),
      'pan' => (string) ($row['pan'] ?? ''),
      'entity_type' => (string) ($row['entity_type'] ?? ''),
    ];
    etds_qc_write_json(etds_qc_session_file($sessionId, 'deductor.json'), $deductor);
    $session = etds_qc_find_session($sessionId);
    if ($session) {
      $session['client_name'] = (string) ($row['client_name'] ?? $session['client_name'] ?? '');
      $session['tan'] = (string) ($row['tan'] ?? $session['tan'] ?? '');
      $session['pan'] = (string) ($row['pan'] ?? $session['pan'] ?? '');
      $session['financial_year'] = (string) ($row['financial_year'] ?? $session['financial_year'] ?? '');
      $session['quarter'] = (string) ($row['quarter'] ?? $session['quarter'] ?? '');
      $session['entity_type'] = (string) ($row['entity_type'] ?? $session['entity_type'] ?? '');
      etds_qc_save_session($session);
    }
    return;
  }

  if ($sheet === 'deductees') {
    etds_qc_write_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => $rows, 'summary' => ['total_records' => count($rows)]]);
    $documents = etds_qc_load_json(etds_qc_session_file($sessionId, 'documents.json'), etds_qc_default_case_documents());
    $documentRows = [];
    foreach ($rows as $row) {
      $documentRows[] = [
        'record_id' => (string) ($row['record_id'] ?? $row['deductee_id'] ?? ''),
        'source_file_id' => (string) ($row['source_file_id'] ?? ''),
        'row_number' => (int) ($row['row_number'] ?? 0),
        'deductee_name' => (string) ($row['deductee_name'] ?? ''),
        'pan' => (string) ($row['pan'] ?? ''),
        'tds_amount' => (string) ($row['tds_amount'] ?? ''),
        'invoice_number' => (string) ($row['invoice_number'] ?? ''),
        'challan_reference' => (string) ($row['challan_reference'] ?? ''),
        'deduction_date' => (string) ($row['deduction_date'] ?? ''),
      ];
    }
    $documents['records'] = $documentRows;
    if (($documents['source_columns'] ?? []) === []) {
      $documents['source_columns'] = ['deductee_name', 'pan', 'tds_amount', 'invoice_number', 'challan_reference', 'deduction_date'];
    }
    etds_qc_write_json(etds_qc_session_file($sessionId, 'documents.json'), $documents);
    return;
  }

  if ($sheet === 'challans') {
    etds_qc_write_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => $rows, 'summary' => ['total_records' => count($rows)]]);
    return;
  }

  if ($sheet === 'salary') {
    etds_qc_write_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => $rows, 'summary' => ['total_records' => count($rows)]]);
    return;
  }

  if ($sheet === 'payments') {
    etds_qc_write_json(etds_qc_session_file($sessionId, 'payments.json'), ['payments' => $rows, 'summary' => ['total_records' => count($rows)]]);
  }
}

function etds_qc_workspace_record_change(string $sessionId, string $sheet, string $recordId, string $field, string $newValue, array $user, string $reason = '', string $mode = 'manual_override'): array {
  $workspace = etds_qc_workspace_records($sessionId);
  $sheetPayload = $workspace[$sheet] ?? null;
  if (!is_array($sheetPayload)) {
    throw new RuntimeException('Workspace sheet not found.');
  }
  $meta = $sheetPayload['meta'];
  $idField = (string) ($meta['id_field'] ?? 'record_id');
  $rows = $sheetPayload['rows'];
  $updatedRow = null;
  $oldValue = '';

  $cleanRows = array_map(static function (array $row): array {
    unset($row['_issues'], $row['_cell_status'], $row['_suggestions']);
    return $row;
  }, $rows);

  $updatedRow = etds_qc_workspace_update_row($cleanRows, $idField, $recordId, static function (array $row) use ($field, $newValue, &$oldValue): array {
    $oldValue = (string) ($row[$field] ?? '');
    $row[$field] = $newValue;
    return $row;
  });

  if ($updatedRow === null) {
    throw new RuntimeException('Record not found for correction.');
  }

  etds_qc_workspace_write_sheet($sessionId, $sheet, $cleanRows);

  $corrections = etds_qc_workspace_corrections($sessionId);
  $cellKey = etds_qc_workspace_cell_key($sheet, $recordId, $field);
  $existingState = is_array($corrections['cell_states'][$cellKey] ?? null) ? $corrections['cell_states'][$cellKey] : [];
  $originalValue = array_key_exists('original_value', $existingState) ? (string) $existingState['original_value'] : $oldValue;
  $event = [
    'change_id' => 'COR-' . substr(bin2hex(random_bytes(4)), 0, 8),
    'sheet' => $sheet,
    'record_id' => $recordId,
    'field' => $field,
    'original_value' => $originalValue,
    'old_value' => $oldValue,
    'new_value' => $newValue,
    'user_id' => $user['id'] ?? 'system',
    'user_name' => $user['name'] ?? ($user['email'] ?? 'System'),
    'timestamp' => etds_qc_now(),
    'reason' => $reason,
    'mode' => $mode,
  ];
  $corrections['history'][] = $event;
  $corrections['cell_states'][$cellKey] = [
    'original_value' => $originalValue,
    'current_value' => $newValue,
    'mode' => $mode,
    'updated_on' => $event['timestamp'],
    'reason' => $reason,
    'ignored' => false,
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'corrections.json'), $corrections);
  etds_qc_audit($sessionId, $user, 'correction_applied', 'Spreadsheet correction applied', ['sheet' => $sheet, 'record_id' => $recordId, 'field' => $field, 'old_value' => $oldValue], ['new_value' => $newValue, 'mode' => $mode, 'reason' => $reason]);

  return ['value' => $newValue, 'old_value' => $oldValue, 'original_value' => $originalValue, 'mode' => $mode, 'cell_key' => $cellKey];
}

function etds_qc_workspace_reset_field(string $sessionId, string $sheet, string $recordId, string $field, array $user): array {
  $corrections = etds_qc_workspace_corrections($sessionId);
  $cellKey = etds_qc_workspace_cell_key($sheet, $recordId, $field);
  $state = is_array($corrections['cell_states'][$cellKey] ?? null) ? $corrections['cell_states'][$cellKey] : [];
  $original = (string) ($state['original_value'] ?? '');
  $result = etds_qc_workspace_record_change($sessionId, $sheet, $recordId, $field, $original, $user, 'Reset to extracted value', 'reset_to_extracted');
  $corrections = etds_qc_workspace_corrections($sessionId);
  unset($corrections['cell_states'][$cellKey]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'corrections.json'), $corrections);
  return $result + ['value' => $original];
}

function etds_qc_workspace_bulk_edit(string $sessionId, string $sheet, array $recordIds, string $field, string $value, array $user, string $reason = ''): array {
  $results = [];
  foreach ($recordIds as $recordId) {
    $recordId = (string) $recordId;
    if ($recordId === '') {
      continue;
    }
    $results[] = etds_qc_workspace_record_change($sessionId, $sheet, $recordId, $field, $value, $user, $reason, 'manual_override');
  }
  return $results;
}

function etds_qc_workspace_ignore_suggestion(string $sessionId, string $sheet, string $recordId, string $field, array $user): void {
  $corrections = etds_qc_workspace_corrections($sessionId);
  $cellKey = etds_qc_workspace_cell_key($sheet, $recordId, $field);
  $state = is_array($corrections['cell_states'][$cellKey] ?? null) ? $corrections['cell_states'][$cellKey] : ['original_value' => '', 'current_value' => '', 'mode' => 'ignored'];
  $state['ignored'] = true;
  $state['mode'] = 'ignored';
  $state['updated_on'] = etds_qc_now();
  $corrections['cell_states'][$cellKey] = $state;
  $corrections['history'][] = [
    'change_id' => 'COR-' . substr(bin2hex(random_bytes(4)), 0, 8),
    'sheet' => $sheet,
    'record_id' => $recordId,
    'field' => $field,
    'original_value' => (string) ($state['original_value'] ?? ''),
    'old_value' => (string) ($state['current_value'] ?? ''),
    'new_value' => (string) ($state['current_value'] ?? ''),
    'user_id' => $user['id'] ?? 'system',
    'user_name' => $user['name'] ?? ($user['email'] ?? 'System'),
    'timestamp' => etds_qc_now(),
    'reason' => 'Suggestion ignored',
    'mode' => 'ignored',
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'corrections.json'), $corrections);
  etds_qc_audit($sessionId, $user, 'suggestion_ignored', 'AI suggestion ignored', ['sheet' => $sheet, 'record_id' => $recordId, 'field' => $field], []);
}

function etds_qc_all_sessions(): array {
  $items = [];
  foreach (glob(ETDS_QC_SESSIONS_ROOT . '/ETD-*', GLOB_ONLYDIR) ?: [] as $directory) {
    $session = etds_qc_load_json($directory . '/case.json', null);
    if (is_array($session)) {
      $items[] = $session;
    }
  }
  usort($items, static fn(array $a, array $b): int => strcmp((string) ($b['created_on'] ?? ''), (string) ($a['created_on'] ?? '')));
  return $items;
}

function etds_qc_session_state(array $session): array {
  $sessionId = (string) ($session['session_id'] ?? '');
  $status = (string) ($session['status'] ?? 'draft');
  if ($sessionId === '') {
    return ['key' => 'draft', 'label' => 'Draft'];
  }
  $meta = etds_qc_case_status_meta($status);
  return ['key' => $status, 'label' => (string) ($meta['label'] ?? ucfirst(str_replace('_', ' ', $status)))];
}

function etds_qc_find_session(string $sessionId): ?array {
  $normalizedId = etds_qc_normalize_case_id($sessionId);
  if (!preg_match('/^ETD-\d{4}-\d{6}$/', $normalizedId)) {
    return null;
  }
  $session = etds_qc_load_json(etds_qc_session_file($normalizedId, 'case.json'), null);
  if (!is_array($session)) {
    return null;
  }
  if (($session['legacy_session_id'] ?? '') === $sessionId) {
    $session['session_id'] = $normalizedId;
  }
  return is_array($session) ? $session : null;
}

function etds_qc_generate_session_id(): string {
  $year = (new DateTimeImmutable('now', new DateTimeZone('Asia/Calcutta')))->format('Y');
  do {
    $next = etds_qc_next_counter('case_seq', $year);
    $sessionId = sprintf('ETD-%s-%06d', $year, $next);
  } while (is_dir(etds_qc_case_root($sessionId)));
  return $sessionId;
}

function etds_qc_create_session(array $payload, array $user): array {
  $sessionId = etds_qc_generate_session_id();
  etds_qc_ensure_session_structure($sessionId);
  $client = [
    'client_name' => clean_input((string) ($payload['client_name'] ?? ''), 150),
    'client_code' => clean_input((string) ($payload['client_code'] ?? ''), 50),
    'tan' => strtoupper(clean_input((string) ($payload['tan'] ?? ''), 10)),
    'pan' => strtoupper(clean_input((string) ($payload['pan'] ?? ''), 10)),
    'address' => clean_multiline((string) ($payload['address'] ?? ''), 500),
    'contact_person' => clean_input((string) ($payload['contact_person'] ?? ''), 120),
    'mobile' => clean_input((string) ($payload['mobile'] ?? ''), 20),
    'email' => clean_input((string) ($payload['email'] ?? ''), 150),
    'financial_year' => clean_input((string) ($payload['financial_year'] ?? ''), 9),
    'quarter' => clean_input((string) ($payload['quarter'] ?? ''), 2),
    'entity_type' => clean_input((string) ($payload['entity_type'] ?? ''), 60),
  ];
  $session = [
    'session_id' => $sessionId,
    'case_id' => $sessionId,
    'client_name' => $client['client_name'],
    'client_code' => $client['client_code'],
    'tan' => $client['tan'],
    'pan' => $client['pan'],
    'financial_year' => $client['financial_year'],
    'quarter' => $client['quarter'],
    'entity_type' => $client['entity_type'],
    'return_type' => clean_input((string) ($payload['return_type'] ?? ''), 4),
    'remarks' => clean_multiline((string) ($payload['remarks'] ?? ''), 500),
    'is_favourite' => false,
    'is_deleted' => false,
    'status' => 'draft',
    'status_label' => 'Draft',
    'progress' => 5,
    'quality_score' => 0,
    'reconciliation_score' => 0,
    'export_readiness' => false,
    'created_by' => $user['id'] ?? 'system',
    'created_by_name' => $user['name'] ?? ($user['email'] ?? 'System'),
    'created_on' => etds_qc_now(),
    'updated_on' => etds_qc_now(),
    'last_action' => 'case_created',
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'case.json'), $session);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'client.json'), $client);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'deductor.json'), [
    'tan' => $client['tan'],
    'pan' => $client['pan'],
    'entity_type' => $client['entity_type'],
  ]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'documents.json'), etds_qc_default_case_documents());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'payments.json'), etds_qc_default_payments());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'extraction.json'), etds_qc_default_extraction());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'ocr.json'), etds_qc_default_ocr());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'validation.json'), etds_qc_default_validation());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'corrections.json'), etds_qc_default_corrections());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'doctor.json'), etds_qc_default_doctor());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'qc.json'), etds_qc_default_qc());
  etds_qc_write_json(etds_qc_session_file($sessionId, 'audit.json'), []);
  etds_qc_audit($sessionId, $user, 'case_created', 'Case created', [], $session);
  return $session;
}

function etds_qc_save_session(array $session): void {
  $session['updated_on'] = etds_qc_now();
  $statusMeta = etds_qc_case_status_meta((string) ($session['status'] ?? 'draft'));
  $session['status_label'] = $statusMeta['label'];
  $session['progress'] = $statusMeta['progress'];
  etds_qc_write_json(etds_qc_session_file((string) $session['session_id'], 'case.json'), $session);
}

function etds_qc_audit(string $sessionId, array $user, string $action, string $event, array $oldValue = [], array $newValue = []): void {
  $file = etds_qc_session_file($sessionId, 'audit.json');
  $events = etds_qc_load_json($file, []);
  if (!is_array($events)) {
    $events = [];
  }
  $sequence = etds_qc_next_counter('audit_seq', etds_qc_normalize_case_id($sessionId));
  $timestamp = etds_qc_now();
  try {
    $date = (new DateTimeImmutable($timestamp))->format('Y-m-d');
    $time = (new DateTimeImmutable($timestamp))->format('H:i:s');
  } catch (Throwable) {
    $date = '';
    $time = '';
  }
  $events[] = [
    'event_id' => 'AUD-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
    'session_id' => etds_qc_normalize_case_id($sessionId),
    'case_id' => etds_qc_normalize_case_id($sessionId),
    'user_id' => $user['id'] ?? 'system',
    'user_name' => $user['name'] ?? ($user['email'] ?? 'System'),
    'action' => $action,
    'event' => $event,
    'old_value' => $oldValue,
    'new_value' => $newValue,
    'ip' => etds_qc_client_ip(),
    'date' => $date,
    'time' => $time,
    'timestamp' => $timestamp,
  ];
  etds_qc_write_json($file, $events);
}

function etds_qc_case_client(string $sessionId): array {
  $client = etds_qc_load_json(etds_qc_session_file($sessionId, 'client.json'), []);
  return is_array($client) ? $client : [];
}

function etds_qc_case_documents(string $sessionId): array {
  $documents = etds_qc_load_json(etds_qc_session_file($sessionId, 'documents.json'), etds_qc_default_case_documents());
  return is_array($documents) ? $documents : etds_qc_default_case_documents();
}

function etds_qc_case_update_status(string $sessionId, string $status, array $user, string $event = 'Case status updated'): void {
  $session = etds_qc_find_session($sessionId);
  if (!$session) {
    return;
  }
  $old = ['status' => $session['status'] ?? 'draft'];
  $session['status'] = $status;
  $session['last_action'] = $status;
  etds_qc_save_session($session);
  etds_qc_audit($sessionId, $user, 'case_status_updated', $event, $old, ['status' => $status]);
}

function etds_qc_detect_document_type(string $extension, string $mimeType = ''): string {
  $extension = strtolower($extension);
  return match (true) {
    in_array($extension, ['xlsx', 'xls', 'csv'], true) => 'Excel',
    $extension === 'pdf' && str_contains(strtolower($mimeType), 'image') => 'Scanned PDF',
    $extension === 'pdf' => 'PDF',
    in_array($extension, ['png', 'jpg', 'jpeg'], true) => 'Images',
    $extension === 'zip' => 'ZIP',
    default => strtoupper($extension),
  };
}

function etds_qc_document_preview_allowed(array $document): bool {
  $type = strtolower((string) ($document['document_type'] ?? ''));
  return in_array($type, ['pdf', 'scanned pdf', 'images'], true);
}

function etds_qc_register_uploads(string $sessionId, array $fileBag, array $user, string $uploadCategory = ''): array {
  $session = etds_qc_find_session($sessionId);
  if (!$session) {
    return ['uploaded' => 0, 'duplicates' => 0, 'versions' => 0];
  }

  etds_qc_ensure_session_structure($sessionId);
  $register = etds_qc_case_documents($sessionId);
  $documents = is_array($register['documents'] ?? null) ? $register['documents'] : [];
  $names = $fileBag['name'] ?? [];
  $tmpNames = $fileBag['tmp_name'] ?? [];
  $sizes = $fileBag['size'] ?? [];
  $errors = $fileBag['error'] ?? [];
  $uploaded = 0;
  $duplicates = 0;
  $versions = 0;

  foreach ($names as $index => $originalName) {
    if (($errors[$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
      continue;
    }
    $extension = strtolower(pathinfo((string) $originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, etds_qc_allowed_extensions(), true)) {
      continue;
    }
    if ((int) ($sizes[$index] ?? 0) > ETDS_QC_MAX_UPLOAD_BYTES) {
      continue;
    }
    $tmpPath = (string) ($tmpNames[$index] ?? '');
    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
      continue;
    }

    $contentHash = sha1_file($tmpPath) ?: '';
    $normalizedOriginalName = strtolower(preg_replace('/[^a-z0-9]+/', '-', pathinfo((string) $originalName, PATHINFO_FILENAME)) ?? 'document');
    $related = array_values(array_filter($documents, static fn(array $document): bool => strtolower((string) ($document['original_name_slug'] ?? '')) === $normalizedOriginalName));
    $versionNumber = count($related) + 1;
    $duplicateOf = null;
    foreach ($documents as $document) {
      if (($document['content_hash'] ?? '') === $contentHash && ($document['is_removed'] ?? false) !== true) {
        $duplicateOf = (string) ($document['document_id'] ?? '');
        break;
      }
    }

    $documentId = 'DOC-' . str_pad((string) (count($documents) + 1), 6, '0', STR_PAD_LEFT);
    $safeName = $documentId . '_v' . str_pad((string) $versionNumber, 2, '0', STR_PAD_LEFT) . '.' . $extension;
    $target = etds_qc_session_file($sessionId, 'uploads/original/' . $safeName);
    if (!move_uploaded_file($tmpPath, $target)) {
      continue;
    }

    $mimeType = etds_qc_detect_mime_type($target);
    $documents[] = [
      'document_id' => $documentId,
      'file_id' => $documentId,
      'file_name' => $safeName,
      'stored_name' => $safeName,
      'original_name' => basename((string) $originalName),
      'original_name_slug' => $normalizedOriginalName,
      'document_type' => etds_qc_detect_document_type($extension, $mimeType),
      'upload_category' => $uploadCategory !== '' ? $uploadCategory : null,
      'upload_time' => etds_qc_now(),
      'uploaded_on' => etds_qc_now(),
      'uploaded_by' => $user['id'] ?? 'system',
      'uploaded_by_name' => $user['name'] ?? ($user['email'] ?? 'System'),
      'ocr_status' => 'Pending',
      'extraction_status' => 'Pending',
      'validation_status' => 'Pending',
      'remarks' => $duplicateOf !== null ? 'Duplicate detected against ' . $duplicateOf : 'Received in intake queue',
      'size_bytes' => (int) ($sizes[$index] ?? 0),
      'mime_type' => $mimeType,
      'extension' => $extension,
      'content_hash' => $contentHash,
      'version_number' => $versionNumber,
      'version_group' => $normalizedOriginalName,
      'duplicate_of' => $duplicateOf,
      'is_duplicate' => $duplicateOf !== null,
      'is_removed' => false,
    ];
    $uploaded++;
    if ($duplicateOf !== null) {
      $duplicates++;
    }
    if ($versionNumber > 1) {
      $versions++;
    }
  }

  $register['documents'] = $documents;
  $register['summary'] = [
    'document_count' => count(array_filter($documents, static fn(array $document): bool => ($document['is_removed'] ?? false) !== true)),
    'duplicate_count' => count(array_filter($documents, static fn(array $document): bool => ($document['is_duplicate'] ?? false) === true && ($document['is_removed'] ?? false) !== true)),
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'documents.json'), $register);

  if ($uploaded > 0) {
    etds_qc_case_update_status($sessionId, 'documents_received', $user, 'Documents received');
  }
  etds_qc_audit($sessionId, $user, 'documents_uploaded', 'Documents uploaded', [], [
    'uploaded' => $uploaded,
    'duplicates' => $duplicates,
    'versions' => $versions,
  ]);

  return ['uploaded' => $uploaded, 'duplicates' => $duplicates, 'versions' => $versions];
}

function etds_qc_toggle_favourite_case(string $sessionId, array $user): ?array {
  $session = etds_qc_find_session($sessionId);
  if (!$session) {
    return null;
  }
  $session['is_favourite'] = !((bool) ($session['is_favourite'] ?? false));
  $session['last_action'] = 'favourite_toggled';
  etds_qc_save_session($session);
  etds_qc_audit($sessionId, $user, 'case_favourite_toggled', 'Favourite status changed', [], ['is_favourite' => $session['is_favourite']]);
  return $session;
}

function etds_qc_duplicate_case(string $sessionId, array $user): ?array {
  $sourceCase = etds_qc_find_session($sessionId);
  if (!$sourceCase) {
    return null;
  }
  $payload = etds_qc_case_client($sessionId) + [
    'client_name' => $sourceCase['client_name'] ?? '',
    'tan' => $sourceCase['tan'] ?? '',
    'financial_year' => $sourceCase['financial_year'] ?? '',
    'quarter' => $sourceCase['quarter'] ?? '',
    'return_type' => $sourceCase['return_type'] ?? '',
    'remarks' => 'Duplicated from ' . $sessionId,
  ];
  $newCase = etds_qc_create_session($payload, $user);
  etds_qc_audit($newCase['session_id'], $user, 'case_duplicated', 'Case duplicated', ['source_case_id' => $sessionId], ['new_case_id' => $newCase['session_id']]);
  return $newCase;
}

function etds_qc_search_sessions(array $filters = []): array {
  $query = strtolower(trim((string) ($filters['query'] ?? '')));
  $financialYear = strtolower(trim((string) ($filters['financial_year'] ?? '')));
  $quarter = strtolower(trim((string) ($filters['quarter'] ?? '')));
  $cases = etds_qc_all_sessions();

  return array_values(array_filter($cases, static function (array $case) use ($query, $financialYear, $quarter): bool {
    if (($case['is_deleted'] ?? false) === true) {
      return false;
    }
    if ($financialYear !== '' && strtolower((string) ($case['financial_year'] ?? '')) !== $financialYear) {
      return false;
    }
    if ($quarter !== '' && strtolower((string) ($case['quarter'] ?? '')) !== $quarter) {
      return false;
    }
    if ($query === '') {
      return true;
    }
    $haystack = strtolower(implode(' ', [
      (string) ($case['session_id'] ?? ''),
      (string) ($case['client_name'] ?? ''),
      (string) ($case['tan'] ?? ''),
      (string) ($case['pan'] ?? ''),
      (string) ($case['quarter'] ?? ''),
      (string) ($case['financial_year'] ?? ''),
    ]));
    return str_contains($haystack, $query);
  }));
}

function etds_qc_dashboard_counts(array $cases): array {
  $counts = [
    'open_cases' => 0,
    'qc_completed' => 0,
    'pending_validation' => 0,
    'pending_reconciliation' => 0,
    'ready_for_return_preparation' => 0,
    'archived' => 0,
  ];
  foreach ($cases as $case) {
    $status = (string) ($case['status'] ?? 'draft');
    if (($case['is_deleted'] ?? false) === true) {
      continue;
    }
    if ($status !== 'archived') {
      $counts['open_cases']++;
    }
    if (in_array($status, ['documents_received', 'extraction_running', 'validation_running'], true)) {
      $counts['pending_validation']++;
    }
    if ($status === 'reconciliation_pending') {
      $counts['pending_reconciliation']++;
    }
    if ($status === 'qc_completed') {
      $counts['qc_completed']++;
    }
    if ($status === 'ready_for_return_preparation') {
      $counts['ready_for_return_preparation']++;
    }
    if ($status === 'archived') {
      $counts['archived']++;
    }
  }
  return $counts;
}

function etds_qc_case_report_rows(string $sessionId, string $type): array {
  $session = etds_qc_find_session($sessionId);
  if (!$session) {
    return [[], []];
  }
  return match ($type) {
    'case_summary' => [
      ['Case Number', 'Client Name', 'Client Code', 'TAN', 'PAN', 'FY', 'Quarter', 'Status', 'Progress'],
      [[
        $session['session_id'] ?? '',
        $session['client_name'] ?? '',
        $session['client_code'] ?? '',
        $session['tan'] ?? '',
        $session['pan'] ?? '',
        $session['financial_year'] ?? '',
        $session['quarter'] ?? '',
        $session['status_label'] ?? '',
        (string) ($session['progress'] ?? 0),
      ]],
    ],
    'document_register' => (function () use ($sessionId): array {
      $documents = etds_qc_case_documents($sessionId);
      $rows = [];
      foreach (($documents['documents'] ?? []) as $document) {
        $rows[] = [
          $document['document_id'] ?? '',
          $document['file_name'] ?? '',
          $document['original_name'] ?? '',
          $document['document_type'] ?? '',
          $document['upload_time'] ?? '',
          $document['uploaded_by_name'] ?? '',
          $document['ocr_status'] ?? '',
          $document['extraction_status'] ?? '',
          $document['validation_status'] ?? '',
          $document['remarks'] ?? '',
        ];
      }
      return [['Document ID', 'File Name', 'Original Name', 'Document Type', 'Upload Time', 'Uploaded By', 'OCR Status', 'Extraction Status', 'Validation Status', 'Remarks'], $rows];
    })(),
    'upload_summary' => (function () use ($sessionId): array {
      $documents = etds_qc_case_documents($sessionId);
      $summary = $documents['summary'] ?? [];
      return [['Metric', 'Value'], [
        ['Documents Received', (string) ($summary['document_count'] ?? 0)],
        ['Duplicates', (string) ($summary['duplicate_count'] ?? 0)],
      ]];
    })(),
    'extraction_summary' => (function () use ($sessionId): array {
      $extraction = etds_qc_load_json(etds_qc_session_file($sessionId, 'extraction.json'), etds_qc_default_extraction());
      $summary = $extraction['summary'] ?? [];
      return [['Metric', 'Value'], [
        ['Documents Processed', (string) ($summary['documents_processed'] ?? 0)],
        ['Documents Pending Review', (string) ($summary['documents_pending_review'] ?? 0)],
        ['Documents Failed', (string) ($summary['documents_failed'] ?? 0)],
        ['Fields Extracted', (string) ($summary['fields_extracted'] ?? 0)],
        ['Fields Missing', (string) ($summary['fields_missing'] ?? 0)],
        ['Overall Confidence', (string) ($summary['overall_confidence'] ?? 0) . '%'],
      ]];
    })(),
    'ocr_summary' => (function () use ($sessionId): array {
      $ocr = etds_qc_load_json(etds_qc_session_file($sessionId, 'ocr.json'), etds_qc_default_ocr());
      $rows = [];
      foreach (($ocr['documents'] ?? []) as $document) {
        $rows[] = [
          $document['document_id'] ?? '',
          $document['document_name'] ?? '',
          $document['classification'] ?? '',
          $document['mode'] ?? '',
          (string) count((array) ($document['pages'] ?? [])),
        ];
      }
      return [['Document ID', 'Document Name', 'Classification', 'OCR Mode', 'Pages'], $rows];
    })(),
    'classification_report' => (function () use ($sessionId): array {
      $documents = etds_qc_case_documents($sessionId);
      $rows = [];
      foreach (($documents['documents'] ?? []) as $document) {
        if (($document['is_removed'] ?? false) === true) {
          continue;
        }
        $rows[] = [
          $document['document_id'] ?? '',
          $document['original_name'] ?? '',
          $document['classification'] ?? '',
          (string) ($document['classification_confidence'] ?? 0) . '%',
          $document['extraction_status'] ?? '',
        ];
      }
      return [['Document ID', 'Original Name', 'Classification', 'Classification Confidence', 'Extraction Status'], $rows];
    })(),
    'confidence_report' => (function () use ($sessionId): array {
      $extraction = etds_qc_load_json(etds_qc_session_file($sessionId, 'extraction.json'), etds_qc_default_extraction());
      $rows = [];
      foreach (($extraction['documents'] ?? []) as $document) {
        $rows[] = [
          $document['document_id'] ?? '',
          $document['classification'] ?? '',
          (string) ($document['overall_confidence'] ?? 0) . '%',
          (string) ($document['fields_extracted'] ?? 0),
          (string) ($document['fields_missing'] ?? 0),
        ];
      }
      return [['Document ID', 'Classification', 'Overall Confidence', 'Fields Extracted', 'Fields Missing'], $rows];
    })(),
    'doctor_diagnosis_report' => (function () use ($sessionId): array {
      $doctor = etds_qc_load_json(etds_qc_session_file($sessionId, 'doctor.json'), etds_qc_default_doctor());
      $rows = [];
      foreach (($doctor['diagnosis'] ?? []) as $diagnosis) {
        $rows[] = [
          $diagnosis['diagnosis_id'] ?? '',
          $diagnosis['diagnosis'] ?? '',
          $diagnosis['priority'] ?? '',
          (string) ($diagnosis['affected_record_count'] ?? 0),
          $diagnosis['likely_cause'] ?? '',
          $diagnosis['estimated_impact'] ?? '',
        ];
      }
      return [['Diagnosis ID', 'Diagnosis', 'Priority', 'Affected Records', 'Likely Cause', 'Estimated Impact'], $rows];
    })(),
    'doctor_prescription_report' => (function () use ($sessionId): array {
      $doctor = etds_qc_load_json(etds_qc_session_file($sessionId, 'doctor.json'), etds_qc_default_doctor());
      $rows = [];
      foreach (($doctor['prescription'] ?? []) as $prescription) {
        $rows[] = [
          $prescription['prescription_id'] ?? '',
          $prescription['priority_label'] ?? '',
          $prescription['priority'] ?? '',
          $prescription['instruction'] ?? '',
          (string) ($prescription['estimated_time_minutes'] ?? 0),
          (string) ($prescription['expected_health_score_before'] ?? 0) . ' -> ' . (string) ($prescription['expected_health_score_after'] ?? 0),
        ];
      }
      return [['Prescription ID', 'Priority Label', 'Priority', 'Instruction', 'Estimated Time (Minutes)', 'Expected Health Score'], $rows];
    })(),
    'doctor_health_score_report' => (function () use ($sessionId): array {
      $doctor = etds_qc_load_json(etds_qc_session_file($sessionId, 'doctor.json'), etds_qc_default_doctor());
      $scores = is_array($doctor['health_scores'] ?? null) ? $doctor['health_scores'] : [];
      return [['Metric', 'Value'], [
        ['Extraction Score', (string) ($scores['extraction_score'] ?? 0)],
        ['Validation Score', (string) ($scores['validation_score'] ?? 0)],
        ['Completeness Score', (string) ($scores['completeness_score'] ?? 0)],
        ['Consistency Score', (string) ($scores['consistency_score'] ?? 0)],
        ['Overall Data Health Score', (string) ($scores['overall_data_health_score'] ?? 0)],
      ]];
    })(),
    'doctor_readiness_report' => (function () use ($sessionId): array {
      $doctor = etds_qc_load_json(etds_qc_session_file($sessionId, 'doctor.json'), etds_qc_default_doctor());
      $readiness = is_array($doctor['readiness'] ?? null) ? $doctor['readiness'] : [];
      $summary = is_array($doctor['summary'] ?? null) ? $doctor['summary'] : [];
      return [['Metric', 'Value'], [
        ['Readiness Status', (string) ($readiness['status'] ?? 'Not Ready')],
        ['Reason', (string) ($readiness['reason'] ?? '')],
        ['Top Priority', (string) ($summary['top_priority'] ?? 'Information')],
        ['Top Diagnosis', (string) ($summary['top_diagnosis'] ?? 'Diagnosis Pending')],
        ['Estimated Time Minutes', (string) ($summary['estimated_time_minutes'] ?? 0)],
      ]];
    })(),
    'correction_log' => (function () use ($sessionId): array {
      $corrections = etds_qc_workspace_corrections($sessionId);
      $rows = [];
      foreach (($corrections['history'] ?? []) as $change) {
        $rows[] = [
          $change['change_id'] ?? '',
          $change['sheet'] ?? '',
          $change['record_id'] ?? '',
          $change['field'] ?? '',
          $change['old_value'] ?? '',
          $change['new_value'] ?? '',
          $change['user_name'] ?? '',
          $change['timestamp'] ?? '',
          $change['reason'] ?? '',
          $change['mode'] ?? '',
        ];
      }
      return [['Change ID', 'Sheet', 'Record ID', 'Field', 'Old Value', 'New Value', 'User', 'Timestamp', 'Reason', 'Mode'], $rows];
    })(),
    'field_change_report' => (function () use ($sessionId): array {
      $corrections = etds_qc_workspace_corrections($sessionId);
      $rows = [];
      foreach (($corrections['cell_states'] ?? []) as $key => $state) {
        $parts = explode('::', (string) $key);
        $rows[] = [
          $parts[0] ?? '',
          $parts[1] ?? '',
          $parts[2] ?? '',
          $state['original_value'] ?? '',
          $state['current_value'] ?? '',
          $state['mode'] ?? '',
          $state['updated_on'] ?? '',
        ];
      }
      return [['Sheet', 'Record ID', 'Field', 'Original Value', 'Current Value', 'Status', 'Updated On'], $rows];
    })(),
    'user_activity' => (function () use ($sessionId): array {
      $audit = etds_qc_load_json(etds_qc_session_file($sessionId, 'audit.json'), []);
      $rows = [];
      foreach ($audit as $entry) {
        if (!in_array((string) ($entry['action'] ?? ''), ['correction_applied', 'suggestion_ignored', 'issue_resolved', 'issue_open'], true) && !str_starts_with((string) ($entry['action'] ?? ''), 'issue_')) {
          continue;
        }
        $rows[] = [
          $entry['user_name'] ?? '',
          $entry['action'] ?? '',
          $entry['event'] ?? '',
          $entry['timestamp'] ?? '',
        ];
      }
      return [['User', 'Action', 'Event', 'Timestamp'], $rows];
    })(),
    'challan_reconciliation_report' => (function () use ($sessionId): array {
      $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation());
      $rows = [];
      foreach (($reconciliation['challan_reconciliation']['rows'] ?? []) as $row) {
        $rows[] = [
          $row['challan_reference'] ?? '',
          (string) ($row['available_amount'] ?? 0),
          (string) ($row['allocated_amount'] ?? 0),
          (string) ($row['unused_amount'] ?? 0),
          (string) ($row['short_allocation'] ?? 0),
          (string) ($row['over_allocation'] ?? 0),
          (string) ($row['match_percent'] ?? 0) . '%',
          $row['reconciliation_status'] ?? '',
        ];
      }
      return [['Challan Reference', 'Available Amount', 'Allocated Amount', 'Unused Amount', 'Short Allocation', 'Over Allocation', 'Match %', 'Status'], $rows];
    })(),
    'deductee_reconciliation_report' => (function () use ($sessionId): array {
      $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation());
      $rows = [];
      foreach (($reconciliation['deductee_reconciliation']['rows'] ?? []) as $row) {
        $rows[] = [
          $row['deductee_name'] ?? '',
          $row['pan'] ?? '',
          (string) ($row['tds_amount'] ?? 0),
          $row['payment_status'] ?? '',
          $row['challan_status'] ?? '',
          $row['reconciliation_status'] ?? '',
        ];
      }
      return [['Deductee Name', 'PAN', 'TDS Amount', 'Payment Status', 'Challan Status', 'Status'], $rows];
    })(),
    'salary_reconciliation_report' => (function () use ($sessionId): array {
      $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation());
      $summary = (array) ($reconciliation['salary_reconciliation']['summary'] ?? []);
      return [['Metric', 'Value'], [
        ['Salary Total', (string) ($summary['salary_total'] ?? 0)],
        ['Tax Deducted', (string) ($summary['tax_deducted'] ?? 0)],
        ['Tax Deposited', (string) ($summary['tax_deposited'] ?? 0)],
        ['Variance', (string) ($summary['variance'] ?? 0)],
      ]];
    })(),
    'quarter_reconciliation_report' => (function () use ($sessionId): array {
      $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation());
      $summary = (array) ($reconciliation['quarter_reconciliation']['summary'] ?? []);
      $rows = [];
      foreach ((array) ($summary['monthly_totals'] ?? []) as $month => $total) {
        $rows[] = [(string) $month, (string) $total];
      }
      return [['Month', 'Quarter Total Contribution'], $rows];
    })(),
    'financial_health_report' => (function () use ($sessionId): array {
      $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation());
      $summary = (array) ($reconciliation['summary'] ?? []);
      return [['Metric', 'Value'], [
        ['Challan Score', (string) ($summary['challan_score'] ?? 0)],
        ['Deductee Score', (string) ($summary['deductee_score'] ?? 0)],
        ['Salary Score', (string) ($summary['salary_score'] ?? 0)],
        ['Quarter Score', (string) ($summary['quarter_score'] ?? 0)],
        ['Financial Health Score', (string) ($summary['financial_health_score'] ?? 0)],
      ]];
    })(),
    'audit_report' => (function () use ($sessionId): array {
      $audit = etds_qc_load_json(etds_qc_session_file($sessionId, 'audit.json'), []);
      $rows = [];
      foreach ($audit as $event) {
        $rows[] = [
          $event['date'] ?? '',
          $event['time'] ?? '',
          $event['user_name'] ?? '',
          $event['action'] ?? '',
          json_encode($event['old_value'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
          json_encode($event['new_value'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
          $event['ip'] ?? '',
        ];
      }
      return [['Date', 'Time', 'User', 'Action', 'Old Value', 'New Value', 'IP'], $rows];
    })(),
    default => [[], []],
  };
}

function etds_qc_allowed_extensions(): array {
  return etds_qc_config()['allowed_extensions'] ?? ['xlsx', 'xls', 'csv', 'pdf', 'png', 'jpg', 'jpeg'];
}

function etds_qc_is_image_extension(string $extension): bool {
  return in_array(strtolower($extension), ['png', 'jpg', 'jpeg'], true);
}

function etds_qc_normalize_header(string $header): string {
  $slug = strtolower(trim($header));
  $slug = preg_replace('/[^a-z0-9]+/', '_', $slug) ?? '';
  $map = ['name' => 'deductee_name', 'deductee' => 'deductee_name', 'deductee_name' => 'deductee_name', 'pan' => 'pan', 'pan_no' => 'pan', 'pan_number' => 'pan', 'amount' => 'tds_amount', 'tds' => 'tds_amount', 'tds_amount' => 'tds_amount', 'date' => 'deduction_date', 'deduction_date' => 'deduction_date', 'invoice' => 'invoice_number', 'invoice_no' => 'invoice_number', 'invoice_number' => 'invoice_number', 'challan' => 'challan_reference', 'challan_ref' => 'challan_reference', 'challan_reference' => 'challan_reference'];
  return $map[$slug] ?? $slug;
}

function etds_qc_tabular_rows_to_records(array $rows): array {
  $header = [];
  $records = [];
  foreach ($rows as $rowIndex => $row) {
    $normalizedRow = [];
    foreach ((array) $row as $index => $value) {
      $normalizedRow[(int) $index] = trim((string) $value);
    }
    ksort($normalizedRow);
    $values = array_values($normalizedRow);
    if ($rowIndex === 0) {
      $header = array_map('etds_qc_normalize_header', $values);
      continue;
    }
    if ($header === []) {
      continue;
    }
    $record = [];
    foreach ($header as $index => $columnName) {
      $record[$columnName] = $values[$index] ?? '';
    }
    if (implode('', $record) === '') {
      continue;
    }
    $records[] = $record;
  }
  return ['columns' => $header, 'records' => $records];
}

function etds_qc_extract_csv(string $path): array {
  $rows = [];
  if (($handle = fopen($path, 'rb')) !== false) {
    while (($row = fgetcsv($handle)) !== false) {
      $rows[] = $row;
    }
    fclose($handle);
  }
  return etds_qc_tabular_rows_to_records($rows);
}

function etds_qc_column_index(string $letters): int {
  $index = 0;
  foreach (str_split($letters) as $letter) {
    $index = ($index * 26) + (ord($letter) - 64);
  }
  return $index - 1;
}

function etds_qc_xlsx_cell_text(SimpleXMLElement $cell, array $sharedStrings): string {
  $type = (string) ($cell['t'] ?? '');
  if ($type === 's') {
    $index = (int) ($cell->v ?? 0);
    return trim((string) ($sharedStrings[$index] ?? ''));
  }
  if ($type === 'inlineStr' && isset($cell->is)) {
    $parts = [];
    foreach ($cell->is->children() as $child) {
      if ($child->getName() === 't') {
        $parts[] = (string) $child;
        continue;
      }
      if ($child->getName() === 'r') {
        $parts[] = (string) ($child->t ?? '');
      }
    }
    return trim(implode('', $parts));
  }
  return trim((string) ($cell->v ?? ''));
}

function etds_qc_extract_xlsx_rows(string $path): array {
  if (!class_exists('ZipArchive')) {
    return [];
  }
  $zip = new ZipArchive();
  if ($zip->open($path) !== true) {
    return [];
  }
  $sharedStrings = [];
  $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
  if (is_string($sharedXml) && $sharedXml !== '') {
    $xml = @simplexml_load_string($sharedXml);
    if ($xml) {
      foreach ($xml->si as $item) {
        if (isset($item->t)) {
          $sharedStrings[] = trim((string) $item->t);
          continue;
        }
        $parts = [];
        foreach ($item->r as $run) {
          $parts[] = (string) ($run->t ?? '');
        }
        $sharedStrings[] = trim(implode('', $parts));
      }
    }
  }
  $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
  $zip->close();
  if (!is_string($sheetXml) || $sheetXml === '') {
    return [];
  }
  $xml = @simplexml_load_string($sheetXml);
  if (!$xml || !isset($xml->sheetData)) {
    return [];
  }
  $rows = [];
  foreach ($xml->sheetData->row as $rowNode) {
    $cells = [];
    foreach ($rowNode->c as $cell) {
      preg_match('/([A-Z]+)/', (string) ($cell['r'] ?? 'A'), $m);
      $index = etds_qc_column_index($m[1] ?? 'A');
      $cells[$index] = etds_qc_xlsx_cell_text($cell, $sharedStrings);
    }
    if ($cells !== []) {
      ksort($cells);
      $rows[] = ['row_number' => (int) ($rowNode['r'] ?? (count($rows) + 1)), 'cells' => $cells];
    }
  }
  return $rows;
}

function etds_qc_extract_salary_register_rows(array $rows): array {
  $records = [];
  $current = null;
  $headerDetected = false;

  $finalize = static function (?array $record) use (&$records): void {
    if (!$record) {
      return;
    }
    $name = trim((string) ($record['deductee_name'] ?? ''));
    $pan = strtoupper(trim((string) ($record['pan'] ?? '')));
    $amount = trim((string) ($record['tds_amount'] ?? ''));
    if ($name === '' && $pan === '' && $amount === '') {
      return;
    }
    $record['deductee_name'] = preg_replace('/\s+/', ' ', $name) ?? $name;
    $records[] = $record;
  };

  foreach ($rows as $row) {
    $cells = (array) ($row['cells'] ?? []);
    $a = trim((string) ($cells[0] ?? ''));
    $b = trim((string) ($cells[1] ?? ''));
    $c = trim((string) ($cells[2] ?? ''));
    $d = trim((string) ($cells[3] ?? ''));
    $e = trim((string) ($cells[4] ?? ''));
    $rowText = strtolower(trim(implode(' ', array_filter([$a, $b, $c, $d, $e], static fn(string $value): bool => $value !== ''))));

    if (!$headerDetected) {
      if (strtoupper($a) === 'NAME' && strtoupper($b) === 'PAN') {
        $headerDetected = true;
      }
      continue;
    }

    if ($rowText === '') {
      $finalize($current);
      $current = null;
      continue;
    }

    $panCandidate = strtoupper(str_replace(' ', '', $b));
    $startsRecord = $a !== ''
      && !in_array(strtoupper($a), ['NAME', 'PAN', 'TAN'], true)
      && preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $panCandidate) === 1;
    if ($startsRecord) {
      $finalize($current);
      $current = [
        'deductee_name' => $a,
        'pan' => $panCandidate,
        'tds_amount' => '',
        'invoice_number' => '',
        'challan_reference' => '',
      ];
      continue;
    }

    if ($current === null) {
      continue;
    }

    if ($a !== '' && $current['pan'] !== '' && $b === '' && stripos($rowText, 'tds') === false) {
      $current['deductee_name'] = trim($current['deductee_name'] . ' ' . $a);
    }

    if (stripos($rowText, 'tds') !== false) {
      $amount = '';
      foreach ([$e, $d, $c] as $candidate) {
        if ($candidate !== '' && is_numeric(str_replace(',', '', $candidate))) {
          $amount = str_replace(',', '', $candidate);
          break;
        }
      }
      $current['tds_amount'] = $amount;
    }
  }

  $finalize($current);

  if ($records === []) {
    return ['columns' => [], 'records' => []];
  }

  return [
    'columns' => ['deductee_name', 'pan', 'tds_amount', 'invoice_number', 'challan_reference'],
    'records' => $records,
  ];
}

function etds_qc_extract_quarterly_salary_rows(array $rows): array {
  $records = [];
  $headerRow = -1;
  $subHeaderRow = -1;
  $schoolName = '';
  $period = '';

  foreach ($rows as $row) {
    $cells = (array) ($row['cells'] ?? []);
    $rowNum = (int) ($row['row_number'] ?? 0);
    $rowText = strtolower(trim(implode(' ', array_filter($cells, static fn($v): bool => trim((string) $v) !== ''))));

    if (str_contains($rowText, 'school') || str_contains($rowText, 'quarter statement')) {
      if (str_contains($rowText, 'school')) {
        $schoolName = trim((string) ($cells[0] ?? ''));
      }
      if (str_contains($rowText, 'quarter statement') || str_contains($rowText, 'march') || str_contains($rowText, 'june')) {
        $period = trim((string) ($cells[0] ?? ''));
      }
      continue;
    }

    if (str_contains($rowText, 'name of the staff') || str_contains($rowText, 'pan number')) {
      $headerRow = $rowNum;
      continue;
    }

    if (str_contains($rowText, 'gross salary') || str_contains($rowText, 'income tax')) {
      $subHeaderRow = $rowNum;
      continue;
    }

    if ($headerRow > 0 && $rowNum > $headerRow) {
      $a = trim((string) ($cells[0] ?? ''));
      $b = trim((string) ($cells[1] ?? ''));
      $c = trim((string) ($cells[2] ?? ''));
      $d = trim((string) ($cells[3] ?? ''));

      if ($a === '' && $b === '' && $c === '' && $d === '') {
        continue;
      }

      if (strtolower($a) === 'total' || strtolower($b) === 'total') {
        continue;
      }

      $panCandidate = strtoupper(preg_replace('/\s+/', '', $d));
      $hasPan = preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $panCandidate) === 1;
      $hasName = $b !== '' && !in_array(strtolower($b), ['name of the staff', 'name', 'pan', 'des', ''], true);

      if (!$hasName && !$hasPan) {
        continue;
      }

      $incomeTaxTotal = trim((string) ($cells[12] ?? ''));
      if ($incomeTaxTotal === '' || !is_numeric(str_replace(',', '', $incomeTaxTotal))) {
        $incomeTaxTotal = '0';
      }

      $grossTotal = trim((string) ($cells[13] ?? ''));
      if ($grossTotal === '' || !is_numeric(str_replace(',', '', $grossTotal))) {
        $grossTotal = '0';
      }

      $records[] = [
        'deductee_name' => preg_replace('/\s+/', ' ', $b) ?? $b,
        'designation' => $c,
        'pan' => $hasPan ? $panCandidate : '',
        'tds_amount' => str_replace(',', '', $incomeTaxTotal),
        'gross_salary' => str_replace(',', '', $grossTotal),
        'invoice_number' => '',
        'challan_reference' => '',
      ];
    }
  }

  if ($records === []) {
    return ['columns' => [], 'records' => [], 'school_name' => $schoolName, 'period' => $period];
  }

  return [
    'columns' => ['deductee_name', 'designation', 'pan', 'tds_amount', 'gross_salary', 'invoice_number', 'challan_reference'],
    'records' => $records,
    'school_name' => $schoolName,
    'period' => $period,
  ];
}

function etds_qc_extract_xlsx(string $path): array {
  $rows = etds_qc_extract_xlsx_rows($path);
  if ($rows === []) {
    return ['columns' => [], 'records' => []];
  }

  $salaryRegister = etds_qc_extract_salary_register_rows($rows);
  if (($salaryRegister['records'] ?? []) !== []) {
    return $salaryRegister;
  }

  $quarterlySalary = etds_qc_extract_quarterly_salary_rows($rows);
  if (($quarterlySalary['records'] ?? []) !== []) {
    return $quarterlySalary;
  }

  $flatRows = [];
  foreach ($rows as $row) {
    $flatRows[] = (array) ($row['cells'] ?? []);
  }
  return etds_qc_tabular_rows_to_records($flatRows);
}

function etds_qc_default_deduction_date(string $sessionId): string {
  $session = etds_qc_find_session($sessionId);
  if (!$session) {
    return '';
  }
  $financialYear = (string) ($session['financial_year'] ?? '');
  $quarter = strtoupper((string) ($session['quarter'] ?? ''));
  if (!preg_match('/^(\d{4})-(\d{2})$/', $financialYear, $match)) {
    return '';
  }
  $startYear = (int) $match[1];
  $endYear = (int) ('20' . $match[2]);

  return match ($quarter) {
    'Q1' => sprintf('%04d-06-30', $startYear),
    'Q2' => sprintf('%04d-09-30', $startYear),
    'Q3' => sprintf('%04d-12-31', $startYear),
    'Q4' => sprintf('%04d-03-31', $endYear),
    default => '',
  };
}

function etds_qc_extract_pdf_text(string $path): array {
  $raw = @file_get_contents($path);
  if (!is_string($raw) || $raw === '') {
    return ['columns' => [], 'records' => [], 'raw_text' => '', 'mode' => 'pdf_unavailable'];
  }
  preg_match_all('/\((.*?)\)\s*Tj/s', $raw, $matches);
  $text = trim(preg_replace('/\s+/u', ' ', implode(' ', $matches[1] ?? [])) ?? '');
  return ['columns' => [], 'records' => [], 'raw_text' => $text, 'mode' => $text !== '' ? 'pdf_text' : 'pdf_needs_manual_review'];
}

function etds_qc_extract_image_text(string $path): array {
  $tesseract = 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';
  if (!is_file($tesseract)) {
    return ['columns' => [], 'records' => [], 'raw_text' => '', 'mode' => 'ocr_unavailable'];
  }
  $tempBase = tempnam(sys_get_temp_dir(), 'etds_qc_ocr_');
  if ($tempBase === false) {
    return ['columns' => [], 'records' => [], 'raw_text' => '', 'mode' => 'ocr_temp_failed'];
  }
  @unlink($tempBase);

  $text = '';
  $exitCode = 1;

  @exec('"' . $tesseract . '" "' . $path . '" "' . $tempBase . '" --psm 1', $output, $exitCode);
  $textPath = $tempBase . '.txt';
  $text = is_file($textPath) ? trim((string) file_get_contents($textPath)) : '';
  if (is_file($textPath)) { @unlink($textPath); }

  if ($text === '' || mb_strlen($text) < 10) {
    @exec('"' . $tesseract . '" "' . $path . '" "' . $tempBase . '" --psm 6', $output, $exitCode);
    $textPath = $tempBase . '.txt';
    $text = is_file($textPath) ? trim((string) file_get_contents($textPath)) : '';
    if (is_file($textPath)) { @unlink($textPath); }
  }

  $rows = [];
  if ($text !== '' && preg_match('/,|;|\t/', $text)) {
    foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $line) {
      if (trim($line) !== '') {
        $rows[] = str_getcsv(str_replace("\t", ',', $line));
      }
    }
  }
  $tabular = $rows !== [] ? etds_qc_tabular_rows_to_records($rows) : ['columns' => [], 'records' => []];
  $tabular['raw_text'] = $text;
  $tabular['mode'] = ($exitCode === 0 && $text !== '') ? 'ocr_text' : 'ocr_review_required';
  return $tabular;
}

function etds_qc_extract_text_tokens(string $text): array {
  $text = strtoupper($text);
  preg_match_all('/[A-Z0-9\/.-]+/', $text, $matches);
  return $matches[0] ?? [];
}

function etds_qc_extract_first_match(string $pattern, string $text): string {
  return preg_match($pattern, $text, $matches) === 1 ? trim((string) ($matches[1] ?? $matches[0] ?? '')) : '';
}

function etds_qc_parse_challan_from_ocr_text(string $text): array {
  $fields = [];
  $confidence = 0;

  $tan = etds_qc_extract_first_match('/\b([A-Z]{4}[0-9]{5}[A-Z])\b/', $text);
  if ($tan !== '') { $fields['tan'] = $tan; $confidence += 20; }

  $crn = etds_qc_extract_first_match('/(?:CRN|Challan\s*Receipt\s*Number)[:\s-]*(\d{10,20})\b/i', $text);
  if ($crn === '') { $crn = etds_qc_extract_first_match('/\b(\d{12,15})\b/', $text); }
  if ($crn !== '') { $fields['crn'] = $crn; $confidence += 10; }

  $taxYear = etds_qc_extract_first_match('/(?:Tax\s*Year|Financial\s*Year)[:\s-]*(\d{4}-\d{2,4})\b/i', $text);
  if ($taxYear === '') { $taxYear = etds_qc_extract_first_match('/\b(20\d{2}-\d{2})\b/', $text); }
  if ($taxYear !== '') { $fields['tax_year'] = $taxYear; $confidence += 10; }

  $amount = etds_qc_extract_first_match('/(?:Amount|₹|Rs\.?|INR)[:\s]*([0-9,\.]+)\b/i', $text);
  if ($amount === '') { $amount = etds_qc_extract_first_match('/\b([0-9]{1,3}(?:,[0-9]{2,3})+(?:\.[0-9]{2})?)\b/', $text); }
  if ($amount !== '') { $fields['amount'] = (float) str_replace(',', '', $amount); $confidence += 15; }

  $majorHead = etds_qc_extract_first_match('/(?:Major\s*Head)[:\s-]*(\d{4})\b/i', $text);
  if ($majorHead === '') { $majorHead = etds_qc_extract_first_match('/\((\d{4})\)/', $text); }
  if ($majorHead !== '') { $fields['major_head'] = $majorHead; $confidence += 10; }

  $minorHead = etds_qc_extract_first_match('/(?:Minor\s*Head)[:\s-]*(\d{3})\b/i', $text);
  if ($minorHead !== '') { $fields['minor_head'] = $minorHead; $confidence += 8; }

  $natureOfPayment = etds_qc_extract_first_match('/(?:Nature\s*of\s*Payment)[:\s-]*(\d{4})\b/i', $text);
  if ($natureOfPayment !== '') { $fields['nature_of_payment'] = $natureOfPayment; $confidence += 10; }

  $paymentMode = etds_qc_extract_first_match('/(?:Payment\s*through|Mode\s*of\s*Payment)[:\s-]*(\w+)/i', $text);
  if ($paymentMode !== '') { $fields['payment_mode'] = ucfirst(strtolower($paymentMode)); $confidence += 5; }

  $bankName = etds_qc_extract_first_match('/(?:Drawn\s*on\s*Bank|Bank)[:\s-]*([A-Z][A-Za-z\s&]+?)(?:\s+(?:Branch|CIN|Date|\n)|$)/i', $text);
  if ($bankName !== '') { $fields['bank_name'] = trim($bankName); $confidence += 5; }

  $bsr = etds_qc_extract_first_match('/(?:BSR(?:\s*Code)?)[:\s-]*(\d{7})\b/i', $text);
  if ($bsr !== '') { $fields['bsr_code'] = $bsr; $confidence += 8; }

  $zao = etds_qc_extract_first_match('/(?:ZAO\s*Code)[:\s-]*(\d+)/i', $text);
  if ($zao !== '') { $fields['zao_code'] = $zao; $confidence += 5; }

  $sectionCode = etds_qc_extract_first_match('/(?:Section(?:\s*Code)?)[:\s-]*(194[A-Z]?)\b/i', $text);
  if ($sectionCode !== '') { $fields['section_code'] = $sectionCode; $confidence += 5; }

  return [
    'fields' => $fields,
    'confidence' => min(98, $confidence),
    'has_data' => count($fields) >= 2,
  ];
}

function etds_qc_confidence_from_presence(string $value, int $base = 70): int {
  if ($value === '') {
    return 0;
  }
  return max(50, min(99, $base));
}

function etds_qc_field_payload(string $field, string $value, int $confidence, int $sourcePage = 1): array {
  return [
    'field' => $field,
    'value' => $value,
    'confidence' => max(0, min(99, $confidence)),
    'source_page' => $sourcePage,
    'bounding_area' => null,
  ];
}

function etds_qc_document_classifier(string $documentName, string $text, array $columns = []): array {
  $haystack = strtoupper($documentName . ' ' . $text . ' ' . implode(' ', $columns));
  $rules = [
    'challan' => ['CHALLAN', 'BSR', 'CIN', 'OLTAS', 'CRN', 'TAX YEAR'],
    'deductee_list' => ['DEDUCTEE', 'PAN', 'DEDUCTEE NAME'],
    'salary_register' => ['SALARY', 'EMPLOYEE', 'BASIC', 'HRA', 'NAME OF THE STAFF', 'INCOME TAX', 'GROSS SALARY'],
    'payment_register' => ['PAYMENT', 'INVOICE', 'VENDOR'],
    'bank_challan' => ['BANK CHALLAN', 'BSR CODE'],
    'form_16' => ['FORM 16'],
    'form_16a' => ['FORM 16A'],
    'form_24q_working' => ['24Q', 'WORKING'],
    'form_26q_working' => ['26Q', 'WORKING'],
    'quarterly_tds' => ['QUARTER STATEMENT', 'QUARTERLY', 'PAN NUMBER', 'MONTH-WISE'],
  ];
  $best = ['classification' => 'unknown_document', 'confidence' => 30];
  foreach ($rules as $classification => $needles) {
    $matches = 0;
    foreach ($needles as $needle) {
      if (str_contains($haystack, $needle)) {
        $matches++;
      }
    }
    if ($matches === 0) {
      continue;
    }
    $confidence = min(98, 45 + ($matches * 18));
    if ($confidence > $best['confidence']) {
      $best = ['classification' => $classification, 'confidence' => $confidence];
    }
  }
  return $best;
}

function etds_qc_find_pdf_converter(): ?string {
  $paths = [
    'pdftoppm',
    __DIR__ . '/../../tools/poppler/poppler-24.02.0/Library/bin/pdftoppm.exe',
    'C:\\Program Files\\poppler\\bin\\pdftoppm.exe',
    'C:\\Program Files\\poppler-24.02.0\\bin\\pdftoppm.exe',
    'C:\\Program Files\\gs\\gs10.02.1\\bin\\gswin64c.exe',
    'C:\\Program Files\\gs\\gs9.56.1\\bin\\gswin64c.exe',
    'C:\\Program Files\\gs\\gs9.55.0\\bin\\gswin64c.exe',
    'C:\\Program Files\\gs\\gs10.03.0\\bin\\gswin64c.exe',
  ];
  foreach ($paths as $path) {
    if (is_file($path)) {
      return $path;
    }
  }
  $output = [];
  $exitCode = 0;
  @exec('where pdftoppm 2>nul', $output, $exitCode);
  if ($exitCode === 0 && !empty($output)) {
    return trim($output[0]);
  }
  return null;
}

function etds_qc_convert_pdf_to_images(string $pdfPath, string $sessionId, string $docId): array {
  $converter = etds_qc_find_pdf_converter();
  if ($converter === null) {
    return ['images' => [], 'error' => 'PDF-to-image conversion tool not available. Please install Poppler (pdftoppm) or Ghostscript.'];
  }

  $tempDir = etds_qc_session_dir($sessionId) . '/temp/ocr/' . $docId;
  if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0775, true);
  }

  $isGhostscript = stripos(basename($converter), 'gs') !== false;
  $images = [];

  if ($isGhostscript) {
    $outputPattern = $tempDir . '/page-%03d.png';
    @exec('"' . $converter . '" -dNOPAUSE -dBATCH -sDEVICE=png16m -r200 -sOutputFile="' . $outputPattern . '" "' . $pdfPath . '"', $output, $exitCode);
    if ($exitCode === 0) {
      foreach (glob($tempDir . '/page-*.png') ?: [] as $file) {
        $pageNum = 1;
        if (preg_match('/page-(\d+)\.png$/', $file, $m)) {
          $pageNum = (int) $m[1];
        }
        $images[] = ['page_number' => $pageNum, 'path' => $file];
      }
      usort($images, static fn(array $a, array $b): int => $a['page_number'] <=> $b['page_number']);
    }
  } else {
    $outputPattern = $tempDir . '/page';
    @exec('"' . $converter . '" -png -r200 "' . $pdfPath . '" "' . $outputPattern . '"', $output, $exitCode);
    if ($exitCode === 0) {
      foreach (glob($tempDir . '/page-*.png') ?: [] as $file) {
        $pageNum = 1;
        if (preg_match('/page-(\d+)\.png$/', $file, $m)) {
          $pageNum = (int) $m[1];
        }
        $images[] = ['page_number' => $pageNum, 'path' => $file];
      }
      usort($images, static fn(array $a, array $b): int => $a['page_number'] <=> $b['page_number']);
    }
  }

  if ($images === []) {
    return ['images' => [], 'error' => 'PDF-to-image conversion failed. The PDF may be corrupted or the converter encountered an error.'];
  }

  return ['images' => $images, 'error' => ''];
}

function etds_qc_extract_pdf_ocr_text(string $path): array {
  $tesseract = 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';
  if (!is_file($tesseract)) {
    return ['pages' => [], 'raw_text' => '', 'mode' => 'ocr_unavailable'];
  }
  $raw = @file_get_contents($path);
  if (!is_string($raw) || $raw === '') {
    return ['pages' => [], 'raw_text' => '', 'mode' => 'pdf_unavailable'];
  }
  preg_match_all('/\((.*?)\)\s*Tj/s', $raw, $matches);
  $text = trim(preg_replace('/\s+/u', ' ', implode(' ', $matches[1] ?? [])) ?? '');

  if ($text !== '' && mb_strlen($text) > 50) {
    return [
      'pages' => [['page_number' => 1, 'text' => $text]],
      'raw_text' => $text,
      'mode' => 'pdf_text',
    ];
  }

  $converter = etds_qc_find_pdf_converter();
  if ($converter === null) {
    return [
      'pages' => [],
      'raw_text' => '',
      'mode' => 'scanned_pdf_needs_manual_review',
      'note' => 'Scanned PDF detected. PDF-to-image conversion tool not available. Please install Poppler (pdftoppm) or upload page images.',
    ];
  }

  $sessionId = '';
  $docId = '';
  if (preg_match('/cases\/([^\/]+)\//', $path, $m)) {
    $sessionId = $m[1];
  }
  if (preg_match('/DOC-(\d+)/', basename($path), $m)) {
    $docId = 'DOC-' . $m[1];
  }
  if ($sessionId === '' || $docId === '') {
    $tempDir = sys_get_temp_dir() . '/etds_qc_pdf_' . md5($path);
  } else {
    $tempDir = etds_qc_session_dir($sessionId) . '/temp/ocr/' . $docId;
  }
  if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0775, true);
  }

  $isGhostscript = stripos(basename($converter), 'gs') !== false;
  $pages = [];

  if ($isGhostscript) {
    $outputPattern = $tempDir . '/page-%03d.png';
    @exec('"' . $converter . '" -dNOPAUSE -dBATCH -sDEVICE=png16m -r200 -sOutputFile="' . $outputPattern . '" "' . $path . '"', $output, $exitCode);
  } else {
    $outputPattern = $tempDir . '/page';
    @exec('"' . $converter . '" -png -r 150 "' . $path . '" "' . $outputPattern . '"', $output, $exitCode);
  }

  $imageFiles = glob($tempDir . '/page-*.png') ?: [];
  if ($imageFiles === []) {
    return [
      'pages' => [],
      'raw_text' => '',
      'mode' => 'scanned_pdf_conversion_failed',
      'note' => 'Scanned PDF detected but page conversion failed. Manual review required.',
    ];
  }

  usort($imageFiles, static function (string $a, string $b): int {
    preg_match('/page-(\d+)\.png$/', $a, $ma);
    preg_match('/page-(\d+)\.png$/', $b, $mb);
    return ((int) ($ma[1] ?? 0)) <=> ((int) ($mb[1] ?? 0));
  });

  $allText = [];
  foreach ($imageFiles as $index => $imageFile) {
    $pageNum = $index + 1;
    $tempBase = tempnam(sys_get_temp_dir(), 'etds_qc_page_ocr_');
    if ($tempBase === false) { continue; }
    @unlink($tempBase);
    @exec('"' . $tesseract . '" "' . $imageFile . '" "' . $tempBase . '" --psm 1', $output, $exitCode);
    $textPath = $tempBase . '.txt';
    $pageText = is_file($textPath) ? trim((string) file_get_contents($textPath)) : '';
    if (is_file($textPath)) { @unlink($textPath); }
    $pages[] = ['page_number' => $pageNum, 'text' => $pageText];
    if ($pageText !== '') {
      $allText[] = $pageText;
    }
  }

  $combinedText = trim(implode("\n\n", $allText));

  if ($combinedText !== '') {
    return [
      'pages' => $pages,
      'raw_text' => $combinedText,
      'mode' => 'pdf_ocr_scanned',
    ];
  }

  return [
    'pages' => $pages,
    'raw_text' => '',
    'mode' => 'scanned_pdf_ocr_failed',
    'note' => 'Scanned PDF processed but OCR could not extract usable text from any page.',
  ];
}

function etds_qc_extract_tabular_document(string $path, string $extension): array {
  return match ($extension) {
    'csv' => etds_qc_extract_csv($path),
    'xlsx' => etds_qc_extract_xlsx($path),
    default => ['columns' => [], 'records' => []],
  };
}

function etds_qc_rows_from_ocr_text(string $text): array {
  $rows = [];
  foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $line) {
    $line = trim($line);
    if ($line === '') {
      continue;
    }
    if (preg_match('/,|;|\t/', $line)) {
      $rows[] = str_getcsv(str_replace("\t", ',', $line));
    }
  }
  return $rows === [] ? ['columns' => [], 'records' => []] : etds_qc_tabular_rows_to_records($rows);
}

function etds_qc_detect_source_page(array $document): int {
  return (int) ($document['source_page'] ?? 1);
}

function etds_qc_extract_document_payload(string $sessionId, array $document): array {
  $path = etds_qc_session_file($sessionId, 'uploads/original/' . ($document['stored_name'] ?? ''));
  $extension = strtolower((string) ($document['extension'] ?? ''));
  $tabular = ['columns' => [], 'records' => []];
  $ocr = ['pages' => [], 'raw_text' => '', 'mode' => 'not_required'];
  $rawText = '';

  if (in_array($extension, ['csv', 'xlsx'], true)) {
    $tabular = etds_qc_extract_tabular_document($path, $extension);
    $lines = [];
    foreach (($tabular['records'] ?? []) as $record) {
      $lines[] = implode(' ', array_map(static fn($value): string => (string) $value, $record));
    }
    $rawText = trim(implode("\n", $lines));
  } elseif ($extension === 'pdf') {
    $pdfText = etds_qc_extract_pdf_text($path);
    $rawText = (string) ($pdfText['raw_text'] ?? '');
    $ocr = etds_qc_extract_pdf_ocr_text($path);
    if ($rawText === '' && ($ocr['raw_text'] ?? '') !== '') {
      $rawText = (string) ($ocr['raw_text'] ?? '');
    }
    $tabular = etds_qc_rows_from_ocr_text($rawText);
  } elseif (etds_qc_is_image_extension($extension)) {
    $imageText = etds_qc_extract_image_text($path);
    $rawText = (string) ($imageText['raw_text'] ?? '');
    $ocr = [
      'pages' => [['page_number' => 1, 'text' => $rawText]],
      'raw_text' => $rawText,
      'mode' => (string) ($imageText['mode'] ?? 'ocr_review_required'),
    ];
    $tabular = ['columns' => $imageText['columns'] ?? [], 'records' => $imageText['records'] ?? []];
  }

  $classification = etds_qc_document_classifier((string) ($document['original_name'] ?? $document['file_name'] ?? ''), $rawText, (array) ($tabular['columns'] ?? []));

  return [
    'classification' => $classification,
    'ocr' => $ocr,
    'tabular' => $tabular,
    'raw_text' => $rawText,
  ];
}

function etds_qc_extraction_record_confidence(array $record, array $preferredFields): int {
  $total = max(1, count($preferredFields));
  $filled = 0;
  foreach ($preferredFields as $field) {
    if (trim((string) ($record[$field] ?? '')) !== '') {
      $filled++;
    }
  }
  return max(55, min(98, (int) round(($filled / $total) * 100)));
}

function etds_qc_extract_structured_entities(string $sessionId, array $document, array $payload): array {
  $records = is_array($payload['tabular']['records'] ?? null) ? $payload['tabular']['records'] : [];
  $text = (string) ($payload['raw_text'] ?? '');
  $classification = (string) ($payload['classification']['classification'] ?? 'unknown_document');
  $ocrPages = is_array($payload['ocr']['pages'] ?? null) ? $payload['ocr']['pages'] : [];
  $ocrMode = (string) ($payload['ocr']['mode'] ?? '');
  $sourcePage = 1;
  $deductor = [];
  $deductees = [];
  $challans = [];
  $salaryRows = [];
  $payments = [];
  $recordRows = [];
  $fieldsExtracted = 0;
  $fieldsMissing = 0;

  $tan = etds_qc_extract_first_match('/\b([A-Z]{4}[0-9]{5}[A-Z])\b/', strtoupper($text));
  $pan = etds_qc_extract_first_match('/\b([A-Z]{5}[0-9]{4}[A-Z])\b/', strtoupper($text));
  $bsr = etds_qc_extract_first_match('/\bBSR(?:\s*CODE)?[:\s-]*([0-9]{7})\b/i', $text);
  $cin = etds_qc_extract_first_match('/\bCIN[:\s-]*([A-Z0-9]{5,})\b/i', $text);
  if ($tan !== '' || $pan !== '' || $bsr !== '' || $cin !== '') {
    foreach ([
      etds_qc_field_payload('tan', $tan, etds_qc_confidence_from_presence($tan, 92), $sourcePage),
      etds_qc_field_payload('pan', $pan, etds_qc_confidence_from_presence($pan, 90), $sourcePage),
      etds_qc_field_payload('bsr', $bsr, etds_qc_confidence_from_presence($bsr, 88), $sourcePage),
      etds_qc_field_payload('cin', $cin, etds_qc_confidence_from_presence($cin, 88), $sourcePage),
    ] as $field) {
      if ($field['value'] !== '') {
        $deductor[$field['field']] = $field;
        $fieldsExtracted++;
      } else {
        $fieldsMissing++;
      }
    }
  }

  $isScannedPdfOcr = $ocrMode === 'pdf_ocr_scanned' && count($ocrPages) > 1;
  if ($isScannedPdfOcr && in_array($classification, ['challan', 'bank_challan', 'unknown_document'], true)) {
    foreach ($ocrPages as $page) {
      $pageText = (string) ($page['text'] ?? '');
      $pageNum = (int) ($page['page_number'] ?? 0);
      if ($pageText === '') { continue; }
      $pageClassification = etds_qc_document_classifier((string) ($document['original_name'] ?? ''), $pageText, []);
      $pageIsChallan = in_array($pageClassification['classification'] ?? '', ['challan', 'bank_challan'], true)
        || preg_match('/(?:CRN|TAN|Tax\s*Year|Major\s*Head)/i', $pageText) === 1;
      if ($pageIsChallan) {
        $ocrChallan = etds_qc_parse_challan_from_ocr_text($pageText);
        if ($ocrChallan['has_data']) {
          $challanConfidence = $ocrChallan['confidence'];
          $f = $ocrChallan['fields'];
          $challans[] = [
            'challan_id' => 'CHL-' . str_pad((string) (count($challans) + 1), 5, '0', STR_PAD_LEFT),
            'document_id' => $document['document_id'] ?? '',
            'fields' => [
              etds_qc_field_payload('crn', $f['crn'] ?? '', etds_qc_confidence_from_presence($f['crn'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('tan', $f['tan'] ?? $tan, etds_qc_confidence_from_presence($f['tan'] ?? $tan, $challanConfidence), $pageNum),
              etds_qc_field_payload('tax_year', $f['tax_year'] ?? '', etds_qc_confidence_from_presence($f['tax_year'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('amount', $f['amount'] !== null ? (string) $f['amount'] : '', etds_qc_confidence_from_presence($f['amount'] !== null ? (string) $f['amount'] : '', $challanConfidence), $pageNum),
              etds_qc_field_payload('major_head', $f['major_head'] ?? '', etds_qc_confidence_from_presence($f['major_head'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('minor_head', $f['minor_head'] ?? '', etds_qc_confidence_from_presence($f['minor_head'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('nature_of_payment', $f['nature_of_payment'] ?? '', etds_qc_confidence_from_presence($f['nature_of_payment'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('payment_mode', $f['payment_mode'] ?? '', etds_qc_confidence_from_presence($f['payment_mode'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('bank_name', $f['bank_name'] ?? '', etds_qc_confidence_from_presence($f['bank_name'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('bsr_code', $f['bsr_code'] ?? $bsr, etds_qc_confidence_from_presence($f['bsr_code'] ?? $bsr, $challanConfidence), $pageNum),
              etds_qc_field_payload('zao_code', $f['zao_code'] ?? '', etds_qc_confidence_from_presence($f['zao_code'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('section_code', $f['section_code'] ?? '', etds_qc_confidence_from_presence($f['section_code'] ?? '', $challanConfidence), $pageNum),
              etds_qc_field_payload('deposit_date', $f['deposit_date'] ?? '', etds_qc_confidence_from_presence($f['deposit_date'] ?? '', $challanConfidence), $pageNum),
            ],
            'confidence' => $challanConfidence,
          ];
        }
      }
    }
    if ($challans !== []) {
      $classification = 'challan';
    }
  }

  if ($challans === [] && in_array($classification, ['challan', 'bank_challan'], true)) {
    $ocrChallan = etds_qc_parse_challan_from_ocr_text($text);
    if ($ocrChallan['has_data']) {
      $challanConfidence = $ocrChallan['confidence'];
      $f = $ocrChallan['fields'];
      $challans[] = [
        'challan_id' => 'CHL-' . str_pad((string) (count($challans) + 1), 5, '0', STR_PAD_LEFT),
        'document_id' => $document['document_id'] ?? '',
        'fields' => [
          etds_qc_field_payload('crn', $f['crn'] ?? '', etds_qc_confidence_from_presence($f['crn'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('tan', $f['tan'] ?? $tan, etds_qc_confidence_from_presence($f['tan'] ?? $tan, $challanConfidence), $sourcePage),
          etds_qc_field_payload('tax_year', $f['tax_year'] ?? '', etds_qc_confidence_from_presence($f['tax_year'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('amount', $f['amount'] !== null ? (string) $f['amount'] : '', etds_qc_confidence_from_presence($f['amount'] !== null ? (string) $f['amount'] : '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('major_head', $f['major_head'] ?? '', etds_qc_confidence_from_presence($f['major_head'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('minor_head', $f['minor_head'] ?? '', etds_qc_confidence_from_presence($f['minor_head'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('nature_of_payment', $f['nature_of_payment'] ?? '', etds_qc_confidence_from_presence($f['nature_of_payment'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('payment_mode', $f['payment_mode'] ?? '', etds_qc_confidence_from_presence($f['payment_mode'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('bank_name', $f['bank_name'] ?? '', etds_qc_confidence_from_presence($f['bank_name'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('bsr_code', $f['bsr_code'] ?? $bsr, etds_qc_confidence_from_presence($f['bsr_code'] ?? $bsr, $challanConfidence), $sourcePage),
          etds_qc_field_payload('zao_code', $f['zao_code'] ?? '', etds_qc_confidence_from_presence($f['zao_code'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('section_code', $f['section_code'] ?? '', etds_qc_confidence_from_presence($f['section_code'] ?? '', $challanConfidence), $sourcePage),
          etds_qc_field_payload('deposit_date', $f['deposit_date'] ?? '', etds_qc_confidence_from_presence($f['deposit_date'] ?? '', $challanConfidence), $sourcePage),
        ],
        'confidence' => $challanConfidence,
      ];
    }
  }

  foreach ($records as $index => $record) {
    $normalized = array_change_key_case($record, CASE_LOWER);
    if (in_array($classification, ['salary_register', 'deductee_list', 'form_24q_working', 'form_26q_working'], true)) {
      $hasMeaningfulData = trim((string) ($normalized['deductee_name'] ?? '')) !== '' || trim((string) ($normalized['pan'] ?? '')) !== '' || trim((string) ($normalized['tds_amount'] ?? '')) !== '';
      if (!$hasMeaningfulData) {
        continue;
      }
      $confidence = etds_qc_extraction_record_confidence($normalized, ['deductee_name', 'pan', 'tds_amount', 'deduction_date']);
      $deductees[] = [
        'deductee_id' => 'DED-' . str_pad((string) (count($deductees) + 1), 5, '0', STR_PAD_LEFT),
        'document_id' => $document['document_id'] ?? '',
        'fields' => [
          etds_qc_field_payload('deductee_name', (string) ($normalized['deductee_name'] ?? ''), etds_qc_confidence_from_presence((string) ($normalized['deductee_name'] ?? ''), $confidence), $sourcePage),
          etds_qc_field_payload('pan', strtoupper((string) ($normalized['pan'] ?? '')), etds_qc_confidence_from_presence((string) ($normalized['pan'] ?? ''), $confidence), $sourcePage),
          etds_qc_field_payload('tds_amount', (string) ($normalized['tds_amount'] ?? ''), etds_qc_confidence_from_presence((string) ($normalized['tds_amount'] ?? ''), $confidence), $sourcePage),
          etds_qc_field_payload('deduction_date', (string) ($normalized['deduction_date'] ?? etds_qc_default_deduction_date($sessionId)), etds_qc_confidence_from_presence((string) ($normalized['deduction_date'] ?? ''), max(60, $confidence - 5)), $sourcePage),
        ],
        'confidence' => $confidence,
      ];
      $salaryRows[] = [
        'salary_id' => 'SAL-' . str_pad((string) (count($salaryRows) + 1), 5, '0', STR_PAD_LEFT),
        'document_id' => $document['document_id'] ?? '',
        'employee_name' => (string) ($normalized['deductee_name'] ?? ''),
        'pan' => strtoupper((string) ($normalized['pan'] ?? '')),
        'amount' => (string) ($normalized['tds_amount'] ?? ''),
        'deduction_date' => (string) ($normalized['deduction_date'] ?? etds_qc_default_deduction_date($sessionId)),
        'confidence' => $confidence,
      ];
      $recordRows[] = [
        'record_id' => 'REC-' . str_pad((string) (count($recordRows) + 1), 5, '0', STR_PAD_LEFT),
        'document_id' => $document['document_id'] ?? '',
        'classification' => $classification,
        'values' => $normalized,
        'confidence' => $confidence,
        'source_page' => $sourcePage,
      ];
    } elseif (in_array($classification, ['challan', 'bank_challan'], true)) {
      $recordRows[] = [
        'record_id' => 'REC-' . str_pad((string) (count($recordRows) + 1), 5, '0', STR_PAD_LEFT),
        'document_id' => $document['document_id'] ?? '',
        'classification' => $classification,
        'values' => $normalized,
        'confidence' => 55,
        'source_page' => $sourcePage,
      ];
    } elseif ($classification === 'payment_register') {
      $hasMeaningfulPaymentData = trim((string) ($normalized['invoice_number'] ?? '')) !== '' || trim((string) ($normalized['deductee_name'] ?? '')) !== '' || trim((string) ($normalized['tds_amount'] ?? '')) !== '';
      if ($hasMeaningfulPaymentData) {
        $paymentConfidence = etds_qc_extraction_record_confidence($normalized, ['invoice_number', 'deductee_name', 'tds_amount', 'deduction_date']);
        $payments[] = [
          'payment_id' => 'PAY-' . str_pad((string) (count($payments) + 1), 5, '0', STR_PAD_LEFT),
          'document_id' => $document['document_id'] ?? '',
          'invoice_number' => (string) ($normalized['invoice_number'] ?? ''),
          'party_name' => (string) ($normalized['deductee_name'] ?? ''),
          'amount' => (string) ($normalized['tds_amount'] ?? ''),
          'payment_date' => (string) ($normalized['deduction_date'] ?? ''),
          'confidence' => $paymentConfidence,
        ];
      }
      $recordRows[] = [
        'record_id' => 'REC-' . str_pad((string) (count($recordRows) + 1), 5, '0', STR_PAD_LEFT),
        'document_id' => $document['document_id'] ?? '',
        'classification' => $classification,
        'values' => $normalized,
        'confidence' => $paymentConfidence,
        'source_page' => $sourcePage,
      ];
    }
    $fieldsExtracted += count(array_filter($normalized, static fn($value): bool => trim((string) $value) !== ''));
    $fieldsMissing += max(0, 4 - count(array_filter($normalized, static fn($value): bool => trim((string) $value) !== '')));
  }

  return [
    'deductor' => $deductor,
    'deductees' => $deductees,
    'challans' => $challans,
    'salary' => $salaryRows,
    'payments' => $payments,
    'records' => $recordRows,
    'fields_extracted' => $fieldsExtracted,
    'fields_missing' => $fieldsMissing,
  ];
}

function etds_qc_reload_source_data(string $sessionId, array $user): array {
  $source = etds_qc_load_json(etds_qc_session_file($sessionId, 'documents.json'), etds_qc_default_case_documents());
  $documents = is_array($source['documents'] ?? null) ? $source['documents'] : [];
  $extraction = etds_qc_default_extraction();
  $ocr = etds_qc_default_ocr();
  $allColumns = [];
  $allRecords = [];
  $allDeductees = [];
  $allChallans = [];
  $allSalary = [];
  $allPayments = [];
  $deductor = etds_qc_case_client($sessionId);
  $documentsProcessed = 0;
  $documentsPending = 0;
  $documentsFailed = 0;
  $confidenceScores = [];
  $fieldExtractedTotal = 0;
  $fieldMissingTotal = 0;

  foreach ($documents as &$document) {
    if (($document['is_removed'] ?? false) === true) {
      continue;
    }
    $extension = strtolower((string) ($document['extension'] ?? ''));
    $payload = etds_qc_extract_document_payload($sessionId, $document);
    $structure = etds_qc_extract_structured_entities($sessionId, $document, $payload);
    $classification = $payload['classification'];
    $ocrPayload = $payload['ocr'];
    $document['classification'] = (string) ($classification['classification'] ?? 'unknown_document');
    $document['classification_confidence'] = (int) ($classification['confidence'] ?? 0);
    $document['ocr_status'] = match (true) {
      etds_qc_is_image_extension($extension) => (($ocrPayload['raw_text'] ?? '') !== '' ? 'Completed' : 'Pending'),
      $extension === 'pdf' && (($ocrPayload['raw_text'] ?? '') !== '') => 'Completed',
      $extension === 'pdf' => 'Pending',
      default => 'Not Required',
    };
    $documentConfidence = [];
    foreach ($structure['records'] as $record) {
      $documentConfidence[] = (int) ($record['confidence'] ?? 0);
    }
    $document['extraction_confidence'] = $documentConfidence === [] ? (int) ($classification['confidence'] ?? 0) : (int) round(array_sum($documentConfidence) / count($documentConfidence));
    $ocrMode = (string) ($ocrPayload['mode'] ?? '');
    $ocrPageCount = count($ocrPayload['pages'] ?? []);
    $isScannedPdfConverted = $ocrMode === 'pdf_ocr_scanned' && $ocrPageCount > 0;
    $isScannedPdfFailed = in_array($ocrMode, ['scanned_pdf_needs_manual_review', 'scanned_pdf_conversion_failed', 'scanned_pdf_ocr_failed'], true);
    $isScannedPdf = $extension === 'pdf' && ($isScannedPdfFailed || $isScannedPdfConverted);
    $document['extraction_status'] = !empty($structure['records']) ? 'extraction_ready' : ((($payload['raw_text'] ?? '') !== '' || ($isScannedPdf && !$isScannedPdfFailed)) ? 'extraction_pending_review' : 'extraction_failed');
    $document['raw_text_excerpt'] = mb_substr((string) ($payload['raw_text'] ?? ''), 0, 500);
    $document['validation_status'] = 'Extraction Ready';
    $document['extraction_note'] = '';
    if ($isScannedPdfConverted && !empty($structure['records'])) {
      $pageCount = count($structure['challans'] ?? []);
      $document['extraction_note'] = 'Scanned PDF processed page-wise through OCR. ' . $pageCount . ' record(s) extracted from ' . $ocrPageCount . ' page(s).';
    } elseif ($isScannedPdfConverted) {
      $document['extraction_note'] = 'Scanned PDF processed page-wise through OCR. Some pages may require manual review.';
    } elseif ($document['extraction_status'] === 'extraction_pending_review') {
      if ($isScannedPdfFailed) {
        $document['extraction_note'] = 'Scanned PDF detected. PDF-to-image conversion tool not available or conversion failed. Please install Poppler (pdftoppm) or upload page images.';
      } elseif (etds_qc_is_image_extension($extension)) {
        $document['extraction_note'] = 'OCR completed but structured extraction requires manual review. Image OCR may require manual verification, especially for rotated or tabular photographs.';
      } else {
        $document['extraction_note'] = 'OCR completed but structured extraction requires manual review.';
      }
    } elseif ($document['extraction_status'] === 'extraction_failed') {
      if (etds_qc_is_image_extension($extension)) {
        $document['extraction_note'] = 'Image OCR could not extract usable text. Manual data entry may be required.';
      } elseif ($extension === 'pdf') {
        $document['extraction_note'] = 'PDF text extraction failed. The document may be scanned or image-based. Please upload page images or enable PDF-to-image OCR support.';
      }
    }

    if ($document['extraction_status'] === 'extraction_ready') {
      $documentsProcessed++;
    } elseif ($document['extraction_status'] === 'extraction_pending_review') {
      $documentsPending++;
    } else {
      $documentsFailed++;
    }
    $confidenceScores[] = (int) ($document['extraction_confidence'] ?? 0);
    $fieldExtractedTotal += (int) ($structure['fields_extracted'] ?? 0);
    $fieldMissingTotal += (int) ($structure['fields_missing'] ?? 0);

    foreach (($payload['tabular']['columns'] ?? []) as $column) {
      if (!in_array($column, $allColumns, true)) {
        $allColumns[] = $column;
      }
    }

    foreach (($structure['deductor'] ?? []) as $field => $value) {
      if (($value['value'] ?? '') !== '') {
        $deductor[$field] = $value['value'];
      }
    }
    $allDeductees = array_merge($allDeductees, $structure['deductees']);
    $allChallans = array_merge($allChallans, $structure['challans']);
    $allSalary = array_merge($allSalary, $structure['salary']);
    $allPayments = array_merge($allPayments, $structure['payments']);
    $allRecords = array_merge($allRecords, $structure['records']);

    $ocr['documents'][] = [
      'document_id' => $document['document_id'] ?? '',
      'document_name' => $document['original_name'] ?? '',
      'classification' => $document['classification'],
      'mode' => (string) ($ocrPayload['mode'] ?? 'not_required'),
      'pages' => $ocrPayload['pages'] ?? [],
    ];
    $extraction['documents'][] = [
      'document_id' => $document['document_id'] ?? '',
      'file_name' => $document['file_name'] ?? '',
      'classification' => $document['classification'],
      'classification_confidence' => $document['classification_confidence'],
      'extraction_status' => $document['extraction_status'],
      'overall_confidence' => $document['extraction_confidence'],
      'fields_extracted' => $structure['fields_extracted'],
      'fields_missing' => $structure['fields_missing'],
      'records_extracted' => count($structure['records']),
    ];
  }
  unset($document);
  $source['documents'] = $documents;
  $source['source_columns'] = $allColumns;
  $source['records'] = $allRecords;
  $source['summary'] = [
    'document_count' => count(array_filter($documents, static fn(array $document): bool => ($document['is_removed'] ?? false) !== true)),
    'duplicate_count' => (int) (($source['summary']['duplicate_count'] ?? 0)),
  ];

  $extraction['source_columns'] = $allColumns;
  $extraction['records'] = $allRecords;
  $extraction['summary'] = [
    'documents_processed' => $documentsProcessed,
    'documents_pending_review' => $documentsPending,
    'documents_failed' => $documentsFailed,
    'fields_extracted' => $fieldExtractedTotal,
    'fields_missing' => $fieldMissingTotal,
    'overall_confidence' => $confidenceScores === [] ? 0 : (int) round(array_sum($confidenceScores) / count($confidenceScores)),
    'status' => $documentsFailed > 0 ? 'extraction_failed' : ($documentsPending > 0 ? 'extraction_pending' : 'extraction_ready'),
    'last_extracted_on' => etds_qc_now(),
  ];

  $ocr['summary'] = [
    'documents_processed' => count($ocr['documents']),
    'documents_pending' => count(array_filter($ocr['documents'], static fn(array $document): bool => in_array((string) ($document['mode'] ?? ''), ['ocr_review_required', 'ocr_unavailable'], true))),
    'pages_processed' => array_sum(array_map(static fn(array $document): int => count((array) ($document['pages'] ?? [])), $ocr['documents'])),
    'status' => empty($ocr['documents']) ? 'pending' : 'completed',
  ];

  etds_qc_write_json(etds_qc_session_file($sessionId, 'documents.json'), $source);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'deductor.json'), $deductor);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => $allDeductees, 'summary' => ['total_records' => count($allDeductees)]]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => $allChallans, 'summary' => ['total_records' => count($allChallans)]]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => $allSalary, 'summary' => ['total_records' => count($allSalary)]]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'payments.json'), ['payments' => $allPayments, 'summary' => ['total_records' => count($allPayments)]]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'extraction.json'), $extraction);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'ocr.json'), $ocr);
  etds_qc_case_update_status($sessionId, $documentsFailed > 0 ? 'extraction_running' : 'validation_running', $user, 'Extraction pipeline executed');
  etds_qc_audit($sessionId, $user, 'extraction_completed', 'AI extraction completed', [], ['records' => count($allRecords), 'confidence' => $extraction['summary']['overall_confidence']]);
  return $source;
}

function etds_qc_issue(string $type, string $severity, string $field, string $message, string $suggestion): array {
  return ['issue_id' => 'ISS-' . substr(bin2hex(random_bytes(4)), 0, 8), 'type' => $type, 'severity' => $severity, 'field' => $field, 'message' => $message, 'suggested_correction' => $suggestion, 'resolution_status' => 'open'];
}

function etds_qc_date_in_financial_year(DateTimeInterface $date, string $financialYear): bool {
  if (!preg_match('/^(\d{4})-(\d{2})$/', $financialYear, $m)) {
    return true;
  }
  $start = new DateTimeImmutable($m[1] . '-04-01', new DateTimeZone('Asia/Calcutta'));
  $end = new DateTimeImmutable(('20' . $m[2]) . '-03-31 23:59:59', new DateTimeZone('Asia/Calcutta'));
  $normalized = new DateTimeImmutable($date->format('Y-m-d') . ' 12:00:00', new DateTimeZone('Asia/Calcutta'));
  return $normalized >= $start && $normalized <= $end;
}

function etds_qc_calculate_quality_score(array $records): int {
  $score = 100;
  foreach ($records as $record) {
    foreach (($record['issues'] ?? []) as $issue) {
      if (($issue['resolution_status'] ?? 'open') !== 'open') {
        continue;
      }
      $score -= (($issue['severity'] ?? '') === 'critical') ? 10 : 3;
    }
  }
  return max(0, $score);
}

function etds_qc_validate_session(string $sessionId, array $user): array {
  if (function_exists('etds_validation_engine_run')) {
    return etds_validation_engine_run($sessionId, $user);
  }
  return etds_qc_default_validation();
}

function etds_doctor_intelli_mode_v1(string $sessionId, array $user): array {
  $session = etds_qc_find_session($sessionId);
  $returnType = strtoupper((string) ($session['return_type'] ?? ''));
  $returnTypeCanonical = etds_qc_return_type_canonical($returnType);
  $assignmentTan = strtoupper((string) ($session['tan'] ?? ''));
  $assignmentFY = (string) ($session['financial_year'] ?? '');

  $deductees = etds_qc_load_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => []]);
  $challans = etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]);
  $salary = etds_qc_load_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => []]);
  $payments = etds_qc_load_json(etds_qc_session_file($sessionId, 'payments.json'), ['payments' => []]);
  $documents = etds_qc_load_json(etds_qc_session_file($sessionId, 'documents.json'), ['documents' => []]);
  $extraction = etds_qc_load_json(etds_qc_session_file($sessionId, 'extraction.json'), ['summary' => [], 'records' => []]);

  $allDeductees = $deductees['deductees'] ?? [];
  $allChallans = $challans['challans'] ?? [];
  $allSalary = $salary['rows'] ?? [];
  $allPayments = $payments['payments'] ?? [];
  $allDocs = $documents['documents'] ?? [];

  $dataUnderstanding = [];
  $findings = [];
  $findingId = 0;

  $nextFinding = static function (string $severity, string $category, string $message, string $treatment, string $source = 'system_rule', string $docId = '', string $recordId = '') use (&$findingId): array {
    $findingId++;
    return [
      'finding_id' => 'INT-F' . str_pad((string) $findingId, 4, '0', STR_PAD_LEFT),
      'severity' => $severity,
      'category' => $category,
      'message' => $message,
      'suggested_treatment' => $treatment,
      'status' => 'open',
      'source' => $source,
      'document_id' => $docId,
      'record_id' => $recordId,
    ];
  };

  foreach ($allDocs as $doc) {
    if (($doc['is_removed'] ?? false) === true) { continue; }
    $docId = (string) ($doc['document_id'] ?? '');
    $ext = strtolower((string) ($doc['extension'] ?? ''));
    $classification = (string) ($doc['classification'] ?? 'unknown_document');
    $ocrStatus = (string) ($doc['ocr_status'] ?? '');
    $extractionStatus = (string) ($doc['extraction_status'] ?? '');
    $extractionNote = (string) ($doc['extraction_note'] ?? '');
    $detectedType = 'unknown';
    $reason = '';

    if (in_array($classification, ['salary_register', 'deductee_list', 'form_24q_working', 'form_26q_working'], true)) {
      $detectedType = 'salary_deductee';
      $reason = 'Document classified as ' . $classification . ' with salary/deductee data.';
    } elseif (in_array($classification, ['challan', 'bank_challan'], true)) {
      $detectedType = 'challan';
      $reason = 'Document classified as ' . $classification . ' with challan data.';
    } elseif ($classification === 'payment_register') {
      $detectedType = 'payment';
      $reason = 'Document classified as payment register.';
    } elseif (in_array($classification, ['quarterly_tds'], true)) {
      $detectedType = 'salary_deductee';
      $reason = 'Document contains quarterly TDS/salary statement.';
    } elseif ($extractionStatus === 'extraction_pending_review') {
      $detectedType = 'ocr_pending_review';
      $reason = 'Document requires manual review.';
    } else {
      $detectedType = 'unknown';
      $reason = 'Document could not be classified.';
    }

    $dataUnderstanding[] = [
      'document_id' => $docId,
      'original_name' => $doc['original_name'] ?? '',
      'detected_type' => $detectedType,
      'classification' => $classification,
      'confidence' => (int) ($doc['classification_confidence'] ?? 0),
      'extraction_status' => $extractionStatus,
      'ocr_status' => $ocrStatus,
      'reason' => $reason,
    ];

    if ($extractionStatus === 'extraction_pending_review') {
      $findings[] = $nextFinding('medium', 'ocr_review', 'Document "' . ($doc['original_name'] ?? $docId) . '" requires manual review.', 'Manually verify the document and correct extracted data.', 'ocr', $docId);
    }
    if ($detectedType === 'unknown' && $extractionStatus !== 'extraction_pending_review') {
      $findings[] = $nextFinding('low', 'data_completeness', 'Document "' . ($doc['original_name'] ?? $docId) . '" could not be classified.', 'Review document and upload with correct categorization.', 'system_rule', $docId);
    }
  }

  $totalStaff = count($allDeductees) + count($allSalary);
  $totalTds = 0;
  foreach ($allDeductees as $d) {
    foreach ($d['fields'] ?? [] as $f) {
      if (($f['field'] ?? '') === 'tds_amount' && ($f['value'] ?? '') !== '') {
        $totalTds += (float) str_replace(',', '', (string) $f['value']);
      }
    }
  }
  foreach ($allSalary as $s) {
    $amt = (float) str_replace(',', '', (string) ($s['amount'] ?? ''));
    if ($amt > 0) { $totalTds += $amt; }
  }
  foreach ($allPayments as $p) {
    $amt = (float) str_replace(',', '', (string) ($p['amount'] ?? ''));
    if ($amt > 0) { $totalTds += $amt; }
  }

  $totalChallan = 0;
  $challanCount = count($allChallans);
  $challanCrns = [];
  foreach ($allChallans as $c) {
    foreach ($c['fields'] ?? [] as $f) {
      if (($f['field'] ?? '') === 'amount' && ($f['value'] ?? '') !== '') {
        $totalChallan += (float) str_replace(',', '', (string) $f['value']);
      }
      if (($f['field'] ?? '') === 'crn' && ($f['value'] ?? '') !== '') {
        $challanCrns[] = (string) $f['value'];
      }
    }
  }

  $ocrPendingCount = 0;
  foreach ($allDocs as $d) {
    if (($d['is_removed'] ?? false)) { continue; }
    if (($d['extraction_status'] ?? '') === 'extraction_pending_review') { $ocrPendingCount++; }
  }

  foreach ($allDeductees as $d) {
    $name = '';
    $pan = '';
    foreach ($d['fields'] ?? [] as $f) {
      if (($f['field'] ?? '') === 'deductee_name') { $name = (string) ($f['value'] ?? ''); }
      if (($f['field'] ?? '') === 'pan') { $pan = strtoupper((string) ($f['value'] ?? '')); }
    }
    if ($pan === '') {
      $findings[] = $nextFinding('high', 'pan_validation', 'PAN missing for deductee "' . $name . '".', 'Enter valid PAN before preparing final output.', 'system_rule', $d['document_id'] ?? '', $d['deductee_id'] ?? '');
    } elseif (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan) !== 1) {
      $findings[] = $nextFinding('high', 'pan_validation', 'Invalid PAN format "' . $pan . '" for "' . $name . '".', 'Correct PAN to match AAAAA9999A format.', 'system_rule', $d['document_id'] ?? '', $d['deductee_id'] ?? '');
    }
    if ($name === '') {
      $findings[] = $nextFinding('medium', 'data_completeness', 'Deductee name missing for PAN ' . $pan . '.', 'Enter the deductee/staff name.', 'system_rule', $d['document_id'] ?? '', $d['deductee_id'] ?? '');
    }
  }

  foreach ($allSalary as $s) {
    $name = (string) ($s['employee_name'] ?? '');
    $pan = strtoupper((string) ($s['pan'] ?? ''));
    if ($pan === '') {
      $findings[] = $nextFinding('high', 'pan_validation', 'PAN missing for staff "' . $name . '".', 'Enter valid PAN before preparing final output.', 'system_rule', $s['document_id'] ?? '', $s['salary_id'] ?? '');
    } elseif (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan) !== 1) {
      $findings[] = $nextFinding('high', 'pan_validation', 'Invalid PAN format "' . $pan . '" for "' . $name . '".', 'Correct PAN to match AAAAA9999A format.', 'system_rule', $s['document_id'] ?? '', $s['salary_id'] ?? '');
    }
  }

  $challanCrnCounts = array_count_values($challanCrns);
  foreach ($challanCrnCounts as $crn => $count) {
    if ($count > 1) {
      $findings[] = $nextFinding('medium', 'duplicate_check', 'Duplicate challan CRN "' . $crn . '" detected (' . $count . ' times).', 'Verify and remove duplicate challan records.', 'duplicate_check');
    }
  }

  $panSeen = [];
  foreach ($allDeductees as $d) {
    $name = '';
    $pan = '';
    foreach ($d['fields'] ?? [] as $f) {
      if (($f['field'] ?? '') === 'deductee_name') { $name = (string) ($f['value'] ?? ''); }
      if (($f['field'] ?? '') === 'pan') { $pan = strtoupper((string) ($f['value'] ?? '')); }
    }
    if ($pan !== '') {
      $key = $pan;
      if (isset($panSeen[$key])) {
        $findings[] = $nextFinding('medium', 'duplicate_check', 'Possible duplicate: PAN "' . $pan . '" appears multiple times.', 'Review and merge/delete duplicate records if confirmed.', 'duplicate_check', $d['document_id'] ?? '', $d['deductee_id'] ?? '');
      }
      $panSeen[$key] = true;
    }
  }

  if ($returnTypeCanonical !== '' && $totalStaff > 0 && in_array($returnTypeCanonical, ['26Q', '27Q', '27EQ'], true)) {
    $findings[] = $nextFinding('medium', 'return_type_check', 'Return type is ' . etds_qc_return_type_label($returnType) . ' but salary/deductee records were found. Verify if this is expected.', 'Confirm return type matches the data uploaded.', 'return_type_check');
  }

  $hasMarchData = false;
  $hasSalaryPattern = false;
  $hasGovtSalaryPattern = false;
  foreach ($allDocs as $doc) {
    $docName = strtolower((string) ($doc['original_name'] ?? ''));
    if (str_contains($docName, 'march') || str_contains($docName, 'quarter')) {
      $hasMarchData = true;
    }
    $class = (string) ($doc['classification'] ?? '');
    if (in_array($class, ['salary_register', 'quarterly_tds', 'deductee_list'], true)) {
      $hasSalaryPattern = true;
    }
    $rawText = strtolower((string) ($doc['raw_text_excerpt'] ?? ''));
    if (str_contains($rawText, 'march') || str_contains($rawText, 'quarter statement') || str_contains($rawText, 'govt') || str_contains($rawText, 'school')) {
      $hasMarchData = true;
      $hasGovtSalaryPattern = true;
    }
  }
  foreach ($allSalary as $s) {
    $desig = strtolower((string) ($s['designation'] ?? ''));
    $empName = strtolower((string) ($s['employee_name'] ?? ''));
    if (str_contains($desig, 'hm') || str_contains($desig, 'tgt') || str_contains($desig, 'pgt') || str_contains($desig, 'pt') || str_contains($empName, 'govt') || str_contains($empName, 'school')) {
      $hasGovtSalaryPattern = true;
    }
  }

  if ($returnTypeCanonical === '24Q' && $hasMarchData && $hasSalaryPattern) {
    $findings[] = $nextFinding('info', 'period_check', 'Government salary pattern detected: March salary appears in Q1 working. This may be valid where March salary is paid/deducted in April. Verify deduction/payment date before finalisation.', 'Verify deduction/payment dates before finalising Q1 24Q return.', 'period_check');
  }

  if ($challanCount > 0) {
    foreach ($allChallans as $c) {
      $challanTan = '';
      foreach ($c['fields'] ?? [] as $f) {
        if (($f['field'] ?? '') === 'tan') { $challanTan = strtoupper((string) ($f['value'] ?? '')); }
      }
      if ($challanTan !== '' && $assignmentTan !== '' && $challanTan !== $assignmentTan) {
        $findings[] = $nextFinding('high', 'challan_matching', 'Challan TAN "' . $challanTan . '" does not match assignment TAN "' . $assignmentTan . '".', 'Upload correct challan or verify selected assignment.', 'challan_match', $c['document_id'] ?? '');
      }
    }
  }

  $difference = $totalChallan - $totalTds;
  $challanMatchStatus = 'no_data';
  $shortfallRatio = 0;
  if ($totalTds > 0 && $totalChallan > 0) {
    $challanMatchStatus = abs($difference) < 1 ? 'matched' : ($difference > 0 ? 'excess' : 'shortfall');
    if ($totalTds > $totalChallan) {
      $shortfall = $totalTds - $totalChallan;
      $shortfallRatio = $totalTds > 0 ? $shortfall / $totalTds : 0;
      if ($shortfallRatio >= 0.5) {
        $findings[] = $nextFinding('critical', 'challan_matching', 'Critical challan shortfall: ₹' . number_format($shortfall) . ' (' . round($shortfallRatio * 100) . '% of TDS). Total TDS: ₹' . number_format($totalTds) . ', Total Challan: ₹' . number_format($totalChallan) . '.', 'Upload missing challans or verify extracted TDS total and period.', 'challan_match');
      } elseif ($shortfallRatio >= 0.25) {
        $findings[] = $nextFinding('high', 'challan_matching', 'Significant challan shortfall: ₹' . number_format($shortfall) . ' (' . round($shortfallRatio * 100) . '% of TDS). Total TDS: ₹' . number_format($totalTds) . ', Total Challan: ₹' . number_format($totalChallan) . '.', 'Upload missing challans or verify extracted TDS total.', 'challan_match');
      } else {
        $findings[] = $nextFinding('high', 'challan_matching', 'Challan shortfall: ₹' . number_format($shortfall) . '. Total TDS: ₹' . number_format($totalTds) . ', Total Challan: ₹' . number_format($totalChallan) . '.', 'Verify challan amounts and ensure all challans are uploaded.', 'challan_match');
      }
    } elseif ($difference > 1000) {
      $findings[] = $nextFinding('medium', 'challan_matching', 'Excess challan amount: ₹' . number_format($difference) . '. Total TDS: ₹' . number_format($totalTds) . ', Total Challan: ₹' . number_format($totalChallan) . '.', 'Verify challan allocation.', 'challan_match');
    }
  } elseif ($totalTds > 0 && $challanCount === 0) {
    $challanMatchStatus = 'no_challan';
    $findings[] = $nextFinding('critical', 'challan_matching', 'No challan data found but TDS of ₹' . number_format($totalTds) . ' exists. Complete challan upload required.', 'Upload challan documents for all TDS deductions.', 'challan_match');
  }

  $criticalCount = count(array_filter($findings, fn($f) => $f['severity'] === 'critical'));
  $highCount = count(array_filter($findings, fn($f) => $f['severity'] === 'high'));
  $mediumCount = count(array_filter($findings, fn($f) => $f['severity'] === 'medium'));
  $lowCount = count(array_filter($findings, fn($f) => in_array($f['severity'], ['low', 'info'], true)));
  $hasBlockingFinding = $criticalCount > 0 || count(array_filter($findings, fn($f) => $f['severity'] === 'high' && $f['category'] === 'challan_matching')) > 0;

  $score = 100;
  $score -= $criticalCount * 20;
  $score -= $highCount * 10;
  $score -= $mediumCount * 5;
  $score -= $lowCount * 2;
  $score -= $ocrPendingCount * 5;

  if ($challanMatchStatus === 'shortfall' && $totalTds > 0) {
    if ($shortfallRatio >= 0.5) {
      $score = min($score, 55);
    } elseif ($shortfallRatio >= 0.25) {
      $score = min($score, 70);
    } else {
      $score = min($score, 80);
    }
  }
  if ($challanMatchStatus === 'no_challan') {
    $score = min($score, 40);
  }
  $score = max(0, min(100, $score));

  if ($hasBlockingFinding) {
    if ($criticalCount > 0 || $score < 50) {
      $readinessStatus = 'Critical Review Required';
      $readinessNote = 'Critical issues must be resolved. ' . ($criticalCount > 0 ? $criticalCount . ' critical finding(s).' : '');
    } else {
      $readinessStatus = 'Not Ready';
      $readinessNote = 'Blocking issues must be resolved before preparing deliverables.';
    }
  } elseif ($score >= 90) {
    $readinessStatus = 'Ready';
    $readinessNote = 'Data quality meets all thresholds.';
  } elseif ($score >= 75) {
    $readinessStatus = 'Ready with Caution';
    $readinessNote = 'Minor issues should be reviewed before final output.';
  } elseif ($score >= 50) {
    $readinessStatus = 'Not Ready';
    $readinessNote = 'Resolve issues before preparing deliverables.';
  } else {
    $readinessStatus = 'Critical Review Required';
    $readinessNote = 'Significant data quality issues must be resolved.';
  }

  $bifurcation = [
    'total_staff' => count($allSalary),
    'total_deductees' => count($allDeductees),
    'total_tds' => $totalTds,
    'total_payments' => count($allPayments),
    'challan_count' => $challanCount,
    'total_challan' => $totalChallan,
    'challan_difference' => $difference,
    'challan_match_status' => $challanMatchStatus,
    'ocr_pending_count' => $ocrPendingCount,
    'unknown_doc_count' => count(array_filter($allDocs, fn($d) => ($d['classification'] ?? '') === 'unknown_document' && !($d['is_removed'] ?? false))),
  ];

  $payload = [
    'data_understanding' => $dataUnderstanding,
    'bifurcation_summary' => $bifurcation,
    'validation_summary' => [
      'total_findings' => count($findings),
      'critical' => $criticalCount,
      'high' => $highCount,
      'medium' => $mediumCount,
      'low' => $lowCount,
    ],
    'challan_match_summary' => [
      'total_tds' => $totalTds,
      'total_challan' => $totalChallan,
      'difference' => $difference,
      'match_status' => $challanMatchStatus,
    ],
    'findings' => $findings,
    'readiness_score' => $score,
    'readiness_status' => $readinessStatus,
    'readiness_note' => $readinessNote,
    'generated_at' => etds_qc_now(),
  ];

  etds_qc_write_json(etds_qc_session_file($sessionId, 'doctor_intelli.json'), $payload);

  if (function_exists('etds_qc_audit')) {
    etds_qc_audit($sessionId, $user, 'doctor_intelli_completed', 'Doctor Intelli Mode V1 executed', [], [
      'readiness_score' => $score,
      'readiness_status' => $readinessStatus,
      'total_findings' => count($findings),
    ]);
  }

  return $payload;
}

function etds_qc_update_issue_status(string $sessionId, string $recordId, string $issueId, string $status, array $user): void {
  $validated = etds_qc_load_json(etds_qc_session_file($sessionId, 'validation.json'), etds_qc_default_validation());
  $findings = is_array($validated['findings'] ?? null) ? $validated['findings'] : [];
  foreach ($findings as &$finding) {
    $sameRecord = $recordId === '' || (string) ($finding['record_reference'] ?? '') === $recordId;
    $sameIssue = (string) ($finding['finding_id'] ?? '') === $issueId || (string) ($finding['rule_id'] ?? '') === $issueId;
    if ($sameRecord && $sameIssue) {
      $finding['status'] = $status;
      $finding['updated_on'] = etds_qc_now();
    }
  }
  unset($finding);
  $validated['findings'] = $findings;
  $openFindings = array_values(array_filter($findings, static fn(array $finding): bool => (string) ($finding['status'] ?? 'open') === 'open'));
  $counts = ['Critical' => 0, 'High' => 0, 'Medium' => 0, 'Low' => 0, 'Information' => 0];
  foreach ($openFindings as $finding) {
    $severity = (string) ($finding['severity'] ?? 'Information');
    if (!array_key_exists($severity, $counts)) {
      $counts[$severity] = 0;
    }
    $counts[$severity]++;
  }
  $score = 100 - ($counts['Critical'] * 15) - ($counts['High'] * 10) - ($counts['Medium'] * 5) - ($counts['Low'] * 2) - $counts['Information'];
  $validated['summary'] = [
    'total_records' => (int) ($validated['summary']['total_records'] ?? 0),
    'quality_score' => max(0, $score),
    'critical' => $counts['Critical'],
    'high' => $counts['High'],
    'medium' => $counts['Medium'],
    'low' => $counts['Low'],
    'information' => $counts['Information'],
    'total_findings' => count($openFindings),
    'ready_status' => $counts['Critical'] === 0 && $counts['High'] === 0,
    'last_validated_on' => etds_qc_now(),
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'validation.json'), $validated);
  $session = etds_qc_find_session($sessionId);
  if ($session) {
    $session['quality_score'] = (int) ($validated['summary']['quality_score'] ?? 0);
    $session['export_readiness'] = etds_qc_export_readiness($sessionId);
    $session['status'] = $session['export_readiness'] ? 'ready_for_return_preparation' : 'qc_in_progress';
    $session['last_action'] = 'issue_' . $status;
    etds_qc_save_session($session);
  }
  etds_qc_audit($sessionId, $user, 'issue_' . $status, 'Issue marked as ' . $status, ['record_id' => $recordId, 'issue_id' => $issueId]);
}

function etds_qc_edit_record(string $sessionId, string $recordId, array $payload, array $user): void {
  $source = etds_qc_load_json(etds_qc_session_file($sessionId, 'source_data.json'), ['records' => []]);
  foreach (($source['records'] ?? []) as &$record) {
    if (($record['record_id'] ?? '') !== $recordId) { continue; }
    $record['deductee_name'] = clean_input((string) ($payload['deductee_name'] ?? ''), 150);
    $record['pan'] = strtoupper(clean_input((string) ($payload['pan'] ?? ''), 10));
    $record['tds_amount'] = clean_input((string) ($payload['tds_amount'] ?? ''), 30);
    $record['deduction_date'] = clean_input((string) ($payload['deduction_date'] ?? ''), 20);
    $record['invoice_number'] = clean_input((string) ($payload['invoice_number'] ?? ''), 50);
    $record['challan_reference'] = clean_input((string) ($payload['challan_reference'] ?? ''), 50);
  }
  unset($record);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'source_data.json'), $source);
  etds_qc_audit($sessionId, $user, 'record_edited', 'Source record updated', ['record_id' => $recordId]);
}

function etds_qc_reconcile(string $sessionId, array $user): array {
  if (function_exists('etds_reconciliation_engine_run')) {
    return etds_reconciliation_engine_run($sessionId, $user);
  }
  return etds_qc_default_reconciliation();
}

function etds_qc_export_readiness(string $sessionId): bool {
  $validated = etds_qc_load_json(etds_qc_session_file($sessionId, 'validation.json'), etds_qc_default_validation());
  $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), ['summary' => [], 'exceptions' => []]);
  $threshold = (int) (etds_qc_config()['quality_threshold'] ?? 100);
  foreach (($validated['findings'] ?? []) as $finding) {
    if (in_array((string) ($finding['severity'] ?? ''), ['Critical', 'High'], true) && (string) ($finding['status'] ?? 'open') === 'open') {
      return false;
    }
  }
  foreach (($reconciliation['exceptions'] ?? []) as $exception) {
    if (($exception['severity'] ?? '') === 'critical' && ($exception['resolution_status'] ?? 'open') === 'open') {
      return false;
    }
  }
  return ((float) ($reconciliation['summary']['difference'] ?? 0.0) === 0.0) && ((int) ($validated['summary']['quality_score'] ?? 0) >= $threshold);
}

function etds_qc_xml(string $value): string { return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8'); }

function etds_qc_cell_ref(int $column, int $row): string {
  $letters = '';
  while ($column > 0) {
    $mod = ($column - 1) % 26;
    $letters = chr(65 + $mod) . $letters;
    $column = intdiv($column - $mod, 26) - 1;
  }
  return $letters . $row;
}

function etds_qc_simple_xlsx(string $target, array $headers, array $rows): void {
  $sharedStrings = [];
  $stringIndex = [];
  $allRows = array_merge([$headers], $rows);
  $sheetRows = [];
  foreach ($allRows as $rowNumber => $row) {
    $cellsXml = '';
    foreach (array_values($row) as $columnIndex => $value) {
      $ref = etds_qc_cell_ref($columnIndex + 1, $rowNumber + 1);
      $value = (string) $value;
      if (is_numeric($value) && $rowNumber > 0 && $columnIndex === 2) {
        $cellsXml .= '<c r="' . $ref . '"><v>' . etds_qc_xml($value) . '</v></c>';
        continue;
      }
      if (!array_key_exists($value, $stringIndex)) {
        $stringIndex[$value] = count($sharedStrings);
        $sharedStrings[] = $value;
      }
      $cellsXml .= '<c r="' . $ref . '" t="s"><v>' . $stringIndex[$value] . '</v></c>';
    }
    $sheetRows[] = '<row r="' . ($rowNumber + 1) . '">' . $cellsXml . '</row>';
  }
  $sharedXmlItems = '';
  foreach ($sharedStrings as $string) { $sharedXmlItems .= '<si><t>' . etds_qc_xml($string) . '</t></si>'; }
  $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>';
  $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
  $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Ready Data" sheetId="1" r:id="rId1"/></sheets></workbook>';
  $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>';
  $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . implode('', $sheetRows) . '</sheetData></worksheet>';
  $sharedXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">' . $sharedXmlItems . '</sst>';
  $tempTarget = tempnam(sys_get_temp_dir(), 'etds_qc_xlsx_');
  if ($tempTarget === false) { throw new RuntimeException('Failed to create temporary XLSX file.'); }
  $zip = new ZipArchive();
  $zip->open($tempTarget, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  $zip->addFromString('[Content_Types].xml', $contentTypes);
  $zip->addFromString('_rels/.rels', $rels);
  $zip->addFromString('xl/workbook.xml', $workbook);
  $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
  $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
  $zip->addFromString('xl/sharedStrings.xml', $sharedXml);
  $zip->close();
  if (!@copy($tempTarget, $target)) {
    @unlink($tempTarget);
    throw new RuntimeException('Failed to publish XLSX file: ' . $target);
  }
  @unlink($tempTarget);
}

function etds_qc_write_export_xlsx(string $sessionId, array $session, array $user): ?string {
  $validated = etds_qc_load_json(etds_qc_session_file($sessionId, 'validated_data.json'), ['records' => []]);
  if (!etds_qc_export_readiness($sessionId)) {
    return null;
  }
  $headers = ['Deductee Name', 'PAN', 'TDS Amount', 'Deduction Date', 'Invoice Number', 'Challan Reference'];
  $rows = [];
  foreach (($validated['records'] ?? []) as $record) {
    $hasOpenCritical = false;
    foreach (($record['issues'] ?? []) as $issue) {
      if (($issue['severity'] ?? '') === 'critical' && ($issue['resolution_status'] ?? 'open') === 'open') {
        $hasOpenCritical = true;
        break;
      }
    }
    if ($hasOpenCritical) { continue; }
    $n = $record['normalized'] ?? [];
    $rows[] = [$n['deductee_name'] ?? '', $n['pan'] ?? '', (string) ($n['tds_amount'] ?? ''), $n['deduction_date'] ?? '', $n['invoice_number'] ?? '', $n['challan_reference'] ?? ''];
  }
  $fileName = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '_', (string) ($session['client_name'] ?? 'CLIENT')) ?? 'CLIENT') . '_' . ($session['quarter'] ?? 'QX') . '_' . ($session['return_type'] ?? '24Q') . '_READY.xlsx';
  etds_qc_simple_xlsx(etds_qc_session_file($sessionId, 'output/' . $fileName), $headers, $rows);
  $session['status'] = 'ready';
  $session['export_readiness'] = true;
  $session['last_action'] = 'export_generated';
  etds_qc_save_session($session);
  etds_qc_audit($sessionId, $user, 'export_generated', 'Export workbook generated', ['file_name' => $fileName]);
  return $fileName;
}

function etds_qc_flash(string $type, string $message): void { $_SESSION['etds_qc_flash'] = ['type' => $type, 'message' => $message]; }
function etds_qc_pull_flash(): ?array { $flash = $_SESSION['etds_qc_flash'] ?? null; unset($_SESSION['etds_qc_flash']); return is_array($flash) ? $flash : null; }

function etds_qc_delete_upload(string $sessionId, string $fileId, array $user): bool {
  $source = etds_qc_load_json(etds_qc_session_file($sessionId, 'documents.json'), etds_qc_default_case_documents());
  $documents = is_array($source['documents'] ?? null) ? $source['documents'] : [];
  $deleted = false;
  foreach ($documents as &$document) {
    if (($document['file_id'] ?? '') !== $fileId && ($document['document_id'] ?? '') !== $fileId) {
      continue;
    }
    $path = etds_qc_session_file($sessionId, 'uploads/original/' . ($document['stored_name'] ?? ''));
    if (is_file($path)) {
      @unlink($path);
    }
    $document['is_removed'] = true;
    $document['remarks'] = 'Removed from document register';
    $document['validation_status'] = 'Removed';
    $deleted = true;
    break;
  }
  unset($document);
  if (!$deleted) {
    return false;
  }
  $source['documents'] = $documents;
  $source['summary'] = [
    'document_count' => count(array_filter($documents, static fn(array $document): bool => ($document['is_removed'] ?? false) !== true)),
    'duplicate_count' => count(array_filter($documents, static fn(array $document): bool => ($document['is_duplicate'] ?? false) === true && ($document['is_removed'] ?? false) !== true)),
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'documents.json'), $source);
  $activeDocuments = (int) ($source['summary']['document_count'] ?? 0);
  etds_qc_case_update_status($sessionId, $activeDocuments > 0 ? 'documents_received' : 'draft', $user, 'Document removed');
  etds_qc_audit($sessionId, $user, 'document_removed', 'Uploaded file removed', [], ['file_id' => $fileId]);
  return true;
}

function etds_qc_archive_session(string $sessionId, array $user): void {
  $session = etds_qc_find_session($sessionId);
  if (!$session) { return; }
  $session['status'] = 'archived';
  $session['last_action'] = 'case_archived';
  etds_qc_save_session($session);
  etds_qc_audit($sessionId, $user, 'case_archived', 'Case archived', [], ['status' => 'archived']);
}

function etds_qc_purge_session(string $sessionId, array $user): void {
  $session = etds_qc_find_session($sessionId);
  if (!$session) { return; }
  $session['is_deleted'] = true;
  $session['status'] = 'deleted';
  $session['last_action'] = 'case_deleted';
  etds_qc_save_session($session);
  etds_qc_audit($sessionId, $user, 'case_deleted', 'Case soft deleted', [], ['status' => 'deleted']);
}

function etds_qc_run_auto_purge(): void {
  $days = (int) (etds_qc_config()['purge_after_days'] ?? 7);
  if ($days <= 0) { return; }
  $cutoff = (new DateTimeImmutable('now', new DateTimeZone('Asia/Calcutta')))->modify('-' . $days . ' days');
  foreach (etds_qc_all_sessions() as $session) {
    if (!in_array((string) ($session['status'] ?? ''), ['archived', 'downloaded'], true)) {
      continue;
    }
    try {
      $updatedAt = new DateTimeImmutable((string) ($session['updated_on'] ?? $session['created_on'] ?? etds_qc_now()));
    } catch (Throwable) {
      continue;
    }
    if ($updatedAt <= $cutoff) {
      etds_qc_purge_session((string) $session['session_id'], ['id' => 'system', 'name' => 'System']);
    }
  }
}

function etds_qc_log_runtime_error(string $context, Throwable $exception): void {
  $logFile = ETDS_QC_STORAGE_ROOT . '/runtime-error.log';
  $line = sprintf(
    "[%s] %s: %s in %s:%d\n",
    etds_qc_now(),
    $context,
    $exception->getMessage(),
    $exception->getFile(),
    $exception->getLine()
  );
  @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}
