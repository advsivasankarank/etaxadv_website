<?php
declare(strict_types=1);

$workspace = strtolower((string) ($_GET['ws'] ?? 'overview'));
if (!in_array($workspace, ['overview', 'intake', 'extraction', 'bench', 'excel'], true)) {
  $workspace = 'overview';
}
$benchTab = strtolower((string) ($_GET['tab'] ?? 'diagnosis'));
if (!in_array($benchTab, ['diagnosis', 'reconciliation', 'treatment', 'readiness'], true)) {
  $benchTab = 'diagnosis';
}

$quality = (int) ($validatedData['summary']['quality_score'] ?? 0);
$reconScore = (int) ($reconciliation['summary']['reconciliation_score'] ?? 0);
$readiness = $activeSession ? etds_qc_export_readiness($sessionId) : false;
$documentsReceived = count($sourceData['documents'] ?? []);
$totalRecords = (int) ($validatedData['summary']['total_records'] ?? 0);
$sourceColumnsCount = count($sourceData['source_columns'] ?? []);
$resolvedIssuesCount = 0;
$criticalIssues = 0;
$moderateIssues = 0;
$minorIssues = 0;
$openIssues = [];

foreach (($validatedData['records'] ?? []) as $record) {
  foreach (($record['issues'] ?? []) as $issue) {
    if (($issue['resolution_status'] ?? 'open') !== 'open') {
      $resolvedIssuesCount++;
      continue;
    }
    $severity = (string) ($issue['severity'] ?? 'warning');
    if ($severity === 'critical') {
      $criticalIssues++;
    } elseif ($severity === 'warning') {
      $moderateIssues++;
    } else {
      $minorIssues++;
    }
    $openIssues[] = [
      'record' => $record,
      'issue' => $issue,
      'severity_label' => $severity === 'critical' ? 'Critical Issue' : ($severity === 'warning' ? 'Moderate Issue' : 'Minor Issue'),
      'tone' => $severity === 'critical' ? 'critical' : ($severity === 'warning' ? 'warning' : 'good'),
    ];
  }
}

$challanRows = is_array($challans['challans'] ?? null) ? $challans['challans'] : [];
$challanMap = [];
foreach ($challanRows as $challan) {
  $challanMap[(string) ($challan['challan_reference'] ?? '')] = $challan;
}
$matchedCount = 0;
$partialCount = 0;
$unmatchedCount = 0;
foreach (($validatedData['records'] ?? []) as $record) {
  $ref = trim((string) ($record['normalized']['challan_reference'] ?? ''));
  if ($ref === '' || !isset($challanMap[$ref])) {
    $unmatchedCount++;
    continue;
  }
  $balance = round((float) ($challanMap[$ref]['balance_total'] ?? 0), 2);
  if ($balance === 0.0) {
    $matchedCount++;
  } elseif ($balance > 0) {
    $partialCount++;
  } else {
    $unmatchedCount++;
  }
}

$healthIssueCount = $criticalIssues + $moderateIssues + $minorIssues;
$treatmentStatusLabel = $openIssues === [] ? 'Treatment Complete' : ($criticalIssues > 0 ? 'Critical Treatment Pending' : 'Treatment In Progress');
$doctorCertificationLabel = $readiness ? 'Doctor Certified' : 'Doctor Review Pending';
$processingResultLabel = $readiness ? 'Fit For Processing' : 'Not Fit For Processing';
$exportResultLabel = $readiness ? 'Ready For Export' : 'Not Ready';
$exportReadinessLabel = $readiness ? 'Ready' : 'Blocked';
$latestSessionId = !empty($sessions) ? (string) ($sessions[0]['session_id'] ?? '') : '';

$workspaceCompletion = [
  'overview' => true,
  'intake' => $activeSession && $documentsReceived > 0,
  'extraction' => $activeSession && $totalRecords > 0,
  'bench' => $activeSession && ($healthIssueCount > 0 || $resolvedIssuesCount > 0 || $reconScore > 0),
  'excel' => $activeSession && !empty($exportFiles),
];
$completedTasks = array_sum(array_map(static fn(bool $done): int => $done ? 1 : 0, $workspaceCompletion));
$pendingTasks = count($workspaceCompletion) - $completedTasks;
$overallProgress = (int) round(($completedTasks / max(count($workspaceCompletion), 1)) * 100);

if (!$activeSession) {
  $caseStatusLabel = 'Awaiting Case Creation';
  $nextActionTitle = 'Create a new diagnostic case';
  $nextActionText = 'Start in Intake Centre and register the client, TAN, financial year, quarter, and return type.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=intake&view=create');
  $nextActionLabel = 'Open Intake Centre';
} elseif ($documentsReceived === 0) {
  $caseStatusLabel = 'Awaiting Intake';
  $nextActionTitle = 'Upload source documents';
  $nextActionText = 'Move to Intake Centre and upload the source files required for extraction.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=intake&view=session&session=' . urlencode($sessionId));
  $nextActionLabel = 'Upload Documents';
} elseif ($totalRecords === 0) {
  $caseStatusLabel = 'Awaiting Extraction';
  $nextActionTitle = 'Run extraction';
  $nextActionText = 'Open Extraction Centre to parse the uploaded files and prepare structured records.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=extraction&view=session&session=' . urlencode($sessionId));
  $nextActionLabel = 'Run Extraction';
} elseif ($healthIssueCount > 0) {
  $caseStatusLabel = 'Needs Doctor Review';
  $nextActionTitle = 'Treat open health issues';
  $nextActionText = 'Open Doctor\'s Bench to review findings, treat exceptions, and improve data health.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=bench&view=session&session=' . urlencode($sessionId) . '&tab=treatment');
  $nextActionLabel = 'Open Treatment';
} elseif (!$readiness) {
  $caseStatusLabel = 'Awaiting Certification';
  $nextActionTitle = 'Complete readiness review';
  $nextActionText = 'Check readiness, reconciliation, and certification status before generating output files.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=bench&view=session&session=' . urlencode($sessionId) . '&tab=readiness');
  $nextActionLabel = 'Review Readiness';
} else {
  $caseStatusLabel = 'Fit For Processing';
  $nextActionTitle = 'Prepare final Excel outputs';
  $nextActionText = 'Open Final Excel Advice to generate the certified clean workbook and download related outputs.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=excel&view=session&session=' . urlencode($sessionId));
  $nextActionLabel = 'Open Final Excel Advice';
}

$buildUrl = static function (array $params = []) use ($sessionId, $workspace, $benchTab): string {
  $resolvedWorkspace = (string) ($params['ws'] ?? $workspace);
  $resolvedSessionId = array_key_exists('session', $params) ? (string) $params['session'] : $sessionId;
  $resolvedView = (string) ($params['view'] ?? ($resolvedSessionId !== '' ? 'session' : 'dashboard'));
  $resolvedTab = (string) ($params['tab'] ?? $benchTab);
  $query = [];
  if ($resolvedView !== 'dashboard') {
    $query['view'] = $resolvedView;
  }
  $query['ws'] = $resolvedWorkspace;
  if ($resolvedSessionId !== '') {
    $query['session'] = $resolvedSessionId;
  }
  if ($resolvedWorkspace === 'bench') {
    $query['tab'] = $resolvedTab;
  }
  return site_href('/fintech/etds-qc/' . ($query !== [] ? '?' . http_build_query($query) : ''));
};

$workspaceLabels = [
  'overview' => 'Case Overview',
  'intake' => 'Intake Centre',
  'extraction' => 'Extraction Centre',
  'bench' => "Doctor's Bench",
  'excel' => 'Final Excel Advice',
];
$workspaceTitles = [
  'overview' => 'Diagnostic case command centre',
  'intake' => 'Document intake and case registration',
  'extraction' => 'Extraction and structured source preparation',
  'bench' => 'Diagnosis, reconciliation, treatment, and readiness',
  'excel' => 'Certification outputs and download centre',
];
$workspaceDescriptions = [
  'overview' => 'Understand the case, what is wrong, and the next recommended action within seconds.',
  'intake' => 'Capture client metadata, upload source files, and confirm the document bundle for diagnosis.',
  'extraction' => 'Run extraction, review parsed structures, and prepare the case for Doctor\'s Bench.',
  'bench' => 'Operate the eTDSDoc Doctor workflow using diagnosis, reconciliation, treatment, and readiness tabs.',
  'excel' => 'Review certification status, blocked issues, generated files, and output readiness before export.',
];
?>
<main id="main-content">
  <section class="container etds-shell">
    <div class="etds-app-shell">
      <aside class="etds-sidebar">
        <div class="etds-sidebar-inner">
          <div class="etds-side-brand">
            <div class="etds-side-brand-mark">eTDSDoc</div>
            <div>
              <p class="etds-side-brand-title">eTDSDoc</p>
              <p class="etds-side-brand-subtitle">Powered by eTDSDoc Doctor</p>
            </div>
          </div>

          <div class="etds-side-case">
            <span class="etds-side-kicker">AI-Driven Data Health Check</span>
            <strong><?= etds_qc_h($activeSession['client_name'] ?? 'No Active Case') ?></strong>
            <p>
              <?= $activeSession ? etds_qc_h((string) $activeSession['tan']) . ' · FY ' . etds_qc_h((string) $activeSession['financial_year']) . ' · ' . etds_qc_h((string) $activeSession['quarter']) : 'Create or open a case to begin intake, extraction, diagnosis, and export preparation.' ?>
            </p>
          </div>

          <nav class="etds-workspace-nav" aria-label="Primary workspaces">
            <?php foreach ($workspaceLabels as $key => $label): ?>
              <a class="etds-workspace-link<?= $workspace === $key ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => $key, 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
                <span class="etds-workspace-icon"><?= etds_qc_nav_icon($key) ?></span>
                <span><?= etds_qc_h($label) ?></span>
              </a>
            <?php endforeach; ?>
          </nav>

          <form class="etds-logout-form" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="logout">
            <button class="etds-logout-button" type="submit" data-confirm="Log out of eTDSDoc now?">
              <span class="etds-workspace-icon"><?= etds_qc_nav_icon('logout') ?></span>
              <span>Logout</span>
            </button>
          </form>
        </div>
      </aside>

      <div class="etds-main">
        <?php etds_qc_render_flash($flash); ?>

        <section class="etds-command-header">
          <div class="etds-command-brand">
            <span class="eyebrow">E Tax Advisors</span>
            <h1>eTDSDoc</h1>
            <p>Powered by eTDSDoc Doctor · Professional TDS Processing Workspace</p>
          </div>
          <div class="etds-command-context">
            <div class="etds-context-item">
              <span class="etds-context-label">Client Name</span>
              <strong><?= etds_qc_h($activeSession['client_name'] ?? 'No active case') ?></strong>
            </div>
            <div class="etds-context-item">
              <span class="etds-context-label">TAN</span>
              <strong><?= etds_qc_h($activeSession['tan'] ?? 'Awaiting intake') ?></strong>
            </div>
            <div class="etds-context-item">
              <span class="etds-context-label">FY</span>
              <strong><?= etds_qc_h($activeSession['financial_year'] ?? 'Pending') ?></strong>
            </div>
            <div class="etds-context-item">
              <span class="etds-context-label">Quarter</span>
              <strong><?= etds_qc_h($activeSession['quarter'] ?? 'Pending') ?></strong>
            </div>
          </div>
          <div class="etds-command-actions">
            <div class="etds-doctor-chip" data-tone="<?= $readiness ? 'good' : ($healthIssueCount > 0 ? 'critical' : 'warning') ?>">
              <span class="etds-context-label">eTDSDoc Doctor Status</span>
              <strong><?= etds_qc_h($doctorCertificationLabel) ?></strong>
            </div>
            <div class="etds-profile-chip">
              <span class="etds-context-label">User Profile</span>
              <strong><?= etds_qc_h((string) ($user['name'] ?? 'Operator')) ?></strong>
            </div>
            <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="logout">
              <button class="btn btn-outline btn-sm" type="submit" data-confirm="Log out of eTDSDoc now?">Logout</button>
            </form>
          </div>
        </section>

        <div class="etds-page-head">
          <div>
            <div class="eyebrow"><?= etds_qc_h($workspaceLabels[$workspace] ?? 'eTDSDoc Doctor') ?></div>
            <h2><?= etds_qc_h($workspaceTitles[$workspace] ?? 'eTDSDoc Doctor') ?></h2>
            <p class="etds-subtitle"><?= etds_qc_h($workspaceDescriptions[$workspace] ?? '') ?></p>
          </div>
          <div>
            <div class="etds-score-row">
              <span class="etds-status-chip" data-tone="<?= $quality >= 95 ? 'good' : ($quality >= 80 ? 'warning' : 'critical') ?>">Data Health Score <?= $quality ?>%</span>
              <span class="etds-status-chip" data-tone="<?= $reconScore >= 95 ? 'good' : ($reconScore >= 80 ? 'warning' : 'critical') ?>">Reconciliation <?= $reconScore ?>%</span>
              <span class="etds-status-chip" data-tone="<?= $readiness ? 'good' : 'critical' ?>"><?= etds_qc_h($exportResultLabel) ?></span>
            </div>
          </div>
        </div>

        <?php if ($workspace === 'overview'): ?>
          <div class="etds-overview-banner">
            <div>
              <span class="eyebrow">Progress Banner</span>
              <h3><?= etds_qc_h($caseStatusLabel) ?></h3>
              <p><?= etds_qc_h($nextActionText) ?></p>
            </div>
            <div class="etds-banner-meta">
              <div class="etds-banner-pill">
                <span>Workflow Progress</span>
                <strong><?= $overallProgress ?>%</strong>
              </div>
              <div class="etds-banner-pill">
                <span>Export Readiness</span>
                <strong><?= etds_qc_h($exportReadinessLabel) ?></strong>
              </div>
            </div>
          </div>

          <div class="etds-progress-card">
            <div class="etds-progress-head">
              <strong>Case Progress</strong>
              <span><?= $completedTasks ?> completed · <?= $pendingTasks ?> pending</span>
            </div>
            <div class="etds-progress"><span style="width: <?= $overallProgress ?>%"></span></div>
          </div>

          <div class="etds-grid etds-dashboard-grid etds-overview-stats">
            <div class="etds-stat"><strong><?= etds_qc_h($activeSession['client_name'] ?? 'Pending') ?></strong><span>Client Name</span></div>
            <div class="etds-stat"><strong><?= etds_qc_h($activeSession['tan'] ?? 'Pending') ?></strong><span>TAN</span></div>
            <div class="etds-stat"><strong><?= etds_qc_h($activeSession['financial_year'] ?? 'Pending') ?></strong><span>FY</span></div>
            <div class="etds-stat"><strong><?= etds_qc_h($activeSession['quarter'] ?? 'Pending') ?></strong><span>Quarter</span></div>
            <div class="etds-stat"><strong><?= etds_qc_h($caseStatusLabel) ?></strong><span>Case Status</span></div>
            <div class="etds-stat"><strong><?= $quality ?>%</strong><span>Data Health Score</span></div>
            <div class="etds-stat"><strong><?= $documentsReceived ?></strong><span>Documents Received</span></div>
            <div class="etds-stat"><strong><?= $healthIssueCount ?></strong><span>Health Issues Count</span></div>
            <div class="etds-stat"><strong><?= etds_qc_h($exportReadinessLabel) ?></strong><span>Export Readiness</span></div>
          </div>

          <div class="etds-grid etds-three-col">
            <div class="etds-panel">
              <h3>Completed Tasks</h3>
              <ul class="etds-check-list">
                <?php foreach ($workspaceCompletion as $key => $done): ?>
                  <?php if ($done): ?>
                    <li><span class="etds-check-dot is-done"></span><?= etds_qc_h($workspaceLabels[$key]) ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
                <?php if ($completedTasks === 0): ?>
                  <li><span class="etds-check-dot"></span>No workflow stages are complete yet.</li>
                <?php endif; ?>
              </ul>
            </div>

            <div class="etds-panel">
              <h3>Pending Tasks</h3>
              <ul class="etds-check-list">
                <?php foreach ($workspaceCompletion as $key => $done): ?>
                  <?php if (!$done): ?>
                    <li><span class="etds-check-dot is-pending"></span><?= etds_qc_h($workspaceLabels[$key]) ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </div>

            <div class="etds-panel">
              <h3>Next Recommended Action</h3>
              <p class="etds-next-title"><?= etds_qc_h($nextActionTitle) ?></p>
              <p><?= etds_qc_h($nextActionText) ?></p>
              <div class="etds-action-row" style="margin-top:16px;">
                <a class="btn btn-primary" href="<?= etds_qc_h($nextActionLink) ?>"><?= etds_qc_h($nextActionLabel) ?></a>
              </div>
            </div>
          </div>

          <div class="etds-panel">
            <div class="etds-panel-head">
              <div>
                <span class="eyebrow">Quick Launch Cards</span>
                <h3>Move through the diagnostic workflow</h3>
              </div>
            </div>
            <div class="etds-launch-grid">
              <?php foreach ([
                ['key' => 'intake', 'title' => 'Intake Centre', 'text' => 'Register the case and confirm the incoming document pack.'],
                ['key' => 'extraction', 'title' => 'Extraction Centre', 'text' => 'Run extraction and inspect structured source data.'],
                ['key' => 'bench', 'title' => "Doctor's Bench", 'text' => 'Diagnose health issues, reconcile, treat, and certify.'],
                ['key' => 'excel', 'title' => 'Final Excel Advice', 'text' => 'Review output readiness and generate final files.'],
              ] as $launch): ?>
                <a class="etds-launch-card" href="<?= etds_qc_h($buildUrl(['ws' => $launch['key'], 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
                  <span class="etds-workspace-icon"><?= etds_qc_nav_icon((string) $launch['key']) ?></span>
                  <h4><?= etds_qc_h((string) $launch['title']) ?></h4>
                  <p><?= etds_qc_h((string) $launch['text']) ?></p>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

        <?php elseif (!$activeSession && $workspace !== 'intake'): ?>
          <div class="etds-empty">
            <h2>No active case selected</h2>
            <p>Start from Intake Centre to create a case and move through the workflow.</p>
            <div class="etds-action-row" style="justify-content:center; margin-top:16px;">
              <a class="btn btn-primary" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=intake&view=create')) ?>">Create Diagnostic Case</a>
              <?php if ($latestSessionId !== ''): ?>
                <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=overview&view=session&session=' . urlencode($latestSessionId))) ?>">Open Latest Case</a>
              <?php endif; ?>
            </div>
          </div>
        <?php elseif ($workspace === 'intake'): ?>
          <div class="etds-grid etds-dashboard-grid">
            <div class="etds-stat"><strong><?= $counts['sessions'] ?></strong><span>Total Cases</span></div>
            <div class="etds-stat"><strong><?= $documentsReceived ?></strong><span>Documents Received</span></div>
            <div class="etds-stat"><strong><?= $counts['validation'] ?></strong><span>Pending Diagnosis</span></div>
            <div class="etds-stat"><strong><?= $counts['ready'] ?></strong><span>Fit for Processing</span></div>
            <div class="etds-stat"><strong><?= $counts['completed'] ?></strong><span>Completed</span></div>
          </div>

          <div class="etds-grid etds-two-col">
            <div class="etds-panel">
              <h2>New Diagnostic Case</h2>
              <p class="etds-section-copy">Create a case before intake begins. This workspace remains focused on metadata capture and source document collection.</p>
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create_session">
                <div class="etds-fields">
                  <div class="etds-field"><label for="client_name">Client Name</label><input id="client_name" name="client_name" required></div>
                  <div class="etds-field"><label for="tan">TAN</label><input id="tan" name="tan" maxlength="10" required></div>
                  <div class="etds-field"><label for="financial_year">Financial Year</label><select id="financial_year" name="financial_year" required><option value="2025-26">2025-26</option><option value="2024-25">2024-25</option></select></div>
                  <div class="etds-field"><label for="quarter">Quarter</label><select id="quarter" name="quarter" required><option>Q1</option><option>Q2</option><option>Q3</option><option>Q4</option></select></div>
                  <div class="etds-field"><label for="return_type">Return Type</label><select id="return_type" name="return_type" required><option>24Q</option><option>26Q</option><option>27Q</option><option>27EQ</option></select></div>
                  <div class="etds-field etds-field-full"><label for="remarks">Case Notes</label><textarea id="remarks" name="remarks"></textarea></div>
                </div>
                <div class="etds-action-row" style="margin-top:18px;">
                  <button class="btn btn-primary" type="submit">Create Diagnostic Case</button>
                </div>
              </form>
            </div>

            <div class="etds-panel">
              <h2>Case Register</h2>
              <?php if (empty($sessions)): ?>
                <div class="etds-empty">No diagnostic cases created yet.</div>
              <?php else: ?>
                <div class="etds-table-shell">
                  <table class="etds-table">
                    <thead>
                      <tr><th>Session</th><th>Client</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                      <?php foreach ($sessions as $row): ?>
                        <?php $rowState = $sessionStates[(string) ($row['session_id'] ?? '')] ?? etds_qc_session_state($row); ?>
                        <tr>
                          <td><?= etds_qc_h((string) $row['session_id']) ?></td>
                          <td><?= etds_qc_h((string) $row['client_name']) ?></td>
                          <td><?= etds_qc_h((string) ($rowState['label'] ?? $row['status'])) ?></td>
                          <td><a class="btn btn-outline btn-sm" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=overview&view=session&session=' . urlencode((string) $row['session_id']))) ?>">Open Case</a></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($activeSession): ?>
            <div class="etds-grid etds-two-col" style="margin-top:24px;">
              <div class="etds-panel">
                <h2>Documents Received</h2>
                <?php if (empty($sourceData['documents'])): ?>
                  <div class="etds-empty">No source documents uploaded yet.</div>
                <?php else: ?>
                  <div class="etds-file-grid">
                    <?php foreach (($sourceData['documents'] ?? []) as $document): ?>
                      <article class="etds-file-card">
                        <h3><?= etds_qc_h((string) ($document['name'] ?? 'Document')) ?></h3>
                        <p class="etds-muted"><?= strtoupper(etds_qc_h((string) ($document['extension'] ?? 'file'))) ?> · <?= number_format(((int) ($document['size'] ?? 0)) / 1024, 1) ?> KB</p>
                      </article>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="etds-panel">
                <h2>Upload Documents</h2>
                <p class="etds-section-copy">Upload payroll, challan, deductee, or working files that will feed the diagnostic workflow.</p>
                <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="upload_documents">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <div class="etds-fields">
                    <div class="etds-field etds-field-full">
                      <label for="documents">Select documents</label>
                      <input id="documents" type="file" name="documents[]" accept=".xls,.xlsx,.csv,.txt,.pdf" multiple required>
                    </div>
                  </div>
                  <div class="etds-action-row" style="margin-top:16px;">
                    <button class="btn btn-primary" type="submit">Upload Files</button>
                    <a class="btn btn-outline" href="<?= etds_qc_h($buildUrl(['ws' => 'extraction', 'view' => 'session'])) ?>">Go to Extraction Centre</a>
                  </div>
                </form>
              </div>
            </div>
          <?php endif; ?>
        <?php elseif ($workspace === 'extraction' && $activeSession): ?>
          <div class="etds-overview-banner etds-extraction-banner">
            <div>
              <span class="eyebrow">Extraction Pipeline</span>
              <h3><?= $totalRecords > 0 ? 'Diagnosis package prepared' : 'Source files ready for extraction' ?></h3>
              <p><?= $totalRecords > 0 ? 'Structured records have been prepared and can now proceed to Doctor\'s Bench.' : 'Run extraction after verifying the uploaded documents and source column context.' ?></p>
            </div>
            <div class="etds-banner-meta">
              <div class="etds-banner-pill">
                <span>Source Columns</span>
                <strong><?= $sourceColumnsCount ?></strong>
              </div>
              <div class="etds-banner-pill">
                <span>Extracted Records</span>
                <strong><?= $totalRecords ?></strong>
              </div>
            </div>
          </div>

          <div class="etds-grid etds-dashboard-grid">
            <div class="etds-stat"><strong><?= $documentsReceived ?></strong><span>Documents Received</span></div>
            <div class="etds-stat"><strong><?= $sourceColumnsCount ?></strong><span>Source Columns</span></div>
            <div class="etds-stat"><strong><?= $totalRecords ?></strong><span>Extracted Records</span></div>
            <div class="etds-stat"><strong><?= $healthIssueCount ?></strong><span>Queued Health Issues</span></div>
            <div class="etds-stat"><strong><?= $resolvedIssuesCount ?></strong><span>Resolved Issues</span></div>
          </div>

          <div class="etds-grid etds-two-col">
            <div class="etds-panel">
              <h2>Extraction Centre</h2>
              <p class="etds-section-copy">This workspace prepares structured records for diagnosis. It should feel operational, not spreadsheet-first.</p>
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="run_extraction">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <div class="etds-action-row">
                  <button class="btn btn-gold" type="submit">Run Extraction &amp; Send to Doctor's Bench</button>
                </div>
              </form>
            </div>

            <div class="etds-panel">
              <h2>Source Columns</h2>
              <?php if (empty($sourceData['source_columns'])): ?>
                <div class="etds-empty">Run extraction to discover the source column structure.</div>
              <?php else: ?>
                <ul class="etds-mini-list">
                  <?php foreach (($sourceData['source_columns'] ?? []) as $column): ?>
                    <li><span><?= etds_qc_h((string) $column) ?></span><span class="etds-chip">Mapped</span></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>

          <div class="etds-panel" style="margin-top:24px;">
            <h2>Records Prepared for Diagnosis</h2>
            <?php if (empty($validatedData['records'])): ?>
              <div class="etds-empty">No extracted records are available yet.</div>
            <?php else: ?>
              <div class="etds-table-shell">
                <table class="etds-table">
                  <thead>
                    <tr><th>Record</th><th>Deductee</th><th>PAN</th><th>Amount</th><th>Findings</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach (($validatedData['records'] ?? []) as $record): ?>
                      <tr>
                        <td><?= etds_qc_h((string) ($record['record_id'] ?? '')) ?></td>
                        <td><?= etds_qc_h((string) ($record['normalized']['deductee_name'] ?? '')) ?></td>
                        <td><?= etds_qc_h((string) ($record['normalized']['pan'] ?? '')) ?></td>
                        <td><?= etds_qc_h((string) ($record['normalized']['tds_amount'] ?? '')) ?></td>
                        <td><?= count($record['issues'] ?? []) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        <?php elseif ($workspace === 'bench' && $activeSession): ?>
          <div class="etds-bench-tabs" role="tablist" aria-label="Doctor's Bench Tabs">
            <?php foreach (['diagnosis' => 'Diagnosis', 'reconciliation' => 'Reconciliation', 'treatment' => 'Treatment', 'readiness' => 'Readiness'] as $tabKey => $tabLabel): ?>
              <a class="etds-bench-tab<?= $benchTab === $tabKey ? ' is-active' : '' ?>" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=bench&view=session&session=' . urlencode($sessionId) . '&tab=' . urlencode($tabKey))) ?>"><?= etds_qc_h($tabLabel) ?></a>
            <?php endforeach; ?>
          </div>

          <?php if ($benchTab === 'diagnosis'): ?>
            <div class="etds-grid etds-dashboard-grid">
              <div class="etds-stat"><strong><?= $quality ?>%</strong><span>Data Health Score</span></div>
              <div class="etds-stat"><strong><?= $criticalIssues ?></strong><span>Critical Issues</span></div>
              <div class="etds-stat"><strong><?= $moderateIssues ?></strong><span>Moderate Issues</span></div>
              <div class="etds-stat"><strong><?= $minorIssues ?></strong><span>Minor Issues</span></div>
              <div class="etds-stat"><strong><?= $healthIssueCount ?></strong><span>Health Issues Queue</span></div>
            </div>

            <div class="etds-grid etds-two-col">
              <div class="etds-panel">
                <h2>Doctor Findings</h2>
                <p class="etds-section-copy">The operator should start here first. Review the diagnosis and understand what is wrong before taking any action.</p>
                <div class="etds-chip-row">
                  <span class="etds-chip">Invalid PAN</span>
                  <span class="etds-chip">Missing PAN</span>
                  <span class="etds-chip">Duplicate Deductee</span>
                  <span class="etds-chip">Invalid Date</span>
                  <span class="etds-chip">Missing Amount</span>
                </div>
              </div>
              <div class="etds-panel">
                <h2>Health Issues Queue</h2>
                <?php if ($openIssues === []): ?>
                  <div class="etds-empty">No open health issues were detected.</div>
                <?php else: ?>
                  <div class="etds-issue-stack">
                    <?php foreach ($openIssues as $entry): ?>
                      <?php $record = $entry['record']; $issue = $entry['issue']; ?>
                      <article class="etds-issue-card" data-severity="<?= etds_qc_h((string) $issue['severity']) ?>">
                        <div class="etds-chip-row" style="margin-bottom:10px;">
                          <span class="etds-status-chip" data-tone="<?= etds_qc_h((string) $entry['tone']) ?>"><?= etds_qc_h((string) $entry['severity_label']) ?></span>
                          <span class="etds-chip"><?= etds_qc_h((string) ($record['record_id'] ?? '')) ?></span>
                        </div>
                        <h4><?= etds_qc_h((string) $issue['message']) ?></h4>
                        <p><?= etds_qc_h((string) ($issue['suggested_correction'] ?? 'Review this record.')) ?></p>
                      </article>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php elseif ($benchTab === 'reconciliation'): ?>
            <div class="etds-grid etds-dashboard-grid">
              <div class="etds-stat"><strong><?= $matchedCount ?></strong><span>Matched</span></div>
              <div class="etds-stat"><strong><?= $partialCount ?></strong><span>Partially Matched</span></div>
              <div class="etds-stat"><strong><?= $unmatchedCount ?></strong><span>Unmatched</span></div>
              <div class="etds-stat"><strong><?= number_format((float) ($reconciliation['summary']['difference'] ?? 0), 2) ?></strong><span>Allocation Variance</span></div>
              <div class="etds-stat"><strong><?= number_format((float) ($reconciliation['summary']['balance'] ?? 0), 2) ?></strong><span>Unallocated Amounts</span></div>
            </div>

            <div class="etds-grid etds-two-col">
              <div class="etds-panel">
                <h2>Challan Reconciliation</h2>
                <p class="etds-section-copy">Verify challan totals, allocations, and summary consistency before certification.</p>
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="add_challan">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <div class="etds-fields">
                    <div class="etds-field"><label>Challan Reference</label><input name="challan_reference" required></div>
                    <div class="etds-field"><label>BSR Code</label><input name="bsr_code"></div>
                    <div class="etds-field"><label>Deposit Date</label><input name="deposit_date" type="date"></div>
                    <div class="etds-field"><label>Section Code</label><input name="section_code"></div>
                    <div class="etds-field"><label>Total Available</label><input name="total_available" type="number" step="0.01" required></div>
                  </div>
                  <div class="etds-action-row" style="margin-top:16px;">
                    <button class="btn btn-outline" type="submit">Add Challan</button>
                  </div>
                </form>
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="margin-top:18px;" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="run_reconciliation">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <button class="btn btn-primary" type="submit">Run Reconciliation</button>
                </form>
              </div>

              <div class="etds-panel">
                <h2>Mismatch Queue</h2>
                <?php if (!empty($reconciliation['exceptions'])): ?>
                  <div class="etds-issue-stack">
                    <?php foreach (($reconciliation['exceptions'] ?? []) as $exception): ?>
                      <article class="etds-issue-card" data-severity="<?= etds_qc_h((string) ($exception['severity'] ?? 'warning')) ?>">
                        <h4><?= etds_qc_h((string) ($exception['message'] ?? '')) ?></h4>
                      </article>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="etds-empty">No reconciliation mismatches are open.</div>
                <?php endif; ?>
              </div>
            </div>
          <?php elseif ($benchTab === 'treatment'): ?>
            <div class="etds-panel">
              <h2>Treatment Suggestions</h2>
              <p class="etds-section-copy">Only exception records appear here. Accept suggestions, edit records, ignore selected findings, or mark them resolved.</p>
              <?php if ($openIssues === []): ?>
                <div class="etds-empty">No treatment items are pending.</div>
              <?php else: ?>
                <div class="etds-issue-stack">
                  <?php foreach ($openIssues as $entry): ?>
                    <?php $record = $entry['record']; $issue = $entry['issue']; ?>
                    <article class="etds-issue-card" data-severity="<?= etds_qc_h((string) $issue['severity']) ?>">
                      <div class="etds-chip-row" style="margin-bottom:10px;">
                        <span class="etds-status-chip" data-tone="<?= etds_qc_h((string) $entry['tone']) ?>"><?= etds_qc_h((string) $entry['severity_label']) ?></span>
                        <span class="etds-chip"><?= etds_qc_h((string) ($record['record_id'] ?? '')) ?></span>
                      </div>
                      <h4><?= etds_qc_h((string) $issue['message']) ?></h4>
                      <p><strong>Suggested correction:</strong> <?= etds_qc_h((string) ($issue['suggested_correction'] ?? 'Review manually.')) ?></p>
                      <div class="etds-issue-actions">
                        <?php foreach (['accepted' => 'Accept Suggestion', 'resolved' => 'Mark Resolved', 'ignored' => 'Ignore'] as $statusValue => $label): ?>
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
                      <details class="etds-issue-edit">
                        <summary>Edit</summary>
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
                    </article>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="etds-readiness-banner" data-tone="<?= $readiness ? 'good' : 'critical' ?>">
              <span class="eyebrow">Doctor Certification</span>
              <h3><?= etds_qc_h($processingResultLabel) ?></h3>
              <p><?= etds_qc_h($exportResultLabel) ?> · <?= etds_qc_h($doctorCertificationLabel) ?></p>
            </div>
            <div class="etds-grid etds-dashboard-grid" style="margin-top:20px;">
              <div class="etds-stat"><strong><?= $quality ?>%</strong><span>Data Health Score</span></div>
              <div class="etds-stat"><strong><?= $matchedCount ?>/<?= max($matchedCount + $partialCount + $unmatchedCount, 1) ?></strong><span>Reconciliation Status</span></div>
              <div class="etds-stat"><strong><?= etds_qc_h($treatmentStatusLabel) ?></strong><span>Treatment Completion Status</span></div>
              <div class="etds-stat"><strong><?= etds_qc_h($doctorCertificationLabel) ?></strong><span>Doctor Certification</span></div>
              <div class="etds-stat"><strong><?= etds_qc_h($exportResultLabel) ?></strong><span>Result</span></div>
            </div>
          <?php endif; ?>
        <?php elseif ($workspace === 'excel' && $activeSession): ?>
          <div class="etds-readiness-banner" data-tone="<?= $readiness ? 'good' : 'critical' ?>">
            <span class="eyebrow">Output Readiness</span>
            <h3><?= etds_qc_h($exportResultLabel) ?></h3>
            <p><?= $readiness ? 'This case can proceed to final Excel generation.' : 'Resolve blocked issues before generating the final clean workbook.' ?></p>
          </div>

          <div class="etds-grid etds-dashboard-grid" style="margin-top:20px;">
            <div class="etds-stat"><strong><?= etds_qc_h($doctorCertificationLabel) ?></strong><span>Doctor Certification</span></div>
            <div class="etds-stat"><strong><?= $quality ?>%</strong><span>Case Health Report</span></div>
            <div class="etds-stat"><strong><?= count($exportFiles) ?></strong><span>Generated Files</span></div>
            <div class="etds-stat"><strong><?= etds_qc_h($exportResultLabel) ?></strong><span>Download Centre</span></div>
            <div class="etds-stat"><strong><?= $healthIssueCount ?></strong><span>Blocked Issues</span></div>
          </div>

          <div class="etds-output-grid">
            <?php foreach ([
              'Working Excel' => 'Operational working pack for internal review.',
              'Correction Excel' => 'Field correction pack for treatment workflow.',
              'Exception Report' => 'Doctor findings summary for unresolved cases.',
              'Final Clean Excel' => 'Certified export for downstream processing.',
            ] as $title => $description): ?>
              <article class="etds-output-card">
                <h3><?= etds_qc_h($title) ?></h3>
                <p><?= etds_qc_h($description) ?></p>
                <span class="etds-chip"><?= $title === 'Final Clean Excel' ? etds_qc_h($exportResultLabel) : 'Generated Output' ?></span>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="etds-grid etds-two-col" style="margin-top:24px;">
            <div class="etds-panel">
              <h2>Download Centre</h2>
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="export_xlsx">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <button class="btn btn-primary" type="submit">Generate Final Clean Excel</button>
              </form>
            </div>
            <div class="etds-panel">
              <h2>Generated Files</h2>
              <?php if (empty($exportFiles)): ?>
                <div class="etds-empty">No output files have been generated yet.</div>
              <?php else: ?>
                <ul class="etds-mini-list">
                  <?php foreach ($exportFiles as $filePath): $fileName = basename($filePath); ?>
                    <li>
                      <span><?= etds_qc_h($fileName) ?></span>
                      <a class="btn btn-outline btn-sm" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download&session=' . urlencode($sessionId) . '&file=' . urlencode($fileName))) ?>">Download</a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
