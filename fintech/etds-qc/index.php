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
$page_title = 'e-TDSDoc | E Tax Advisors';
$page_description = 'e-TDSDoc - Diagnose. Reconcile. Prepare. Your Intelligent TDS Data Health Checker for intake, diagnosis, reconciliation, and processing preparation.';
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

function etds_qc_render_flash(?array $flash): void {
  if (!$flash) {
    return;
  }
  $class = ($flash['type'] ?? '') === 'success' ? 'etds-alert etds-alert-success' : 'etds-alert etds-alert-error';
  echo '<div class="' . $class . '">' . etds_qc_h((string) ($flash['message'] ?? '')) . '</div>';
}

function etds_qc_nav_icon(string $name): string {
  return match ($name) {
    'dashboard' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/><path d="M9 20v-5h6v5"/></svg>',
    'intake' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5h16M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M9 11h6M9 15h4"/></svg>',
    'extraction' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v10"/><path d="m8 10 4 4 4-4"/><path d="M5 18h14"/></svg>',
    'bench' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v4H6z"/><path d="M9 8v3.5a3 3 0 0 1-.88 2.12L6 15.75V19h12v-3.25l-2.12-2.13A3 3 0 0 1 15 11.5V8"/></svg>',
    'excel' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v6h6"/><path d="m9 11 6 6M15 11l-6 6"/></svg>',
    'logout' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 17l5-5-5-5"/><path d="M15 12H4"/></svg>',
    default => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/></svg>',
  };
}

if ($view === 'login'):
require_once dirname(__DIR__, 2) . '/includes/header.php';
?>
<main id="main-content">
  <section class="container etds-login-wrap">
    <div class="etds-login-card">
      <div class="eyebrow">Internal Access</div>
      <h1>e-TDSDoc</h1>
      <p class="etds-subtitle">Diagnose. Reconcile. Prepare.</p>
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= etds_qc_h($page_title) ?></title>
  <meta name="description" content="<?= etds_qc_h($page_description) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= etds_qc_h($extra_css) ?>">
</head>
<body class="etds-app-mode">
<?php require __DIR__ . '/render_app_v3.php'; ?>
<script src="<?= etds_qc_h(site_href('/fintech/etds-qc/assets/js/etds-qc.js')) ?>"></script>
</body>
</html>
<?php
exit;
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
