<?php
declare(strict_types=1);

$requestedWorkspace = strtolower((string) ($_GET['ws'] ?? 'dashboard'));
$dataTab = strtolower((string) ($_GET['dc'] ?? 'overview'));
$doctorTab = strtolower((string) ($_GET['db'] ?? 'diagnosis'));
$reportTab = strtolower((string) ($_GET['rc'] ?? 'qcreports'));
$spreadsheetSheet = strtolower((string) ($_GET['sheet'] ?? 'deductees'));

if ($requestedWorkspace === 'intake') {
  $requestedWorkspace = 'data';
  $dataTab = $dataTab === 'overview' ? 'upload' : $dataTab;
}
if ($requestedWorkspace === 'extraction') {
  $requestedWorkspace = 'data';
  $dataTab = $dataTab === 'overview' ? 'extraction' : $dataTab;
}
if ($requestedWorkspace === 'bench') {
  $requestedWorkspace = 'data';
  $dataTab = 'bench';
  $doctorTab = strtolower((string) ($_GET['tab'] ?? $doctorTab));
}
if ($requestedWorkspace === 'excel') {
  $requestedWorkspace = 'data';
  $dataTab = 'output';
}

$workspace = in_array($requestedWorkspace, ['dashboard', 'data', 'returns', 'reports'], true)
  ? $requestedWorkspace
  : 'dashboard';

$validDataTabs = ['overview', 'upload', 'extraction', 'spreadsheet', 'validation', 'reconciliation', 'bench', 'output'];
if (!in_array($dataTab, $validDataTabs, true)) {
  $dataTab = 'overview';
}

$validDoctorTabs = ['diagnosis', 'reconciliation', 'treatment', 'readiness', 'certification'];
if (!in_array($doctorTab, $validDoctorTabs, true)) {
  $doctorTab = 'diagnosis';
}

$validReportTabs = ['qcreports', 'exceptions', 'analytics', 'audit', 'system', 'admin'];
if (!in_array($reportTab, $validReportTabs, true)) {
  $reportTab = 'qcreports';
}

$validSpreadsheetSheets = ['deductor', 'deductees', 'challans', 'salary', 'payments'];
if (!in_array($spreadsheetSheet, $validSpreadsheetSheets, true)) {
  $spreadsheetSheet = 'deductees';
}

$quality = (int) ($validatedData['summary']['quality_score'] ?? 0);
$reconScore = (int) ($reconciliation['summary']['reconciliation_score'] ?? 0);
$readiness = (bool) ($validatedData['summary']['ready_status'] ?? false);
$totalRecords = count($extractionData['records'] ?? []);
$sourceColumnsCount = count($sourceData['source_columns'] ?? []);
$documentsReceived = count($sourceData['documents'] ?? []);
$exportFiles = $exportFiles ?? [];
$latestSessionId = !empty($sessions) ? (string) ($sessions[0]['session_id'] ?? '') : '';
$documentsProcessed = (int) ($extractionData['summary']['documents_processed'] ?? 0);
$documentsPendingReview = (int) ($extractionData['summary']['documents_pending_review'] ?? 0);
$documentsFailed = (int) ($extractionData['summary']['documents_failed'] ?? 0);
$fieldsExtracted = (int) ($extractionData['summary']['fields_extracted'] ?? 0);
$fieldsMissing = (int) ($extractionData['summary']['fields_missing'] ?? 0);
$overallExtractionConfidence = (int) ($extractionData['summary']['overall_confidence'] ?? 0);
$ocrPagesProcessed = (int) ($ocrData['summary']['pages_processed'] ?? 0);

$criticalIssues = (int) ($validatedData['summary']['critical'] ?? 0);
$highIssues = (int) ($validatedData['summary']['high'] ?? 0);
$moderateIssues = (int) ($validatedData['summary']['medium'] ?? 0);
$minorIssues = (int) (($validatedData['summary']['low'] ?? 0) + ($validatedData['summary']['information'] ?? 0));
$reconSummary = is_array($reconciliation['summary'] ?? null) ? $reconciliation['summary'] : [];
$reconChallan = is_array($reconciliation['challan_reconciliation'] ?? null) ? $reconciliation['challan_reconciliation'] : ['summary' => [], 'rows' => [], 'issues' => []];
$reconDeductee = is_array($reconciliation['deductee_reconciliation'] ?? null) ? $reconciliation['deductee_reconciliation'] : ['summary' => [], 'rows' => [], 'issues' => []];
$reconSalary = is_array($reconciliation['salary_reconciliation'] ?? null) ? $reconciliation['salary_reconciliation'] : ['summary' => [], 'issues' => []];
$reconQuarter = is_array($reconciliation['quarter_reconciliation'] ?? null) ? $reconciliation['quarter_reconciliation'] : ['summary' => [], 'issues' => []];
$reconDocument = is_array($reconciliation['document_reconciliation'] ?? null) ? $reconciliation['document_reconciliation'] : ['summary' => []];
$reconIssues = is_array($reconciliation['issues'] ?? null) ? $reconciliation['issues'] : [];
$resolvedIssuesCount = 0;
$openIssues = [];
$issueLabelMap = [];

foreach (($validatedData['findings'] ?? []) as $finding) {
  if (!is_array($finding)) {
    continue;
  }
  $issueLabelMap[(string) ($finding['rule_id'] ?? '')] = (string) ($finding['rule_name'] ?? 'Rule Finding');
  if ((string) ($finding['status'] ?? 'open') === 'open') {
    $openIssues[] = $finding;
    continue;
  }
  $resolvedIssuesCount++;
}

$healthIssueCount = count($openIssues);
$rowsFlagged = $documentsPendingReview + $documentsFailed;

$challanRows = is_array($challans['challans'] ?? null) ? $challans['challans'] : [];
$matchedCount = (int) ($reconChallan['summary']['matched'] ?? 0);
$partialCount = (int) ($reconChallan['summary']['partially_matched'] ?? 0);
$unmatchedCount = (int) ($reconChallan['summary']['unmatched'] ?? 0);
$matchedAmount = (float) ($reconChallan['summary']['allocated_total'] ?? 0);
$partialAmount = (float) ($reconChallan['summary']['unused_total'] ?? 0);
$unmatchedAmount = max(0.0, abs((float) ($reconSummary['difference'] ?? 0)));

$issueTypeCounts = [];
$issueTypeTones = [];
$severityToneMap = ['Critical' => 'critical', 'High' => 'critical', 'Medium' => 'warning', 'Low' => 'warning', 'Information' => 'good'];
$severityBadgeMap = ['Critical' => 'is-critical', 'High' => 'is-critical', 'Medium' => 'is-warning', 'Low' => 'is-warning', 'Information' => 'is-good'];
$doctorFindingChips = [];
foreach ($openIssues as $finding) {
  $severity = (string) ($finding['severity'] ?? 'Information');
  $issueTypeCounts[$severity] = ($issueTypeCounts[$severity] ?? 0) + 1;
  $issueTypeTones[$severity] = $severityToneMap[$severity] ?? 'warning';
}
foreach ($issueTypeCounts as $severity => $count) {
  $doctorFindingChips[] = ['label' => $severity . ': ' . $count, 'tone' => $issueTypeTones[$severity] ?? 'warning'];
}
if ($doctorFindingChips === []) {
  $doctorFindingChips[] = ['label' => 'Diagnosis Complete', 'tone' => 'good'];
}

$doctorSummary = is_array($doctorData['summary'] ?? null) ? $doctorData['summary'] : [];
$doctorDiagnoses = is_array($doctorData['diagnosis'] ?? null) ? $doctorData['diagnosis'] : [];
$doctorPrescriptions = is_array($doctorData['prescription'] ?? null) ? $doctorData['prescription'] : [];
$doctorScores = is_array($doctorData['health_scores'] ?? null) ? $doctorData['health_scores'] : [];
$doctorReadiness = is_array($doctorData['readiness'] ?? null) ? $doctorData['readiness'] : [];
$doctorTopDiagnosis = (string) ($doctorSummary['top_diagnosis'] ?? 'Diagnosis Pending');
$doctorTopPriority = (string) ($doctorSummary['top_priority'] ?? 'Information');
$doctorExpectedImprovement = (string) ($doctorSummary['expected_improvement'] ?? ((string) ($doctorScores['overall_data_health_score'] ?? 0) . ' -> ' . (string) ($doctorScores['overall_data_health_score'] ?? 0)));
$doctorEstimatedMinutes = (int) ($doctorSummary['estimated_time_minutes'] ?? 0);
$doctorReadinessStatus = (string) ($doctorReadiness['status'] ?? ($doctorSummary['readiness'] ?? 'Not Ready'));
$doctorReadinessReason = (string) ($doctorReadiness['reason'] ?? 'Doctor intelligence is waiting for validation findings.');
$doctorPrescriptionLead = $doctorPrescriptions[0] ?? null;
$doctorDiagnosisLead = $doctorDiagnoses[0] ?? null;
$doctorGenerated = !empty($doctorSummary['last_generated_on']);
$spreadsheetSheets = is_array($spreadsheetWorkspace ?? null) ? $spreadsheetWorkspace : [];
$activeSheetPayload = $spreadsheetSheets[$spreadsheetSheet] ?? ['meta' => ['label' => ucfirst($spreadsheetSheet), 'id_field' => 'record_id', 'fields' => []], 'rows' => []];
$activeSheetMeta = is_array($activeSheetPayload['meta'] ?? null) ? $activeSheetPayload['meta'] : ['label' => ucfirst($spreadsheetSheet), 'id_field' => 'record_id', 'fields' => []];
$activeSheetRows = is_array($activeSheetPayload['rows'] ?? null) ? $activeSheetPayload['rows'] : [];
$activeSheetIdField = (string) ($activeSheetMeta['id_field'] ?? 'record_id');
$correctionHistory = is_array($correctionsData['history'] ?? null) ? $correctionsData['history'] : [];

$primaryIssueType = array_key_first($issueTypeCounts);
$primaryIssueLabel = $primaryIssueType !== null
  ? ($issueLabelMap[$primaryIssueType] ?? ucwords(str_replace('_', ' ', $primaryIssueType)))
  : 'Extraction Review';

$documentChecklist = [
  ['label' => 'Salary Register', 'status' => $documentsReceived > 0 ? 'received' : 'pending'],
  ['label' => 'Deductor Master', 'status' => $documentsReceived > 1 ? 'received' : 'pending'],
  ['label' => 'Challan Register', 'status' => $documentsReceived > 2 ? 'received' : 'pending'],
  ['label' => 'Working Spreadsheet', 'status' => $documentsReceived > 3 ? 'received' : 'pending'],
];
$documentsPending = count(array_filter($documentChecklist, static fn(array $item): bool => $item['status'] === 'pending'));

$openCases = max(0, (int) ($dashboardCounts['open_cases'] ?? $counts['sessions']));
$casesReady = $counts['ready'];
$casesBlocked = max(0, $counts['validation'] + $counts['reconciliation']);
$notifications = [];
if (!$activeSession) {
  $notifications[] = 'Create a case to begin the Version 1 QC workflow.';
}
if ($documentsPending > 0 && $activeSession) {
  $notifications[] = $documentsPending . ' source document group' . ($documentsPending > 1 ? 's are' : ' is') . ' still pending.';
}
if ($activeSession && !empty($activeSession['is_favourite'])) {
  $notifications[] = 'This case is marked as a favourite for quick access.';
}
if ($activeSession && in_array((string) ($activeSession['status'] ?? ''), ['documents_received', 'extraction_running'], true)) {
  $notifications[] = 'The case is currently in the intake foundation phase.';
}
if (($dashboardCounts['pending_validation'] ?? 0) > 0) {
  $notifications[] = (int) $dashboardCounts['pending_validation'] . ' case(s) are waiting for future validation enablement.';
}
if ($notifications === []) {
  $notifications[] = 'The active case is stable and ready for the next workflow step.';
}

$activityTimeline = [];
$timelineSteps = [
  ['label' => 'Case Created', 'done' => $activeSession !== null],
  ['label' => 'Upload Reviewed', 'done' => $documentsReceived > 0],
  ['label' => 'AI Extraction Completed', 'done' => $totalRecords > 0],
  ['label' => 'Validation Completed', 'done' => $totalRecords > 0],
  ['label' => 'Doctor Treatment Reviewed', 'done' => $resolvedIssuesCount > 0 || $openIssues === []],
  ['label' => 'QC Certification', 'done' => $readiness],
];
$timelineMinute = 12;
foreach ($timelineSteps as $index => $step) {
  $hour = 9 + intdiv($timelineMinute + ($index * 7), 60);
  $minute = ($timelineMinute + ($index * 7)) % 60;
  $activityTimeline[] = [
    'time' => str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $minute, 2, '0', STR_PAD_LEFT) . ' AM',
    'label' => $step['label'],
    'tone' => $step['done'] ? 'good' : 'warning',
  ];
}
if (!empty($auditTrail ?? [])) {
  $activityTimeline = $auditTrail;
}

$workflowStages = [
  ['key' => 'intake', 'label' => 'Document Intake'],
  ['key' => 'extraction', 'label' => 'AI Extraction'],
  ['key' => 'spreadsheet', 'label' => 'Spreadsheet Review'],
  ['key' => 'validation', 'label' => 'Validation'],
  ['key' => 'reconciliation', 'label' => 'Reconciliation'],
  ['key' => 'certification', 'label' => 'QC Certification'],
  ['key' => 'output', 'label' => 'Clean Output'],
];

$currentStage = 'intake';
if ($documentsReceived > 0) {
  $currentStage = 'extraction';
}
if ($sourceColumnsCount > 0) {
  $currentStage = 'spreadsheet';
}
if ($totalRecords > 0) {
  $currentStage = 'validation';
}
if ($challanRows !== []) {
  $currentStage = 'reconciliation';
}
if ($healthIssueCount === 0 && $totalRecords > 0) {
  $currentStage = 'certification';
}
if ($readiness) {
  $currentStage = 'output';
}
if ($workspace === 'data') {
  $currentStage = match ($dataTab) {
    'overview', 'upload' => 'intake',
    'extraction' => 'extraction',
    'spreadsheet' => 'spreadsheet',
    'validation' => 'validation',
    'reconciliation' => 'reconciliation',
    'bench' => match ($doctorTab) {
      'diagnosis' => 'validation',
      'reconciliation' => 'reconciliation',
      'treatment' => 'certification',
      'readiness', 'certification' => 'output',
      default => 'validation',
    },
    'output' => 'output',
    default => $currentStage,
  };
}

$stageKeys = array_column($workflowStages, 'key');
$currentStageIndex = array_search($currentStage, $stageKeys, true);
$currentStageIndex = $currentStageIndex === false ? 0 : $currentStageIndex;

$financialHealthScore = max(0, min(100, $reconScore > 0 ? $reconScore : ($readiness ? 100 : 0)));
$allocationVariance = (float) ($reconSummary['difference'] ?? 0);
$unallocatedAmount = (float) ($reconSummary['balance'] ?? 0);
$challanAvailableTotal = (float) ($reconChallan['summary']['available_total'] ?? 0);
$challanAllocatedTotal = (float) ($reconChallan['summary']['allocated_total'] ?? 0);
$deducteeMatchedCount = (int) ($reconDeductee['summary']['matched'] ?? 0);
$deducteePartialCount = (int) ($reconDeductee['summary']['partially_matched'] ?? 0);
$deducteeUnmatchedCount = (int) ($reconDeductee['summary']['unmatched'] ?? 0);
$salaryVariance = (float) ($reconSalary['summary']['variance'] ?? 0);
$quarterConsistency = (string) ($reconQuarter['summary']['cross_quarter_consistency'] ?? 'review_required');
$documentReadyRecords = (int) ($reconDocument['summary']['ready_records'] ?? 0);
$reconTopIssue = $reconIssues[0] ?? null;
$reconEstimatedImprovement = $reconTopIssue !== null
  ? min(100, $financialHealthScore + max(4, min(18, count($reconIssues) * 3)))
  : $financialHealthScore;
$topPriorityIssue = $doctorTopDiagnosis !== 'Diagnosis Pending'
  ? $doctorTopDiagnosis
  : ($healthIssueCount > 0 ? $primaryIssueLabel . ' requires treatment attention' : ($unallocatedAmount > 0 ? 'Reconciliation variance requires review' : 'QC certification readiness review is next'));
$estimatedResolution = $doctorEstimatedMinutes > 0
  ? $doctorEstimatedMinutes . ' Minutes'
  : ($healthIssueCount > 0 ? max(4, min(12, $healthIssueCount)) . ' Minutes' : ($unallocatedAmount > 0 ? '8 Minutes' : '2 Minutes'));
$scoreImprovement = (int) preg_replace('/^.*->\s*/', '', $doctorExpectedImprovement) ?: ($quality > 0 ? min(100, $quality + 12) : 94);
$doctorCertificationDate = $doctorReadinessStatus === 'Ready for QC Certification' ? date('d M Y') : 'Pending';
$certifiedBy = $readiness ? 'e-TDS Doctor' : (string) ($user['name'] ?? (string) ($user['email'] ?? 'Operator'));
$doctorCertificationLabel = $doctorReadinessStatus === 'Ready for QC Certification' ? 'QC Certified' : 'QC Review Pending';
$processingResultLabel = $doctorReadinessStatus === 'Ready for QC Certification' ? 'Fit For Processing' : ($doctorReadinessStatus === 'Ready After Corrections' ? 'Ready After Corrections' : 'Not Fit For Processing');
$exportResultLabel = $doctorReadinessStatus === 'Ready for QC Certification' ? 'Ready For Clean Output' : 'Not Ready';
$returnCentreRoadmap = [
  'Return Preparation',
  'Correction Returns',
  'Government Utility Export',
  'Filing Centre',
  'Acknowledgements',
  'Return History',
  'Compliance Status',
];

$statusKey = (string) ($activeSession['status'] ?? 'draft');
$statusLabel = (string) ($activeSession['status_label'] ?? $caseStatusLabel ?? 'Draft');

if (!$activeSession) {
  $caseStatusLabel = 'Awaiting Case Creation';
  $nextActionTitle = 'Create a new QC case';
  $nextActionText = 'Start in Case Dashboard, assign the client, and move into Data Centre.';
  $nextActionLabel = 'Create New Case';
} elseif ($statusKey === 'draft') {
  $caseStatusLabel = 'Awaiting Intake';
  $nextActionTitle = 'Open Upload Centre';
  $nextActionText = 'Collect the source bundle and complete the intake register.';
  $nextActionLabel = 'Open Data Centre';
} elseif (in_array($statusKey, ['documents_received', 'extraction_running'], true)) {
  $caseStatusLabel = $statusLabel;
  $nextActionTitle = 'Review the intake register';
  $nextActionText = 'Confirm documents, duplicates, versions, and source readiness for later extraction phases.';
  $nextActionLabel = 'Review Intake';
} elseif (in_array($statusKey, ['validation_running', 'reconciliation_pending', 'qc_in_progress'], true)) {
  $caseStatusLabel = $statusLabel;
  $nextActionTitle = 'Open Doctor\'s Bench';
  $nextActionText = 'Review the latest Doctor findings, resolve priority issues, and confirm validation readiness.';
  $nextActionLabel = 'Open Diagnosis';
} elseif ($statusKey === 'qc_completed') {
  $caseStatusLabel = 'QC Completed';
  $nextActionTitle = 'Open Report Centre';
  $nextActionText = 'Download case, document, upload, and audit reports.';
  $nextActionLabel = 'Open Reports';
} elseif ($statusKey === 'ready_for_return_preparation') {
  $caseStatusLabel = 'Ready for Return Preparation';
  $nextActionTitle = 'Open Return Centre roadmap';
  $nextActionText = 'The case is prepared for the future return workflow in Version 2.';
  $nextActionLabel = 'View Roadmap';
} else {
  $caseStatusLabel = $statusLabel;
  $nextActionTitle = 'Review current case';
  $nextActionText = 'Use the dashboard and report centre to monitor case readiness.';
  $nextActionLabel = 'Review Case';
}

$buildUrl = static function (array $params = []) use ($sessionId, $workspace, $dataTab, $doctorTab, $reportTab, $spreadsheetSheet): string {
  $resolvedWorkspace = (string) ($params['ws'] ?? $workspace);
  $resolvedSessionId = array_key_exists('session', $params) ? (string) $params['session'] : $sessionId;
  $resolvedView = (string) ($params['view'] ?? ($resolvedSessionId !== '' ? 'session' : 'dashboard'));
  $resolvedDataTab = (string) ($params['dc'] ?? $dataTab);
  $resolvedDoctorTab = (string) ($params['db'] ?? $doctorTab);
  $resolvedReportTab = (string) ($params['rc'] ?? $reportTab);
  $resolvedSheet = (string) ($params['sheet'] ?? $spreadsheetSheet);
  $query = ['ws' => $resolvedWorkspace];
  if ($resolvedView !== 'dashboard') {
    $query['view'] = $resolvedView;
  }
  if ($resolvedSessionId !== '') {
    $query['session'] = $resolvedSessionId;
  }
  if ($resolvedWorkspace === 'data') {
    $query['dc'] = $resolvedDataTab;
    if ($resolvedDataTab === 'spreadsheet') {
      $query['sheet'] = $resolvedSheet;
    }
    if ($resolvedDataTab === 'bench') {
      $query['db'] = $resolvedDoctorTab;
    }
  }
  if ($resolvedWorkspace === 'reports') {
    $query['rc'] = $resolvedReportTab;
  }
  return site_href('/fintech/etds-qc/?' . http_build_query($query));
};

$workspaceItems = [
  ['key' => 'dashboard', 'label' => 'Case Dashboard', 'status' => 'ready'],
  ['key' => 'data', 'label' => 'Data Centre', 'status' => $healthIssueCount > 0 ? 'attention' : ($documentsReceived > 0 ? 'ready' : 'active')],
  ['key' => 'returns', 'label' => 'Return Centre', 'status' => 'blocked'],
  ['key' => 'reports', 'label' => 'Report Centre', 'status' => 'ready'],
];

$dataTabs = [
  'overview' => 'Overview',
  'upload' => 'Upload',
  'extraction' => 'Extraction',
  'spreadsheet' => 'Spreadsheet',
  'validation' => 'Validation',
  'reconciliation' => 'Reconciliation',
  'bench' => 'Doctor\'s Bench',
  'output' => 'QC Output',
];
$reportTabs = [
  'qcreports' => 'QC Reports',
  'exceptions' => 'Exception Reports',
  'analytics' => 'Analytics',
  'audit' => 'Audit Logs',
  'system' => 'System Reports',
  'admin' => 'Administration',
];

?>
<div class="qc-app" data-workspace="<?= etds_qc_h($workspace) ?>">
  <aside class="qc-sidebar" id="qc-sidebar">
    <div class="qc-sidebar__brand">
      <button class="qc-sidebar__close" type="button" data-sidebar-close aria-label="Close navigation">×</button>
      <div class="qc-sidebar__mark">eT</div>
      <div>
        <p class="qc-sidebar__eta">E Tax Advisors</p>
        <h1 class="qc-sidebar__title">e-TDSDoc</h1>
        <p class="qc-sidebar__tag">Powered by e-TDS Doctor</p>
      </div>
    </div>

    <nav class="qc-sidebar__nav" aria-label="Primary application navigation">
      <?php foreach ($workspaceItems as $item): ?>
        <a class="qc-sidebar__link<?= $workspace === $item['key'] ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => $item['key'], 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
          <span class="qc-sidebar__icon"><?= etds_qc_nav_icon($item['key'] === 'data' ? 'extraction' : ($item['key'] === 'returns' ? 'excel' : ($item['key'] === 'reports' ? 'dashboard' : 'dashboard'))) ?></span>
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
        <button class="qc-menu-btn" type="button" data-sidebar-toggle aria-label="Open navigation">
          <span></span><span></span><span></span>
        </button>
        <a class="qc-topbar__brand" href="<?= etds_qc_h($buildUrl(['ws' => 'dashboard', 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
          <span class="qc-topbar__brand-mark">eT</span>
          <span class="qc-topbar__brand-copy">
            <strong>e-TDSDoc</strong>
            <small>Diagnose. Reconcile. Prepare.</small>
          </span>
        </a>
        <div class="qc-topbar__tagline">
          <span>e-TDS Doctor</span>
          <small>Your Intelligent TDS Data Health Checker</small>
        </div>
      </div>

      <div class="qc-topbar__center">
        <span class="qc-chip"><label>Client</label><strong><?= etds_qc_h($activeSession['client_name'] ?? 'No active case') ?></strong></span>
        <span class="qc-chip"><label>TAN</label><strong><?= etds_qc_h($activeSession['tan'] ?? 'Pending') ?></strong></span>
        <span class="qc-chip"><label>FY</label><strong><?= etds_qc_h($activeSession['financial_year'] ?? 'Pending') ?></strong></span>
        <span class="qc-chip"><label>Quarter</label><strong><?= etds_qc_h($activeSession['quarter'] ?? 'Pending') ?></strong></span>
        <span class="qc-chip qc-chip--selector"><label>Current Case</label><strong><?= etds_qc_h($activeSession['session_id'] ?? ($latestSessionId !== '' ? $latestSessionId : 'Select')) ?></strong></span>
      </div>

      <div class="qc-topbar__right">
        <span class="qc-status-chip is-online">e-TDS Doctor: Online</span>
        <span class="qc-status-chip is-online">Validation Layer: Online</span>
        <span class="qc-user-chip"><?= etds_qc_h((string) ($user['name'] ?? 'Operator')) ?></span>
        <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="logout">
          <button class="btn btn-outline-primary btn-sm" type="submit" data-confirm="Log out of e-TDSDoc now?">Logout</button>
        </form>
      </div>
    </header>

    <main class="qc-main">
      <?php etds_qc_render_flash($flash); ?>

      <section class="qc-tracker">
        <?php foreach ($workflowStages as $index => $stage): ?>
          <?php $state = $index < $currentStageIndex ? 'done' : ($index === $currentStageIndex ? 'current' : 'pending'); ?>
          <div class="qc-tracker__step is-<?= etds_qc_h($state) ?>">
            <span class="qc-tracker__dot"></span>
            <span class="qc-tracker__label"><?= etds_qc_h($stage['label']) ?></span>
          </div>
        <?php endforeach; ?>
      </section>

      <?php if ($workspace === 'dashboard'): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Case Dashboard</span>
              <h2>Enterprise case control for Version 1 data validation workflow</h2>
              <p>Track assignments, current case status, notifications, and progress before entering the operational data workspace.</p>
            </div>
            <div class="qc-head-summary">
              <span class="qc-status-chip <?= $readiness ? 'is-online' : 'is-alert' ?>">Current Case: <?= etds_qc_h($caseStatusLabel) ?></span>
            </div>
          </div>

          <div class="qc-metrics qc-metrics--five">
            <article class="qc-metric-card"><strong><?= (int) ($dashboardCounts['open_cases'] ?? $openCases) ?></strong><span>Open Cases</span></article>
            <article class="qc-metric-card"><strong><?= (int) ($dashboardCounts['qc_completed'] ?? 0) ?></strong><span>QC Completed</span></article>
            <article class="qc-metric-card"><strong><?= (int) ($dashboardCounts['pending_validation'] ?? 0) ?></strong><span>Pending Validation</span></article>
            <article class="qc-metric-card"><strong><?= (int) ($dashboardCounts['pending_reconciliation'] ?? 0) ?></strong><span>Pending Reconciliation</span></article>
            <article class="qc-metric-card"><strong><?= (int) ($dashboardCounts['ready_for_return_preparation'] ?? 0) ?></strong><span>Ready for Return Preparation</span></article>
          </div>

          <section class="qc-banner">
            <div>
              <span class="qc-kicker">Current Assignment</span>
              <h3><?= etds_qc_h($nextActionTitle) ?></h3>
              <p><?= etds_qc_h($nextActionText) ?></p>
            </div>
            <div class="qc-banner__score">
              <strong><?= $readiness ? 'V1' : 'QC' ?></strong>
              <span>Version Focus</span>
            </div>
          </section>

          <div class="qc-grid qc-grid--dashboard">
            <article class="qc-panel">
              <h3>Current Case</h3>
              <div class="qc-detail-list">
                <div><span>Client Name</span><strong><?= etds_qc_h($activeSession['client_name'] ?? 'Not selected') ?></strong></div>
                <div><span>Client Code</span><strong><?= etds_qc_h($activeSession['client_code'] ?? ($activeClient['client_code'] ?? 'Pending')) ?></strong></div>
                <div><span>TAN</span><strong><?= etds_qc_h($activeSession['tan'] ?? 'Pending') ?></strong></div>
                <div><span>PAN</span><strong><?= etds_qc_h($activeSession['pan'] ?? ($activeClient['pan'] ?? 'Pending')) ?></strong></div>
                <div><span>Financial Year</span><strong><?= etds_qc_h($activeSession['financial_year'] ?? 'Pending') ?></strong></div>
                <div><span>Quarter</span><strong><?= etds_qc_h($activeSession['quarter'] ?? 'Pending') ?></strong></div>
                <div><span>Workflow Status</span><strong><?= etds_qc_h($caseStatusLabel) ?></strong></div>
              </div>
              <?php if ($activeSession): ?>
                <div class="qc-action-row" style="margin-top:12px;">
                  <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_favourite">
                    <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                    <button class="btn btn-outline" type="submit"><?= !empty($activeSession['is_favourite']) ? 'Unfavourite' : 'Favourite' ?></button>
                  </form>
                  <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="duplicate_case">
                    <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                    <button class="btn btn-outline" type="submit">Duplicate</button>
                  </form>
                  <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="close_case">
                    <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                    <button class="btn btn-outline" type="submit" data-confirm="Close this case?">Close</button>
                  </form>
                </div>
              <?php endif; ?>
            </article>

            <article class="qc-panel">
              <h3>Workflow Progress</h3>
              <div class="qc-signal-list">
                <div><span>Data Centre Phase</span><strong><?= ucfirst($currentStage) ?></strong></div>
                <div><span>Health Issues</span><strong><?= $healthIssueCount ?></strong></div>
                <div><span>QC Outputs</span><strong><?= count($exportFiles) ?></strong></div>
                <div><span>Next Action</span><strong><?= etds_qc_h($nextActionLabel) ?></strong></div>
              </div>
            </article>

            <article class="qc-panel">
              <h3>Notifications</h3>
              <ul class="qc-stage-list">
                <?php foreach ($notifications as $note): ?>
                  <li class="is-current"><?= etds_qc_h($note) ?></li>
                <?php endforeach; ?>
              </ul>
            </article>

            <article class="qc-panel">
              <h3>Quick Launch</h3>
              <ul class="qc-stage-list">
                <li class="is-done"><a href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => 'overview', 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">Open Data Centre</a></li>
                <li class="is-pending"><a href="<?= etds_qc_h($buildUrl(['ws' => 'reports', 'rc' => 'qcreports', 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">Open Report Centre</a></li>
                <li class="is-pending"><a href="<?= etds_qc_h($buildUrl(['ws' => 'returns', 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">View Version 2 Roadmap</a></li>
              </ul>
            </article>
          </div>

          <div class="qc-grid qc-grid--two">
            <article class="qc-panel">
              <h3>Recent Cases</h3>
              <form method="get" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" class="qc-form-block" style="margin-bottom:12px;">
                <input type="hidden" name="ws" value="dashboard">
                <div class="etds-fields">
                  <div class="etds-field"><label for="search">Search Cases</label><input id="search" name="search" value="<?= etds_qc_h($searchQuery ?? '') ?>" placeholder="Case no / Client / TAN / PAN"></div>
                  <div class="etds-field"><label for="financial_year_filter">Financial Year</label><select id="financial_year_filter" name="financial_year"><?php foreach (($masters['financial_years'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"<?= (($financialYearFilter ?? '') === (string) ($item['code'] ?? '')) ? ' selected' : '' ?>><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?><option value=""<?= ($financialYearFilter ?? '') === '' ? ' selected' : '' ?>>All</option></select></div>
                  <div class="etds-field"><label for="quarter_filter">Quarter</label><select id="quarter_filter" name="quarter"><?php foreach (($masters['quarters'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"<?= (($quarterFilter ?? '') === (string) ($item['code'] ?? '')) ? ' selected' : '' ?>><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?><option value=""<?= ($quarterFilter ?? '') === '' ? ' selected' : '' ?>>All</option></select></div>
                </div>
                <div class="qc-action-row" style="margin-top:10px;">
                  <button class="btn btn-primary" type="submit">Search</button>
                  <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=dashboard')) ?>">Clear</a>
                </div>
              </form>
              <?php if (empty($sessions)): ?>
                <div class="qc-empty-inline">No cases are available yet.</div>
              <?php else: ?>
                <ul class="qc-checklist">
                  <?php foreach (array_slice($sessions, 0, 5) as $row): ?>
                    <?php $rowState = $sessionStates[(string) ($row['session_id'] ?? '')] ?? etds_qc_session_state($row); ?>
                    <li>
                      <span><a href="<?= etds_qc_h($buildUrl(['ws' => 'dashboard', 'session' => (string) ($row['session_id'] ?? ''), 'view' => 'session'])) ?>"><?= etds_qc_h((string) ($row['session_id'] ?? '')) ?></a> · <?= etds_qc_h((string) ($row['client_name'] ?? '')) ?><?= !empty($row['is_favourite']) ? ' · Favourite' : '' ?></span>
                      <strong><?= etds_qc_h((string) ($rowState['label'] ?? 'Pending')) ?></strong>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </article>

            <article class="qc-panel">
              <h3>Case Timeline</h3>
              <div class="qc-timeline">
                <?php foreach ($activityTimeline as $item): ?>
                  <div class="qc-timeline__item">
                    <span class="qc-timeline__time"><?= etds_qc_h($item['time']) ?></span>
                    <span class="qc-timeline__state is-<?= etds_qc_h($item['tone']) ?>"></span>
                    <div>
                      <strong><?= etds_qc_h($item['label']) ?></strong>
                      <p>Recorded against the active case workflow.</p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </article>
          </div>
        </section>

      <?php elseif ($workspace === 'data'): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Data Centre</span>
              <h2>Version 1 flagship workspace for TDS data validation and quality control</h2>
              <p>Everything related to intake, extraction, spreadsheet review, validation, reconciliation, Doctor's Bench, and QC output lives inside this workspace.</p>
            </div>
          </div>

          <div class="qc-tabbar" role="tablist" aria-label="Data Centre tabs">
            <?php foreach ($dataTabs as $tabKey => $tabLabel): ?>
              <a class="qc-tabbar__tab<?= $dataTab === $tabKey ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => $tabKey, 'db' => $doctorTab, 'view' => $activeSession ? 'session' : 'dashboard'])) ?>"><?= etds_qc_h($tabLabel) ?></a>
            <?php endforeach; ?>
          </div>

          <?php if (!$activeSession && !in_array($dataTab, ['overview', 'upload'], true)): ?>
            <section class="qc-empty">
              <h2>No active case selected</h2>
              <p>Create a case in Case Dashboard before using the Data Centre operational modules.</p>
              <div class="qc-action-row">
                <a class="btn btn-primary" href="<?= etds_qc_h($buildUrl(['ws' => 'dashboard', 'view' => 'dashboard'])) ?>">Open Case Dashboard</a>
              </div>
            </section>
          <?php elseif ($dataTab === 'overview'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $documentsReceived ?></strong><span>Upload Centre</span></article>
              <article class="qc-metric-card"><strong><?= (int) ($sourceData['summary']['duplicate_count'] ?? 0) ?></strong><span>Duplicates Detected</span></article>
              <article class="qc-metric-card"><strong><?= count(array_filter(($sourceData['documents'] ?? []), static fn(array $document): bool => ((int) ($document['version_number'] ?? 1)) > 1 && (($document['is_removed'] ?? false) !== true))) ?></strong><span>Versioned Uploads</span></article>
              <article class="qc-metric-card"><strong><?= (int) ($activeSession['progress'] ?? 0) ?>%</strong><span>Case Progress</span></article>
              <article class="qc-metric-card"><strong><?= count($auditTrail ?? []) ?></strong><span>Audit Events</span></article>
            </div>

            <section class="qc-banner">
              <div>
                <span class="qc-kicker">Data Centre Overview</span>
                <h3><?= etds_qc_h($nextActionTitle) ?></h3>
                <p><?= etds_qc_h($nextActionText) ?></p>
              </div>
              <div class="qc-banner__score">
                <strong><?= $quality ?>%</strong>
                <span>Data Health</span>
              </div>
            </section>

            <div class="qc-grid qc-grid--dashboard">
              <article class="qc-panel">
                <h3>Upload Centre</h3>
                <div class="qc-detail-list">
                  <div><span>Documents Received</span><strong><?= $documentsReceived ?></strong></div>
                  <div><span>Pending Source Groups</span><strong><?= $documentsPending ?></strong></div>
                </div>
              </article>
              <article class="qc-panel">
                <h3>AI Extraction Centre</h3>
                <div class="qc-detail-list">
                  <div><span>Phase Status</span><strong>Reserved</strong></div>
                  <div><span>Current Focus</span><strong>Case Intake</strong></div>
                </div>
              </article>
              <article class="qc-panel">
                <h3>Validation Centre</h3>
                <div class="qc-detail-list">
                  <div><span>Phase Status</span><strong>Reserved</strong></div>
                  <div><span>Pending Cases</span><strong><?= (int) ($dashboardCounts['pending_validation'] ?? 0) ?></strong></div>
                </div>
              </article>
              <article class="qc-panel">
                <h3>QC Output Centre</h3>
                <div class="qc-detail-list">
                  <div><span>Reports Available</span><strong>4</strong></div>
                  <div><span>QC Certificate</span><strong>Later Phase</strong></div>
                </div>
              </article>
            </div>

            <div class="qc-launch-grid">
              <?php foreach ($dataTabs as $tabKey => $tabLabel): ?>
                <?php if ($tabKey === 'overview') { continue; } ?>
                <a class="qc-launch-card" href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => $tabKey, 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
                  <span class="qc-launch-card__icon"><?= etds_qc_nav_icon($tabKey === 'output' ? 'excel' : ($tabKey === 'bench' ? 'bench' : ($tabKey === 'reconciliation' ? 'dashboard' : 'extraction'))) ?></span>
                  <h4><?= etds_qc_h($tabLabel) ?></h4>
                  <p>
                    <?php
                    echo etds_qc_h(match ($tabKey) {
                      'upload' => 'Receive and classify source documents.',
                      'extraction' => 'Run AI extraction into structured rows.',
                      'spreadsheet' => 'Inspect spreadsheet-level data and masters.',
                      'validation' => 'Review quality issues and validation score.',
                      'reconciliation' => 'Match challans, deductees, and balances.',
                      'bench' => 'Operate Diagnosis, Treatment, and Certification.',
                      'output' => 'Generate Version 1 QC deliverables only.',
                      default => '',
                    });
                    ?>
                  </p>
                </a>
              <?php endforeach; ?>
            </div>

          <?php elseif ($dataTab === 'upload'): ?>
            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Upload Centre</h3>
                <p class="qc-copy">Document intake for salary files, deductor masters, challan data, and working spreadsheets.</p>
                <?php if (!$activeSession): ?>
                  <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" class="qc-form-block">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="create_session">
                    <div class="etds-fields">
                      <div class="etds-field"><label for="client_name">Client Name</label><input id="client_name" name="client_name" required></div>
                      <div class="etds-field"><label for="client_code">Client Code</label><input id="client_code" name="client_code"></div>
                      <div class="etds-field"><label for="tan">TAN</label><input id="tan" name="tan" maxlength="10" required></div>
                      <div class="etds-field"><label for="pan">PAN</label><input id="pan" name="pan" maxlength="10"></div>
                      <div class="etds-field"><label for="financial_year">Financial Year</label><select id="financial_year" name="financial_year" required><?php foreach (($masters['financial_years'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?></select></div>
                      <div class="etds-field"><label for="quarter">Quarter</label><select id="quarter" name="quarter" required><?php foreach (($masters['quarters'] ?? []) as $item): ?><option value="<?= etds_qc_h((string) ($item['code'] ?? '')) ?>"<?= ((string) ($item['code'] ?? '') === 'Q2') ? ' selected' : '' ?>><?= etds_qc_h((string) ($item['label'] ?? '')) ?></option><?php endforeach; ?></select></div>
                      <div class="etds-field"><label for="entity_type">Entity Type</label><input id="entity_type" name="entity_type" placeholder="Company / Firm / Trust"></div>
                      <div class="etds-field"><label for="return_type">Return Type</label><select id="return_type" name="return_type" required><option selected>24Q</option><option>26Q</option><option>27Q</option><option>27EQ</option></select></div>
                      <div class="etds-field"><label for="contact_person">Contact Person</label><input id="contact_person" name="contact_person"></div>
                      <div class="etds-field"><label for="mobile">Mobile</label><input id="mobile" name="mobile"></div>
                      <div class="etds-field"><label for="email">Email</label><input id="email" name="email" type="email"></div>
                      <div class="etds-field etds-field-full"><label for="address">Address</label><textarea id="address" name="address" rows="2"></textarea></div>
                      <div class="etds-field etds-field-full"><label for="remarks">Remarks</label><textarea id="remarks" name="remarks" rows="2"></textarea></div>
                    </div>
                    <div class="qc-action-row"><button class="btn btn-primary" type="submit">Create New Case</button></div>
                  </form>
                <?php else: ?>
                  <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="upload_documents">
                    <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                    <div class="etds-fields">
                      <div class="etds-field etds-field-full">
                        <label for="documents">Source Bundle</label>
                        <div class="qc-dropzone" data-dropzone>
                          <input id="documents" type="file" name="documents[]" accept=".xls,.xlsx,.csv,.txt,.pdf,.png,.jpg,.jpeg,.zip" multiple required>
                          <p>Drag and drop files here or browse to upload Excel, PDF, scanned PDF, images, and ZIP bundles.</p>
                        </div>
                        <div class="etds-progress" hidden data-upload-progress><span style="width:0%"></span></div>
                        <div class="qc-note">Duplicate detection, safe naming, version tracking, preview, and removal are captured in the document register.</div>
                      </div>
                    </div>
                    <div class="qc-action-row">
                      <button class="btn btn-primary" type="submit">Upload Files</button>
                      <a class="btn btn-outline" href="<?= etds_qc_h($buildUrl(['ws' => 'reports', 'rc' => 'qcreports', 'view' => 'session'])) ?>">Open Reports</a>
                    </div>
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
              <article class="qc-panel">
                <h3>Document Register</h3>
                <?php if (empty($sourceData['documents'])): ?>
                  <div class="qc-empty-inline">No source files are registered yet.</div>
                <?php else: ?>
                  <div class="etds-table-shell">
                    <table class="etds-table">
                      <thead><tr><th>Document ID</th><th>File</th><th>Type</th><th>Uploaded</th><th>Version</th><th>Status</th><th>Actions</th></tr></thead>
                      <tbody>
                        <?php foreach (($sourceData['documents'] ?? []) as $document): ?>
                          <?php if (($document['is_removed'] ?? false) === true) { continue; } ?>
                          <tr>
                            <td><?= etds_qc_h((string) ($document['document_id'] ?? '')) ?></td>
                            <td>
                              <strong><?= etds_qc_h((string) ($document['file_name'] ?? '')) ?></strong><br>
                              <small><?= etds_qc_h((string) ($document['original_name'] ?? '')) ?></small>
                            </td>
                            <td><?= etds_qc_h((string) ($document['document_type'] ?? strtoupper((string) ($document['extension'] ?? 'FILE')))) ?></td>
                            <td><?= etds_qc_h((string) ($document['upload_time'] ?? ($document['uploaded_on'] ?? ''))) ?></td>
                            <td>v<?= str_pad((string) ((int) ($document['version_number'] ?? 1)), 2, '0', STR_PAD_LEFT) ?><?= !empty($document['is_duplicate']) ? ' · Duplicate' : '' ?></td>
                            <td><?= etds_qc_h((string) ($document['validation_status'] ?? 'Pending')) ?></td>
                            <td>
                              <div class="qc-action-row">
                                <?php if (etds_qc_document_preview_allowed($document)): ?>
                                  <a class="btn btn-outline btn-sm" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=preview&session=' . urlencode($sessionId) . '&file=' . urlencode((string) ($document['stored_name'] ?? '')))) ?>" target="_blank" rel="noopener">Preview</a>
                                <?php endif; ?>
                                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                                  <?= csrf_field() ?>
                                  <input type="hidden" name="action" value="delete_upload">
                                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                                  <input type="hidden" name="file_id" value="<?= etds_qc_h((string) ($document['document_id'] ?? '')) ?>">
                                  <button class="btn btn-outline btn-sm" type="submit" data-confirm="Remove this document?">Remove</button>
                                </form>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </article>
            <?php endif; ?>

          <?php elseif ($dataTab === 'extraction'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $documentsReceived ?></strong><span>Source Files</span></article>
              <article class="qc-metric-card"><strong><?= $documentsProcessed ?></strong><span>Processed</span></article>
              <article class="qc-metric-card"><strong><?= $documentsPendingReview ?></strong><span>Pending Review</span></article>
              <article class="qc-metric-card"><strong><?= $documentsFailed ?></strong><span>Failed</span></article>
              <article class="qc-metric-card"><strong><?= $overallExtractionConfidence ?>%</strong><span>Confidence</span></article>
            </div>

            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>AI Extraction Centre</h3>
                <div class="qc-detail-list">
                  <div><span>Document Classification</span><strong><?= $documentsReceived > 0 ? 'Completed' : 'Pending' ?></strong></div>
                  <div><span>Structured Rows</span><strong><?= $totalRecords ?></strong></div>
                  <div><span>Fields Extracted</span><strong><?= $fieldsExtracted ?></strong></div>
                  <div><span>Fields Missing</span><strong><?= $fieldsMissing ?></strong></div>
                </div>
                <div class="qc-note">Phase 3 performs classification, OCR where required, structured extraction, field mapping, and confidence scoring only.</div>
                <?php if ($activeSession): ?>
                  <div class="qc-action-row" style="margin-top:16px;">
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="extract_validate">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <button class="btn btn-primary" type="submit">Run AI Extraction</button>
                    </form>
                    <a class="btn btn-outline" href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => 'spreadsheet', 'view' => 'session'])) ?>">Open Spreadsheet</a>
                  </div>
                <?php endif; ?>
              </article>

              <article class="qc-panel">
                <h3>Extraction Review</h3>
                <div class="qc-detail-list">
                  <div><span>Documents Processed</span><strong><?= $documentsProcessed ?></strong></div>
                  <div><span>Documents Pending Review</span><strong><?= $documentsPendingReview ?></strong></div>
                  <div><span>OCR Pages</span><strong><?= $ocrPagesProcessed ?></strong></div>
                  <div><span>Overall Extraction Confidence</span><strong><?= $overallExtractionConfidence ?>%</strong></div>
                </div>
              </article>
            </div>

            <article class="qc-panel">
              <h3>Document Classification</h3>
              <?php if (empty($sourceData['documents'])): ?>
                <div class="qc-empty-inline">No uploaded documents are available for classification yet.</div>
              <?php else: ?>
                <div class="etds-table-shell">
                  <table class="etds-table">
                    <thead><tr><th>Document</th><th>Classification</th><th>Class Confidence</th><th>OCR</th><th>Extraction</th></tr></thead>
                    <tbody>
                      <?php foreach (($sourceData['documents'] ?? []) as $document): if (($document['is_removed'] ?? false) === true) { continue; } ?>
                        <tr>
                          <td><?= etds_qc_h((string) ($document['original_name'] ?? $document['file_name'] ?? '')) ?></td>
                          <td><?= etds_qc_h((string) ($document['classification'] ?? 'Unknown Document')) ?></td>
                          <td><?= (int) ($document['classification_confidence'] ?? 0) ?>%</td>
                          <td><?= etds_qc_h((string) ($document['ocr_status'] ?? 'Pending')) ?></td>
                          <td><?= etds_qc_h((string) ($document['extraction_status'] ?? 'Pending')) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </article>

          <?php elseif ($dataTab === 'spreadsheet'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= count($activeSheetRows) ?></strong><span><?= etds_qc_h((string) ($activeSheetMeta['label'] ?? 'Rows')) ?></span></article>
              <article class="qc-metric-card"><strong><?= $healthIssueCount ?></strong><span>Open Issues</span></article>
              <article class="qc-metric-card"><strong><?= count($correctionHistory) ?></strong><span>Corrections Logged</span></article>
              <article class="qc-metric-card"><strong><?= (int) ($doctorScores['overall_data_health_score'] ?? $quality) ?>%</strong><span>Data Health Score</span></article>
              <article class="qc-metric-card"><strong><?= etds_qc_h($doctorReadinessStatus) ?></strong><span>Readiness</span></article>
            </div>

            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Spreadsheet Workspace</h3>
                <div class="qc-detail-list">
                  <div><span>Active Sheet</span><strong><?= etds_qc_h((string) ($activeSheetMeta['label'] ?? ucfirst($spreadsheetSheet))) ?></strong></div>
                  <div><span>Editable Fields</span><strong><?= count((array) ($activeSheetMeta['fields'] ?? [])) ?></strong></div>
                  <div><span>Top Priority</span><strong><?= etds_qc_h($doctorTopPriority) ?></strong></div>
                  <div><span>Expected Improvement</span><strong><?= etds_qc_h($doctorExpectedImprovement) ?></strong></div>
                </div>
                <div class="qc-note">Use inline editing, bulk update, suggestion actions, and the validation loop here without re-running extraction.</div>
              </article>

              <article class="qc-panel">
                <h3>Validation Loop</h3>
                <div class="qc-action-row">
                  <?php if ($activeSession): ?>
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="run_validation">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <input type="hidden" name="return_to" value="spreadsheet">
                      <input type="hidden" name="sheet" value="<?= etds_qc_h($spreadsheetSheet) ?>">
                      <button class="btn btn-primary" type="submit">Run Validation</button>
                    </form>
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="run_doctor">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <input type="hidden" name="return_to" value="spreadsheet">
                      <input type="hidden" name="sheet" value="<?= etds_qc_h($spreadsheetSheet) ?>">
                      <button class="btn btn-outline" type="submit">Run Doctor Analysis</button>
                    </form>
                  <?php endif; ?>
                </div>
                <div class="qc-detail-list" style="margin-top:16px;">
                  <div><span>Validation Findings</span><strong><?= $healthIssueCount ?></strong></div>
                  <div><span>Doctor Diagnosis</span><strong><?= etds_qc_h($doctorTopDiagnosis) ?></strong></div>
                  <div><span>Estimated Time</span><strong><?= $doctorEstimatedMinutes ?> Minutes</strong></div>
                  <div><span>Ready State</span><strong><?= etds_qc_h($doctorReadinessStatus) ?></strong></div>
                </div>
              </article>
            </div>

            <div class="qc-sheet-tabs">
              <?php foreach ($spreadsheetSheets as $sheetKey => $sheetPayload): ?>
                <a class="qc-sheet-tab<?= $spreadsheetSheet === $sheetKey ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => 'spreadsheet', 'sheet' => $sheetKey, 'view' => $activeSession ? 'session' : 'dashboard'])) ?>"><?= etds_qc_h((string) (($sheetPayload['meta']['label'] ?? ucfirst((string) $sheetKey)))) ?></a>
              <?php endforeach; ?>
            </div>

            <section class="qc-spreadsheet-shell" data-spreadsheet-grid data-session-id="<?= etds_qc_h($sessionId) ?>" data-sheet="<?= etds_qc_h($spreadsheetSheet) ?>" data-csrf="<?= etds_qc_h(csrf_token()) ?>">
              <div class="qc-spreadsheet-toolbar">
                <div class="qc-spreadsheet-toolbar__group">
                  <input type="search" class="qc-sheet-search" placeholder="Search rows or values" data-sheet-search>
                  <input type="search" class="qc-sheet-replace-find" placeholder="Find text" data-sheet-find>
                  <input type="text" class="qc-sheet-replace-value" placeholder="Replace with" data-sheet-replace>
                  <button type="button" class="btn btn-outline" data-sheet-replace-run>Replace Visible</button>
                </div>
                <div class="qc-spreadsheet-toolbar__group">
                  <select data-bulk-field>
                    <option value="">Bulk edit field</option>
                    <?php foreach ((array) ($activeSheetMeta['fields'] ?? []) as $field): ?>
                      <option value="<?= etds_qc_h((string) $field) ?>"><?= etds_qc_h(ucwords(str_replace('_', ' ', (string) $field))) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input type="text" placeholder="Bulk value" data-bulk-value>
                  <button type="button" class="btn btn-outline" data-bulk-apply>Apply to Selected Rows</button>
                </div>
              </div>

              <div class="qc-spreadsheet-layout">
                <article class="qc-panel qc-spreadsheet-panel">
                  <?php if ($activeSheetRows === []): ?>
                    <div class="qc-empty-inline">No rows are available for this sheet yet.</div>
                  <?php else: ?>
                    <div class="qc-sheet-table-wrap">
                      <table class="qc-sheet-table">
                        <thead>
                          <tr>
                            <th class="is-select"><input type="checkbox" data-select-all></th>
                            <th>Record</th>
                            <?php foreach ((array) ($activeSheetMeta['fields'] ?? []) as $field): ?>
                              <th data-sort-field="<?= etds_qc_h((string) $field) ?>">
                                <button type="button" class="qc-sheet-sort"><?= etds_qc_h(ucwords(str_replace('_', ' ', (string) $field))) ?></button>
                                <span class="qc-col-resize" data-col-resize></span>
                              </th>
                            <?php endforeach; ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($activeSheetRows as $row): $recordId = (string) ($row[$activeSheetIdField] ?? $row['record_id'] ?? ''); ?>
                            <tr data-record-id="<?= etds_qc_h($recordId) ?>">
                              <td class="is-select"><input type="checkbox" data-row-select value="<?= etds_qc_h($recordId) ?>"></td>
                              <td class="qc-sheet-record"><?= etds_qc_h($recordId) ?></td>
                              <?php foreach ((array) ($activeSheetMeta['fields'] ?? []) as $field): ?>
                                <?php
                                  $cellValue = (string) ($row[$field] ?? '');
                                  $cellStatus = (string) (($row['_cell_status'][$field] ?? 'valid'));
                                  $suggestion = $row['_suggestions'][$field] ?? null;
                                ?>
                                <td class="qc-sheet-cell is-<?= etds_qc_h($cellStatus) ?>" data-status="<?= etds_qc_h($cellStatus) ?>" data-field="<?= etds_qc_h((string) $field) ?>">
                                  <div class="qc-sheet-cell__editor" contenteditable="true" spellcheck="false" data-cell-editor data-record-id="<?= etds_qc_h($recordId) ?>" data-field="<?= etds_qc_h((string) $field) ?>" data-original-value="<?= etds_qc_h($cellValue) ?>"><?= etds_qc_h($cellValue) ?></div>
                                  <span class="qc-sheet-status"><?= etds_qc_h(str_replace('_', ' ', strtoupper($cellStatus))) ?></span>
                                  <?php if ($suggestion !== null): ?>
                                    <div class="qc-sheet-suggestion" data-suggested-value="<?= etds_qc_h((string) ($suggestion['value'] ?? '')) ?>" data-suggestion-reason="<?= etds_qc_h((string) ($suggestion['reason'] ?? '')) ?>">
                                      <button type="button" class="qc-sheet-pill" data-apply-suggestion data-record-id="<?= etds_qc_h($recordId) ?>" data-field="<?= etds_qc_h((string) $field) ?>" data-value="<?= etds_qc_h((string) ($suggestion['value'] ?? '')) ?>" data-reason="<?= etds_qc_h((string) ($suggestion['reason'] ?? '')) ?>">Apply Suggestion</button>
                                      <button type="button" class="qc-sheet-pill is-muted" data-ignore-suggestion data-record-id="<?= etds_qc_h($recordId) ?>" data-field="<?= etds_qc_h((string) $field) ?>">Ignore</button>
                                    </div>
                                  <?php endif; ?>
                                </td>
                              <?php endforeach; ?>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                </article>

                <aside class="qc-panel qc-spreadsheet-sidebar">
                  <h3>Doctor Suggestions</h3>
                  <?php if ($doctorPrescriptionLead === null && $healthIssueCount === 0): ?>
                    <div class="qc-empty-inline">No active Doctor prescription is blocking this sheet right now.</div>
                  <?php else: ?>
                    <div class="qc-detail-list">
                      <div><span>Top Diagnosis</span><strong><?= etds_qc_h($doctorTopDiagnosis) ?></strong></div>
                      <div><span>Prescription</span><strong><?= etds_qc_h((string) ($doctorPrescriptionLead['instruction'] ?? 'Review current Doctor findings.')) ?></strong></div>
                      <div><span>Reason</span><strong><?= etds_qc_h($doctorReadinessReason) ?></strong></div>
                      <div><span>Expected Improvement</span><strong><?= etds_qc_h($doctorExpectedImprovement) ?></strong></div>
                    </div>
                  <?php endif; ?>

                  <h3 style="margin-top:18px;">Reconciliation Watchlist</h3>
                  <?php if ($reconIssues === []): ?>
                    <div class="qc-empty-inline">No reconciliation warnings are attached to the current sheet.</div>
                  <?php else: ?>
                    <div class="qc-detail-list">
                      <div><span>Financial Health</span><strong><?= $financialHealthScore ?>%</strong></div>
                      <div><span>Open Mismatches</span><strong><?= count($reconIssues) ?></strong></div>
                      <div><span>Top Issue</span><strong><?= etds_qc_h((string) ($reconTopIssue['message'] ?? 'Review reconciliation output.')) ?></strong></div>
                      <div><span>Suggested Action</span><strong><?= etds_qc_h((string) ($reconTopIssue['suggested_action'] ?? 'Open the Reconciliation workspace for details.')) ?></strong></div>
                    </div>
                  <?php endif; ?>

                  <h3 style="margin-top:18px;">Correction Reports</h3>
                  <?php if ($activeSession): ?>
                    <div class="qc-action-row">
                      <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=correction_log')) ?>">Correction Log</a>
                      <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=field_change_report')) ?>">Field Change Report</a>
                      <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=user_activity')) ?>">User Activity</a>
                    </div>
                  <?php endif; ?>

                  <h3 style="margin-top:18px;">Recent Corrections</h3>
                  <?php if ($correctionHistory === []): ?>
                    <div class="qc-empty-inline">No spreadsheet corrections have been recorded yet.</div>
                  <?php else: ?>
                    <div class="qc-detail-list">
                      <?php foreach (array_slice(array_reverse($correctionHistory), 0, 5) as $correction): ?>
                        <div><span><?= etds_qc_h((string) ($correction['field'] ?? 'field')) ?></span><strong><?= etds_qc_h((string) ($correction['new_value'] ?? '')) ?></strong></div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </aside>
              </div>
            </section>

          <?php elseif ($dataTab === 'validation'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $quality ?>%</strong><span>Data Health Score</span></article>
              <article class="qc-metric-card"><strong><?= $criticalIssues ?></strong><span>Critical</span></article>
              <article class="qc-metric-card"><strong><?= $highIssues ?></strong><span>High</span></article>
              <article class="qc-metric-card"><strong><?= $moderateIssues ?></strong><span>Medium</span></article>
              <article class="qc-metric-card"><strong><?= $minorIssues ?></strong><span>Low + Information</span></article>
            </div>

            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Validation Centre</h3>
                <div class="qc-signal-chips">
                  <?php foreach ($doctorFindingChips as $chip): ?>
                    <span class="qc-signal-chip <?= ($chip['tone'] ?? '') === 'critical' ? 'is-critical' : (($chip['tone'] ?? '') === 'warning' ? 'is-warning' : 'is-good') ?>"><?= etds_qc_h((string) ($chip['label'] ?? 'Finding')) ?></span>
                  <?php endforeach; ?>
                </div>
                <div class="qc-note">Rules are loaded from the modular validation repository and executed through the reusable Validation Rules Engine.</div>
                <?php if ($activeSession): ?>
                  <div class="qc-action-row" style="margin-top:16px;">
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="run_validation">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <button class="btn btn-primary" type="submit">Run Validation + Doctor</button>
                    </form>
                    <a class="btn btn-outline" href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => 'bench', 'db' => 'diagnosis', 'view' => 'session'])) ?>">Open Doctor's Bench</a>
                  </div>
                <?php endif; ?>
              </article>

              <article class="qc-panel">
                <h3>Health Issues Queue</h3>
                <div class="qc-issue-stack">
                  <?php if ($openIssues === []): ?>
                    <div class="qc-empty-inline">Diagnosis complete. No open Doctor findings are present in `validation.json`.</div>
                  <?php else: ?>
                    <?php foreach (array_slice($openIssues, 0, 6) as $finding): ?>
                      <div class="qc-detail-list" style="padding:12px 0; border-top:1px solid rgba(12, 44, 43, 0.08);">
                        <div><span><?= etds_qc_h((string) ($finding['rule_name'] ?? 'Rule Finding')) ?></span><strong><?= etds_qc_h((string) ($finding['severity'] ?? 'Information')) ?></strong></div>
                        <div><span>Record Reference</span><strong><?= etds_qc_h((string) ($finding['record_reference'] ?? 'N/A')) ?></strong></div>
                        <div><span>Doctor's Findings</span><strong><?= etds_qc_h((string) ($finding['message'] ?? 'Validation finding raised.')) ?></strong></div>
                        <div><span>Treatment Suggestion</span><strong><?= etds_qc_h((string) ($finding['suggested_action'] ?? 'Review the extracted data.')) ?></strong></div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </article>
            </div>

          <?php elseif ($dataTab === 'reconciliation'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $financialHealthScore ?>%</strong><span>Financial Health Score</span></article>
              <article class="qc-metric-card"><strong><?= $matchedCount ?></strong><span>Matched Challans</span></article>
              <article class="qc-metric-card"><strong><?= $partialCount ?></strong><span>Partially Matched</span></article>
              <article class="qc-metric-card"><strong><?= $unmatchedCount ?></strong><span>Unmatched Challans</span></article>
              <article class="qc-metric-card"><strong><?= count($reconIssues) ?></strong><span>Mismatch Queue</span></article>
            </div>

            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Enterprise Reconciliation</h3>
                <div class="qc-detail-list">
                  <div><span>Challan Reconciliation</span><strong><?= $matchedCount ?> Matched / <?= $partialCount ?> Partial / <?= $unmatchedCount ?> Unmatched</strong></div>
                  <div><span>Deductee Reconciliation</span><strong><?= $deducteeMatchedCount ?> Matched / <?= $deducteePartialCount ?> Partial / <?= $deducteeUnmatchedCount ?> Unmatched</strong></div>
                  <div><span>Allocated Amount</span><strong>Rs <?= number_format($challanAllocatedTotal, 2) ?></strong></div>
                  <div><span>Available Amount</span><strong>Rs <?= number_format($challanAvailableTotal, 2) ?></strong></div>
                  <div><span>Allocation Variance</span><strong>Rs <?= number_format($allocationVariance, 2) ?></strong></div>
                  <div><span>Unallocated Amounts</span><strong>Rs <?= number_format($unallocatedAmount, 2) ?></strong></div>
                  <div><span>Salary Variance</span><strong>Rs <?= number_format($salaryVariance, 2) ?></strong></div>
                  <div><span>Quarter Consistency</span><strong><?= etds_qc_h(ucwords(str_replace('_', ' ', $quarterConsistency))) ?></strong></div>
                </div>
                <?php if ($activeSession): ?>
                  <div class="qc-action-row" style="margin-top:16px;">
                    <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="run_reconciliation">
                      <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                      <button class="btn btn-primary" type="submit">Run Reconciliation</button>
                    </form>
                    <a class="btn btn-outline" href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => 'bench', 'db' => 'reconciliation', 'view' => 'session'])) ?>">Open Doctor's Bench</a>
                  </div>
                <?php endif; ?>
              </article>

              <article class="qc-panel">
                <h3>Mismatch Queue</h3>
                <?php if ($reconIssues === []): ?>
                  <div class="qc-empty-inline">Diagnosis complete. No reconciliation mismatches are currently open in `reconciliation.json`.</div>
                <?php else: ?>
                  <div class="qc-issue-stack">
                    <?php foreach (array_slice($reconIssues, 0, 6) as $issue): ?>
                      <div class="qc-detail-list" style="padding:12px 0; border-top:1px solid rgba(12, 44, 43, 0.08);">
                        <div><span><?= etds_qc_h(ucwords((string) ($issue['module'] ?? 'reconciliation'))) ?></span><strong><?= etds_qc_h((string) ($issue['severity'] ?? 'Information')) ?></strong></div>
                        <div><span>Record Reference</span><strong><?= etds_qc_h((string) ($issue['record_reference'] ?? 'N/A')) ?></strong></div>
                        <div><span>Issue</span><strong><?= etds_qc_h((string) ($issue['message'] ?? 'Mismatch identified.')) ?></strong></div>
                        <div><span>Suggested Action</span><strong><?= etds_qc_h((string) ($issue['suggested_action'] ?? 'Review affected records.')) ?></strong></div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </article>
            </div>

          <?php elseif ($dataTab === 'bench'): ?>
            <section class="qc-priority-banner">
              <div>
                <span class="qc-kicker">Doctor's Bench</span>
                <h3>Intelligence command centre</h3>
                <p>Powered by e-TDS Doctor. Doctor's Bench reads `doctor.json` and presents diagnosis, prescription, expected improvement, and readiness guidance.</p>
              </div>
              <?php if ($activeSession): ?>
                <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" data-ajax="reload">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="run_doctor">
                  <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                  <button class="btn btn-primary" type="submit">Run Doctor Intelligence</button>
                </form>
              <?php endif; ?>
            </section>

            <div class="qc-tabbar" role="tablist" aria-label="Doctor's Bench tabs">
              <?php foreach (['diagnosis' => 'Diagnosis', 'reconciliation' => 'Reconciliation', 'treatment' => 'Treatment', 'readiness' => 'Readiness', 'certification' => 'QC Certification'] as $tabKey => $tabLabel): ?>
                <a class="qc-tabbar__tab<?= $doctorTab === $tabKey ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => 'data', 'dc' => 'bench', 'db' => $tabKey, 'view' => $activeSession ? 'session' : 'dashboard'])) ?>"><?= etds_qc_h($tabLabel) ?></a>
              <?php endforeach; ?>
            </div>

            <?php if ($doctorTab === 'diagnosis'): ?>
              <div class="qc-metrics qc-metrics--five">
                <article class="qc-metric-card"><strong><?= (int) ($doctorScores['overall_data_health_score'] ?? 0) ?>%</strong><span>Overall Data Health Score</span></article>
                <article class="qc-metric-card"><strong><?= count($doctorDiagnoses) ?></strong><span>Diagnosis Summary</span></article>
                <article class="qc-metric-card"><strong><?= etds_qc_h($doctorTopPriority) ?></strong><span>Top Priority</span></article>
                <article class="qc-metric-card"><strong><?= etds_qc_h($doctorReadinessStatus) ?></strong><span>Readiness</span></article>
                <article class="qc-metric-card"><strong><?= $doctorEstimatedMinutes ?></strong><span>Estimated Time</span></article>
              </div>
              <div class="qc-grid qc-grid--two">
                <article class="qc-panel">
                  <h3>Diagnosis Summary</h3>
                  <?php if ($doctorDiagnoses === [] && !$doctorGenerated): ?>
                    <div class="qc-empty-inline">Doctor intelligence is pending. Run the Doctor engine after validation to generate diagnosis clusters.</div>
                  <?php elseif ($doctorDiagnoses === []): ?>
                    <div class="qc-empty-inline">Diagnosis complete. No diagnosis clusters are active for this case.</div>
                  <?php else: ?>
                    <div class="qc-detail-list">
                      <?php foreach (array_slice($doctorDiagnoses, 0, 4) as $diagnosis): ?>
                        <div><span><?= etds_qc_h((string) ($diagnosis['diagnosis'] ?? 'Diagnosis')) ?></span><strong><?= etds_qc_h((string) ($diagnosis['priority'] ?? 'Information')) ?></strong></div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                  <div class="qc-note">This workspace does not rerun validation. It reads existing findings and transforms them into operator guidance.</div>
                </article>
                <article class="qc-panel">
                  <h3>Top Priority</h3>
                  <?php if ($doctorDiagnosisLead === null && !$doctorGenerated): ?>
                    <div class="qc-empty-inline">No diagnosis has been generated yet.</div>
                  <?php elseif ($doctorDiagnosisLead === null): ?>
                    <div class="qc-empty-inline">No active diagnosis cluster requires escalation.</div>
                  <?php else: ?>
                    <div class="qc-detail-list">
                      <div><span>Diagnosis</span><strong><?= etds_qc_h((string) ($doctorDiagnosisLead['diagnosis'] ?? 'Diagnosis')) ?></strong></div>
                      <div><span>Likely Cause</span><strong><?= etds_qc_h((string) ($doctorDiagnosisLead['likely_cause'] ?? 'Pending')) ?></strong></div>
                      <div><span>Affected Records</span><strong><?= (int) ($doctorDiagnosisLead['affected_record_count'] ?? 0) ?></strong></div>
                      <div><span>Estimated Impact</span><strong><?= etds_qc_h((string) ($doctorDiagnosisLead['estimated_impact'] ?? 'Pending')) ?></strong></div>
                    </div>
                  <?php endif; ?>
                </article>
              </div>

            <?php elseif ($doctorTab === 'reconciliation'): ?>
              <div class="qc-metrics qc-metrics--five">
                <article class="qc-metric-card"><strong><?= $financialHealthScore ?>%</strong><span>Financial Health</span></article>
                <article class="qc-metric-card"><strong><?= count($reconIssues) ?></strong><span>Top Reconciliation Issues</span></article>
                <article class="qc-metric-card"><strong>Rs <?= number_format($allocationVariance, 2) ?></strong><span>Allocation Variance</span></article>
                <article class="qc-metric-card"><strong>Rs <?= number_format($unallocatedAmount, 2) ?></strong><span>Unused Amount</span></article>
                <article class="qc-metric-card"><strong><?= $reconEstimatedImprovement ?>%</strong><span>Expected Improvement</span></article>
              </div>
              <div class="qc-grid qc-grid--two">
                <article class="qc-panel">
                  <h3>Financial Health</h3>
                  <div class="qc-detail-list">
                    <div><span>Challan Score</span><strong><?= (int) ($reconSummary['challan_score'] ?? 0) ?>%</strong></div>
                    <div><span>Deductee Score</span><strong><?= (int) ($reconSummary['deductee_score'] ?? 0) ?>%</strong></div>
                    <div><span>Salary Score</span><strong><?= (int) ($reconSummary['salary_score'] ?? 0) ?>%</strong></div>
                    <div><span>Quarter Score</span><strong><?= (int) ($reconSummary['quarter_score'] ?? 0) ?>%</strong></div>
                    <div><span>Ready Status</span><strong><?= ($reconSummary['ready_status'] ?? false) ? 'Ready' : 'Review Required' ?></strong></div>
                    <div><span>Ready Records</span><strong><?= $documentReadyRecords ?></strong></div>
                  </div>
                </article>
                <article class="qc-panel">
                  <h3>Top Reconciliation Issues</h3>
                  <?php if ($reconIssues === []): ?>
                    <div class="qc-empty-inline">No reconciliation issues are open. Financial consistency is stable for this case.</div>
                  <?php else: ?>
                    <ul class="qc-checklist">
                      <?php foreach (array_slice($reconIssues, 0, 4) as $issue): ?>
                        <li><span class="qc-checklist__dot <?= (($severityBadgeMap[(string) ($issue['severity'] ?? 'Information')] ?? 'is-good') === 'is-critical') ? 'is-pending' : 'is-received' ?>"></span><?= etds_qc_h((string) ($issue['message'] ?? 'Reconciliation issue')) ?><strong><?= etds_qc_h((string) ($issue['severity'] ?? 'Information')) ?></strong></li>
                      <?php endforeach; ?>
                    </ul>
                    <div class="qc-note">Suggested next action: <?= etds_qc_h((string) ($reconTopIssue['suggested_action'] ?? 'Review the affected mismatches in the Spreadsheet Workspace.')) ?></div>
                  <?php endif; ?>
                </article>
              </div>

            <?php elseif ($doctorTab === 'treatment'): ?>
              <div class="qc-grid qc-grid--treatment">
                <article class="qc-panel">
                  <h3>Doctor's Prescription</h3>
                  <?php if ($doctorPrescriptions === [] && !$doctorGenerated): ?>
                    <div class="qc-empty-inline">No prescription is available yet. Generate Doctor intelligence after validation.</div>
                  <?php elseif ($doctorPrescriptions === []): ?>
                    <div class="qc-empty-inline">No corrective prescription is required. The current Doctor profile is stable.</div>
                  <?php else: ?>
                    <div class="qc-detail-list">
                      <?php foreach (array_slice($doctorPrescriptions, 0, 5) as $prescription): ?>
                        <div><span><?= etds_qc_h((string) ($prescription['priority_label'] ?? 'Priority')) ?></span><strong><?= etds_qc_h((string) ($prescription['instruction'] ?? 'Review the affected records.')) ?></strong></div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </article>

                <article class="qc-panel">
                  <h3>Expected Improvement</h3>
                  <div class="qc-detail-list">
                    <div><span>Current Health Score</span><strong><?= (int) ($doctorScores['overall_data_health_score'] ?? 0) ?>%</strong></div>
                    <div><span>Expected Improvement</span><strong><?= etds_qc_h($doctorExpectedImprovement) ?></strong></div>
                    <div><span>Estimated Time</span><strong><?= $doctorEstimatedMinutes ?> Minutes</strong></div>
                  </div>
                  <div class="qc-intelligence-card">
                    <span class="qc-kicker">e-TDS Doctor Intelligence</span>
                    <h4>Treatment Guidance</h4>
                    <p><?= etds_qc_h((string) ($doctorPrescriptionLead['recommended_resolution'] ?? 'Review the recommended resolution, correct affected records, and rerun the Doctor engine.')) ?></p>
                  </div>
                </article>
              </div>

            <?php elseif ($doctorTab === 'readiness'): ?>
              <div class="qc-metrics qc-metrics--five">
                <article class="qc-metric-card"><strong><?= (int) ($doctorScores['extraction_score'] ?? 0) ?>%</strong><span>Extraction Score</span></article>
                <article class="qc-metric-card"><strong><?= (int) ($doctorScores['validation_score'] ?? 0) ?>%</strong><span>Validation Score</span></article>
                <article class="qc-metric-card"><strong><?= (int) ($doctorScores['completeness_score'] ?? 0) ?>%</strong><span>Completeness Score</span></article>
                <article class="qc-metric-card"><strong><?= (int) ($doctorScores['consistency_score'] ?? 0) ?>%</strong><span>Consistency Score</span></article>
                <article class="qc-metric-card"><strong><?= (int) ($doctorScores['overall_data_health_score'] ?? 0) ?>%</strong><span>Overall Data Health Score</span></article>
              </div>
              <section class="qc-banner <?= $doctorReadinessStatus === 'Ready for QC Certification' ? 'is-good' : 'is-alert' ?>">
                <div>
                  <span class="qc-kicker">Readiness Review</span>
                  <h3><?= etds_qc_h($doctorReadinessStatus) ?></h3>
                  <p><?= etds_qc_h($doctorReadinessReason) ?></p>
                </div>
              </section>

            <?php else: ?>
              <div class="qc-grid qc-grid--two">
                <article class="qc-panel">
                  <h3>Doctor Certification</h3>
                  <div class="qc-detail-list">
                    <div><span>Overall Data Health Score</span><strong><?= (int) ($doctorScores['overall_data_health_score'] ?? 0) ?>%</strong></div>
                    <div><span>Top Priority</span><strong><?= etds_qc_h($doctorTopPriority) ?></strong></div>
                    <div><span>Top Diagnosis</span><strong><?= etds_qc_h($doctorTopDiagnosis) ?></strong></div>
                    <div><span>Ready Status</span><strong><?= etds_qc_h($doctorReadinessStatus) ?></strong></div>
                  </div>
                  <div class="qc-cert-status <?= $doctorReadinessStatus === 'Ready for QC Certification' ? 'is-good' : 'is-alert' ?>"><?= $doctorReadinessStatus === 'Ready for QC Certification' ? 'READY FOR QC CERTIFICATION' : 'DOCTOR REVIEW REQUIRED' ?></div>
                </article>
                <article class="qc-panel">
                  <h3>Scoring Methodology</h3>
                  <ul class="qc-checklist">
                    <li><span class="qc-checklist__dot is-received"></span>Extraction Score<strong>20%</strong></li>
                    <li><span class="qc-checklist__dot is-received"></span>Validation Score<strong>40%</strong></li>
                    <li><span class="qc-checklist__dot is-received"></span>Completeness Score<strong>20%</strong></li>
                    <li><span class="qc-checklist__dot is-received"></span>Consistency Score<strong>20%</strong></li>
                  </ul>
                </article>
              </div>
            <?php endif; ?>

          <?php elseif ($dataTab === 'output'): ?>
            <section class="qc-banner <?= $doctorReadinessStatus === 'Ready for QC Certification' ? 'is-good' : 'is-alert' ?>">
              <div>
                <span class="qc-kicker">QC Output Centre</span>
                <h3><?= $doctorReadinessStatus === 'Ready for QC Certification' ? 'Doctor Certification Cleared' : 'Doctor Certification Pending' ?></h3>
                <p>This workspace reads Doctor intelligence and shows whether the case is clear for downstream output preparation.</p>
              </div>
            </section>

            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Doctor Certification Summary</h3>
                <div class="qc-detail-list">
                  <div><span>Overall Data Health Score</span><strong><?= (int) ($doctorScores['overall_data_health_score'] ?? 0) ?>%</strong></div>
                  <div><span>Top Diagnosis</span><strong><?= etds_qc_h($doctorTopDiagnosis) ?></strong></div>
                  <div><span>Expected Improvement</span><strong><?= etds_qc_h($doctorExpectedImprovement) ?></strong></div>
                  <div><span>Output Readiness</span><strong><?= etds_qc_h($doctorReadinessStatus) ?></strong></div>
                </div>
              </article>
              <article class="qc-panel">
                <h3>Blocked Issues</h3>
                <div class="qc-signal-list">
                  <div><span>Critical</span><strong><?= $criticalIssues ?></strong></div>
                  <div><span>High</span><strong><?= $highIssues ?></strong></div>
                  <div><span>Medium</span><strong><?= $moderateIssues ?></strong></div>
                  <div><span>Low + Information</span><strong><?= $minorIssues ?></strong></div>
                  <div><span>OCR Pages</span><strong><?= $ocrPagesProcessed ?></strong></div>
                </div>
              </article>
            </div>

            <div class="qc-output-grid">
              <?php foreach ([
                'Doctor Diagnosis Report' => ['copy' => 'Clustered diagnosis output for the current case.', 'state' => $doctorGenerated ? 'Ready' : 'Pending'],
                'Prescription Report' => ['copy' => 'Actionable Doctor prescriptions with expected score improvements.', 'state' => $doctorGenerated ? 'Ready' : 'Pending'],
                'Health Score Report' => ['copy' => 'Extraction, validation, completeness, consistency, and overall health scores.', 'state' => 'Ready'],
                'Readiness Report' => ['copy' => 'Doctor readiness determination and reasoning.', 'state' => $doctorGenerated ? 'Ready' : 'Pending'],
                'Validation Handover' => ['copy' => 'Validation findings consumed by the Doctor engine.', 'state' => $doctorGenerated ? 'Ready' : 'Pending'],
              ] as $title => $meta): ?>
                <article class="qc-output-card">
                  <h4><?= etds_qc_h($title) ?></h4>
                  <p><?= etds_qc_h($meta['copy']) ?></p>
                  <span class="qc-signal-chip <?= in_array($meta['state'], ['Blocked', 'Pending'], true) ? 'is-warning' : 'is-good' ?>"><?= etds_qc_h($meta['state']) ?></span>
                </article>
              <?php endforeach; ?>
            </div>

            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Doctor Intelligence Downloads</h3>
                <?php if ($activeSession): ?>
                  <div class="qc-action-row">
                    <a class="btn btn-primary" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=doctor_diagnosis_report')) ?>">Diagnosis Report</a>
                    <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=doctor_prescription_report')) ?>">Prescription Report</a>
                    <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=doctor_health_score_report')) ?>">Health Score Report</a>
                    <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=doctor_readiness_report')) ?>">Readiness Report</a>
                  </div>
                <?php endif; ?>
                <div class="qc-note">Doctor intelligence transforms validation findings into operational guidance. Reconciliation, return preparation, FVU, TaxPro, and utility exports remain intentionally deferred.</div>
              </article>

              <article class="qc-panel">
                <h3>Structured JSON Outputs</h3>
                <ul class="qc-checklist">
                  <li><span class="qc-checklist__dot is-received"></span>deductor.json<strong>Ready</strong></li>
                  <li><span class="qc-checklist__dot is-received"></span>deductees.json<strong>Ready</strong></li>
                  <li><span class="qc-checklist__dot is-received"></span>challans.json<strong>Ready</strong></li>
                  <li><span class="qc-checklist__dot is-received"></span>salary.json<strong>Ready</strong></li>
                  <li><span class="qc-checklist__dot is-received"></span>payments.json<strong>Ready</strong></li>
                  <li><span class="qc-checklist__dot is-received"></span>extraction.json<strong>Ready</strong></li>
                  <li><span class="qc-checklist__dot is-received"></span>ocr.json<strong>Ready</strong></li>
                  <li><span class="qc-checklist__dot <?= $doctorGenerated ? 'is-received' : 'is-pending' ?>"></span>doctor.json<strong><?= $doctorGenerated ? 'Ready' : 'Pending' ?></strong></li>
                </ul>
              </article>
            </div>
          <?php endif; ?>
        </section>

      <?php elseif ($workspace === 'returns'): ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Return Centre</span>
              <h2>Available in Version 2</h2>
              <p>Version 1 does not implement return preparation. This workspace is intentionally reserved to preserve the future navigation and UI framework.</p>
            </div>
          </div>

          <section class="qc-banner is-alert">
            <div>
              <span class="qc-kicker">Version 2 Roadmap</span>
              <h3>Future return preparation workspace shell</h3>
              <p>All actions remain disabled until the Version 2 rollout.</p>
            </div>
          </section>

          <div class="qc-grid qc-grid--two">
            <article class="qc-panel">
              <h3>Roadmap</h3>
              <ul class="qc-checklist">
                <?php foreach ($returnCentreRoadmap as $item): ?>
                  <li><span class="qc-checklist__dot is-pending"></span><?= etds_qc_h($item) ?><strong>Version 2</strong></li>
                <?php endforeach; ?>
              </ul>
            </article>
            <article class="qc-panel">
              <h3>Version 2 Placeholder Validation</h3>
              <div class="qc-detail-list">
                <div><span>Navigation Reserved</span><strong>Yes</strong></div>
                <div><span>Actions Enabled</span><strong>No</strong></div>
                <div><span>Return Utilities</span><strong>Deferred</strong></div>
                <div><span>Compliance Status</span><strong>Future</strong></div>
              </div>
              <div class="qc-intelligence-card">
                <span class="qc-kicker">Future Modules</span>
                <h4>Coming Soon</h4>
                <p>Return Preparation, Correction Returns, Utility Export, Filing, Acknowledgements, and Return History will arrive without changing this shell.</p>
              </div>
            </article>
          </div>
        </section>

      <?php else: ?>
        <section class="qc-workspace">
          <div class="qc-workspace__head">
            <div>
              <span class="qc-kicker">Report Centre</span>
              <h2>Version 1 reporting, auditability, and administration workspace</h2>
              <p>QC reports, exception reports, analytics, audit logs, and administration live here. Compliance document generation remains a Version 2 placeholder.</p>
            </div>
          </div>

          <div class="qc-tabbar" role="tablist" aria-label="Report Centre tabs">
            <?php foreach ($reportTabs as $tabKey => $tabLabel): ?>
              <a class="qc-tabbar__tab<?= $reportTab === $tabKey ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => 'reports', 'rc' => $tabKey, 'view' => $activeSession ? 'session' : 'dashboard'])) ?>"><?= etds_qc_h($tabLabel) ?></a>
            <?php endforeach; ?>
          </div>

          <?php if ($reportTab === 'qcreports'): ?>
            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>QC Reports</h3>
                <div class="qc-detail-list">
                  <div><span>Case Summary</span><strong>Available</strong></div>
                  <div><span>Document Register</span><strong>Available</strong></div>
                  <div><span>Upload Summary</span><strong>Available</strong></div>
                  <div><span>Extraction Summary</span><strong>Available</strong></div>
                </div>
                <?php if ($activeSession): ?>
                  <div class="qc-action-row" style="margin-top:12px;">
                    <a class="btn btn-primary" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=case_summary')) ?>">Case Summary</a>
                    <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=document_register')) ?>">Document Register</a>
                    <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=upload_summary')) ?>">Upload Summary</a>
                    <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=extraction_summary')) ?>">Extraction Summary</a>
                  </div>
                <?php endif; ?>
              </article>
              <article class="qc-panel">
                <h3>Production Readiness Report</h3>
                <div class="qc-signal-list">
                  <div><span>Version 1 Scope</span><strong>AI Extraction Engine</strong></div>
                  <div><span>Validation</span><strong>Deferred</strong></div>
                  <div><span>Navigation Upgrade Path</span><strong>Preserved</strong></div>
                </div>
              </article>
            </div>

          <?php elseif ($reportTab === 'exceptions'): ?>
            <article class="qc-panel">
              <h3>Extraction Confidence Reports</h3>
              <div class="qc-action-row">
                <?php if ($activeSession): ?>
                  <a class="btn btn-primary" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=confidence_report')) ?>">Extraction Confidence Report</a>
                  <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=classification_report')) ?>">Document Classification Report</a>
                <?php endif; ?>
              </div>
              <div class="qc-note">Phase 3 reports document classification and extraction confidence. Validation exceptions will arrive in Phase 4.</div>
            </article>

          <?php elseif ($reportTab === 'analytics'): ?>
            <div class="qc-metrics qc-metrics--five">
              <article class="qc-metric-card"><strong><?= $openCases ?></strong><span>Open Cases</span></article>
              <article class="qc-metric-card"><strong><?= $documentsProcessed ?></strong><span>Processed</span></article>
              <article class="qc-metric-card"><strong><?= $documentsPendingReview ?></strong><span>Pending Review</span></article>
              <article class="qc-metric-card"><strong><?= $documentsFailed ?></strong><span>Failed</span></article>
              <article class="qc-metric-card"><strong><?= $overallExtractionConfidence ?>%</strong><span>Confidence</span></article>
            </div>
            <article class="qc-panel">
              <h3>Analytics Dashboard</h3>
              <div class="qc-note">This dashboard focuses on Phase 3 extraction analytics: classification, OCR, extracted fields, and confidence trends.</div>
            </article>

          <?php elseif ($reportTab === 'audit'): ?>
            <article class="qc-panel">
              <h3>Audit Logs</h3>
              <?php if ($activeSession): ?>
                <div class="qc-action-row" style="margin-bottom:12px;">
                  <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?action=download_report&session=' . urlencode($sessionId) . '&report=audit_report')) ?>">Download Audit Report</a>
                </div>
              <?php endif; ?>
              <div class="qc-timeline">
                <?php foreach ($activityTimeline as $item): ?>
                  <div class="qc-timeline__item">
                    <span class="qc-timeline__time"><?= etds_qc_h($item['time']) ?></span>
                    <span class="qc-timeline__state is-<?= etds_qc_h($item['tone']) ?>"></span>
                    <div>
                      <strong><?= etds_qc_h($item['label']) ?></strong>
                      <p>Captured as part of the enterprise audit trail.</p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </article>

          <?php elseif ($reportTab === 'system'): ?>
            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>System Reports</h3>
                <div class="qc-detail-list">
                  <div><span>Application Version</span><strong>e-TDSDoc V1</strong></div>
                  <div><span>Platform Scope</span><strong>TDS QC Platform</strong></div>
                  <div><span>Active Workspace</span><strong>Report Centre</strong></div>
                  <div><span>Upgrade Path</span><strong>Version 2 Reserved</strong></div>
                </div>
              </article>
              <article class="qc-panel">
                <h3>Platform Health</h3>
                <div class="qc-signal-list">
                  <div><span>Doctor Engine</span><strong>Online</strong></div>
                  <div><span>Validation Layer</span><strong>Online</strong></div>
                  <div><span>Return Features</span><strong>Disabled</strong></div>
                </div>
                <div class="qc-note">System Reports remain focused on Version 1 operational health, auditability, and upgrade readiness.</div>
              </article>
            </div>

          <?php else: ?>
            <div class="qc-grid qc-grid--two">
              <article class="qc-panel">
                <h3>Administration</h3>
                <div class="qc-detail-list">
                  <div><span>User</span><strong><?= etds_qc_h((string) ($user['name'] ?? 'Operator')) ?></strong></div>
                  <div><span>Role</span><strong><?= etds_qc_h((string) ($user['role'] ?? 'operator')) ?></strong></div>
                  <div><span>Workspace Scope</span><strong>Version 1</strong></div>
                </div>
              </article>
              <article class="qc-panel">
                <h3>Future Report Cards</h3>
                <ul class="qc-checklist">
                  <li><span class="qc-checklist__dot is-pending"></span>Form 16<strong>Version 2</strong></li>
                  <li><span class="qc-checklist__dot is-pending"></span>Form 16A<strong>Version 2</strong></li>
                  <li><span class="qc-checklist__dot is-pending"></span>Form 27D<strong>Version 2</strong></li>
                </ul>
              </article>
            </div>
          <?php endif; ?>
        </section>
      <?php endif; ?>
    </main>
  </div>
</div>
