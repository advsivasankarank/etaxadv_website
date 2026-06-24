<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap_runtime.php';
etds_qc_bootstrap();

$extra_head = <<<HTML
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
HTML;
$extra_css = site_href('/fintech/etds-qc/assets/css/etds-qc.css');

$view = $_GET['view'] ?? 'dashboard';
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$isAjax = (string) ($_REQUEST['ajax'] ?? '0') === '1';
$flash = etds_qc_pull_flash();
$user = etds_qc_current_user();
$page_title = 'e-TDS QC Tool | E Tax Advisors';
$page_description = 'AI-Driven Data Health Check for internal TDS intake, diagnosis, reconciliation, and processing preparation.';
$page_path = '/fintech/etds-qc/';

function etds_qc_respond(bool $isAjax, string $redirect, string $type, string $message, array $extra = []): never {
  if ($isAjax) {
    json_response([
      'ok' => $type === 'success',
      'type' => $type,
      'message' => $message,
      'redirect' => $redirect,
    ] + $extra);
  }
  etds_qc_flash($type, $message);
  header('Location: ' . $redirect);
  exit;
}

try {

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf($_POST['_csrf'] ?? null)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=login'), 'error', 'Security token expired. Please try again.');
  }
  $email = clean_input((string) ($_POST['email'] ?? ''), 150);
  $password = (string) ($_POST['password'] ?? '');
  if (etds_qc_login($email, $password)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/'), 'success', 'Welcome back. e-TDS Doctor is ready.');
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=login'), 'error', 'Invalid email or password.');
}

if ($action === 'logout') {
  etds_qc_logout();
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=login'), 'success', 'You have been signed out.');
}

if ($view !== 'login') {
  etds_qc_require_auth();
  $user = etds_qc_current_user();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'login') {
  if (!verify_csrf($_POST['_csrf'] ?? null)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/'), 'error', 'Security token expired. Please try again.');
  }
}

if ($action === 'create_session' && $user) {
  $tan = strtoupper(clean_input((string) ($_POST['tan'] ?? ''), 10));
  if ($tan === '' || !etds_qc_tan_valid($tan)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?ws=intake&view=create'), 'error', 'Enter a valid TAN in the format AAAA99999A.');
  }
  $session = etds_qc_create_session($_POST, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=intake&session=' . urlencode($session['session_id'])), 'success', 'QC session ' . $session['session_id'] . ' created.');
}

if ($action === 'upload_documents' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $session = etds_qc_find_session($sessionId);
  if ($session && isset($_FILES['documents'])) {
    etds_qc_ensure_session_structure($sessionId);
    $source = etds_qc_load_json(etds_qc_session_file($sessionId, 'source_data.json'), ['documents' => [], 'source_columns' => [], 'records' => []]);
    $documents = is_array($source['documents'] ?? null) ? $source['documents'] : [];
    $names = $_FILES['documents']['name'] ?? [];
    $tmpNames = $_FILES['documents']['tmp_name'] ?? [];
    $sizes = $_FILES['documents']['size'] ?? [];
    $errors = $_FILES['documents']['error'] ?? [];
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
      $fileId = 'FIL-' . str_pad((string) (count($documents) + 1), 4, '0', STR_PAD_LEFT);
      $storedName = gmdate('Ymd_His') . '_' . preg_replace('/[^A-Za-z0-9._-]+/', '-', basename((string) $originalName));
      $target = etds_qc_session_file($sessionId, 'uploads/original/' . $storedName);
      if (move_uploaded_file((string) $tmpNames[$index], $target)) {
        $documents[] = [
          'file_id' => $fileId,
          'file_name' => basename((string) $originalName),
          'stored_name' => $storedName,
          'extension' => $extension,
          'mime_type' => etds_qc_detect_mime_type($target),
          'size_bytes' => (int) ($sizes[$index] ?? 0),
          'uploaded_on' => etds_qc_now(),
          'uploaded_by' => $user['id'],
          'uploaded_by_name' => $user['name'] ?? $user['email'],
          'extraction_status' => 'pending',
        ];
      }
    }
    $source['documents'] = $documents;
    etds_qc_write_json(etds_qc_session_file($sessionId, 'source_data.json'), $source);
    etds_qc_audit($sessionId, $user, 'documents_uploaded', 'Documents uploaded', ['count' => count($documents)]);
    $session['last_action'] = 'documents_uploaded';
    etds_qc_save_session($session);
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=extraction&session=' . urlencode($sessionId)), 'success', 'Documents uploaded successfully.');
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=extraction&session=' . urlencode($sessionId)), 'error', 'No documents were uploaded.');
}

if ($action === 'delete_upload' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $deleted = etds_qc_delete_upload($sessionId, (string) ($_POST['file_id'] ?? ''), $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=extraction&session=' . urlencode($sessionId)), $deleted ? 'success' : 'error', $deleted ? 'Upload deleted and data refreshed.' : 'Upload could not be deleted.');
}

if ($action === 'extract_validate' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_reload_source_data($sessionId, $user);
  etds_qc_validate_session($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=bench&tab=diagnosis&session=' . urlencode($sessionId)), 'success', 'Diagnosis complete.');
}

if ($action === 'issue_status' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_update_issue_status(
    $sessionId,
    (string) ($_POST['record_id'] ?? ''),
    (string) ($_POST['issue_id'] ?? ''),
    (string) ($_POST['issue_status'] ?? 'resolved'),
    $user
  );
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=bench&tab=treatment&session=' . urlencode($sessionId)), 'success', 'Issue updated.');
}

if ($action === 'edit_record' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_edit_record($sessionId, (string) ($_POST['record_id'] ?? ''), $_POST, $user);
  etds_qc_validate_session($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=bench&tab=treatment&session=' . urlencode($sessionId)), 'success', 'Record updated and revalidated.');
}

if ($action === 'add_challan' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $challans = etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]);
  $rows = is_array($challans['challans'] ?? null) ? $challans['challans'] : [];
  $rows[] = [
    'challan_id' => 'CHL-' . str_pad((string) (count($rows) + 1), 4, '0', STR_PAD_LEFT),
    'challan_reference' => clean_input((string) ($_POST['challan_reference'] ?? ''), 50),
    'bsr_code' => clean_input((string) ($_POST['bsr_code'] ?? ''), 20),
    'deposit_date' => clean_input((string) ($_POST['deposit_date'] ?? ''), 20),
    'section_code' => clean_input((string) ($_POST['section_code'] ?? ''), 20),
    'total_available' => round((float) ($_POST['total_available'] ?? 0), 2),
    'allocated_total' => 0,
    'balance_total' => round((float) ($_POST['total_available'] ?? 0), 2),
  ];
  etds_qc_write_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => $rows]);
  etds_qc_audit($sessionId, $user, 'challan_added', 'Challan added', ['challan_reference' => $_POST['challan_reference'] ?? '']);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=bench&tab=reconciliation&session=' . urlencode($sessionId)), 'success', 'Challan saved.');
}

if ($action === 'run_reconciliation' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_reconcile($sessionId, $user);
  $session = etds_qc_find_session($sessionId);
  if ($session) {
    $session['export_readiness'] = etds_qc_export_readiness($sessionId);
    if ($session['export_readiness']) {
      $session['status'] = 'ready';
    }
    etds_qc_save_session($session);
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=bench&tab=reconciliation&session=' . urlencode($sessionId)), 'success', 'Reconciliation completed.');
}

if ($action === 'export_xlsx' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $session = etds_qc_find_session($sessionId);
  if ($session) {
    $fileName = etds_qc_write_export_xlsx($sessionId, $session, $user);
    if ($fileName) {
      etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=excel&session=' . urlencode($sessionId)), 'success', 'Excel file generated: ' . $fileName, ['file_name' => $fileName]);
    } else {
      etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=bench&tab=readiness&session=' . urlencode($sessionId)), 'error', 'Export is blocked until validation and reconciliation are fully clean.');
    }
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=session&ws=excel&session=' . urlencode($sessionId)), 'error', 'Session was not found.');
}

if ($action === 'archive_session' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_archive_session($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?ws=intake'), 'success', 'Session archived.');
}

if ($action === 'purge_session' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_purge_session($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?ws=intake'), 'success', 'Session purged. Metadata and audit log were retained.');
}

if ($action === 'download' && $user) {
  $sessionId = (string) ($_GET['session'] ?? '');
  $file = basename((string) ($_GET['file'] ?? ''));
  $target = etds_qc_session_file($sessionId, 'output/' . $file);
  if (is_file($target)) {
    $session = etds_qc_find_session($sessionId);
    if ($session) {
      $session['status'] = 'downloaded';
      $session['last_action'] = 'export_downloaded';
      etds_qc_save_session($session);
      etds_qc_audit($sessionId, $user, 'export_downloaded', 'Export workbook downloaded', ['file_name' => $file]);
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Content-Length: ' . filesize($target));
    readfile($target);
    exit;
  }
}

if ($action === 'preview' && $user) {
  $sessionId = (string) ($_GET['session'] ?? '');
  $file = basename((string) ($_GET['file'] ?? ''));
  $target = etds_qc_session_file($sessionId, 'uploads/original/' . $file);
  if (is_file($target)) {
    header('Content-Type: ' . etds_qc_detect_mime_type($target));
    header('Content-Disposition: inline; filename="' . $file . '"');
    readfile($target);
    exit;
  }
}

require_once dirname(__DIR__, 2) . '/includes/header.php';

function etds_qc_render_flash(?array $flash): void {
  if (!$flash) {
    return;
  }
  $class = ($flash['type'] ?? '') === 'success' ? 'etds-alert etds-alert-success' : 'etds-alert etds-alert-error';
  echo '<div class="' . $class . '">' . etds_qc_h((string) ($flash['message'] ?? '')) . '</div>';
}

function etds_qc_nav_icon(string $name): string {
  return match ($name) {
    'overview' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/><path d="M9 20v-5h6v5"/></svg>',
    'intake' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5h16M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M9 11h6M9 15h4"/></svg>',
    'extraction' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v10"/><path d="m8 10 4 4 4-4"/><path d="M5 18h14"/></svg>',
    'bench' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v4H6z"/><path d="M9 8v3.5a3 3 0 0 1-.88 2.12L6 15.75V19h12v-3.25l-2.12-2.13A3 3 0 0 1 15 11.5V8"/></svg>',
    'excel' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v6h6"/><path d="m9 11 6 6M15 11l-6 6"/></svg>',
    'logout' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 17l5-5-5-5"/><path d="M15 12H4"/></svg>',
    default => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/></svg>',
  };
}

if ($view === 'login'):
?>
<main id="main-content">
  <section class="container etds-login-wrap">
    <div class="etds-login-card">
      <div class="eyebrow">Internal Access</div>
      <h1>e-TDS QC Tool</h1>
      <p class="etds-subtitle">AI-Driven Data Health Check</p>
      <?php etds_qc_render_flash($flash); ?>
      <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="login">
        <div class="etds-fields">
          <div class="etds-field etds-field-full">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="admin@etaxadv.local" required autofocus>
          </div>
          <div class="etds-field etds-field-full">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
          </div>
        </div>
        <div class="etds-action-row" style="margin-top:18px;">
          <button class="btn btn-primary" type="submit">Sign In</button>
          <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech-tools.php')) ?>">Back to Fintech Tools</a>
        </div>
      </form>
    </div>
  </section>
</main>
<?php
require_once dirname(__DIR__, 2) . '/includes/footer.php';
exit;
endif;

$sessions = etds_qc_all_sessions();
$sessionId = (string) ($_GET['session'] ?? '');
$activeSession = $sessionId !== '' ? etds_qc_find_session($sessionId) : null;
$sourceData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'source_data.json'), ['documents' => [], 'records' => [], 'source_columns' => []]) : ['documents' => [], 'records' => [], 'source_columns' => []];
$validatedData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'validated_data.json'), ['summary' => [], 'records' => []]) : ['summary' => [], 'records' => []];
$challans = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]) : ['challans' => []];
$reconciliation = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), ['summary' => [], 'exceptions' => []]) : ['summary' => [], 'exceptions' => []];
$exportFiles = $activeSession ? (glob(etds_qc_session_file($sessionId, 'output/*.xlsx')) ?: []) : [];
$sessionStates = [];
foreach ($sessions as $row) {
  $sessionStates[(string) ($row['session_id'] ?? '')] = etds_qc_session_state($row);
}

$counts = [
  'sessions' => count($sessions),
  'validation' => count(array_filter($sessionStates, static fn(array $state): bool => ($state['key'] ?? '') === 'pending_validation')),
  'reconciliation' => count(array_filter($sessionStates, static fn(array $state): bool => ($state['key'] ?? '') === 'pending_reconciliation')),
  'ready' => count(array_filter($sessionStates, static fn(array $state): bool => ($state['key'] ?? '') === 'ready')),
  'completed' => count(array_filter($sessionStates, static fn(array $state): bool => ($state['key'] ?? '') === 'completed')),
];

require __DIR__ . '/render_app_v2.php';
require_once dirname(__DIR__, 2) . '/includes/footer.php';
exit;
?>
<main id="main-content">
  <section class="container etds-shell">
    <?php etds_qc_render_flash($flash); ?>

    <?php if ($view === 'create'): ?>
      <div class="etds-page-head">
        <div>
          <div class="eyebrow">Create Diagnostic Session</div>
          <h1>New e-TDS Doctor Session</h1>
          <p class="etds-subtitle">Generate a session ID, capture client metadata, and start the diagnostic workflow.</p>
        </div>
        <div class="etds-chip-row">
          <span class="etds-chip">Operator: <?= etds_qc_h((string) ($user['name'] ?? '')) ?></span>
          <span class="etds-chip">JSON Storage</span>
        </div>
      </div>

      <div class="etds-panel">
        <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="create_session">
          <div class="etds-fields">
            <div class="etds-field">
              <label for="client_name">Client Name</label>
              <input id="client_name" name="client_name" required>
            </div>
            <div class="etds-field">
              <label for="tan">TAN</label>
              <input id="tan" name="tan" maxlength="10" required>
            </div>
            <div class="etds-field">
              <label for="financial_year">Financial Year</label>
              <select id="financial_year" name="financial_year" required>
                <option value="2025-26">2025-26</option>
                <option value="2024-25">2024-25</option>
              </select>
            </div>
            <div class="etds-field">
              <label for="quarter">Quarter</label>
              <select id="quarter" name="quarter" required>
                <option>Q1</option>
                <option>Q2</option>
                <option>Q3</option>
                <option>Q4</option>
              </select>
            </div>
            <div class="etds-field">
              <label for="return_type">Return Type</label>
              <select id="return_type" name="return_type" required>
                <option>24Q</option>
                <option>26Q</option>
                <option>27Q</option>
                <option>27EQ</option>
              </select>
            </div>
            <div class="etds-field etds-field-full">
              <label for="remarks">Remarks</label>
              <textarea id="remarks" name="remarks"></textarea>
            </div>
          </div>
          <div class="etds-action-row" style="margin-top:18px;">
            <button class="btn btn-primary" type="submit">Create Session</button>
            <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">Back to Dashboard</a>
          </div>
        </form>
      </div>
    <?php elseif ($view === 'session' && $activeSession): ?>
      <?php
      $quality = (int) ($validatedData['summary']['quality_score'] ?? 0);
      $reconScore = (int) ($reconciliation['summary']['reconciliation_score'] ?? 0);
      $readiness = etds_qc_export_readiness($sessionId);
      $activeState = $sessionStates[$sessionId] ?? etds_qc_session_state($activeSession);
      ?>
      <div class="etds-page-head">
        <div>
          <div class="eyebrow">Diagnostic Session</div>
          <h1><?= etds_qc_h((string) $activeSession['session_id']) ?> <span class="etds-muted"><?= etds_qc_h((string) $activeSession['client_name']) ?></span></h1>
          <p class="etds-subtitle"><?= etds_qc_h((string) $activeSession['tan']) ?> · FY <?= etds_qc_h((string) $activeSession['financial_year']) ?> · <?= etds_qc_h((string) $activeSession['quarter']) ?> · <?= etds_qc_h((string) $activeSession['return_type']) ?></p>
          <div class="etds-chip-row" style="margin-top:12px;">
            <span class="etds-chip">Status: <?= etds_qc_h((string) ($activeState['label'] ?? $activeSession['status'])) ?></span>
            <span class="etds-chip">Last Action: <?= etds_qc_h((string) $activeSession['last_action']) ?></span>
          </div>
        </div>
        <div>
          <div class="etds-score-row">
            <span class="etds-status-chip" data-tone="<?= $quality >= 95 ? 'good' : ($quality >= 80 ? 'warning' : 'critical') ?>">Data Health Score <?= $quality ?>%</span>
            <span class="etds-status-chip" data-tone="<?= $reconScore >= 95 ? 'good' : ($reconScore >= 80 ? 'warning' : 'critical') ?>">Reconciliation <?= $reconScore ?>%</span>
            <span class="etds-status-chip" data-tone="<?= $readiness ? 'good' : 'critical' ?>"><?= $readiness ? 'Fit for Processing' : 'Treatment Required' ?></span>
          </div>
          <div class="etds-progress"><span style="width: <?= max($quality, 5) ?>%;"></span></div>
        </div>
      </div>

      <div class="etds-command-grid">
        <div>
          <div class="etds-panel">
            <h3>Diagnostic Intake</h3>
            <p class="etds-subtitle">Upload Excel, CSV, PDF, and image files. CSV and XLSX are interpreted when e-TDS Doctor runs a diagnosis.</p>
            <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="upload_documents">
              <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
              <div class="etds-field">
                <label for="documents">Case Files</label>
                <input id="documents" name="documents[]" type="file" multiple required>
              </div>
              <div class="etds-action-row" style="margin-top:16px;">
                <button class="btn btn-primary" type="submit">Upload Case Files</button>
              </div>
            </form>
            <div style="margin-top:18px;">
              <?php foreach (($sourceData['documents'] ?? []) as $document): ?>
                <div class="etds-doc-item">
                  <div>
                    <strong><?= etds_qc_h((string) $document['file_name']) ?></strong>
                    <div class="etds-muted"><?= etds_qc_h((string) strtoupper((string) $document['extension'])) ?> · <?= number_format(((int) $document['size_bytes']) / 1024, 1) ?> KB · <?= etds_qc_h((string) $document['extraction_status']) ?></div>
                    <?php if (!empty($document['raw_text_excerpt'])): ?>
                      <div class="etds-muted" style="margin-top:4px;">OCR/Text excerpt: <?= etds_qc_h((string) $document['raw_text_excerpt']) ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="etds-action-row">
                    <a class="btn btn-outline btn-sm" target="_blank" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=preview&session=' . urlencode($sessionId) . '&file=' . urlencode((string) $document['stored_name']))) ?>">Preview</a>
                    <form class="etds-inline-form" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete_upload">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <input type="hidden" name="file_id" value="<?= etds_qc_h((string) $document['file_id']) ?>">
                      <button class="btn btn-outline btn-sm" type="submit" data-confirm="Delete this uploaded file and refresh the extracted data?">Delete</button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
              <?php if (empty($sourceData['documents'])): ?>
                <p class="etds-muted">No files uploaded yet.</p>
              <?php endif; ?>
            </div>
            <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="margin-top:18px;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="extract_validate">
              <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
              <button class="btn btn-gold" type="submit">Run Diagnosis</button>
            </form>
          </div>

          <div class="etds-panel" id="reconciliation">
            <h3>Challan Register</h3>
            <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="add_challan">
              <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
              <div class="etds-fields">
                <div class="etds-field">
                  <label>Challan Reference</label>
                  <input name="challan_reference" required>
                </div>
                <div class="etds-field">
                  <label>BSR Code</label>
                  <input name="bsr_code">
                </div>
                <div class="etds-field">
                  <label>Deposit Date</label>
                  <input name="deposit_date" type="date">
                </div>
                <div class="etds-field">
                  <label>Section Code</label>
                  <input name="section_code">
                </div>
                <div class="etds-field">
                  <label>Total Available</label>
                  <input name="total_available" type="number" step="0.01" required>
                </div>
              </div>
              <div class="etds-action-row" style="margin-top:16px;">
                <button class="btn btn-outline" type="submit">Add Challan</button>
              </div>
            </form>
            <ul class="etds-mini-list" style="margin-top:18px;">
              <?php foreach (($challans['challans'] ?? []) as $challan): ?>
                <li>
                  <span><strong><?= etds_qc_h((string) $challan['challan_reference']) ?></strong><br><span class="etds-muted"><?= etds_qc_h((string) $challan['section_code']) ?> · BSR <?= etds_qc_h((string) $challan['bsr_code']) ?></span></span>
                  <span><?= number_format((float) ($challan['total_available'] ?? 0), 2) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
            <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="margin-top:18px;" data-ajax="reload">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="run_reconciliation">
              <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
              <button class="btn btn-gold" type="submit">Run Reconciliation</button>
            </form>
          </div>
        </div>

        <div>
          <div class="etds-panel">
            <h2>Doctor's Findings</h2>
            <p class="etds-subtitle">Only health issues appear here. Clean records stay out of the way.</p>
            <?php
            $visibleIssues = 0;
            foreach (($validatedData['records'] ?? []) as $record):
              foreach (($record['issues'] ?? []) as $issue):
                if (($issue['resolution_status'] ?? 'open') !== 'open') {
                  continue;
                }
                $visibleIssues++;
            ?>
              <article class="etds-issue-card" data-severity="<?= etds_qc_h((string) $issue['severity']) ?>">
                <div class="etds-chip-row" style="margin-bottom:10px;">
                  <span class="etds-status-chip" data-tone="<?= $issue['severity'] === 'critical' ? 'critical' : 'warning' ?>"><?= etds_qc_h($issue['severity'] === 'critical' ? 'Critical Issue' : 'Moderate Issue') ?></span>
                  <span class="etds-chip"><?= etds_qc_h((string) $record['record_id']) ?></span>
                </div>
                <h4><?= etds_qc_h((string) $issue['message']) ?></h4>
                <p><strong>Deductee:</strong> <?= etds_qc_h((string) ($record['normalized']['deductee_name'] ?? 'Unknown')) ?> · <strong>PAN:</strong> <?= etds_qc_h((string) ($record['normalized']['pan'] ?? '')) ?></p>
                <p><strong>Treatment suggestion:</strong> <?= etds_qc_h((string) $issue['suggested_correction']) ?></p>
                <div class="etds-issue-actions">
                  <?php foreach (['resolved' => 'Mark Resolved', 'accepted' => 'Accept', 'ignored' => 'Ignore'] as $statusValue => $label): ?>
                    <form class="etds-inline-form" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="issue_status">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <input type="hidden" name="record_id" value="<?= etds_qc_h((string) $record['record_id']) ?>">
                      <input type="hidden" name="issue_id" value="<?= etds_qc_h((string) $issue['issue_id']) ?>">
                      <input type="hidden" name="issue_status" value="<?= etds_qc_h($statusValue) ?>">
                      <button class="btn btn-outline btn-sm" type="submit"><?= etds_qc_h($label) ?></button>
                    </form>
                  <?php endforeach; ?>
                </div>
                <div class="etds-issue-edit">
                  <details>
                    <summary>Edit Record</summary>
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="margin-top:14px;" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="edit_record">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <input type="hidden" name="record_id" value="<?= etds_qc_h((string) $record['record_id']) ?>">
                      <div class="etds-fields">
                        <div class="etds-field"><label>Name</label><input name="deductee_name" value="<?= etds_qc_h((string) ($record['normalized']['deductee_name'] ?? '')) ?>"></div>
                        <div class="etds-field"><label>PAN</label><input name="pan" value="<?= etds_qc_h((string) ($record['normalized']['pan'] ?? '')) ?>"></div>
                        <div class="etds-field"><label>Amount</label><input name="tds_amount" value="<?= etds_qc_h((string) ($record['normalized']['tds_amount'] ?? '')) ?>"></div>
                        <div class="etds-field"><label>Date</label><input name="deduction_date" value="<?= etds_qc_h((string) ($record['normalized']['deduction_date'] ?? '')) ?>"></div>
                        <div class="etds-field"><label>Invoice</label><input name="invoice_number" value="<?= etds_qc_h((string) ($record['normalized']['invoice_number'] ?? '')) ?>"></div>
                        <div class="etds-field"><label>Challan Reference</label><input name="challan_reference" value="<?= etds_qc_h((string) ($record['normalized']['challan_reference'] ?? '')) ?>"></div>
                      </div>
                      <div class="etds-action-row" style="margin-top:16px;">
                        <button class="btn btn-primary btn-sm" type="submit">Save &amp; Revalidate</button>
                      </div>
                    </form>
                  </details>
                </div>
              </article>
            <?php
              endforeach;
            endforeach;
            ?>
            <?php if ($visibleIssues === 0): ?>
              <div class="etds-empty">Diagnosis complete. No open health issues are currently in the queue.</div>
            <?php endif; ?>
          </div>
        </div>

        <div>
          <div class="etds-panel">
            <h3>Case Summary</h3>
            <ul class="etds-mini-list">
              <li><span>Total Records</span><strong><?= etds_qc_h((string) ($validatedData['summary']['total_records'] ?? 0)) ?></strong></li>
              <li><span>Fit for Processing</span><strong><?= etds_qc_h((string) ($validatedData['summary']['passed_records'] ?? 0)) ?></strong></li>
              <li><span>Critical Issues</span><strong><?= etds_qc_h((string) ($validatedData['summary']['failed_records'] ?? 0)) ?></strong></li>
              <li><span>Moderate Issues</span><strong><?= etds_qc_h((string) ($validatedData['summary']['warning_records'] ?? 0)) ?></strong></li>
              <li><span>Source Columns</span><strong><?= etds_qc_h((string) count($sourceData['source_columns'] ?? [])) ?></strong></li>
            </ul>
          </div>

          <div class="etds-panel">
            <h3>Reconciliation Dashboard</h3>
            <ul class="etds-mini-list">
              <li><span>Challan Total</span><strong><?= number_format((float) ($reconciliation['summary']['challan_total'] ?? 0), 2) ?></strong></li>
              <li><span>Allocated Total</span><strong><?= number_format((float) ($reconciliation['summary']['allocated_total'] ?? 0), 2) ?></strong></li>
              <li><span>Deductee Total</span><strong><?= number_format((float) ($reconciliation['summary']['deductee_total'] ?? 0), 2) ?></strong></li>
              <li><span>Difference</span><strong><?= number_format((float) ($reconciliation['summary']['difference'] ?? 0), 2) ?></strong></li>
            </ul>
            <?php if (!empty($reconciliation['exceptions'])): ?>
              <div style="margin-top:16px;">
                <?php foreach (($reconciliation['exceptions'] ?? []) as $exception): ?>
                  <div class="etds-doc-item">
                    <span><?= etds_qc_h((string) $exception['message']) ?></span>
                    <span class="etds-status-chip" data-tone="<?= ($exception['severity'] ?? '') === 'critical' ? 'critical' : 'warning' ?>"><?= etds_qc_h((string) $exception['severity']) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="etds-panel" id="export">
            <h3>Fit for Processing</h3>
            <p class="etds-subtitle">Processing output is allowed only when critical health issues are closed and reconciliation difference is zero.</p>
            <div class="etds-chip-row" style="margin-bottom:16px;">
              <span class="etds-status-chip" data-tone="<?= $readiness ? 'good' : 'critical' ?>"><?= $readiness ? 'Fit for Processing' : 'Treatment Required' ?></span>
            </div>
            <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="export_xlsx">
              <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
              <button class="btn btn-primary" type="submit">Generate Processing Excel</button>
            </form>
            <?php if (!empty($exportFiles)): ?>
              <ul class="etds-mini-list" style="margin-top:16px;">
                <?php foreach ($exportFiles as $filePath): $fileName = basename($filePath); ?>
                  <li>
                    <span><?= etds_qc_h($fileName) ?></span>
                    <a class="btn btn-outline btn-sm" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download&session=' . urlencode($sessionId) . '&file=' . urlencode($fileName))) ?>">Download</a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <div class="etds-action-row" style="margin-top:16px;">
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="archive_session">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <button class="btn btn-outline btn-sm" type="submit">Archive Session</button>
              </form>
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="purge_session">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <button class="btn btn-outline btn-sm" type="submit" data-confirm="Purge uploads, extracted data, and generated files for this session?">Purge Session</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="etds-page-head">
        <div>
          <div class="eyebrow">e-TDS Doctor Command Centre</div>
          <h1>e-TDS QC Tool Dashboard</h1>
          <p class="etds-subtitle">AI-Driven Data Health Check for TDS intake, diagnosis, reconciliation, and processing preparation.</p>
        </div>
        <div class="etds-action-row">
          <a class="btn btn-primary" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=create')) ?>">New Session</a>
          <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="logout">
            <button class="btn btn-outline" type="submit">Sign Out</button>
          </form>
        </div>
      </div>

      <div class="etds-grid etds-dashboard-grid">
        <div class="etds-stat"><strong><?= $counts['sessions'] ?></strong><span>Sessions Created</span></div>
        <div class="etds-stat"><strong><?= $counts['validation'] ?></strong><span>Pending Diagnosis</span></div>
        <div class="etds-stat"><strong><?= $counts['reconciliation'] ?></strong><span>Pending Reconciliation</span></div>
        <div class="etds-stat"><strong><?= $counts['ready'] ?></strong><span>Fit for Processing</span></div>
        <div class="etds-stat"><strong><?= $counts['completed'] ?></strong><span>Completed</span></div>
      </div>

      <div class="etds-grid etds-two-col">
        <div class="etds-panel">
          <h2>Quick Actions</h2>
          <div class="etds-action-row" style="margin-top:16px;">
            <a class="btn btn-primary" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=create')) ?>">Create Diagnostic Session</a>
            <?php if (!empty($sessions)): ?>
              <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=session&session=' . urlencode((string) $sessions[0]['session_id']))) ?>">Continue Latest Session</a>
            <?php endif; ?>
          </div>
        </div>

        <div class="etds-panel">
          <h2>Access Model</h2>
          <p class="etds-subtitle">Authenticated internal users only. Sessions are stored in isolated JSON folders with audit logs and export history.</p>
          <div class="etds-chip-row" style="margin-top:12px;">
            <span class="etds-chip">Role: <?= etds_qc_h((string) ($user['role'] ?? 'operator')) ?></span>
            <span class="etds-chip">User: <?= etds_qc_h((string) ($user['email'] ?? '')) ?></span>
          </div>
        </div>
      </div>

      <div class="etds-panel" style="margin-top:24px;">
        <h2>Recent Sessions</h2>
        <?php if (empty($sessions)): ?>
          <div class="etds-empty">No QC sessions have been created yet.</div>
        <?php else: ?>
          <div class="etds-table-shell">
            <table class="etds-table">
              <thead>
                <tr>
                  <th>Session</th>
                  <th>Client</th>
                  <th>Return</th>
                  <th>Status</th>
                  <th>Data Health</th>
                  <th>Reconciliation</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sessions as $row): ?>
                  <?php $rowState = $sessionStates[(string) ($row['session_id'] ?? '')] ?? etds_qc_session_state($row); ?>
                  <tr>
                    <td><?= etds_qc_h((string) $row['session_id']) ?></td>
                    <td><?= etds_qc_h((string) $row['client_name']) ?></td>
                    <td><?= etds_qc_h((string) $row['quarter']) ?> / <?= etds_qc_h((string) $row['return_type']) ?></td>
                    <td><?= etds_qc_h((string) ($rowState['label'] ?? $row['status'])) ?></td>
                    <td><?= etds_qc_h((string) $row['quality_score']) ?>%</td>
                    <td><?= etds_qc_h((string) $row['reconciliation_score']) ?>%</td>
                    <td><a class="btn btn-outline btn-sm" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=session&session=' . urlencode((string) $row['session_id']))) ?>">Open Session</a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<script src="<?= etds_qc_h(site_href('/fintech/etds-qc/assets/js/etds-qc.js')) ?>"></script>
<?php require_once dirname(__DIR__, 2) . '/includes/footer.php'; ?>
<?php
} catch (Throwable $exception) {
  etds_qc_log_runtime_error('index.php', $exception);
  if (!headers_sent()) {
    etds_qc_flash('error', 'The request could not be completed. Please try again or contact support.');
    header('Location: ' . site_href('/fintech/etds-qc/'));
    exit;
  }
  throw $exception;
}
?>
