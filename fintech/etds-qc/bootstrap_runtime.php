<?php
declare(strict_types=1);

const ETDS_QC_SESSION_NAME = 'ETDS_QC_SESSION';
const ETDS_QC_STORAGE_ROOT = __DIR__ . '/../../storage/etds-qc';
const ETDS_QC_SESSIONS_ROOT = ETDS_QC_STORAGE_ROOT . '/sessions';
const ETDS_QC_USERS_FILE = ETDS_QC_STORAGE_ROOT . '/users.json';
const ETDS_QC_CONFIG_FILE = ETDS_QC_STORAGE_ROOT . '/config.json';
const ETDS_QC_MAX_UPLOAD_BYTES = 15 * 1024 * 1024;

if (session_status() === PHP_SESSION_NONE) {
  session_name(ETDS_QC_SESSION_NAME);
  session_start();
}

require_once dirname(__DIR__, 2) . '/includes/security.php';

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

function etds_qc_bootstrap(): void {
  foreach ([ETDS_QC_STORAGE_ROOT, ETDS_QC_SESSIONS_ROOT] as $directory) {
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
      'password_hash' => '$2y$12$.zeHQgu5aE9Dkz0sfrl1ge1DCT9mwcwi/RxIYw4fO1zQrsq9GiqrG',
      'status' => 'active',
      'created_on' => etds_qc_now(),
      'updated_on' => etds_qc_now(),
    ]]);
  }

  if (!is_file(ETDS_QC_CONFIG_FILE)) {
    etds_qc_write_json(ETDS_QC_CONFIG_FILE, [
      'quality_threshold' => 100,
      'purge_after_days' => 7,
      'allowed_extensions' => ['xlsx', 'xls', 'csv', 'pdf', 'png', 'jpg', 'jpeg'],
    ]);
  }

  etds_qc_run_auto_purge();
}

function etds_qc_now(): string {
  return (new DateTimeImmutable('now', new DateTimeZone('Asia/Calcutta')))->format(DateTimeInterface::ATOM);
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

function etds_qc_session_dir(string $sessionId): string {
  return ETDS_QC_SESSIONS_ROOT . '/' . $sessionId;
}

function etds_qc_session_file(string $sessionId, string $fileName): string {
  return etds_qc_session_dir($sessionId) . '/' . $fileName;
}

function etds_qc_all_sessions(): array {
  $items = [];
  foreach (glob(ETDS_QC_SESSIONS_ROOT . '/QC-*', GLOB_ONLYDIR) ?: [] as $directory) {
    $session = etds_qc_load_json($directory . '/session.json', null);
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
    return ['key' => 'pending_validation', 'label' => 'Pending Validation'];
  }
  if (in_array($status, ['downloaded', 'archived', 'purged'], true)) {
    return ['key' => 'completed', 'label' => ucfirst($status)];
  }
  $validated = etds_qc_load_json(etds_qc_session_file($sessionId, 'validated_data.json'), ['summary' => [], 'records' => []]);
  $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), ['summary' => [], 'exceptions' => []]);
  $exportFiles = glob(etds_qc_session_file($sessionId, 'output/*.xlsx')) ?: [];
  $qualityScore = (int) ($validated['summary']['quality_score'] ?? 0);
  $totalRecords = (int) ($validated['summary']['total_records'] ?? 0);
  $threshold = (int) (etds_qc_config()['quality_threshold'] ?? 100);
  $difference = $reconciliation['summary']['difference'] ?? null;
  $hasReconciliation = !empty($reconciliation['summary']) || !empty($reconciliation['exceptions']);
  if ($exportFiles !== []) {
    return ['key' => 'ready', 'label' => 'Export Generated'];
  }
  if (etds_qc_export_readiness($sessionId)) {
    return ['key' => 'ready', 'label' => 'Ready For Export'];
  }
  if ($totalRecords === 0 || $qualityScore < $threshold) {
    return ['key' => 'pending_validation', 'label' => 'Pending Validation'];
  }
  if (!$hasReconciliation || $difference === null || (float) $difference !== 0.0) {
    return ['key' => 'pending_reconciliation', 'label' => 'Pending Reconciliation'];
  }
  return ['key' => 'pending_validation', 'label' => 'Pending Review'];
}

function etds_qc_find_session(string $sessionId): ?array {
  if (!preg_match('/^QC-\d{4}-\d{4}$/', $sessionId)) {
    return null;
  }
  $session = etds_qc_load_json(etds_qc_session_file($sessionId, 'session.json'), null);
  return is_array($session) ? $session : null;
}

function etds_qc_generate_session_id(): string {
  $year = (new DateTimeImmutable('now', new DateTimeZone('Asia/Calcutta')))->format('Y');
  $max = 0;
  foreach (etds_qc_all_sessions() as $session) {
    if (preg_match('/^QC-' . preg_quote($year, '/') . '-(\d{4})$/', (string) ($session['session_id'] ?? ''), $m)) {
      $max = max($max, (int) $m[1]);
    }
  }
  return sprintf('QC-%s-%04d', $year, $max + 1);
}

function etds_qc_create_session(array $payload, array $user): array {
  $sessionId = etds_qc_generate_session_id();
  foreach (['uploads/original', 'output', 'audit', 'logs'] as $path) {
    @mkdir(etds_qc_session_file($sessionId, $path), 0775, true);
  }
  $session = [
    'session_id' => $sessionId,
    'client_name' => clean_input((string) ($payload['client_name'] ?? ''), 150),
    'tan' => strtoupper(clean_input((string) ($payload['tan'] ?? ''), 10)),
    'financial_year' => clean_input((string) ($payload['financial_year'] ?? ''), 9),
    'quarter' => clean_input((string) ($payload['quarter'] ?? ''), 2),
    'return_type' => clean_input((string) ($payload['return_type'] ?? ''), 4),
    'remarks' => clean_multiline((string) ($payload['remarks'] ?? ''), 500),
    'status' => 'draft',
    'quality_score' => 0,
    'reconciliation_score' => 0,
    'export_readiness' => false,
    'created_by' => $user['id'] ?? 'system',
    'created_by_name' => $user['name'] ?? ($user['email'] ?? 'System'),
    'created_on' => etds_qc_now(),
    'updated_on' => etds_qc_now(),
    'last_action' => 'session_created',
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'session.json'), $session);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'source_data.json'), ['documents' => [], 'source_columns' => [], 'records' => []]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'validated_data.json'), ['summary' => [], 'records' => []]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'reconciliation.json'), ['summary' => [], 'exceptions' => []]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'audit/audit-log.json'), []);
  etds_qc_audit($sessionId, $user, 'session_created', 'QC session created');
  return $session;
}

function etds_qc_save_session(array $session): void {
  $session['updated_on'] = etds_qc_now();
  etds_qc_write_json(etds_qc_session_file((string) $session['session_id'], 'session.json'), $session);
}

function etds_qc_audit(string $sessionId, array $user, string $action, string $event, array $meta = []): void {
  $file = etds_qc_session_file($sessionId, 'audit/audit-log.json');
  $events = etds_qc_load_json($file, []);
  if (!is_array($events)) {
    $events = [];
  }
  $events[] = [
    'event_id' => 'AUD-' . str_pad((string) (count($events) + 1), 4, '0', STR_PAD_LEFT),
    'session_id' => $sessionId,
    'user_id' => $user['id'] ?? 'system',
    'action' => $action,
    'event' => $event,
    'meta' => $meta,
    'timestamp' => etds_qc_now(),
  ];
  etds_qc_write_json($file, $events);
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

function etds_qc_extract_xlsx(string $path): array {
  if (!class_exists('ZipArchive')) {
    return ['columns' => [], 'records' => []];
  }
  $zip = new ZipArchive();
  if ($zip->open($path) !== true) {
    return ['columns' => [], 'records' => []];
  }
  $sharedStrings = [];
  $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
  if (is_string($sharedXml) && $sharedXml !== '') {
    $xml = @simplexml_load_string($sharedXml);
    if ($xml) {
      foreach ($xml->si as $item) {
        $sharedStrings[] = isset($item->t) ? (string) $item->t : '';
      }
    }
  }
  $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
  $zip->close();
  if (!is_string($sheetXml) || $sheetXml === '') {
    return ['columns' => [], 'records' => []];
  }
  $xml = @simplexml_load_string($sheetXml);
  if (!$xml || !isset($xml->sheetData)) {
    return ['columns' => [], 'records' => []];
  }
  $rows = [];
  foreach ($xml->sheetData->row as $rowNode) {
    $cells = [];
    foreach ($rowNode->c as $cell) {
      preg_match('/([A-Z]+)/', (string) ($cell['r'] ?? 'A'), $m);
      $index = etds_qc_column_index($m[1] ?? 'A');
      $type = (string) ($cell['t'] ?? '');
      $cells[$index] = $type === 's' ? ($sharedStrings[(int) ($cell->v ?? 0)] ?? '') : (string) ($cell->v ?? '');
    }
    if ($cells !== []) {
      ksort($cells);
      $rows[] = $cells;
    }
  }
  return etds_qc_tabular_rows_to_records($rows);
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
  @exec('"' . $tesseract . '" "' . $path . '" "' . $tempBase . '" --psm 6', $output, $exitCode);
  $textPath = $tempBase . '.txt';
  $text = is_file($textPath) ? trim((string) file_get_contents($textPath)) : '';
  if (is_file($textPath)) {
    @unlink($textPath);
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
  $tabular['mode'] = $exitCode === 0 ? 'ocr_text' : 'ocr_review_required';
  return $tabular;
}

function etds_qc_reload_source_data(string $sessionId, array $user): array {
  $source = etds_qc_load_json(etds_qc_session_file($sessionId, 'source_data.json'), ['documents' => [], 'source_columns' => [], 'records' => []]);
  $documents = is_array($source['documents'] ?? null) ? $source['documents'] : [];
  $allColumns = [];
  $allRecords = [];
  $recordCount = 0;
  foreach ($documents as &$document) {
    $extension = strtolower((string) ($document['extension'] ?? ''));
    $path = etds_qc_session_file($sessionId, 'uploads/original/' . ($document['stored_name'] ?? ''));
    $extracted = ['columns' => [], 'records' => [], 'raw_text' => '', 'mode' => 'stored_only'];
    if ($extension === 'csv') {
      $extracted = etds_qc_extract_csv($path);
      $document['extraction_status'] = 'extracted_csv';
    } elseif ($extension === 'xlsx') {
      $extracted = etds_qc_extract_xlsx($path);
      $document['extraction_status'] = 'extracted_xlsx';
    } elseif ($extension === 'pdf') {
      $extracted = etds_qc_extract_pdf_text($path);
      $document['extraction_status'] = (string) ($extracted['mode'] ?? 'pdf_needs_manual_review');
    } elseif (etds_qc_is_image_extension($extension)) {
      $extracted = etds_qc_extract_image_text($path);
      $document['extraction_status'] = (string) ($extracted['mode'] ?? 'ocr_review_required');
    } elseif ($extension === 'xls') {
      $document['extraction_status'] = 'manual_conversion_required';
    } else {
      $document['extraction_status'] = 'stored_only';
    }
    $document['raw_text_excerpt'] = mb_substr((string) ($extracted['raw_text'] ?? ''), 0, 500);
    foreach (($extracted['columns'] ?? []) as $column) {
      if (!in_array($column, $allColumns, true)) {
        $allColumns[] = $column;
      }
    }
    foreach (($extracted['records'] ?? []) as $index => $record) {
      $recordCount++;
      $allRecords[] = ['record_id' => 'REC-' . str_pad((string) $recordCount, 4, '0', STR_PAD_LEFT), 'source_file_id' => $document['file_id'], 'row_number' => $index + 2] + $record;
    }
  }
  unset($document);
  $source['documents'] = $documents;
  $source['source_columns'] = $allColumns;
  $source['records'] = $allRecords;
  etds_qc_write_json(etds_qc_session_file($sessionId, 'source_data.json'), $source);
  etds_qc_audit($sessionId, $user, 'extraction_completed', 'Source data extraction refreshed', ['records' => count($allRecords)]);
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
  $end = new DateTimeImmutable(('20' . $m[2]) . '-03-31', new DateTimeZone('Asia/Calcutta'));
  return $date >= $start && $date <= $end;
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
  $source = etds_qc_load_json(etds_qc_session_file($sessionId, 'source_data.json'), ['records' => [], 'source_columns' => []]);
  $records = is_array($source['records'] ?? null) ? $source['records'] : [];
  $columns = is_array($source['source_columns'] ?? null) ? $source['source_columns'] : [];
  $requiredColumns = ['deductee_name', 'pan', 'tds_amount', 'deduction_date'];
  $missingColumns = array_values(array_diff($requiredColumns, $columns));
  $invoiceCounts = [];
  $panCounts = [];
  $transactionCounts = [];
  foreach ($records as $record) {
    $invoice = trim((string) ($record['invoice_number'] ?? ''));
    $pan = strtoupper(trim((string) ($record['pan'] ?? '')));
    $signature = md5(json_encode([$pan, $record['deductee_name'] ?? '', $record['tds_amount'] ?? '', $record['deduction_date'] ?? '', $invoice]));
    if ($invoice !== '') { $invoiceCounts[$invoice] = ($invoiceCounts[$invoice] ?? 0) + 1; }
    if ($pan !== '') { $panCounts[$pan] = ($panCounts[$pan] ?? 0) + 1; }
    $transactionCounts[$signature] = ($transactionCounts[$signature] ?? 0) + 1;
  }
  $financialYear = (string) (etds_qc_find_session($sessionId)['financial_year'] ?? '');
  $validated = ['summary' => [], 'records' => []];
  $failed = 0; $warning = 0; $passed = 0;
  foreach ($records as $record) {
    $issues = [];
    foreach ($missingColumns as $column) {
      $issues[] = etds_qc_issue('missing_column', 'critical', $column, 'Required source column is missing.', 'Re-upload source with the required column.');
    }
    $name = trim((string) ($record['deductee_name'] ?? ''));
    $pan = strtoupper(str_replace(' ', '', trim((string) ($record['pan'] ?? ''))));
    $amountRaw = trim((string) ($record['tds_amount'] ?? ''));
    $date = trim((string) ($record['deduction_date'] ?? ''));
    $invoice = trim((string) ($record['invoice_number'] ?? ''));
    $challanRef = trim((string) ($record['challan_reference'] ?? ''));
    if ($name === '') { $issues[] = etds_qc_issue('missing_name', 'critical', 'deductee_name', 'Deductee name is required.', 'Update the source record.'); }
    if ($pan === '') { $issues[] = etds_qc_issue('missing_pan', 'critical', 'pan', 'PAN is required.', 'Enter the deductee PAN.'); }
    elseif (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan)) { $issues[] = etds_qc_issue('invalid_pan', 'critical', 'pan', 'PAN must match AAAAA9999A format.', 'Correct the PAN.'); }
    if ($amountRaw === '' || !is_numeric($amountRaw) || (float) $amountRaw <= 0) { $issues[] = etds_qc_issue('invalid_amount', 'critical', 'tds_amount', 'Amount must be numeric and greater than zero.', 'Correct the amount value.'); }
    if ($date === '') { $issues[] = etds_qc_issue('missing_date', 'critical', 'deduction_date', 'Deduction date is required.', 'Enter the deduction date.'); }
    else {
      $dateObj = date_create($date);
      if (!$dateObj) { $issues[] = etds_qc_issue('invalid_date', 'critical', 'deduction_date', 'Date is not valid.', 'Use a valid date.'); }
      else {
        $today = new DateTimeImmutable('today', new DateTimeZone('Asia/Calcutta'));
        if ($dateObj > $today) { $issues[] = etds_qc_issue('future_date', 'critical', 'deduction_date', 'Date cannot be in the future.', 'Correct the date.'); }
        if ($financialYear !== '' && !etds_qc_date_in_financial_year($dateObj, $financialYear)) { $issues[] = etds_qc_issue('out_of_fy', 'critical', 'deduction_date', 'Date falls outside the selected financial year.', 'Correct the date or FY.'); }
      }
    }
    if ($invoice !== '' && ($invoiceCounts[$invoice] ?? 0) > 1) { $issues[] = etds_qc_issue('duplicate_invoice', 'warning', 'invoice_number', 'Invoice number appears more than once.', 'Review the duplicate.'); }
    if ($pan !== '' && ($panCounts[$pan] ?? 0) > 1) { $issues[] = etds_qc_issue('duplicate_pan', 'warning', 'pan', 'PAN appears multiple times in this session.', 'Review duplicate deductee records.'); }
    $signature = md5(json_encode([$pan, $name, $amountRaw, $date, $invoice]));
    if (($transactionCounts[$signature] ?? 0) > 1) { $issues[] = etds_qc_issue('duplicate_transaction', 'critical', 'record', 'This transaction appears more than once.', 'Keep the valid record and resolve the duplicate.'); }
    if ($challanRef === '') { $issues[] = etds_qc_issue('missing_challan_reference', 'warning', 'challan_reference', 'Challan reference is missing.', 'Map the record during reconciliation.'); }
    $normalized = ['deductee_name' => $name, 'pan' => $pan, 'tds_amount' => is_numeric($amountRaw) ? round((float) $amountRaw, 2) : $amountRaw, 'deduction_date' => $date, 'invoice_number' => $invoice, 'challan_reference' => $challanRef];
    $criticalCount = count(array_filter($issues, static fn(array $i): bool => ($i['severity'] ?? '') === 'critical'));
    $warningCount = count(array_filter($issues, static fn(array $i): bool => ($i['severity'] ?? '') === 'warning'));
    $status = $criticalCount > 0 ? 'failed' : ($warningCount > 0 ? 'warning' : 'passed');
    if ($status === 'failed') { $failed++; } elseif ($status === 'warning') { $warning++; } else { $passed++; }
    $validated['records'][] = ['record_id' => $record['record_id'], 'status' => $status, 'source_file_id' => $record['source_file_id'] ?? null, 'row_number' => $record['row_number'] ?? null, 'normalized' => $normalized, 'issues' => $issues];
  }
  $qualityScore = etds_qc_calculate_quality_score($validated['records']);
  $validated['summary'] = ['total_records' => count($records), 'passed_records' => $passed, 'failed_records' => $failed, 'warning_records' => $warning, 'quality_score' => $qualityScore, 'ready_status' => false, 'last_validated_on' => etds_qc_now()];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'validated_data.json'), $validated);
  $session = etds_qc_find_session($sessionId);
  if ($session) {
    $session['status'] = 'validation';
    $session['quality_score'] = $qualityScore;
    $session['last_action'] = 'validation_run_completed';
    $session['export_readiness'] = etds_qc_export_readiness($sessionId);
    if ($session['export_readiness']) { $session['status'] = 'ready'; }
    etds_qc_save_session($session);
  }
  etds_qc_audit($sessionId, $user, 'validation_completed', 'Validation completed', ['quality_score' => $qualityScore]);
  return $validated;
}

function etds_qc_update_issue_status(string $sessionId, string $recordId, string $issueId, string $status, array $user): void {
  $validated = etds_qc_load_json(etds_qc_session_file($sessionId, 'validated_data.json'), ['summary' => [], 'records' => []]);
  foreach (($validated['records'] ?? []) as &$record) {
    if (($record['record_id'] ?? '') !== $recordId) { continue; }
    foreach (($record['issues'] ?? []) as &$issue) {
      if (($issue['issue_id'] ?? '') === $issueId) {
        $issue['resolution_status'] = $status;
      }
    }
    unset($issue);
  }
  unset($record);
  $validated['summary']['quality_score'] = etds_qc_calculate_quality_score($validated['records'] ?? []);
  $validated['summary']['last_validated_on'] = etds_qc_now();
  etds_qc_write_json(etds_qc_session_file($sessionId, 'validated_data.json'), $validated);
  $session = etds_qc_find_session($sessionId);
  if ($session) {
    $session['quality_score'] = (int) ($validated['summary']['quality_score'] ?? 0);
    $session['export_readiness'] = etds_qc_export_readiness($sessionId);
    if ($session['export_readiness']) { $session['status'] = 'ready'; }
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
  $validated = etds_qc_load_json(etds_qc_session_file($sessionId, 'validated_data.json'), ['records' => []]);
  $challans = etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]);
  $challanRows = is_array($challans['challans'] ?? null) ? $challans['challans'] : [];
  $challanMap = [];
  $challanTotal = 0.0;
  foreach ($challanRows as $challan) {
    $ref = (string) ($challan['challan_reference'] ?? '');
    $challanMap[$ref] = $challan;
    $challanTotal += (float) ($challan['total_available'] ?? 0);
  }
  $allocated = [];
  $deducteeTotal = 0.0;
  $exceptions = [];
  foreach (($validated['records'] ?? []) as $record) {
    $amount = (float) (($record['normalized']['tds_amount'] ?? 0));
    $ref = trim((string) (($record['normalized']['challan_reference'] ?? '')));
    $deducteeTotal += $amount;
    if ($ref === '') {
      $exceptions[] = ['exception_id' => 'RECN-' . substr(bin2hex(random_bytes(4)), 0, 8), 'type' => 'deductee_without_allocation', 'severity' => 'critical', 'record_id' => $record['record_id'], 'message' => 'Deductee record has no challan allocation.', 'resolution_status' => 'open'];
      continue;
    }
    if (!isset($challanMap[$ref])) {
      $exceptions[] = ['exception_id' => 'RECN-' . substr(bin2hex(random_bytes(4)), 0, 8), 'type' => 'missing_challan', 'severity' => 'critical', 'record_id' => $record['record_id'], 'message' => 'Record references a challan that is not available in the register.', 'resolution_status' => 'open'];
      continue;
    }
    $allocated[$ref] = ($allocated[$ref] ?? 0) + $amount;
  }
  $allocatedTotal = 0.0;
  foreach ($challanRows as &$challan) {
    $ref = (string) ($challan['challan_reference'] ?? '');
    $challan['allocated_total'] = round((float) ($allocated[$ref] ?? 0), 2);
    $challan['balance_total'] = round((float) ($challan['total_available'] ?? 0) - $challan['allocated_total'], 2);
    $allocatedTotal += $challan['allocated_total'];
    if ($challan['balance_total'] > 0) {
      $exceptions[] = ['exception_id' => 'RECN-' . substr(bin2hex(random_bytes(4)), 0, 8), 'type' => 'unallocated_challan', 'severity' => 'warning', 'challan_reference' => $ref, 'message' => 'Challan has unallocated balance of ' . number_format((float) $challan['balance_total'], 2), 'resolution_status' => 'open'];
    } elseif ($challan['balance_total'] < 0) {
      $exceptions[] = ['exception_id' => 'RECN-' . substr(bin2hex(random_bytes(4)), 0, 8), 'type' => 'excess_utilization', 'severity' => 'critical', 'challan_reference' => $ref, 'message' => 'Challan is over-allocated by ' . number_format(abs((float) $challan['balance_total']), 2), 'resolution_status' => 'open'];
    }
  }
  unset($challan);
  $difference = round($challanTotal - $allocatedTotal, 2);
  $score = 100;
  foreach ($exceptions as $e) { $score -= ($e['severity'] === 'critical') ? 15 : 5; }
  $score = max(0, $score);
  $result = ['summary' => ['challan_total' => round($challanTotal, 2), 'allocated_total' => round($allocatedTotal, 2), 'deductee_total' => round($deducteeTotal, 2), 'balance' => round($challanTotal - $allocatedTotal, 2), 'difference' => $difference, 'reconciliation_score' => $score, 'ready_status' => false], 'exceptions' => $exceptions];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => $challanRows]);
  etds_qc_write_json(etds_qc_session_file($sessionId, 'reconciliation.json'), $result);
  $session = etds_qc_find_session($sessionId);
  if ($session) {
    $session['status'] = 'reconciliation';
    $session['reconciliation_score'] = $score;
    $session['export_readiness'] = etds_qc_export_readiness($sessionId);
    if ($session['export_readiness']) { $session['status'] = 'ready'; }
    $session['last_action'] = 'reconciliation_run_completed';
    etds_qc_save_session($session);
  }
  etds_qc_audit($sessionId, $user, 'reconciliation_completed', 'Reconciliation completed', ['difference' => $difference]);
  return $result;
}

function etds_qc_export_readiness(string $sessionId): bool {
  $validated = etds_qc_load_json(etds_qc_session_file($sessionId, 'validated_data.json'), ['summary' => [], 'records' => []]);
  $reconciliation = etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), ['summary' => [], 'exceptions' => []]);
  $threshold = (int) (etds_qc_config()['quality_threshold'] ?? 100);
  foreach (($validated['records'] ?? []) as $record) {
    foreach (($record['issues'] ?? []) as $issue) {
      if (($issue['severity'] ?? '') === 'critical' && ($issue['resolution_status'] ?? 'open') === 'open') {
        return false;
      }
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
  $source = etds_qc_load_json(etds_qc_session_file($sessionId, 'source_data.json'), ['documents' => [], 'source_columns' => [], 'records' => []]);
  $documents = is_array($source['documents'] ?? null) ? $source['documents'] : [];
  $deleted = false;
  $documents = array_values(array_filter($documents, static function (array $document) use ($sessionId, $fileId, &$deleted): bool {
    if (($document['file_id'] ?? '') !== $fileId) { return true; }
    $path = etds_qc_session_file($sessionId, 'uploads/original/' . ($document['stored_name'] ?? ''));
    if (is_file($path)) { @unlink($path); }
    $deleted = true;
    return false;
  }));
  if (!$deleted) { return false; }
  $source['documents'] = $documents;
  etds_qc_write_json(etds_qc_session_file($sessionId, 'source_data.json'), $source);
  etds_qc_reload_source_data($sessionId, $user);
  etds_qc_validate_session($sessionId, $user);
  etds_qc_audit($sessionId, $user, 'upload_deleted', 'Uploaded file deleted', ['file_id' => $fileId]);
  return true;
}

function etds_qc_archive_session(string $sessionId, array $user): void {
  $session = etds_qc_find_session($sessionId);
  if (!$session) { return; }
  $session['status'] = 'archived';
  $session['last_action'] = 'session_archived';
  etds_qc_save_session($session);
  etds_qc_audit($sessionId, $user, 'session_archived', 'Session archived');
}

function etds_qc_purge_session(string $sessionId, array $user): void {
  $session = etds_qc_find_session($sessionId);
  if (!$session) { return; }
  foreach (['uploads/original', 'output', 'logs'] as $directory) {
    $path = etds_qc_session_file($sessionId, $directory);
    if (!is_dir($path)) { continue; }
    foreach (glob($path . '/*') ?: [] as $item) {
      if (is_file($item)) { @unlink($item); }
    }
  }
  foreach (['source_data.json', 'validated_data.json', 'challans.json', 'reconciliation.json'] as $fileName) {
    $path = etds_qc_session_file($sessionId, $fileName);
    if (is_file($path)) { @unlink($path); }
  }
  $session['status'] = 'purged';
  $session['last_action'] = 'session_purged';
  etds_qc_save_session($session);
  etds_qc_audit($sessionId, $user, 'session_purged', 'Session purged');
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
