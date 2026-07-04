<?php
declare(strict_types=1);

$requestedView = strtolower((string) ($_GET['view'] ?? 'gateway'));
$spreadsheetSheet = strtolower((string) ($_GET['sheet'] ?? 'deductees'));
$etdsdocTab = strtolower((string) ($_GET['tab'] ?? 'examination'));

$validViews = ['gateway', 'upload-console', 'etdsdoc', 'deliverables'];
if (!in_array($requestedView, $validViews, true)) {
  $requestedView = 'gateway';
}

$validEtdsdocTabs = ['examination', 'diagnosis', 'treatment', 'review'];
if (!in_array($etdsdocTab, $validEtdsdocTabs, true)) {
  $etdsdocTab = 'examination';
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
  return etds_qc_h(site_href('/fintech/etds-qc/?view=etdsdoc&tab=review&sheet=' . urlencode($sheet) . ($sessionId !== '' ? '&session=' . urlencode($sessionId) : '')));
};

$tabUrl = static function (string $tab) use ($sessionId): string {
  return etds_qc_h(site_href('/fintech/etds-qc/?view=etdsdoc&tab=' . urlencode($tab) . ($sessionId !== '' ? '&session=' . urlencode($sessionId) : '')));
};
?>
<div class="app-layout">
  <aside class="app-sidebar">
    <div class="app-sidebar__brand">
      <a class="app-sidebar__logo" href="<?= $viewUrl('gateway') ?>">
        <div class="app-sidebar__logo-text">eTDSDoc</div>
        <div class="app-sidebar__logo-sub">TDS QC Suite</div>
      </a>
    </div>

    <nav class="app-sidebar__nav">
      <a class="app-sidebar__link<?= $requestedView === 'gateway' ? ' is-active' : '' ?>" href="<?= $viewUrl('gateway') ?>">
        <svg viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/><path d="M9 20v-5h6v5"/></svg>
        <span>Gateway</span>
      </a>
      <a class="app-sidebar__link<?= $requestedView === 'upload-console' ? ' is-active' : '' ?>" href="<?= $viewUrl('upload-console') ?>">
        <svg viewBox="0 0 24 24"><path d="M4 7.5h16M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M9 11h6M9 15h4"/></svg>
        <span>Upload Console</span>
      </a>
      <a class="app-sidebar__link<?= $requestedView === 'etdsdoc' ? ' is-active' : '' ?>" href="<?= $viewUrl('etdsdoc') ?>">
        <svg viewBox="0 0 24 24"><path d="M12 4v10"/><path d="m8 10 4 4 4-4"/><path d="M5 18h14"/></svg>
        <span>eTDSDoc</span>
      </a>
      <a class="app-sidebar__link<?= $requestedView === 'deliverables' ? ' is-active' : '' ?>" href="<?= $viewUrl('deliverables') ?>">
        <svg viewBox="0 0 24 24"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v6h6"/></svg>
        <span>Deliverables</span>
      </a>
    </nav>

    <div class="app-sidebar__spacer"></div>

    <div class="app-sidebar__bottom">
      <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="logout">
        <button class="app-sidebar__logout" type="submit">
          <svg viewBox="0 0 24 24"><path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3"/><path d="M10 17l5-5-5-5"/><path d="M15 12H4"/></svg>
          <span>Logout</span>
        </button>
      </form>
    </div>
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

      <?php if ($requestedView === 'gateway'): ?>
        <section class="screen-gateway">
          <div class="screen-header">
            <div class="screen-header__text">
              <h2>TDS QC Gateway</h2>
              <p class="screen-header__subtitle">Create or select client entity and confirm FY, Quarter and Return Type for TDS QC assignment.</p>
            </div>
            <div class="screen-header__actions">
              <?php if ($activeSession): ?>
                <a class="btn btn-primary" href="<?= $viewUrl('upload-console') ?>">Proceed to Upload Console &rarr;</a>
              <?php endif; ?>
            </div>
          </div>

          <div class="gateway-section">
            <div class="gateway-section__header">
              <h3>Existing TDS QC Assignments</h3>
              <p>Select an existing assignment or alter client/assignment details.</p>
            </div>

            <div class="search-bar" style="margin-bottom:12px;">
              <form method="get" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="display:flex; gap:8px; width:100%;">
                <input type="hidden" name="view" value="gateway">
                <input type="search" name="search" value="<?= etds_qc_h($searchQuery ?? '') ?>" placeholder="Search by case, client, TAN...">
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
              <div class="empty-state"><p>No assignments yet. Create your first assignment below.</p></div>
            <?php else: ?>
              <div class="case-table-wrap">
                <table class="case-table">
                  <thead>
                    <tr>
                      <th>Case ID</th>
                      <th>Client</th>
                      <th>TAN</th>
                      <th>FY</th>
                      <th>Quarter</th>
                      <th>Status</th>
                      <th class="is-actions">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($sessions as $row): ?>
                      <?php $rowState = $sessionStates[(string) ($row['session_id'] ?? '')] ?? etds_qc_session_state($row); ?>
                      <tr>
                        <td><a class="case-id" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=upload-console&session=' . urlencode((string) ($row['session_id'] ?? '')))) ?>"><?= etds_qc_h((string) ($row['session_id'] ?? '')) ?></a></td>
                        <td><?= etds_qc_h((string) ($row['client_name'] ?? '')) ?></td>
                        <td><?= etds_qc_h((string) ($row['tan'] ?? '')) ?></td>
                        <td><?= etds_qc_h((string) ($row['financial_year'] ?? '')) ?></td>
                        <td><?= etds_qc_h((string) ($row['quarter'] ?? '')) ?></td>
                        <td><span class="app-topbar__status <?= ($rowState['key'] ?? '') === 'ready_for_return_preparation' ? 'is-ready' : 'is-active' ?>"><?= etds_qc_h((string) ($rowState['label'] ?? 'Draft')) ?></span></td>
                        <td class="is-actions">
                          <div class="case-actions">
                            <?php if (($row['is_favourite'] ?? false)): ?>
                              <span class="case-favourite" title="Favourite">&#9733;</span>
                            <?php endif; ?>
                            <a class="btn-action-select" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=upload-console&session=' . urlencode((string) ($row['session_id'] ?? '')))) ?>">Select</a>
                            <a class="btn-action-alter" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?view=gateway&session=' . urlencode((string) ($row['session_id'] ?? '')))) ?>">Alter</a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>

          <div class="gateway-section" style="margin-top:24px;">
            <div class="gateway-section__header">
              <h3>Create New Assignment</h3>
            </div>

            <div class="gateway-form-card">
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_session">

                <div class="form-grid">
                  <div class="form-section-label">Entity Details</div>
                  <div class="form-field"><label for="client_name">Client Name <span class="required">*</span></label><input id="client_name" name="client_name" required placeholder="Enter client or entity name"></div>
                  <div class="form-field"><label for="tan">TAN <span class="required">*</span></label><input id="tan" name="tan" maxlength="10" required placeholder="AAAA99999A"></div>
                  <div class="form-field"><label for="contact_person">Contact Person Name</label><input id="contact_person" name="contact_person" placeholder="Optional"></div>
                  <div class="form-field"><label for="mobile">Mobile Number</label><input id="mobile" name="mobile" maxlength="20" placeholder="Optional"></div>
                  <div class="form-field"><label for="email">Email ID</label><input id="email" name="email" type="email" maxlength="150" placeholder="Optional"></div>
                  <input type="hidden" name="pan" value="">
                  <input type="hidden" name="client_code" value="">
                  <input type="hidden" name="entity_type" value="">
                  <input type="hidden" name="remarks" value="">

                  <div class="form-section-label">Assignment Details</div>
                  <div class="form-field"><label for="financial_year">Financial Year <span class="required">*</span></label><select id="financial_year" name="financial_year" required><?php foreach (($masters['financial_years'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?></select></div>
                  <div class="form-field"><label for="quarter">Quarter <span class="required">*</span></label><select id="quarter" name="quarter" required><?php foreach (($masters['quarters'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?></select></div>
                  <div class="form-field"><label for="return_type">Return Type <span class="required">*</span></label><select id="return_type" name="return_type" required><option>24Q</option><option>26Q</option><option>27Q</option><option>27EQ</option></select></div>
                </div>
                <div style="margin-top:16px; display:flex; gap:8px;">
                  <button class="btn btn-primary" type="submit">Confirm Assignment &amp; Proceed</button>
                </div>
              </form>
            </div>
          </div>
        </section>

      <?php elseif ($requestedView === 'upload-console'): ?>
        <section class="screen-upload-console">
          <div class="screen-header">
            <div class="screen-header__text">
              <h2>Upload Console</h2>
              <p class="screen-header__subtitle">Upload Deductee/TDS/Form details and Challan files category-wise for examination.</p>
            </div>
            <div class="screen-header__actions">
              <a class="btn btn-outline" href="<?= $viewUrl('gateway') ?>">&larr; Back to Gateway</a>
              <a class="btn btn-primary" href="<?= $viewUrl('etdsdoc') ?>">Proceed to eTDSDoc &rarr;</a>
            </div>
          </div>

          <?php if (!$activeSession): ?>
            <div class="empty-state">
              <p>No case selected. Go to Gateway and create or select one first.</p>
              <a class="btn btn-primary" href="<?= $viewUrl('gateway') ?>">Open Gateway</a>
            </div>
          <?php else: ?>
            <div class="upload-categories">

              <div class="upload-card">
                <div class="upload-card__header">
                  <div class="upload-card__icon">
                    <svg viewBox="0 0 24 24"><path d="M4 7.5h16M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="M9 11h6M9 15h4"/></svg>
                  </div>
                  <div>
                    <h3>Deductee / TDS / Form Details Upload</h3>
                    <p>Deductee details, TDS deduction details, form working files, 24Q/26Q/27Q/27EQ related data.</p>
                  </div>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="upload_documents">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <input type="hidden" name="upload_category" value="deductee_tds_form">
                  <div class="dropzone" data-dropzone onclick="this.querySelector('input[type=file]').click()">
                    <input type="file" name="documents[]" accept=".xls,.xlsx,.csv,.txt,.pdf,.png,.jpg,.jpeg,.zip" multiple required>
                    <p>Drag and drop files here or click to browse</p>
                    <p style="font-size:11px; color:#9ca3af;">Excel, PDF, CSV, Images, ZIP</p>
                  </div>
                  <div class="progress" hidden data-upload-progress><span style="width:0%"></span></div>
                  <div style="margin-top:10px;">
                    <button class="btn btn-primary btn-sm" type="submit">Upload Deductee/TDS Files</button>
                  </div>
                </form>
              </div>

              <div class="upload-card">
                <div class="upload-card__header">
                  <div class="upload-card__icon upload-card__icon--challan">
                    <svg viewBox="0 0 24 24"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v6h6"/></svg>
                  </div>
                  <div>
                    <h3>Challan Upload</h3>
                    <p>Challan details, challan copies, payment proof, challan allocation/reconciliation source files.</p>
                  </div>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="upload_documents">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <input type="hidden" name="upload_category" value="challan">
                  <div class="dropzone" data-dropzone onclick="this.querySelector('input[type=file]').click()">
                    <input type="file" name="documents[]" accept=".xls,.xlsx,.csv,.txt,.pdf,.png,.jpg,.jpeg,.zip" multiple required>
                    <p>Drag and drop files here or click to browse</p>
                    <p style="font-size:11px; color:#9ca3af;">Excel, PDF, CSV, Images, ZIP</p>
                  </div>
                  <div class="progress" hidden data-upload-progress><span style="width:0%"></span></div>
                  <div style="margin-top:10px;">
                    <button class="btn btn-primary btn-sm" type="submit">Upload Challan Files</button>
                  </div>
                </form>
              </div>

            </div>

            <?php
              $allDocs = $sourceData['documents'] ?? [];
              $activeDocs = array_values(array_filter($allDocs, static fn(array $d): bool => ($d['is_removed'] ?? false) !== true));
              $docFilePath = etds_qc_session_file($sessionId, 'documents.json');
            ?>
            <div style="margin-top:24px;">
              <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <h3 style="font-size:13px; font-weight:600;">Uploaded Files</h3>
                <span style="font-size:10px; font-weight:600; padding:2px 8px; border-radius:10px; background:#f0fdfa; color:#0d9488;"><?= etds_qc_h($sessionId) ?></span>
                <span style="font-size:10px; color:#9ca3af;"><?= count($activeDocs) ?> file<?= count($activeDocs) !== 1 ? 's' : '' ?></span>
              </div>
              <?php if (empty($activeDocs)): ?>
                <div class="empty-state" style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:24px;">
                  <p>No files uploaded for this assignment yet.</p>
                </div>
              <?php else: ?>
                <div class="file-list">
                  <?php foreach ($activeDocs as $document): ?>
                    <div class="file-list__item">
                      <span class="file-list__name"><?= etds_qc_h((string) ($document['original_name'] ?? $document['file_name'] ?? '')) ?></span>
                      <span class="file-list__meta"><?= etds_qc_h((string) ($document['document_type'] ?? '')) ?></span>
                      <?php if (!empty($document['upload_category'])): ?>
                        <span class="file-list__category"><?= etds_qc_h($document['upload_category'] === 'challan' ? 'Challan' : 'Deductee/TDS') ?></span>
                      <?php else: ?>
                        <span class="file-list__category file-list__category--old">Previous</span>
                      <?php endif; ?>
                      <span class="file-list__meta">v<?= str_pad((string) ((int) ($document['version_number'] ?? 1)), 2, '0', STR_PAD_LEFT) ?></span>
                      <?php if (!empty($document['upload_time'])): ?>
                        <span class="file-list__meta"><?= etds_qc_h((string) date('d M Y, H:i', strtotime((string) $document['upload_time']))) ?></span>
                      <?php endif; ?>
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
            </div>
          <?php endif; ?>
        </section>

      <?php elseif ($requestedView === 'etdsdoc'): ?>
        <section class="screen-etdsdoc">
          <div class="screen-header">
            <div class="screen-header__text">
              <h2>eTDSDoc Diagnostic Workspace</h2>
              <p class="screen-header__subtitle">Examine, diagnose, treat and review TDS data before final output.</p>
            </div>
            <div class="screen-header__actions">
              <a class="btn btn-outline" href="<?= $viewUrl('upload-console') ?>">&larr; Back to Upload Console</a>
              <a class="btn btn-primary" href="<?= $viewUrl('deliverables') ?>">Proceed to Deliverables &rarr;</a>
            </div>
          </div>

          <?php if (!$activeSession): ?>
            <div class="empty-state"><p>No case selected.</p><a class="btn btn-primary" href="<?= $viewUrl('gateway') ?>">Open Gateway</a></div>
          <?php else: ?>

            <div class="etdsdoc-tabs">
              <a class="etdsdoc-tabs__tab<?= $etdsdocTab === 'examination' ? ' is-active' : '' ?>" href="<?= $tabUrl('examination') ?>">Examination</a>
              <a class="etdsdoc-tabs__tab<?= $etdsdocTab === 'diagnosis' ? ' is-active' : '' ?>" href="<?= $tabUrl('diagnosis') ?>">Diagnosis</a>
              <a class="etdsdoc-tabs__tab<?= $etdsdocTab === 'treatment' ? ' is-active' : '' ?>" href="<?= $tabUrl('treatment') ?>">Treatment</a>
              <a class="etdsdoc-tabs__tab<?= $etdsdocTab === 'review' ? ' is-active' : '' ?>" href="<?= $tabUrl('review') ?>">Review Summary</a>
            </div>

            <?php if ($etdsdocTab === 'examination'): ?>
              <div class="etdsdoc-tab-content">
                <h3>Examination</h3>
                <p style="font-size:12px; color:#6b7280; margin-bottom:16px;">Review uploaded source files and run AI extraction to populate structured data.</p>

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
              </div>

            <?php elseif ($etdsdocTab === 'diagnosis'): ?>
              <div class="etdsdoc-tab-content">
                <h3>Diagnosis</h3>
                <p style="font-size:12px; color:#6b7280; margin-bottom:16px;">Identify errors, defects, missing fields and mismatches through validation and doctor intelligence.</p>

                <div class="severity-row">
                  <div class="severity-card is-critical"><strong><?= $criticalIssues ?></strong><span>Critical</span></div>
                  <div class="severity-card is-high"><strong><?= $highIssues ?></strong><span>High</span></div>
                  <div class="severity-card is-medium"><strong><?= $moderateIssues ?></strong><span>Medium</span></div>
                  <div class="severity-card is-low"><strong><?= $minorIssues ?></strong><span>Low / Info</span></div>
                </div>

                <div class="doctor-rec">
                  <h4>Doctor Recommendation</h4>
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
                </div>
              </div>

            <?php elseif ($etdsdocTab === 'treatment'): ?>
              <div class="etdsdoc-tab-content">
                <h3>Treatment</h3>
                <p style="font-size:12px; color:#6b7280; margin-bottom:16px;">Review diagnostic findings and apply corrections, suggestions, or manual overrides.</p>

                <?php
                  $findings = $validatedData['findings'] ?? [];
                  $openFindings = array_filter($findings, static fn(array $f): bool => ($f['status'] ?? 'open') === 'open');
                ?>
                <?php if (empty($openFindings)): ?>
                  <div class="empty-state"><p>No open findings to treat. Run Diagnosis first.</p></div>
                <?php else: ?>
                  <table class="data-table">
                    <thead><tr><th>Severity</th><th>Rule</th><th>Field</th><th>Message</th><th>Record</th><th>Status</th></tr></thead>
                    <tbody>
                      <?php foreach ($openFindings as $finding): ?>
                        <tr>
                          <td><span class="severity-badge severity-badge--<?= strtolower((string) ($finding['severity'] ?? '')) ?>"><?= etds_qc_h((string) ($finding['severity'] ?? '')) ?></span></td>
                          <td><?= etds_qc_h((string) ($finding['rule_name'] ?? $finding['rule_id'] ?? '')) ?></td>
                          <td><?= etds_qc_h((string) ($finding['field'] ?? '')) ?></td>
                          <td><?= etds_qc_h((string) ($finding['message'] ?? '')) ?></td>
                          <td><?= etds_qc_h((string) ($finding['record_reference'] ?? '')) ?></td>
                          <td>
                            <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload" style="display:inline;">
                              <?= csrf_field() ?>
                              <input type="hidden" name="action" value="issue_status">
                              <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                              <input type="hidden" name="record_id" value="<?= etds_qc_h((string) ($finding['record_reference'] ?? '')) ?>">
                              <input type="hidden" name="issue_id" value="<?= etds_qc_h((string) ($finding['finding_id'] ?? '')) ?>">
                              <input type="hidden" name="issue_status" value="resolved">
                              <button class="btn btn-sm btn-outline" type="submit">Resolve</button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                <?php endif; ?>

                <div style="display:flex; gap:8px; margin-top:16px;">
                  <a class="btn btn-outline" href="<?= $tabUrl('review') ?>">Open Review Summary &rarr;</a>
                </div>
              </div>

            <?php elseif ($etdsdocTab === 'review'): ?>
              <div class="etdsdoc-tab-content">
                <div class="screen-spreadsheet__toolbar">
                  <div class="screen-spreadsheet__tabs">
                    <?php foreach ($spreadsheetSheets as $sheetKey => $sheetPayload): ?>
                      <a class="screen-spreadsheet__tab<?= $spreadsheetSheet === $sheetKey ? ' is-active' : '' ?>" href="<?= $sheetUrl($sheetKey) ?>"><?= etds_qc_h((string) (($sheetPayload['meta']['label'] ?? ucfirst((string) $sheetKey)))) ?></a>
                    <?php endforeach; ?>
                  </div>
                  <div class="screen-spreadsheet__tools">
                    <input type="search" placeholder="Search..." data-sheet-search style="padding:5px 10px; border:1px solid #d1d5db; border-radius:4px; font-size:11px; width:160px;">
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload" style="display:inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="run_validation">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <input type="hidden" name="return_to" value="review">
                      <input type="hidden" name="sheet" value="<?= etds_qc_h($spreadsheetSheet) ?>">
                      <button class="btn btn-sm btn-outline" type="submit">Validate</button>
                    </form>
                    <a class="btn btn-sm btn-outline" href="<?= $tabUrl('diagnosis') ?>">Doctor</a>
                    <a class="btn btn-sm btn-primary" href="<?= $viewUrl('deliverables') ?>">Deliverables</a>
                  </div>
                </div>

                <div class="screen-spreadsheet__grid" data-spreadsheet-grid data-session-id="<?= etds_qc_h($sessionId) ?>" data-sheet="<?= etds_qc_h($spreadsheetSheet) ?>" data-csrf="<?= etds_qc_h(csrf_token()) ?>">
                  <?php if ($activeSheetRows === []): ?>
                    <div class="empty-state"><p>No data yet. Run extraction in the Examination tab first.</p></div>
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
              </div>

            <?php endif; ?>
          <?php endif; ?>
        </section>

      <?php elseif ($requestedView === 'deliverables'): ?>
        <section class="screen-deliverables">
          <div class="screen-header">
            <div class="screen-header__text">
              <h2>Deliverables</h2>
              <p class="screen-header__subtitle">Generate final clean output, QC reports and reconciliation summaries.</p>
            </div>
            <div class="screen-header__actions">
              <a class="btn btn-outline" href="<?= $viewUrl('etdsdoc') ?>">&larr; Back to eTDSDoc</a>
            </div>
          </div>

          <?php if (!$activeSession): ?>
            <div class="empty-state"><p>No case selected.</p><a class="btn btn-primary" href="<?= $viewUrl('gateway') ?>">Open Gateway</a></div>
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
