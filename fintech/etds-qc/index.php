<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap_runtime.php';
etds_qc_bootstrap();
send_security_headers();

$extra_head = <<<HTML
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
HTML;
$extra_css = site_href('/fintech/etds-qc/assets/css/etds-qc.css');

$view = $_GET['view'] ?? 'gateway';
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$isAjax = (string) ($_REQUEST['ajax'] ?? '0') === '1';
$flash = etds_qc_pull_flash();
$user = etds_qc_current_user();
$page_title = 'eTDSDoc | E Tax Advisors';
$page_description = 'eTDSDoc - Examine. Diagnose. Treat. Your Intelligent TDS Data QC workspace for intake, diagnosis, reconciliation, and deliverables preparation.';
$page_path = '/fintech/etds-qc/';

$viewAliases = [
  'cases' => 'gateway',
  'upload' => 'upload-console',
  'extract' => 'etdsdoc',
  'doctor' => 'etdsdoc',
  'spreadsheet' => 'etdsdoc',
  'output' => 'deliverables',
];
if (isset($viewAliases[$view]) && $action === null) {
  $aliasTarget = $viewAliases[$view];
  $aliasParams = $_GET;
  unset($aliasParams['view']);
  if ($view === 'extract') { $aliasParams['tab'] = 'examination'; }
  if ($view === 'doctor') { $aliasParams['tab'] = 'diagnosis'; }
  if ($view === 'spreadsheet') { $aliasParams['tab'] = 'review'; }
  $aliasParams['view'] = $aliasTarget;
  header('Location: ' . site_href('/fintech/etds-qc/?' . http_build_query($aliasParams)));
  exit;
}

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
  if (!rate_limit_check('login', 10)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=login'), 'error', 'Too many login attempts. Please wait 15 minutes and try again.');
  }
  if (!verify_csrf($_POST['_csrf'] ?? null)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'error', 'Security token expired. Please try again.');
  }
  $email = clean_input((string) ($_POST['email'] ?? ''), 150);
  $password = (string) ($_POST['password'] ?? '');
  if (etds_qc_login($email, $password)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'success', 'Welcome back.');
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
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'error', 'Security token expired. Please try again.');
  }
}

if ($action === 'create_session' && $user) {
  $tan = strtoupper(clean_input((string) ($_POST['tan'] ?? ''), 10));
  if ($tan === '' || !etds_qc_tan_valid($tan)) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'error', 'Enter a valid TAN in the format AAAA99999A.');
  }
  $session = etds_qc_create_session($_POST, $user);
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=upload-console&session=' . urlencode($session['session_id'])), 'success', 'Case ' . $session['session_id'] . ' created. Proceed to upload documents.');
}

if ($action === 'upload_documents' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $uploadCategory = (string) ($_POST['upload_category'] ?? '');
  $session = etds_qc_find_session($sessionId);
  if ($session && isset($_FILES['documents'])) {
    $result = etds_qc_register_uploads($sessionId, $_FILES['documents'], $user, $uploadCategory);
    if (($result['uploaded'] ?? 0) > 0) {
      $suffix = (($result['duplicates'] ?? 0) > 0 || ($result['versions'] ?? 0) > 0)
        ? ' Duplicate and version details were captured in the document register.'
        : '';
      etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=upload-console&session=' . urlencode($sessionId)), 'success', 'Documents uploaded successfully.' . $suffix);
    }
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=upload-console&session=' . urlencode($sessionId)), 'error', 'No documents were uploaded.');
}

if ($action === 'delete_upload' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $deleted = etds_qc_delete_upload($sessionId, (string) ($_POST['file_id'] ?? ''), $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=upload-console&session=' . urlencode($sessionId)), $deleted ? 'success' : 'error', $deleted ? 'Upload deleted and data refreshed.' : 'Upload could not be deleted.');
}

if ($action === 'extract_validate' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_reload_source_data($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&session=' . urlencode($sessionId)), 'success', 'AI extraction completed. Structured data is ready for review.');
}

if ($action === 'workspace_edit' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $result = etds_qc_workspace_record_change(
    $sessionId,
    (string) ($_POST['sheet'] ?? 'deductees'),
    (string) ($_POST['record_id'] ?? ''),
    (string) ($_POST['field'] ?? ''),
    clean_input((string) ($_POST['value'] ?? ''), 250),
    $user,
    clean_multiline((string) ($_POST['reason'] ?? ''), 250),
    (string) ($_POST['mode'] ?? 'manual_override')
  );
  if ($isAjax) {
    json_response(['ok' => true] + $result);
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode((string) ($_POST['sheet'] ?? 'deductees')) . '&session=' . urlencode($sessionId)), 'success', 'Cell updated.');
}

if ($action === 'workspace_bulk_edit' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $sheet = (string) ($_POST['sheet'] ?? 'deductees');
  $recordIds = array_values(array_filter(array_map('strval', (array) ($_POST['record_ids'] ?? []))));
  $results = etds_qc_workspace_bulk_edit(
    $sessionId,
    $sheet,
    $recordIds,
    (string) ($_POST['field'] ?? ''),
    clean_input((string) ($_POST['value'] ?? ''), 250),
    $user,
    clean_multiline((string) ($_POST['reason'] ?? ''), 250)
  );
  if ($isAjax) {
    json_response(['ok' => true, 'updated' => count($results)]);
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode($sheet) . '&session=' . urlencode($sessionId)), 'success', count($results) . ' cell correction(s) saved.');
}

if ($action === 'workspace_reset_field' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $sheet = (string) ($_POST['sheet'] ?? 'deductees');
  $result = etds_qc_workspace_reset_field($sessionId, $sheet, (string) ($_POST['record_id'] ?? ''), (string) ($_POST['field'] ?? ''), $user);
  if ($isAjax) {
    json_response(['ok' => true] + $result);
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode($sheet) . '&session=' . urlencode($sessionId)), 'success', 'Cell reset to extracted value.');
}

if ($action === 'workspace_ignore_suggestion' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $sheet = (string) ($_POST['sheet'] ?? 'deductees');
  etds_qc_workspace_ignore_suggestion($sessionId, $sheet, (string) ($_POST['record_id'] ?? ''), (string) ($_POST['field'] ?? ''), $user);
  if ($isAjax) {
    json_response(['ok' => true, 'mode' => 'ignored']);
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode($sheet) . '&session=' . urlencode($sessionId)), 'success', 'Suggestion ignored.');
}

if ($action === 'workspace_apply_suggestion' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $sheet = (string) ($_POST['sheet'] ?? 'deductees');
  $value = (string) ($_POST['suggested_value'] ?? '');
  $result = etds_qc_workspace_record_change(
    $sessionId,
    $sheet,
    (string) ($_POST['record_id'] ?? ''),
    (string) ($_POST['field'] ?? ''),
    $value,
    $user,
    clean_multiline((string) ($_POST['reason'] ?? 'Applied AI suggestion'), 250),
    'ai_suggested'
  );
  if ($isAjax) {
    json_response(['ok' => true] + $result);
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode($sheet) . '&session=' . urlencode($sessionId)), 'success', 'AI suggestion applied.');
}

if ($action === 'run_validation' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $returnTo = (string) ($_POST['return_to'] ?? 'diagnosis');
  etds_qc_workspace_sync_case_data($sessionId);
  etds_qc_validate_session($sessionId, $user);
  etds_doctor_engine_run($sessionId, $user);
  $redirect = $returnTo === 'review'
    ? site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode((string) ($_POST['sheet'] ?? 'deductees')) . '&session=' . urlencode($sessionId))
    : site_href('/fintech/etds-qc/?view=etdsdoc&tab=diagnosis&session=' . urlencode($sessionId));
  etds_qc_respond($isAjax, $redirect, 'success', 'Validation rules engine executed. Doctor intelligence is ready.');
}

if ($action === 'run_doctor' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $returnTo = (string) ($_POST['return_to'] ?? 'diagnosis');
  etds_doctor_engine_run($sessionId, $user);
  $redirect = $returnTo === 'review'
    ? site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode((string) ($_POST['sheet'] ?? 'deductees')) . '&session=' . urlencode($sessionId))
    : site_href('/fintech/etds-qc/?view=etdsdoc&tab=diagnosis&session=' . urlencode($sessionId));
  etds_qc_respond($isAjax, $redirect, 'success', 'Doctor intelligence regenerated from the latest validation findings.');
}

if ($action === 'run_doctor_intelli' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_workspace_sync_case_data($sessionId);
  etds_qc_validate_session($sessionId, $user);
  etds_doctor_intelli_mode_v1($sessionId, $user);
  $redirect = site_href('/fintech/etds-qc/?view=etdsdoc&tab=diagnosis&session=' . urlencode($sessionId));
  etds_qc_respond($isAjax, $redirect, 'success', 'Doctor Intelli Mode V1 executed. Diagnosis and readiness score updated.');
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
  etds_doctor_engine_run($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=diagnosis&session=' . urlencode($sessionId)), 'success', 'Issue updated.');
}

if ($action === 'edit_record' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_edit_record($sessionId, (string) ($_POST['record_id'] ?? ''), $_POST, $user);
  etds_qc_validate_session($sessionId, $user);
  etds_doctor_engine_run($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&session=' . urlencode($sessionId)), 'success', 'Record updated and revalidated.');
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
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&session=' . urlencode($sessionId)), 'success', 'Challan saved.');
}

if ($action === 'run_reconciliation' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_reconcile($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&session=' . urlencode($sessionId)), 'success', 'Enterprise reconciliation completed.');
}

if ($action === 'export_xlsx' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $session = etds_qc_find_session($sessionId);
  if ($session) {
    $fileName = etds_qc_write_export_xlsx($sessionId, $session, $user);
    if ($fileName) {
      etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=deliverables&session=' . urlencode($sessionId)), 'success', 'QC output file generated: ' . $fileName, ['file_name' => $fileName]);
    } else {
      etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=deliverables&session=' . urlencode($sessionId)), 'error', 'QC output is blocked until validation and reconciliation are fully clean.');
    }
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=deliverables&session=' . urlencode($sessionId)), 'error', 'Session was not found.');
}

if ($action === 'archive_session' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_archive_session($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'success', 'Case archived.');
}

if ($action === 'purge_session' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_purge_session($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'success', 'Case deleted. The record was soft deleted and retained for audit.');
}

if ($action === 'toggle_favourite' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $session = etds_qc_toggle_favourite_case($sessionId, $user);
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'success', ($session && ($session['is_favourite'] ?? false)) ? 'Case added to favourites.' : 'Case removed from favourites.');
}

if ($action === 'duplicate_case' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  $newCase = etds_qc_duplicate_case($sessionId, $user);
  if ($newCase) {
    etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=upload-console&session=' . urlencode($newCase['session_id'])), 'success', 'Case duplicated as ' . $newCase['session_id'] . '.');
  }
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'error', 'The case could not be duplicated.');
}

if ($action === 'close_case' && $user) {
  $sessionId = (string) ($_POST['session_id'] ?? '');
  etds_qc_case_update_status($sessionId, 'qc_completed', $user, 'Case closed');
  etds_qc_respond($isAjax, site_href('/fintech/etds-qc/?view=gateway'), 'success', 'Case closed.');
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

if ($action === 'download_report' && $user) {
  $sessionId = (string) ($_GET['session'] ?? $_POST['session_id'] ?? '');
  $type = (string) ($_GET['report'] ?? $_POST['report'] ?? '');
  [$headers, $rows] = etds_qc_case_report_rows($sessionId, $type);
  if ($headers !== []) {
    $fileName = $sessionId . '-' . $type . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    $handle = fopen('php://output', 'wb');
    if ($handle !== false) {
      fputcsv($handle, $headers);
      foreach ($rows as $row) {
        fputcsv($handle, $row);
      }
      fclose($handle);
    }
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
      <h1>eTDSDoc</h1>
      <p class="etds-subtitle">Examine. Diagnose. Treat.</p>
      <?php etds_qc_render_flash($flash); ?>
      <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="login">
        <div class="etds-fields">
          <div class="etds-field etds-field-full">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Enter your email address" required autofocus>
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

$financialYearFilter = clean_input((string) ($_GET['financial_year'] ?? ''), 9);
$quarterFilter = clean_input((string) ($_GET['quarter'] ?? ''), 2);
$searchQuery = clean_input((string) ($_GET['search'] ?? ''), 120);
$allSessions = etds_qc_all_sessions();
$sessions = etds_qc_search_sessions([
  'query' => $searchQuery,
  'financial_year' => $financialYearFilter,
  'quarter' => $quarterFilter,
]);
$sessionId = (string) ($_GET['session'] ?? '');
$activeSession = $sessionId !== '' ? etds_qc_find_session($sessionId) : null;
if ($activeSession) {
  $sessionId = (string) ($activeSession['session_id'] ?? $sessionId);
} else {
  $sessionId = '';
  $activeSession = null;
}
$sourceData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'documents.json'), etds_qc_default_case_documents()) : etds_qc_default_case_documents();
$extractionData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'extraction.json'), etds_qc_default_extraction()) : etds_qc_default_extraction();
$ocrData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'ocr.json'), etds_qc_default_ocr()) : etds_qc_default_ocr();
$validatedData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'validation.json'), etds_qc_default_validation()) : etds_qc_default_validation();
$doctorData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'doctor.json'), etds_qc_default_doctor()) : etds_qc_default_doctor();
$doctorIntelli = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'doctor_intelli.json'), []) : [];
$correctionsData = $activeSession ? etds_qc_workspace_corrections($sessionId) : etds_qc_default_corrections();
$spreadsheetWorkspace = $activeSession ? etds_qc_workspace_records($sessionId) : [];
$challans = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]) : ['challans' => []];
$deducteesData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => []]) : ['deductees' => []];
$salaryData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => []]) : ['rows' => []];
$paymentsData = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'payments.json'), etds_qc_default_payments()) : etds_qc_default_payments();
$reconciliation = $activeSession ? etds_qc_load_json(etds_qc_session_file($sessionId, 'reconciliation.json'), etds_qc_default_reconciliation()) : etds_qc_default_reconciliation();
$exportFiles = $activeSession ? (glob(etds_qc_session_file($sessionId, 'output/*.xlsx')) ?: []) : [];
$masters = [
  'financial_years' => etds_qc_master('financial_years'),
  'quarters' => etds_qc_master('quarters'),
];
$activeClient = $activeSession ? etds_qc_case_client($sessionId) : [];
$auditTrail = $activeSession ? etds_qc_case_timeline_from_audit($sessionId) : [];
$sessionStates = [];
foreach ($allSessions as $row) {
  $sessionStates[(string) ($row['session_id'] ?? '')] = etds_qc_session_state($row);
}
$dashboardCounts = etds_qc_dashboard_counts($allSessions);
$counts = [
  'sessions' => count($allSessions),
  'validation' => $dashboardCounts['pending_validation'],
  'reconciliation' => $dashboardCounts['pending_reconciliation'],
  'ready' => $dashboardCounts['ready_for_return_preparation'],
  'completed' => $dashboardCounts['qc_completed'],
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
    header('Location: ' . site_href('/fintech/etds-qc/?view=gateway'));
    exit;
  }
  throw $exception;
}
?>
