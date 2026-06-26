<?php
declare(strict_types=1);

$workspace = strtolower((string) ($_GET['ws'] ?? 'dashboard'));
if (!in_array($workspace, ['dashboard', 'intake', 'extraction', 'bench', 'excel'], true)) {
  $workspace = 'dashboard';
}
$benchTab = strtolower((string) ($_GET['tab'] ?? 'diagnosis'));
if (!in_array($benchTab, ['diagnosis', 'reconciliation', 'treatment', 'readiness'], true)) {
  $benchTab = 'diagnosis';
}

$quality = (int) ($validatedData['summary']['quality_score'] ?? 0);
$reconScore = (int) ($reconciliation['summary']['reconciliation_score'] ?? 0);
$readiness = $activeSession ? etds_qc_export_readiness($sessionId) : false;
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
$rowsFlagged = count($openIssues);
$issueLabelMap = [
  'missing_challan_reference' => 'Missing Challan Reference',
  'invalid_pan' => 'Invalid PAN',
  'missing_pan' => 'Missing PAN',
  'duplicate_deductee' => 'Duplicate Deductee',
  'invalid_date' => 'Invalid Date',
  'missing_amount' => 'Missing Amount',
];
$issueTypeCounts = [];
$issueTypeTones = [];
foreach ($openIssues as $entry) {
  $issueType = (string) ($entry['issue']['type'] ?? 'open_issue');
  $issueTypeCounts[$issueType] = (int) ($issueTypeCounts[$issueType] ?? 0) + 1;
  $issueTypeTones[$issueType] = (string) ($entry['tone'] ?? 'warning');
}
$doctorFindingChips = [];
foreach ($issueTypeCounts as $issueType => $count) {
  $doctorFindingChips[] = [
    'label' => ($issueLabelMap[$issueType] ?? ucwords(str_replace('_', ' ', $issueType))) . ': ' . $count,
    'tone' => $issueTypeTones[$issueType] ?? 'warning',
  ];
}
$primaryIssueType = array_key_first($issueTypeCounts);
$primaryIssueLabel = $primaryIssueType !== null
  ? ($issueLabelMap[$primaryIssueType] ?? ucwords(str_replace('_', ' ', $primaryIssueType)))
  : 'Open Health Issue';

$financialHealthScore = max(0, min(100, $reconScore > 0 ? $reconScore : ($readiness ? 100 : ($matchedCount > 0 ? 96 : 78))));
$documentsReceived = count($sourceData['documents'] ?? []);
$documentChecklist = [
  ['label' => 'Salary Register', 'status' => $documentsReceived > 0 ? 'received' : 'pending'],
  ['label' => 'Deductee Master', 'status' => $documentsReceived > 1 ? 'received' : 'pending'],
  ['label' => 'Challan Register', 'status' => $documentsReceived > 2 ? 'received' : 'pending'],
  ['label' => 'Working File', 'status' => $documentsReceived > 3 ? 'received' : 'pending'],
];
$documentsPending = count(array_filter($documentChecklist, static fn(array $item): bool => $item['status'] === 'pending'));
$openCases = max(1, $counts['sessions']);
$casesReady = $counts['ready'];
$casesBlocked = max(0, $counts['validation'] + $counts['reconciliation']);
$activityTimeline = [];
$timelineSteps = [
  ['key' => 'intake', 'label' => 'Document Classification', 'depends' => $documentsReceived > 0],
  ['key' => 'ocr', 'label' => 'OCR Completed', 'depends' => $documentsReceived > 0],
  ['key' => 'mapping', 'label' => 'Field Mapping Completed', 'depends' => $sourceColumnsCount > 0],
  ['key' => 'dedup', 'label' => 'Duplicate Detection Completed', 'depends' => $totalRecords > 0],
  ['key' => 'validation', 'label' => 'Validation Completed', 'depends' => $totalRecords > 0],
  ['key' => 'challan', 'label' => 'Challan Matching Completed', 'depends' => $challanRows !== []],
];
$timelineOffset = 0;
foreach ($timelineSteps as $step) {
  $hour = 10 + intval(($timelineOffset + 42) / 60);
  $minute = ($timelineOffset + 42) % 60;
  $activityTimeline[] = [
    'time' => str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $minute, 2, '0', STR_PAD_LEFT) . ' AM',
    'label' => $step['label'],
    'tone' => $step['depends'] ? 'good' : 'warning',
  ];
  $timelineOffset++;
}

$matchedAmount = 0.0;
$partialAmount = 0.0;
$unmatchedAmount = 0.0;
foreach (($validatedData['records'] ?? []) as $record) {
  $ref = trim((string) ($record['normalized']['challan_reference'] ?? ''));
  $amount = (float) ($record['normalized']['tds_amount'] ?? 0);
  if ($ref === '' || !isset($challanMap[$ref])) {
    $unmatchedAmount += $amount;
  } else {
    $balance = round((float) ($challanMap[$ref]['balance_total'] ?? 0), 2);
    if ($balance === 0.0) {
      $matchedAmount += $amount;
    } else {
      $partialAmount += $amount;
    }
  }
}
$allocationVariance = (float) ($reconciliation['summary']['difference'] ?? 0);
$unallocatedAmount = (float) ($reconciliation['summary']['balance'] ?? 0);
$topPriorityIssue = $healthIssueCount > 0
  ? $primaryIssueLabel . ' requires treatment attention'
  : ($unallocatedAmount > 0 ? 'Unallocated challan amount requires attention' : 'Review readiness and certify the case');
$estimatedResolution = $healthIssueCount > 0 ? max(4, min(12, $healthIssueCount)) . ' Minutes' : ($unallocatedAmount > 0 ? '8 Minutes' : '2 Minutes');
$scoreImprovement = $quality > 0 ? min(100, $quality + 12) : 94;
$doctorCertificationDate = $readiness ? date('d M Y') : 'Pending';
$certifiedBy = $readiness ? 'e-TDS Doctor' : (string) ($user['name'] ?? (string) ($user['email'] ?? 'Operator'));
$currentStage = match ($workspace) {
  'intake' => 'intake',
  'extraction' => 'extraction',
  'bench' => match ($benchTab) {
    'diagnosis' => 'diagnosis',
    'reconciliation' => 'treatment',
    'treatment' => 'treatment',
    'readiness' => 'certification',
    default => 'diagnosis',
  },
  'excel' => 'final',
  default => $documentsReceived === 0 ? 'intake' : ($totalRecords === 0 ? 'extraction' : ($healthIssueCount > 0 ? 'diagnosis' : ($readiness ? 'final' : 'certification'))),
};
$workflowStages = [
  ['key' => 'intake', 'label' => 'Intake'],
  ['key' => 'extraction', 'label' => 'Extraction'],
  ['key' => 'diagnosis', 'label' => 'Doctor Diagnosis'],
  ['key' => 'treatment', 'label' => 'Treatment'],
  ['key' => 'certification', 'label' => 'Certification'],
  ['key' => 'final', 'label' => 'Return Preparation Centre'],
];
$stageKeys = array_column($workflowStages, 'key');
$currentStageIndex = array_search($currentStage, $stageKeys, true);
$currentStageIndex = $currentStageIndex === false ? 0 : $currentStageIndex;

if (!$activeSession) {
  $caseStatusLabel = 'Awaiting Case Creation';
  $nextActionTitle = 'Create a new case in Intake Centre';
  $nextActionText = 'Register the client, TAN, financial year, quarter, and return type to begin the workflow.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=intake&view=create');
  $nextActionLabel = 'Open Intake Centre';
} elseif ($documentsReceived === 0) {
  $caseStatusLabel = 'Awaiting Source Documents';
  $nextActionTitle = 'Upload the remaining source bundle';
  $nextActionText = 'Complete intake before extraction can begin.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=intake&view=session&session=' . urlencode($sessionId));
  $nextActionLabel = 'Upload Files';
} elseif ($totalRecords === 0) {
  $caseStatusLabel = 'Ready For Extraction';
  $nextActionTitle = 'Run extraction and field mapping';
  $nextActionText = 'Transform uploaded documents into structured records for Doctor\'s Bench.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=extraction&view=session&session=' . urlencode($sessionId));
  $nextActionLabel = 'Proceed to Extraction';
} elseif ($healthIssueCount > 0 || $unallocatedAmount > 0) {
  $caseStatusLabel = 'Doctor Attention Required';
  $nextActionTitle = 'Resolve high-priority treatment items';
  $nextActionText = $healthIssueCount > 0
    ? 'Health issues must be resolved before certification can proceed.'
    : 'Reconciliation variance requires action before certification.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=bench&view=session&session=' . urlencode($sessionId) . '&tab=treatment');
  $nextActionLabel = 'Resolve Now';
} elseif (!$readiness) {
  $caseStatusLabel = 'Certification Pending';
  $nextActionTitle = 'Review readiness and certify';
  $nextActionText = 'All treatment items are nearly resolved. Review the readiness tab next.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=bench&view=session&session=' . urlencode($sessionId) . '&tab=readiness');
  $nextActionLabel = 'Open Readiness';
} else {
  $caseStatusLabel = 'Fit For Processing';
  $nextActionTitle = 'Generate final Excel outputs';
  $nextActionText = 'Certification is complete. Continue to Return Preparation Centre.';
  $nextActionLink = site_href('/fintech/etds-qc/?ws=excel&view=session&session=' . urlencode($sessionId));
  $nextActionLabel = 'Open Return Preparation Centre';
}

$buildUrl = static function (array $params = []) use ($sessionId, $workspace, $benchTab): string {
  $resolvedWorkspace = (string) ($params['ws'] ?? $workspace);
  $resolvedSessionId = array_key_exists('session', $params) ? (string) $params['session'] : $sessionId;
  $resolvedView = (string) ($params['view'] ?? ($resolvedSessionId !== '' ? 'session' : 'dashboard'));
  $resolvedTab = (string) ($params['tab'] ?? $benchTab);
  $query = ['ws' => $resolvedWorkspace];
  if ($resolvedView !== 'dashboard') {
    $query['view'] = $resolvedView;
  }
  if ($resolvedSessionId !== '') {
    $query['session'] = $resolvedSessionId;
  }
  if ($resolvedWorkspace === 'bench') {
    $query['tab'] = $resolvedTab;
  }
  return site_href('/fintech/etds-qc/?' . http_build_query($query));
};

$workspaceItems = [
  ['key' => 'dashboard', 'label' => 'Case Dashboard', 'status' => 'ready'],
  ['key' => 'intake', 'label' => 'Intake Centre', 'status' => $documentsReceived > 0 ? 'ready' : 'active'],
  ['key' => 'extraction', 'label' => 'Extraction Centre', 'status' => $totalRecords > 0 ? 'ready' : 'active'],
  ['key' => 'bench', 'label' => "Doctor's Bench", 'status' => $healthIssueCount > 0 ? 'attention' : 'ready'],
  ['key' => 'excel', 'label' => 'Return Preparation Centre', 'status' => $readiness ? 'ready' : 'blocked'],
];

?>
<div class="qc-app" data-workspace="<?= etds_qc_h($workspace) ?>">
  <aside class="qc-sidebar" id="qc-sidebar">
    <div class="qc-sidebar__brand">
      <div class="qc-sidebar__mark">eT</div>
      <div>
        <h1 class="qc-sidebar__title">e-TDSDoc</h1>
        <p class="qc-sidebar__tag">Diagnose. Reconcile. Prepare.</p>
      </div>
      <button class="qc-sidebar__close" type="button" data-sidebar-close aria-label="Close">&times;</button>
    </div>

    <nav class="qc-sidebar__nav" aria-label="Primary application navigation">
      <?php foreach ($workspaceItems as $item): ?>
        <a class="qc-sidebar__link<?= $workspace === $item['key'] ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => $item['key'], 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
          <span class="qc-sidebar__icon"><?= etds_qc_nav_icon($item['key']) ?></span>
          <span class="qc-sidebar__label"><?= etds_qc_h($item['label']) ?></span>
          <span class="qc-sidebar__state is-<?= etds_qc_h($item['status']) ?>"></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <form class="qc-sidebar__logout" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="logout">
      <button class="qc-logout-btn" type="submit" data-confirm="Log out of e-TDSDoc now?">
        <span class="qc-sidebar__icon"><?= etds_qc_nav_icon('logout') ?></span>
        <span>Logout</span>
      </button>
    </form>
  </aside>
  <button class="qc-sidebar-overlay" type="button" data-sidebar-close aria-label="Close navigation overlay"></button>

  <div class="qc-shell">
    <header class="qc-topbar">
      <div class="qc-topbar__left">
        <button class="qc-menu-btn" type="button" data-sidebar-toggle aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
        <a class="qc-topbar__brand" href="<?= etds_qc_h($buildUrl(['ws' => 'dashboard', 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
          <span class="qc-topbar__brand-mark">eT</span>
          <span class="qc-topbar__brand-copy">
            <strong>e-TDSDoc</strong>
            <small>Diagnose. Reconcile. Prepare.</small>
          </span>
        </a>
      </div>

      <div class="qc-topbar__center">
        <span class="qc-chip"><label>Case</label><strong><?= etds_qc_h($activeSession['session_id'] ?? 'None') ?></strong></span>
        <span class="qc-chip"><label>Client</label><strong><?= etds_qc_h($activeSession['client_name'] ?? '—') ?></strong></span>
        <span class="qc-chip"><label>TAN</label><strong><?= etds_qc_h($activeSession['tan'] ?? '—') ?></strong></span>
        <span class="qc-chip"><label>FY</label><strong><?= etds_qc_h($activeSession['financial_year'] ?? '—') ?></strong></span>
        <span class="qc-chip"><label>Qtr</label><strong><?= etds_qc_h($activeSession['quarter'] ?? '—') ?></strong></span>
      </div>

      <div class="qc-topbar__right">
        <span class="qc-status-chip is-online">Doctor Online</span>
        <span class="qc-user-chip"><?= etds_qc_h((string) ($user['name'] ?? 'Operator')) ?></span>
        <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="logout">
          <button type="submit" data-confirm="Log out of e-TDSDoc?">Logout</button>
        </form>
      </div>
    </header>

    <main class="qc-main">
      <?php etds_qc_render_flash($flash); ?>

      <section class="qc-tracker">
        <?php foreach ($workflowStages as $index => $stage): ?>
          <?php
            $state = $index < $currentStageIndex ? 'done' : ($index === $currentStageIndex ? 'current' : 'pending');
          ?>
          <div class="qc-tracker__step is-<?= etds_qc_h($state) ?>">
            <span class="qc-tracker__dot"></span>
            <span class="qc-tracker__label"><?= etds_qc_h($stage['label']) ?></span>
          </div>
        <?php endforeach; ?>
      </section>

      <?php if (!$activeSession && $workspace !== 'intake' && $workspace !== 'dashboard'): ?>
        <section class="qc-workspace" style="grid-template-columns: 280px 1fr; display: grid;">
          <aside class="qc-panel" style="padding: 12px;">
            <h3 style="font-size: 12px; margin: 0 0 8px; color: var(--qc-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 700;">Case Navigator</h3>
            <div style="margin-bottom: 10px;">
              <input type="text" placeholder="Search cases..." style="width: 100%; padding: 6px 10px; border: 1px solid var(--qc-border); border-radius: 5px; font-size: 11px; background: #f8fafc;">
            </div>
            <div style="margin-bottom: 10px;">
              <a class="btn btn-primary" style="width: 100%; font-size: 11px;" href="<?= etds_qc_h($buildUrl(['ws' => 'intake', 'view' => 'create'])) ?>">+ New Case</a>
            </div>
            <div style="font-size: 10px; color: var(--qc-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 700; margin-bottom: 6px;">Recent Cases</div>
            <?php if (!empty($sessions)): ?>
              <?php foreach (array_slice($sessions, 0, 5) as $row): ?>
                <a href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=dashboard&view=session&session=' . urlencode((string) $row['session_id']))) ?>" style="display: block; padding: 6px 8px; border-radius: 5px; font-size: 11px; color: var(--qc-text); text-decoration: none; margin-bottom: 2px; border: 1px solid transparent; transition: background .1s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                  <strong style="display: block; font-size: 11px;"><?= etds_qc_h((string) $row['client_name']) ?></strong>
                  <span style="font-size: 10px; color: var(--qc-muted);"><?= etds_qc_h((string) $row['quarter']) ?> · <?= etds_qc_h((string) $row['return_type']) ?></span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div style="font-size: 11px; color: var(--qc-muted); padding: 8px;">No cases yet</div>
            <?php endif; ?>
          </aside>
          <div class="qc-panel" style="display: flex; align-items: center; justify-content: center; min-height: 300px;">
            <div style="text-align: center; color: var(--qc-muted);">
              <div style="font-size: 32px; margin-bottom: 8px; opacity: .3;">📋</div>
              <h2 style="font-size: 14px; font-weight: 600; margin: 0 0 4px; color: var(--qc-text);">Select a case to begin</h2>
              <p style="font-size: 11px; margin: 0;">Choose a case from the navigator or create a new one.</p>
            </div>
          </div>
        </section>
      <?php elseif ($workspace === 'dashboard'): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Case Dashboard</span>
              <h2>Operational command centre for the active TDS case</h2>
              <p>See where the case stands, what needs attention, and the next recommended action immediately.</p>
            </div>
            <div class="qc-head-summary">
              <span class="qc-status-chip <?= $readiness ? 'is-online' : 'is-alert' ?>">Current Case: <?= etds_qc_h($caseStatusLabel) ?></span>
            </div>
          </div>

          <div class="qc-metrics qc-metrics--four">
            <article class="qc-metric-card"><strong><?= $openCases ?></strong><span>Open Cases</span></article>
            <article class="qc-metric-card"><strong><?= $casesReady ?></strong><span>Cases Ready</span></article>
            <article class="qc-metric-card"><strong><?= $casesBlocked ?></strong><span>Cases Blocked</span></article>
            <article class="qc-metric-card"><strong><?= $criticalIssues ?></strong><span>Critical Issues</span></article>
          </div>

          <section class="qc-banner">
            <div>
              <span class="qc-kicker">Progress Banner</span>
              <h3><?= etds_qc_h($nextActionTitle) ?></h3>
              <p><?= etds_qc_h($nextActionText) ?></p>
            </div>
            <div class="qc-banner__actions">
              <a class="btn btn-light" href="<?= etds_qc_h($nextActionLink) ?>"><?= etds_qc_h($nextActionLabel) ?></a>
              <div class="qc-banner__score">
                <strong><?= $quality ?>%</strong>
                <span>Data Health Score</span>
              </div>
            </div>
          </section>

          <div class="qc-grid qc-grid--dashboard">
            <article class="qc-panel">
              <h3>Current Case</h3>
              <div class="qc-detail-list">
                <div><span>Client Name</span><strong><?= etds_qc_h($activeSession['client_name'] ?? 'Pending') ?></strong></div>
                <div><span>TAN</span><strong><?= etds_qc_h($activeSession['tan'] ?? 'Pending') ?></strong></div>
                <div><span>FY</span><strong><?= etds_qc_h($activeSession['financial_year'] ?? 'Pending') ?></strong></div>
                <div><span>Quarter</span><strong><?= etds_qc_h($activeSession['quarter'] ?? 'Pending') ?></strong></div>
              </div>
            </article>
            <article class="qc-panel">
              <h3>Case Metrics</h3>
              <div class="qc-signal-list">
                <div><span>Documents Received</span><strong><?= $documentsReceived ?></strong></div>
                <div><span>Cases Ready For Export</span><strong><?= $casesReady ?></strong></div>
                <div><span>Next Action</span><strong><?= etds_qc_h($nextActionLabel) ?></strong></div>
              </div>
            </article>
            <article class="qc-panel">
              <h3>Completed Stages</h3>
              <ul class="qc-stage-list">
                <?php $hasCompleted = false; ?>
                <?php foreach ($workflowStages as $index => $stage): ?>
                  <?php if ($index < $currentStageIndex): ?>
                    <?php $hasCompleted = true; ?>
                    <li class="is-done"><?= etds_qc_h($stage['label']) ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
                <?php if (!$hasCompleted): ?>
                  <li class="is-pending">No stages completed yet</li>
                <?php endif; ?>
              </ul>
            </article>
            <article class="qc-panel">
              <h3>Pending Stages</h3>
              <ul class="qc-stage-list">
                <?php foreach ($workflowStages as $index => $stage): ?>
                  <?php if ($index >= $currentStageIndex): ?>
                    <li class="<?= $index === $currentStageIndex ? 'is-current' : 'is-pending' ?>"><?= etds_qc_h($stage['label']) ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </article>
          </div>

          <div class="qc-launch-grid">
            <?php foreach ([
              ['ws' => 'intake', 'title' => 'Intake Centre', 'copy' => 'Receive and organize the source bundle.'],
              ['ws' => 'extraction', 'title' => 'Extraction Centre', 'copy' => 'Transform uploaded files into structured data.'],
              ['ws' => 'bench', 'title' => "Doctor's Bench", 'copy' => 'Diagnose, reconcile, treat, and certify the case.'],
              ['ws' => 'excel', 'title' => 'Return Preparation Centre', 'copy' => 'Prepare output files once the case is fit.'],
            ] as $card): ?>
              <a class="qc-launch-card" href="<?= etds_qc_h($buildUrl(['ws' => $card['ws'], 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
                <span class="qc-launch-card__icon"><?= etds_qc_nav_icon($card['ws']) ?></span>
                <h4><?= etds_qc_h($card['title']) ?></h4>
                <p><?= etds_qc_h($card['copy']) ?></p>
              </a>
            <?php endforeach; ?>
          </div>

          <section class="qc-panel">
            <h3>Case Signals</h3>
            <div class="qc-signal-chips">
              <span class="qc-signal-chip is-warning"><?= etds_qc_h($primaryIssueLabel) ?>: <?= (int) ($issueTypeCounts[$primaryIssueType] ?? 0) ?></span>
              <span class="qc-signal-chip is-warning">Open Health Issues: <?= $healthIssueCount ?></span>
              <span class="qc-signal-chip is-warning">Unallocated Challans: <?= $unallocatedAmount > 0 ? 1 : 0 ?></span>
              <span class="qc-signal-chip is-warning">Pending Reconciliation: <?= $casesBlocked > 0 ? 1 : 0 ?></span>
            </div>
          </section>
        </section>

      <?php elseif ($workspace === 'intake'): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Intake Centre</span>
              <h2>Receive and organize source documents</h2>
              <p>Capture the case structure, monitor document readiness, and move the bundle to extraction.</p>
            </div>
          </div>

          <div class="qc-grid qc-grid--two">
            <article class="qc-panel">
              <h3>Case Information</h3>
              <div class="qc-detail-list">
                <div><span>Client</span><strong><?= etds_qc_h($activeSession['client_name'] ?? 'New Case') ?></strong></div>
                <div><span>TAN</span><strong><?= etds_qc_h($activeSession['tan'] ?? 'Pending') ?></strong></div>
                <div><span>FY</span><strong><?= etds_qc_h($activeSession['financial_year'] ?? '2025-26') ?></strong></div>
                <div><span>Quarter</span><strong><?= etds_qc_h($activeSession['quarter'] ?? 'Q2') ?></strong></div>
              </div>
              <?php if (!$activeSession): ?>
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" class="qc-form-block">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="create_session">
                  <div class="etds-fields">
                    <div class="etds-field"><label for="client_name">Client Name</label><input id="client_name" name="client_name" required></div>
                    <div class="etds-field"><label for="tan">TAN</label><input id="tan" name="tan" maxlength="10" required></div>
                    <div class="etds-field"><label for="financial_year">Financial Year</label><select id="financial_year" name="financial_year" required><option value="2025-26">2025-26</option><option value="2024-25">2024-25</option></select></div>
                    <div class="etds-field"><label for="quarter">Quarter</label><select id="quarter" name="quarter" required><option>Q1</option><option selected>Q2</option><option>Q3</option><option>Q4</option></select></div>
                    <div class="etds-field"><label for="return_type">Return Type</label><select id="return_type" name="return_type" required><option>24Q</option><option selected>26Q</option><option>27Q</option><option>27EQ</option></select></div>
                    <div class="etds-field etds-field-full"><label for="remarks">Notes</label><textarea id="remarks" name="remarks"></textarea></div>
                  </div>
                  <div class="qc-action-row"><button class="btn btn-primary" type="submit">Create Diagnostic Case</button></div>
                </form>
              <?php endif; ?>
            </article>

            <article class="qc-panel">
              <h3>Document Checklist</h3>
              <ul class="qc-checklist">
                <?php foreach ($documentChecklist as $item): ?>
                  <li><span class="qc-checklist__dot is-<?= etds_qc_h($item['status']) ?>"></span><?= etds_qc_h($item['label']) ?><strong><?= ucfirst($item['status']) ?></strong></li>
                <?php endforeach; ?>
              </ul>
              <div class="qc-mini-metrics">
                <div><span>Documents Received</span><strong><?= $documentsReceived ?></strong></div>
                <div><span>Documents Pending</span><strong><?= $documentsPending ?></strong></div>
              </div>
            </article>
          </div>

          <?php if ($activeSession): ?>
            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Upload Queue</h3>
                <p class="qc-copy">Source documents are grouped by operational relevance before extraction.</p>
                <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="upload_documents">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <div class="etds-fields">
                    <div class="etds-field etds-field-full">
                      <label for="documents">Upload Files</label>
                      <input id="documents" type="file" name="documents[]" accept=".xls,.xlsx,.csv,.txt,.pdf" multiple required>
                    </div>
                  </div>
                  <div class="qc-action-row">
                    <button class="btn btn-primary" type="submit">Upload Files</button>
                    <a class="btn btn-outline" href="<?= etds_qc_h($buildUrl(['ws' => 'extraction', 'view' => 'session'])) ?>">Proceed to Extraction</a>
                  </div>
                </form>
              </article>

              <article class="qc-panel">
                <h3>Document Register</h3>
                <?php if (empty($sourceData['documents'])): ?>
                  <div class="qc-empty-inline">No source files are registered yet.</div>
                <?php else: ?>
                  <div class="etds-table-shell">
                    <table class="etds-table">
                      <thead><tr><th>File</th><th>Type</th><th>Size</th><th>Status</th></tr></thead>
                      <tbody>
                        <?php foreach (($sourceData['documents'] ?? []) as $document): ?>
                          <tr>
                            <td><?= etds_qc_h((string) ($document['file_name'] ?? '')) ?></td>
                            <td><?= strtoupper(etds_qc_h((string) ($document['extension'] ?? 'file'))) ?></td>
                            <td><?= number_format(((int) ($document['size_bytes'] ?? 0)) / 1024, 1) ?> KB</td>
                            <td><?= etds_qc_h((string) ($document['extraction_status'] ?? 'pending')) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
                <div class="qc-note">Notes: <?= $documentsReceived > 0 ? $documentsReceived . ' source file' . ($documentsReceived > 1 ? 's' : '') . ' registered for this case.' : 'No source files registered yet.' ?></div>
              </article>
            </div>
          <?php endif; ?>
        </section>

      <?php elseif ($workspace === 'extraction' && $activeSession): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Extraction Centre</span>
              <h2>Transform uploaded documents into structured datasets</h2>
              <p>Monitor parsing progress, mapping, classification, and AI-visible activity without exposing engine internals.</p>
            </div>
          </div>

          <div class="qc-metrics qc-metrics--five">
            <article class="qc-metric-card"><strong><?= $documentsReceived ?></strong><span>Source Files</span></article>
            <article class="qc-metric-card"><strong><?= $totalRecords ?></strong><span>Rows Parsed</span></article>
            <article class="qc-metric-card"><strong><?= $rowsFlagged ?></strong><span>Rows Flagged</span></article>
            <article class="qc-metric-card"><strong><?= $sourceColumnsCount ?></strong><span>Field Mapping</span></article>
            <article class="qc-metric-card"><strong><?= count($activityTimeline) ?></strong><span>Doctor Activity Log</span></article>
          </div>

          <div class="qc-grid qc-grid--two">
            <article class="qc-panel">
              <h3>Extraction Run Summary</h3>
              <div class="qc-detail-list">
                <div><span>Extraction Status</span><strong><?= $documentsReceived > 0 ? 'Completed' : 'Pending' ?></strong></div>
                <div><span>Extraction Exceptions</span><strong><?= $rowsFlagged ?></strong></div>
                <div><span>Extracted Records Summary</span><strong><?= $totalRecords ?> deductee rows</strong></div>
                <div><span>Document Types</span><strong><?= $documentsReceived > 0 ? implode(' / ', array_unique(array_map(static fn(array $doc): string => strtoupper((string) ($doc['extension'] ?? 'file')), ($sourceData['documents'] ?? [])))) : 'Pending' ?></strong></div>
              </div>
              <div class="qc-action-row">
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="extract_validate">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <button class="btn btn-primary" type="submit">Run Extraction</button>
                </form>
                <a class="btn btn-outline" href="<?= etds_qc_h($buildUrl(['ws' => 'bench', 'tab' => 'diagnosis', 'view' => 'session'])) ?>">Open Doctor's Bench</a>
              </div>
            </article>

            <article class="qc-panel">
              <h3>Field Mapping and Exceptions</h3>
              <ul class="qc-mapping-list">
                <?php foreach ([
                  'Employee Name → deductee_name',
                  'PAN No → pan',
                  'TDS Amount → tds_amount',
                  'Challan Ref → challan_reference',
                  'Deduction Date → deduction_date',
                ] as $mapping): ?>
                  <li><?= etds_qc_h($mapping) ?></li>
                <?php endforeach; ?>
              </ul>
              <div class="qc-note">Extraction Exceptions: Deductee rows are missing challan references and remain queued for reconciliation.</div>
            </article>
          </div>

          <article class="qc-panel">
            <h3>e-TDS Doctor Activity Log</h3>
            <div class="qc-timeline">
              <?php foreach ($activityTimeline as $item): ?>
                <div class="qc-timeline__item">
                  <span class="qc-timeline__time"><?= etds_qc_h($item['time']) ?></span>
                  <span class="qc-timeline__state is-<?= etds_qc_h($item['tone']) ?>"></span>
                  <div>
                    <strong><?= etds_qc_h($item['label']) ?></strong>
                    <p>Execution recorded for the active case bundle.</p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </article>
        </section>

      <?php elseif ($workspace === 'bench' && $activeSession): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Doctor's Bench</span>
              <h2>Flagship workspace for diagnosis, treatment, and certification</h2>
              <p>Use this workspace to understand what is wrong, what needs attention, and what should happen next.</p>
            </div>
          </div>

          <section class="qc-priority-banner">
            <div>
              <span class="qc-kicker">Top Priority Issue</span>
              <h3><?= etds_qc_h($topPriorityIssue) ?></h3>
              <p>Estimated Resolution Time: <?= etds_qc_h($estimatedResolution) ?></p>
            </div>
            <a class="btn btn-light" href="<?= etds_qc_h($buildUrl(['ws' => 'bench', 'tab' => 'treatment', 'view' => 'session'])) ?>">Resolve Now</a>
          </section>

          <div class="qc-tabbar" role="tablist" aria-label="Doctor's Bench tabs">
            <?php foreach (['diagnosis' => 'Diagnosis', 'reconciliation' => 'Reconciliation', 'treatment' => 'Treatment', 'readiness' => 'Readiness'] as $tabKey => $tabLabel): ?>
              <a class="qc-tabbar__tab<?= $benchTab === $tabKey ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => 'bench', 'tab' => $tabKey, 'view' => 'session'])) ?>"><?= etds_qc_h($tabLabel) ?></a>
            <?php endforeach; ?>
          </div>

          <?php if ($benchTab === 'diagnosis'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $quality ?>%</strong><span>Data Health Score</span></article>
              <article class="qc-metric-card"><strong><?= $criticalIssues ?></strong><span>Critical Issues</span></article>
              <article class="qc-metric-card"><strong><?= $moderateIssues ?></strong><span>Moderate Issues</span></article>
              <article class="qc-metric-card"><strong><?= $minorIssues ?></strong><span>Minor Issues</span></article>
              <article class="qc-metric-card"><strong><?= $healthIssueCount ?></strong><span>Health Issues Queue</span></article>
            </div>
            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Doctor Findings</h3>
                <div class="qc-signal-chips">
                  <?php if ($doctorFindingChips === []): ?>
                    <span class="qc-signal-chip is-good">No active findings</span>
                  <?php else: ?>
                    <?php foreach ($doctorFindingChips as $chip): ?>
                      <span class="qc-signal-chip <?= ($chip['tone'] ?? '') === 'critical' ? 'is-critical' : 'is-warning' ?>"><?= etds_qc_h((string) ($chip['label'] ?? 'Finding')) ?></span>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
                <div class="qc-note">Priority Issues: <?= etds_qc_h($primaryIssueLabel) ?> must be completed before certification can proceed.</div>
              </article>
              <article class="qc-panel">
                <h3>Health Issues Queue</h3>
                <div class="qc-issue-stack">
                  <?php if ($openIssues === []): ?>
                    <div class="qc-empty-inline">No diagnosis findings are pending.</div>
                  <?php else: ?>
                    <?php foreach (array_slice($openIssues, 0, 5) as $entry): ?>
                      <article class="qc-issue">
                        <span class="qc-signal-chip <?= ($entry['tone'] ?? '') === 'critical' ? 'is-critical' : 'is-warning' ?>"><?= etds_qc_h((string) ($entry['severity_label'] ?? 'Issue')) ?></span>
                        <h4><?= etds_qc_h((string) ($entry['issue']['message'] ?? 'Review this record')) ?></h4>
                        <p><?= etds_qc_h((string) ($entry['issue']['suggested_correction'] ?? 'Correct the source record.')) ?></p>
                      </article>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </article>
            </div>

          <?php elseif ($benchTab === 'reconciliation'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $financialHealthScore ?>%</strong><span>Financial Health Score</span></article>
              <article class="qc-metric-card"><strong>₹<?= number_format($matchedAmount, 0) ?></strong><span>Matched Amount</span></article>
              <article class="qc-metric-card"><strong>₹<?= number_format($partialAmount, 0) ?></strong><span>Partially Matched Amount</span></article>
              <article class="qc-metric-card"><strong>₹<?= number_format($unmatchedAmount, 0) ?></strong><span>Unmatched Amount</span></article>
              <article class="qc-metric-card"><strong>₹<?= number_format($unallocatedAmount, 0) ?></strong><span>Unallocated Amounts</span></article>
            </div>
            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Financial Reconciliation</h3>
                <div class="qc-detail-list">
                  <div><span>Matched Amount</span><strong>₹<?= number_format($matchedAmount, 0) ?></strong></div>
                  <div><span>Allocation Variance</span><strong>₹<?= number_format($allocationVariance, 0) ?></strong></div>
                  <div><span>Deductee Reconciliation</span><strong><?= $matchedCount ?> Matched</strong></div>
                  <div><span>Summary Reconciliation</span><strong><?= $partialCount ?> Partial</strong></div>
                </div>
              </article>
            <article class="qc-panel">
                <h3>Mismatch Queue</h3>
                <ul class="qc-checklist">
                  <?php if ($unallocatedAmount > 0): ?>
                    <li><span class="qc-checklist__dot is-pending"></span>Challan variance of ₹<?= number_format($unallocatedAmount, 0) ?> remains unallocated<strong>Attention</strong></li>
                  <?php endif; ?>
                  <?php if ($unmatchedCount > 0): ?>
                    <li><span class="qc-checklist__dot is-pending"></span><?= $unmatchedCount ?> deductee row<?= $unmatchedCount > 1 ? 's' : '' ?> await challan reference confirmation<strong>Review</strong></li>
                  <?php endif; ?>
                  <?php if ($matchedCount > 0): ?>
                    <li><span class="qc-checklist__dot is-received"></span><?= $matchedCount ?> challan group<?= $matchedCount > 1 ? 's' : '' ?> are matched<strong>Complete</strong></li>
                  <?php endif; ?>
                  <?php if ($unallocatedAmount === 0 && $unmatchedCount === 0 && $matchedCount === 0): ?>
                    <div class="qc-empty-inline">No reconciliation mismatches found.</div>
                  <?php endif; ?>
                </ul>
                <div class="qc-action-row" style="margin-top: 16px;">
                  <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="run_reconciliation">
                    <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                    <button class="btn btn-primary" type="submit">Run Reconciliation</button>
                  </form>
                </div>
              </article>
            </div>

          <?php elseif ($benchTab === 'treatment'): ?>
            <div class="qc-grid qc-grid--treatment">
              <article class="qc-panel">
                <h3>Treatment Suggestions</h3>
                <div class="qc-issue-stack">
                  <?php if ($openIssues === []): ?>
                    <div class="qc-empty-inline">No treatment items are pending.</div>
                  <?php else: ?>
                    <?php foreach (array_slice($openIssues, 0, 4) as $entry): ?>
                      <?php $record = $entry['record']; $issue = $entry['issue']; ?>
                      <article class="qc-issue">
                        <span class="qc-signal-chip <?= ($entry['tone'] ?? '') === 'critical' ? 'is-critical' : 'is-warning' ?>"><?= etds_qc_h((string) $entry['severity_label']) ?></span>
                        <h4><?= etds_qc_h((string) $issue['message']) ?></h4>
                        <p><?= etds_qc_h((string) ($issue['suggested_correction'] ?? 'Review manually.')) ?></p>
                        <div class="qc-action-row">
                          <form class="etds-inline-form" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="issue_status">
                            <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                            <input type="hidden" name="record_id" value="<?= etds_qc_h((string) ($record['record_id'] ?? '')) ?>">
                            <input type="hidden" name="issue_id" value="<?= etds_qc_h((string) ($issue['issue_id'] ?? '')) ?>">
                            <input type="hidden" name="issue_status" value="accepted">
                            <button class="btn btn-outline btn-sm" type="submit">Accept Suggestion</button>
                          </form>
                          <form class="etds-inline-form" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="issue_status">
                            <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                            <input type="hidden" name="record_id" value="<?= etds_qc_h((string) ($record['record_id'] ?? '')) ?>">
                            <input type="hidden" name="issue_id" value="<?= etds_qc_h((string) ($issue['issue_id'] ?? '')) ?>">
                            <input type="hidden" name="issue_status" value="ignored">
                            <button class="btn btn-outline btn-sm" type="submit">Ignore</button>
                          </form>
                          <form class="etds-inline-form" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="issue_status">
                            <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                            <input type="hidden" name="record_id" value="<?= etds_qc_h((string) ($record['record_id'] ?? '')) ?>">
                            <input type="hidden" name="issue_id" value="<?= etds_qc_h((string) ($issue['issue_id'] ?? '')) ?>">
                            <input type="hidden" name="issue_status" value="resolved">
                            <button class="btn btn-primary btn-sm" type="submit">Mark Resolved</button>
                          </form>
                        </div>
                      </article>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </article>

              <article class="qc-panel">
                <h3>Doctor's Prescription</h3>
                <ul class="qc-prescription">
                  <?php if ($unmatchedCount > 0): ?>
                    <li>Map challan references for <?= $unmatchedCount ?> deductee record<?= $unmatchedCount > 1 ? 's' : '' ?></li>
                  <?php endif; ?>
                  <?php if ($challanRows !== [] && $unallocatedAmount > 0): ?>
                    <li>Review the challan register before reconciliation</li>
                  <?php endif; ?>
                  <?php if ($openIssues !== []): ?>
                    <li>Re-run readiness after treatment is complete</li>
                  <?php endif; ?>
                  <?php if ($unmatchedCount === 0 && $unallocatedAmount === 0 && $openIssues === []): ?>
                    <li>All items are treated. Proceed to certification.</li>
                  <?php endif; ?>
                </ul>
                <div class="qc-detail-list">
                  <div><span>Recommended Sequence</span><strong>Intake -> Diagnosis -> Treatment</strong></div>
                  <div><span>Estimated Resolution Time</span><strong><?= etds_qc_h($estimatedResolution) ?></strong></div>
                  <div><span>Expected Score Improvement</span><strong><?= $quality ?>% -> <?= $scoreImprovement ?>%</strong></div>
                </div>
                <div class="qc-intelligence-card">
                  <span class="qc-kicker">e-TDS Doctor Intelligence</span>
                  <h4>Coming Soon</h4>
                  <p>Reserved for guided intelligence and advanced treatment sequencing.</p>
                </div>
              </article>
            </div>

          <?php else: ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $quality ?>%</strong><span>Data Health Score</span></article>
              <article class="qc-metric-card"><strong><?= $financialHealthScore ?>%</strong><span>Financial Health Score</span></article>
              <article class="qc-metric-card"><strong><?= $openIssues === [] ? 'Complete' : 'In Progress' ?></strong><span>Treatment Completion</span></article>
              <article class="qc-metric-card"><strong><?= $readiness ? 'Ready' : 'Pending' ?></strong><span>Certification Readiness</span></article>
              <article class="qc-metric-card"><strong><?= etds_qc_h($doctorCertificationLabel) ?></strong><span>Doctor Certification Status</span></article>
            </div>
            <section class="qc-banner <?= $readiness ? 'is-good' : 'is-alert' ?>">
              <div>
                <span class="qc-kicker">Readiness</span>
                <h3><?= etds_qc_h($processingResultLabel) ?></h3>
                <p><?= etds_qc_h($exportResultLabel) ?><?= $readiness ? '' : ' - Resolve the remaining blocked items before export.' ?></p>
              </div>
              <?php if ($readiness): ?>
                <div class="qc-banner__actions">
                  <a class="btn btn-light" href="<?= etds_qc_h($buildUrl(['ws' => 'excel', 'view' => 'session'])) ?>">Proceed to Return Preparation Centre</a>
                </div>
              <?php endif; ?>
            </section>
          <?php endif; ?>
        </section>

      <?php elseif ($workspace === 'excel' && $activeSession): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Return Preparation Centre</span>
              <h2>Output-only workspace for certified file preparation</h2>
              <p>Review certification, blocked issues, generated files, and the output readiness state.</p>
            </div>
          </div>

          <section class="qc-banner <?= $readiness ? 'is-good' : 'is-alert' ?>">
            <div>
              <span class="qc-kicker">Output Readiness Banner</span>
              <h3><?= etds_qc_h($exportResultLabel) ?></h3>
              <p><?= $readiness ? 'The case is fit for processing.' : 'One blocked issue still prevents final clean output.' ?></p>
            </div>
          </section>

          <div class="qc-grid qc-grid--two">
            <article class="qc-panel">
              <h3>e-TDS Doctor Certification</h3>
              <div class="qc-detail-list">
                <div><span>Data Health Score</span><strong><?= $quality ?>%</strong></div>
                <div><span>Financial Health Score</span><strong><?= $financialHealthScore ?>%</strong></div>
                <div><span>Doctor Certification Date</span><strong><?= etds_qc_h($doctorCertificationDate) ?></strong></div>
                <div><span>Certified By</span><strong><?= etds_qc_h($certifiedBy) ?></strong></div>
              </div>
              <div class="qc-cert-status <?= $readiness ? 'is-good' : 'is-alert' ?>"><?= $readiness ? 'FIT FOR PROCESSING' : 'NOT READY' ?></div>
            </article>

            <article class="qc-panel">
              <h3>Case Health Report</h3>
              <div class="qc-signal-list">
                <div><span>Generated Files</span><strong><?= count($exportFiles) ?></strong></div>
                <div><span>Blocked Issues</span><strong><?= $readiness ? 0 : max($healthIssueCount, 1) ?></strong></div>
                <div><span>Available Outputs</span><strong><?= $readiness ? '4' : max(0, count($exportFiles)) ?></strong></div>
              </div>
            </article>
          </div>

          <div class="qc-output-grid">
            <?php foreach ([
              'Working Excel' => ['copy' => 'Operational working pack for internal review.', 'state' => 'Ready'],
              'Correction Excel' => ['copy' => 'Correction pack for treatment follow-up.', 'state' => 'Ready'],
              'Exception Report' => ['copy' => 'Doctor findings summary for open items.', 'state' => 'Ready'],
              'Final Clean Excel' => ['copy' => 'Certified export for downstream processing.', 'state' => $readiness ? 'Ready' : 'Blocked'],
            ] as $title => $meta): ?>
              <article class="qc-output-card">
                <h4><?= etds_qc_h($title) ?></h4>
                <p><?= etds_qc_h($meta['copy']) ?></p>
                <span class="qc-signal-chip <?= $meta['state'] === 'Blocked' ? 'is-critical' : 'is-good' ?>"><?= etds_qc_h($meta['state']) ?></span>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="qc-grid qc-grid--two">
            <article class="qc-panel">
              <h3>Download Centre</h3>
              <div class="qc-action-row">
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="export_xlsx">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <button class="btn btn-primary" type="submit">Generate Final Clean Excel</button>
                </form>
              </div>
            </article>
            <article class="qc-panel">
              <h3>Generated Files</h3>
              <?php if (empty($exportFiles)): ?>
                <div class="qc-empty-inline">No generated files available yet.</div>
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
              <?php if (!$readiness): ?>
                <div class="qc-note">Blocked Issues: Challan variance of ₹<?= number_format($unallocatedAmount, 0) ?> remains unresolved.</div>
              <?php endif; ?>
            </article>
          </div>
        </section>
      <?php endif; ?>
    </main>
  </div>
</div>

