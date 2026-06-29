<?php
declare(strict_types=1);

$requestedView = strtolower((string) ($_GET['view'] ?? 'cases'));
$spreadsheetSheet = strtolower((string) ($_GET['sheet'] ?? 'deductees'));

$validViews = ['cases', 'upload', 'extract', 'doctor', 'spreadsheet', 'output'];
if (!in_array($requestedView, $validViews, true)) {
  $requestedView = 'cases';
}

$validSpreadsheetSheets = ['deductor', 'deductees', 'challans', 'salary', 'payments'];
if (!in_array($spreadsheetSheet, $validSpreadsheetSheets, true)) {
  $spreadsheetSheet = 'deductees';
}

$quality = (int) ($validatedData['summary']['quality_score'] ?? 0);
$readiness = (bool) ($validatedData['summary']['ready_status'] ?? false);
$totalRecords = count($extractionData['records'] ?? []);
$documentsReceived = count($sourceData['documents'] ?? []);
$documentsProcessed = (int) ($extractionData['summary']['documents_processed'] ?? 0);
$overallExtractionConfidence = (int) ($extractionData['summary']['overall_confidence'] ?? 0);

$criticalIssues = (int) ($validatedData['summary']['critical'] ?? 0);
$highIssues = (int) ($validatedData['summary']['high'] ?? 0);
$moderateIssues = (int) ($validatedData['summary']['medium'] ?? 0);
$minorIssues = (int) (($validatedData['summary']['low'] ?? 0) + ($validatedData['summary']['information'] ?? 0));

$doctorSummary = is_array($doctorData['summary'] ?? null) ? $doctorData['summary'] : [];
$doctorReadiness = is_array($doctorData['readiness'] ?? null) ? $doctorData['readiness'] : [];
$doctorReadinessStatus = (string) ($doctorReadiness['status'] ?? ($doctorSummary['readiness'] ?? 'Not Ready'));
$doctorReadinessReason = (string) ($doctorReadiness['reason'] ?? 'Run Doctor analysis after validation.');
$doctorTopDiagnosis = (string) ($doctorSummary['top_diagnosis'] ?? 'Diagnosis Pending');
$doctorEstimatedMinutes = (int) ($doctorSummary['estimated_time_minutes'] ?? 0);

$spreadsheetSheets = is_array($spreadsheetWorkspace ?? null) ? $spreadsheetWorkspace : [];
$activeSheetPayload = $spreadsheetSheets[$spreadsheetSheet] ?? ['meta' => ['label' => ucfirst($spreadsheetSheet), 'id_field' => 'record_id', 'fields' => []], 'rows' => []];
$activeSheetMeta = is_array($activeSheetPayload['meta'] ?? null) ? $activeSheetPayload['meta'] : ['label' => ucfirst($spreadsheetSheet), 'id_field' => 'record_id', 'fields' => []];
$activeSheetRows = is_array($activeSheetPayload['rows'] ?? null) ? $activeSheetPayload['rows'] : [];
$activeSheetIdField = (string) ($activeSheetMeta['id_field'] ?? 'record_id');
$doctorScores = is_array($doctorData['health_scores'] ?? null) ? $doctorData['health_scores'] : [];

$reconciliation = $reconciliation ?? ['summary' => []];
$reconSummary = is_array($reconciliation['summary'] ?? null) ? $reconciliation['summary'] : [];

$statusKey = (string) ($activeSession['status'] ?? 'draft');
$statusLabel = (string) ($activeSession['status_label'] ?? ucfirst(str_replace('_', ' ', $statusKey)));

if ($statusKey === 'draft') {
  $statusClass = 'is-draft';
} elseif (in_array($statusKey, ['documents_received', 'extraction_running', 'validation_running'], true)) {
  $statusClass = 'is-active';
} elseif ($statusKey === 'ready_for_return_preparation') {
  $statusClass = 'is-ready';
} else {
  $statusClass = 'is-active';
}

$viewUrl = static function (string $view, array $extra = []) use ($sessionId): string {
  $params = ['view' => $view];
  if ($sessionId !== '') {
    $params['session'] = $sessionId;
  }
  $params += $extra;
  return etds_qc_h(site_href('/fintech/etds-qc/?' . http_build_query($params)));
};

$sheetUrl = static function (string $sheet) use ($sessionId): string {
  return etds_qc_h(site_href('/fintech/etds-qc/?view=spreadsheet&sheet=' . urlencode($sheet) . ($sessionId !== '' ? '&session=' . urlencode($sessionId) : '')));
};
?>
<div class="app-layout">
  <aside class="app-sidebar">
    <a class="app-sidebar__logo" href="<?= $viewUrl('cases') ?>">eTDSDoc</a>

    <a class="app-sidebar__link<?= $requestedView === 'cases' ? ' is-active' : '' ?>" href="<?= $viewUrl('cases') ?>" title="Cases">
      <svg viewBox="0 0 24 24"><path d="M3 7.5h18M3 12h18M3 16.5h18"/></svg>
    </a>
    <a class="app-sidebar__link<?= $requestedView === 'upload' ? ' is-active' : '' ?>" href="<?= $viewUrl('upload') ?>" title="Intake">
      <svg viewBox="0 0 24 24"><path d="M4 7.5h16M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M9 11h6M9 15h4"/></svg>
    </a>
    <a class="app-sidebar__link<?= $requestedView === 'extract' ? ' is-active' : '' ?>" href="<?= $viewUrl('extract') ?>" title="Workspace">
      <svg viewBox="0 0 24 24"><path d="M12 4v10"/><path d="m8 10 4 4 4-4"/><path d="M5 18h14"/></svg>
    </a>
    <a class="app-sidebar__link<?= $requestedView === 'doctor' ? ' is-active' : '' ?>" href="<?= $viewUrl('doctor') ?>" title="Doctor">
      <svg viewBox="0 0 24 24"><path d="M6 4h12v4H6z"/><path d="M9 8v3.5a3 3 0 0 1-.88 2.12L6 15.75V19h12v-3.25l-2.12-2.13A3 3 0 0 1 15 11.5V8"/></svg>
    </a>
    <a class="app-sidebar__link<?= $requestedView === 'output' ? ' is-active' : '' ?>" href="<?= $viewUrl('output') ?>" title="Output">
      <svg viewBox="0 0 24 24"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v6h6"/></svg>
    </a>

    <div class="app-sidebar__spacer"></div>

    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="logout">
      <button class="app-sidebar__logout" type="submit" title="Logout">
        <svg viewBox="0 0 24 24"><path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 17l5-5-5-5"/><path d="M15 12H4"/></svg>
      </button>
    </form>
  </aside>

  <div class="app-main">
    <header class="app-topbar">
      <?php if ($activeSession): ?>
        <div class="app-topbar__case">
          <label>Case</label>
          <strong><?= etds_qc_h($sessionId) ?></strong>
        </div>
        <div class="app-topbar__sep"></div>
        <div class="app-topbar__case">
          <label>Client</label>
          <strong><?= etds_qc_h($activeSession['client_name'] ?? '') ?></strong>
        </div>
        <div class="app-topbar__sep"></div>
        <div class="app-topbar__case">
          <label>TAN</label>
          <strong><?= etds_qc_h($activeSession['tan'] ?? '') ?></strong>
        </div>
        <div class="app-topbar__sep"></div>
        <span class="app-topbar__status <?= etds_qc_h($statusClass) ?>"><?= etds_qc_h($statusLabel) ?></span>
      <?php else: ?>
        <div class="app-topbar__case">
          <strong>eTDSDoc</strong>
        </div>
      <?php endif; ?>
      <div class="app-topbar__spacer"></div>
      <span class="app-topbar__user"><?= etds_qc_h((string) ($user['name'] ?? 'Operator')) ?></span>
    </header>

    <main class="app-content">
      <?php etds_qc_render_flash($flash); ?>

      <?php if ($requestedView === 'cases'): ?>
        <section class="screen-landing">
          <div class="screen-landing__header">
            <h2>Cases</h2>
            <div class="screen-landing__actions">
              <button class="btn btn-primary" type="button" onclick="document.getElementById('new-case-form').style.display=document.getElementById('new-case-form').style.display==='none'?'block':'none'">+ New Case</button>
            </div>
          </div>

          <div id="new-case-form" style="display:none; margin-bottom:16px;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:16px;">
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_session">
                <div class="form-grid">
                  <div class="form-field"><label for="client_name">Client Name</label><input id="client_name" name="client_name" required></div>
                  <div class="form-field"><label for="tan">TAN</label><input id="tan" name="tan" maxlength="10" required placeholder="AAAA99999A"></div>
                  <div class="form-field"><label for="pan">PAN</label><input id="pan" name="pan" maxlength="10"></div>
                  <div class="form-field"><label for="financial_year">Financial Year</label><select id="financial_year" name="financial_year" required><?php foreach (($masters['financial_years'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?></select></div>
                  <div class="form-field"><label for="quarter">Quarter</label><select id="quarter" name="quarter" required><?php foreach (($masters['quarters'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?></select></div>
                  <div class="form-field"><label for="return_type">Return Type</label><select id="return_type" name="return_type" required><option>24Q</option><option>26Q</option><option>27Q</option><option>27EQ</option></select></div>
                  <div class="form-field form-field--full"><label for="client_code">Client Code</label><input id="client_code" name="client_code"></div>
                  <div class="form-field form-field--full"><label for="remarks">Remarks</label><textarea id="remarks" name="remarks" rows="2"></textarea></div>
                </div>
                <div style="margin-top:12px; display:flex; gap:8px;">
                  <button class="btn btn-primary" type="submit">Create Case</button>
                  <button class="btn btn-outline" type="button" onclick="this.closest('div[style]').style.display='none'">Cancel</button>
                </div>
              </form>
            </div>
          </div>

          <div class="search-bar" style="margin-bottom:12px;">
            <form method="get" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="display:flex; gap:8px; width:100%;">
              <input type="hidden" name="view" value="cases">
              <input type="search" name="search" value="<?= etds_qc_h($searchQuery ?? '') ?>" placeholder="Search by case, client, TAN, PAN...">
              <select name="financial_year" style="padding:7px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:12px;">
                <option value="">All FY</option>
                <?php foreach (($masters['financial_years'] ?? []) as $item): ?>
                  <option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"<?= (($financialYearFilter ?? '') === (string) ($item['code'] ?? '')) ? ' selected' : '' ?>><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option>
                <?php endforeach; ?>
              </select>
              <select name="quarter" style="padding:7px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:12px;">
                <option value="">All Q</option>
                <?php foreach (($masters['quarters'] ?? []) as $item): ?>
                  <option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"<?= (($quarterFilter ?? '') === (string) ($item['code'] ?? '')) ? ' selected' : '' ?>><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-outline" type="submit">Search</button>
            </form>
          </div>

          <?php if (empty($sessions)): ?>
            <div class="empty-state"><p>No cases yet. Create your first case to begin.</p></div>
          <?php else: ?>
            <table class="case-table">
              <thead>
                <tr>
                  <th>Case ID</th>
                  <th>Client</th>
                  <th>TAN</th>
                  <th>FY</th>
                  <th>Quarter</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sessions as $row): ?>
                  <?php $rowState = $sessionStates[(string) ($row['session_id'] ?? '')] ?? etds_qc_session_state($row); ?>
                  <tr>
                    <td><a class="case-id" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=upload&session=' . urlencode((string) ($row['session_id'] ?? '')))) ?>"><?= etds_qc_h((string) ($row['session_id'] ?? '')) ?></a></td>
                    <td><?= etds_qc_h((string) ($row['client_name'] ?? '')) ?></td>
                    <td><?= etds_qc_h((string) ($row['tan'] ?? '')) ?></td>
                    <td><?= etds_qc_h((string) ($row['financial_year'] ?? '')) ?></td>
                    <td><?= etds_qc_h((string) ($row['quarter'] ?? '')) ?></td>
                    <td><span class="app-topbar__status <?= ($rowState['key'] ?? '') === 'ready_for_return_preparation' ? 'is-ready' : 'is-active' ?>"><?= etds_qc_h((string) ($rowState['label'] ?? 'Draft')) ?></span></td>
                    <td>
                      <?php if (($row['is_favourite'] ?? false)): ?>
                        <span style="color:#f59e0b;">&#9733;</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </section>

      <?php elseif ($requestedView === 'upload'): ?>
        <section class="screen-upload">
          <h2>Document Intake</h2>

          <?php if (!$activeSession): ?>
            <div class="empty-state">
              <p>No case selected. Go to Cases and create or select one first.</p>
              <a class="btn btn-primary" href="<?= $viewUrl('cases') ?>">Open Cases</a>
            </div>
          <?php else: ?>
            <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="upload_documents">
              <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">

              <div class="dropzone" data-dropzone onclick="this.querySelector('input[type=file]').click()">
                <input type="file" name="documents[]" accept=".xls,.xlsx,.csv,.txt,.pdf,.png,.jpg,.jpeg,.zip" multiple required>
                <p>Drag and drop files here or click to browse</p>
                <p style="font-size:11px; color:#9ca3af;">Excel, PDF, CSV, Images, ZIP</p>
              </div>
              <div class="progress" hidden data-upload-progress><span style="width:0%"></span></div>

              <div style="margin-top:12px; display:flex; gap:8px;">
                <button class="btn btn-primary" type="submit">Upload Files</button>
                <a class="btn btn-outline" href="<?= $viewUrl('extract') ?>">Continue to Extract &rarr;</a>
              </div>
            </form>

            <?php if (!empty($sourceData['documents'])): ?>
              <div class="file-list" style="margin-top:16px;">
                <?php foreach (($sourceData['documents'] ?? []) as $document): ?>
                  <?php if (($document['is_removed'] ?? false) === true) { continue; } ?>
                  <div class="file-list__item">
                    <span class="file-list__name"><?= etds_qc_h((string) ($document['original_name'] ?? $document['file_name'] ?? '')) ?></span>
                    <span class="file-list__meta"><?= etds_qc_h((string) ($document['document_type'] ?? '')) ?></span>
                    <span class="file-list__meta">v<?= str_pad((string) ((int) ($document['version_number'] ?? 1)), 2, '0', STR_PAD_LEFT) ?></span>
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload" style="display:inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete_upload">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <input type="hidden" name="file_id" value="<?= etds_qc_h((string) ($document['document_id'] ?? '')) ?>">
                      <button class="file-list__remove" type="submit" data-confirm="Remove this file?">Remove</button>
                    </form>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </section>

      <?php elseif ($requestedView === 'extract'): ?>
        <section class="screen-extract">
          <h2>Processing Workspace</h2>

          <?php if (!$activeSession): ?>
            <div class="empty-state"><p>No case selected.</p><a class="btn btn-primary" href="<?= $viewUrl('cases') ?>">Open Cases</a></div>
          <?php else: ?>
            <div class="summary-row">
              <div class="summary-card"><strong><?= $documentsReceived ?></strong><span>Source Files</span></div>
              <div class="summary-card"><strong><?= $documentsProcessed ?></strong><span>Processed</span></div>
              <div class="summary-card"><strong><?= $totalRecords ?></strong><span>Rows Extracted</span></div>
              <div class="summary-card"><strong><?= $overallExtractionConfidence ?>%</strong><span>Confidence</span></div>
            </div>

            <div style="display:flex; gap:8px; margin-bottom:16px;">
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="extract_validate">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <button class="btn btn-primary" type="submit">Extract Data</button>
              </form>
              <a class="btn btn-outline" href="<?= $viewUrl('spreadsheet') ?>">Open Workspace &rarr;</a>
            </div>

            <?php if (!empty($sourceData['documents'])): ?>
              <table class="data-table">
                <thead><tr><th>Document</th><th>Classification</th><th>Confidence</th><th>Extraction</th></tr></thead>
                <tbody>
                  <?php foreach (($sourceData['documents'] ?? []) as $document): ?>
                    <?php if (($document['is_removed'] ?? false) === true) { continue; } ?>
                    <tr>
                      <td><?= etds_qc_h((string) ($document['original_name'] ?? $document['file_name'] ?? '')) ?></td>
                      <td><?= etds_qc_h((string) ($document['classification'] ?? 'Unknown')) ?></td>
                      <td><?= (int) ($document['classification_confidence'] ?? 0) ?>%</td>
                      <td><?= etds_qc_h((string) ($document['extraction_status'] ?? 'Pending')) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          <?php endif; ?>
        </section>

      <?php elseif ($requestedView === 'doctor'): ?>
        <section class="screen-doctor">
          <h2>eTDSDoc Doctor</h2>

          <?php if (!$activeSession): ?>
            <div class="empty-state"><p>No case selected.</p><a class="btn btn-primary" href="<?= $viewUrl('cases') ?>">Open Cases</a></div>
          <?php else: ?>
            <div class="severity-row">
              <div class="severity-card is-critical"><strong><?= $criticalIssues ?></strong><span>Critical</span></div>
              <div class="severity-card is-high"><strong><?= $highIssues ?></strong><span>High</span></div>
              <div class="severity-card is-medium"><strong><?= $moderateIssues ?></strong><span>Medium</span></div>
              <div class="severity-card is-low"><strong><?= $minorIssues ?></strong><span>Low / Info</span></div>
            </div>

            <div class="doctor-rec">
              <h3>Doctor Recommendation</h3>
              <?php if ($doctorReadinessStatus === 'Ready for QC Certification'): ?>
                <p style="color:#059669; font-weight:600;">Ready for QC Certification. Data quality meets all thresholds.</p>
              <?php elseif ($doctorReadinessStatus === 'Ready After Corrections'): ?>
                <p>Ready after corrections. Top: <?= etds_qc_h($doctorTopDiagnosis) ?>. Est. <?= $doctorEstimatedMinutes ?> min to resolve.</p>
              <?php else: ?>
                <p><?= etds_qc_h($doctorReadinessReason) ?></p>
              <?php endif; ?>
            </div>

            <div style="display:flex; gap:8px;">
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="run_validation">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <button class="btn btn-primary" type="submit">Run Validation + Doctor</button>
              </form>
              <a class="btn btn-outline" href="<?= $viewUrl('spreadsheet') ?>">Open Workspace &rarr;</a>
            </div>
          <?php endif; ?>
        </section>

      <?php elseif ($requestedView === 'spreadsheet'): ?>
        <section class="screen-spreadsheet">
          <div class="screen-spreadsheet__toolbar">
            <div class="screen-spreadsheet__tabs">
              <?php foreach ($spreadsheetSheets as $sheetKey => $sheetPayload): ?>
                <a class="screen-spreadsheet__tab<?= $spreadsheetSheet === $sheetKey ? ' is-active' : '' ?>" href="<?= $sheetUrl($sheetKey) ?>"><?= etds_qc_h((string) (($sheetPayload['meta']['label'] ?? ucfirst((string) $sheetKey)))) ?></a>
              <?php endforeach; ?>
            </div>
            <div class="screen-spreadsheet__tools">
              <input type="search" placeholder="Search..." data-sheet-search style="padding:5px 10px; border:1px solid #d1d5db; border-radius:4px; font-size:11px; width:160px;">
              <?php if ($activeSession): ?>
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload" style="display:inline;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="run_validation">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <input type="hidden" name="return_to" value="spreadsheet">
                  <input type="hidden" name="sheet" value="<?= etds_qc_h($spreadsheetSheet) ?>">
                  <button class="btn btn-sm btn-outline" type="submit">Validate</button>
                </form>
                <a class="btn btn-sm btn-outline" href="<?= $viewUrl('doctor') ?>">Doctor</a>
                <a class="btn btn-sm btn-primary" href="<?= $viewUrl('output') ?>">Output</a>
              <?php endif; ?>
            </div>
          </div>

          <div class="screen-spreadsheet__grid" data-spreadsheet-grid data-session-id="<?= etds_qc_h($sessionId) ?>" data-sheet="<?= etds_qc_h($spreadsheetSheet) ?>" data-csrf="<?= etds_qc_h(csrf_token()) ?>">
            <?php if ($activeSheetRows === []): ?>
              <div class="empty-state"><p>No data yet. Run extraction first.</p></div>
            <?php else: ?>
              <table class="sheet-table">
                <thead>
                  <tr>
                    <th class="is-select"><input type="checkbox" data-select-all></th>
                    <th>Record</th>
                    <?php foreach ((array) ($activeSheetMeta['fields'] ?? []) as $field): ?>
                      <th data-sort-field="<?= etds_qc_h((string) $field) ?>">
                        <button type="button" data-sort-field="<?= etds_qc_h((string) $field) ?>"><?= etds_qc_h(ucwords(str_replace('_', ' ', (string) $field))) ?></button>
                      </th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($activeSheetRows as $row): $recordId = (string) ($row[$activeSheetIdField] ?? $row['record_id'] ?? ''); ?>
                    <tr data-record-id="<?= etds_qc_h($recordId) ?>">
                      <td class="is-select"><input type="checkbox" data-row-select value="<?= etds_qc_h($recordId) ?>"></td>
                      <td class="is-record"><?= etds_qc_h($recordId) ?></td>
                      <?php foreach ((array) ($activeSheetMeta['fields'] ?? []) as $field): ?>
                        <?php
                          $cellValue = (string) ($row[$field] ?? '');
                          $cellStatus = (string) (($row['_cell_status'][$field] ?? 'valid'));
                          $suggestion = $row['_suggestions'][$field] ?? null;
                        ?>
                        <td class="cell is-<?= etds_qc_h($cellStatus) ?>" data-field="<?= etds_qc_h((string) $field) ?>">
                          <div class="cell__editor" contenteditable="true" spellcheck="false" data-cell-editor data-record-id="<?= etds_qc_h($recordId) ?>" data-field="<?= etds_qc_h((string) $field) ?>" data-original-value="<?= etds_qc_h($cellValue) ?>"><?= etds_qc_h($cellValue) ?></div>
                          <span class="cell__status"><?= etds_qc_h(str_replace('_', ' ', strtoupper($cellStatus))) ?></span>
                          <?php if ($suggestion !== null): ?>
                            <div class="cell__suggestion">
                              <button type="button" class="cell__pill" data-apply-suggestion data-record-id="<?= etds_qc_h($recordId) ?>" data-field="<?= etds_qc_h((string) $field) ?>" data-value="<?= etds_qc_h((string) ($suggestion['value'] ?? '')) ?>" data-reason="<?= etds_qc_h((string) ($suggestion['reason'] ?? '')) ?>">Apply</button>
                              <button type="button" class="cell__pill is-muted" data-ignore-suggestion data-record-id="<?= etds_qc_h($recordId) ?>" data-field="<?= etds_qc_h((string) $field) ?>">Ignore</button>
                            </div>
                          <?php endif; ?>
                        </td>
                      <?php endforeach; ?>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </section>

      <?php elseif ($requestedView === 'output'): ?>
        <section class="screen-output">
          <h2>Output Package</h2>

          <?php if (!$activeSession): ?>
            <div class="empty-state"><p>No case selected.</p><a class="btn btn-primary" href="<?= $viewUrl('cases') ?>">Open Cases</a></div>
          <?php else: ?>
            <div class="output-status <?= $readiness ? 'is-ready' : 'is-blocked' ?>">
              <?php if ($readiness): ?>
                <strong style="color:#059669;">Ready for Clean Output</strong>
                <p style="font-size:12px; color:#6b7280; margin-top:4px;">Quality: <?= $quality ?>% &middot; All critical/high issues resolved</p>
              <?php else: ?>
                <strong style="color:#dc2626;">Not Ready</strong>
                <p style="font-size:12px; color:#6b7280; margin-top:4px;">Resolve critical and high issues before export. Quality: <?= $quality ?>%</p>
              <?php endif; ?>
            </div>

            <div class="output-actions">
              <?php if ($readiness): ?>
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload" style="display:contents;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="export_xlsx">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <button class="is-primary" type="submit">Download Clean Excel</button>
                </form>
              <?php endif; ?>

              <a href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=doctor_diagnosis_report')) ?>">Download Exception Report</a>
              <a href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=case_summary')) ?>">Download Case Summary</a>

              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="display:contents;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="close_case">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <button class="is-danger" type="submit" data-confirm="Close this case?">Close Case</button>
              </form>
            </div>
          <?php endif; ?>
        </section>

      <?php endif; ?>
    </main>
  </div>
</div>
