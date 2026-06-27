<?php
declare(strict_types=1);

$workspace = strtolower((string) ($_GET['ws'] ?? ($activeSession ? 'bench' : 'intake')));
if (!in_array($workspace, ['intake', 'extraction', 'bench', 'excel'], true)) {
  $workspace = $activeSession ? 'bench' : 'intake';
}
$benchTab = strtolower((string) ($_GET['tab'] ?? 'diagnosis'));
if (!in_array($benchTab, ['diagnosis', 'reconciliation', 'treatment', 'readiness'], true)) {
  $benchTab = 'diagnosis';
}

$activeState = $activeSession ? ($sessionStates[$sessionId] ?? etds_qc_session_state($activeSession)) : null;
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

$treatmentStatusLabel = $openIssues === [] ? 'Complete' : ($criticalIssues > 0 ? 'Critical Items Open' : 'In Progress');
$doctorCertificationLabel = $readiness ? 'Doctor Certified' : 'Doctor Review Pending';
$processingResultLabel = $readiness ? 'Fit For Processing' : 'Not Fit For Processing';
$exportResultLabel = $readiness ? 'Ready For Export' : 'Not Ready';
$latestSessionId = !empty($sessions) ? (string) ($sessions[0]['session_id'] ?? '') : '';

$buildUrl = static function (array $params = []) use ($sessionId, $view, $workspace, $benchTab): string {
  $resolvedWorkspace = (string) ($params['ws'] ?? $workspace);
  $resolvedSessionId = array_key_exists('session', $params) ? (string) $params['session'] : $sessionId;
  $resolvedView = (string) ($params['view'] ?? ($resolvedSessionId !== '' ? 'session' : ($view === 'create' ? 'create' : 'dashboard')));
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
?>
<main id="main-content">
  <section class="container etds-shell">
    <div class="etds-app-shell">
      <aside class="etds-sidebar">
        <div class="etds-sidebar-inner">
          <div class="etds-side-brand">
            <div class="etds-side-brand-mark">eT</div>
            <div>
              <p class="etds-side-brand-title">e-TDS QC Tool</p>
              <p class="etds-side-brand-subtitle">e-TDS Doctor Command Centre</p>
            </div>
          </div>

          <?php if ($activeSession): ?>
            <div class="etds-side-case">
              <span class="etds-side-kicker">Active Case</span>
              <strong><?= etds_qc_h((string) $activeSession['client_name']) ?></strong>
              <p><?= etds_qc_h((string) $activeSession['session_id']) ?> · <?= etds_qc_h((string) $activeSession['quarter']) ?> / <?= etds_qc_h((string) $activeSession['return_type']) ?></p>
            </div>
          <?php endif; ?>

          <nav class="etds-workspace-nav" aria-label="Primary workspaces">
            <?php foreach ([
              'intake' => 'Intake Centre',
              'extraction' => 'Extraction Centre',
              'bench' => "Doctor's Bench",
              'excel' => 'Final Excel Advice',
            ] as $key => $label): ?>
              <a class="etds-workspace-link<?= $workspace === $key ? ' is-active' : '' ?>" href="<?= etds_qc_h($buildUrl(['ws' => $key, 'view' => $activeSession ? 'session' : 'dashboard'])) ?>">
                <span class="etds-workspace-icon"><?= etds_qc_nav_icon($key) ?></span>
                <span><?= etds_qc_h($label) ?></span>
              </a>
            <?php endforeach; ?>
          </nav>

          <form class="etds-logout-form" method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="logout">
            <button class="etds-logout-button" type="submit" data-confirm="Log out of e-TDS QC Tool now?">
              <span class="etds-workspace-icon"><?= etds_qc_nav_icon('logout') ?></span>
              <span>Logout</span>
            </button>
          </form>
        </div>
      </aside>

      <div class="etds-main">
        <?php etds_qc_render_flash($flash); ?>

        <div class="etds-page-head">
          <div>
            <div class="eyebrow"><?= etds_qc_h(match ($workspace) {
              'intake' => 'Intake Centre',
              'extraction' => 'Extraction Centre',
              'bench' => "Doctor's Bench",
              'excel' => 'Final Excel Advice',
              default => 'e-TDS Doctor',
            }) ?></div>
            <h1><?= etds_qc_h(match ($workspace) {
              'intake' => 'Capture and organise every diagnostic case',
              'extraction' => 'Prepare source files for clinical review',
              'bench' => 'Investigate, reconcile, treat, and certify',
              'excel' => 'Prepare certified output files and advice',
              default => 'e-TDS Doctor',
            }) ?></h1>
            <p class="etds-subtitle">
              <?php if ($activeSession): ?>
                <?= etds_qc_h((string) $activeSession['session_id']) ?> · <?= etds_qc_h((string) $activeSession['client_name']) ?> · <?= etds_qc_h((string) $activeSession['tan']) ?> · FY <?= etds_qc_h((string) $activeSession['financial_year']) ?>
              <?php else: ?>
                AI-Driven Data Health Check for TDS intake, diagnosis, treatment, reconciliation, and export readiness.
              <?php endif; ?>
            </p>
          </div>
          <div>
            <div class="etds-score-row">
              <span class="etds-status-chip" data-tone="<?= $quality >= 95 ? 'good' : ($quality >= 80 ? 'warning' : 'critical') ?>">Data Health Score <?= $quality ?>%</span>
              <span class="etds-status-chip" data-tone="<?= $reconScore >= 95 ? 'good' : ($reconScore >= 80 ? 'warning' : 'critical') ?>">Reconciliation <?= $reconScore ?>%</span>
              <span class="etds-status-chip" data-tone="<?= $readiness ? 'good' : 'critical' ?>"><?= etds_qc_h($processingResultLabel) ?></span>
            </div>
          </div>
        </div>

        <?php if (!$activeSession && $workspace !== 'intake'): ?>
          <div class="etds-empty">
            <h2>No active case selected</h2>
            <p>Start from Intake Centre to create a case and move through the workflow.</p>
            <div class="etds-action-row" style="justify-content:center; margin-top:16px;">
              <a class="btn btn-primary" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=intake&view=create')) ?>">Create Diagnostic Session</a>
              <?php if ($latestSessionId !== ''): ?>
                <a class="btn btn-outline" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=intake&view=session&session=' . urlencode($latestSessionId))) ?>">Open Latest Case</a>
              <?php endif; ?>
            </div>
          </div>
        <?php elseif ($workspace === 'intake'): ?>
          <div class="etds-grid etds-dashboard-grid">
            <div class="etds-stat"><strong><?= $counts['sessions'] ?></strong><span>Total Cases</span></div>
            <div class="etds-stat"><strong><?= $counts['validation'] ?></strong><span>Pending Diagnosis</span></div>
            <div class="etds-stat"><strong><?= $counts['reconciliation'] ?></strong><span>Pending Reconciliation</span></div>
            <div class="etds-stat"><strong><?= $counts['ready'] ?></strong><span>Fit for Processing</span></div>
            <div class="etds-stat"><strong><?= $counts['completed'] ?></strong><span>Completed</span></div>
          </div>

          <div class="etds-grid etds-two-col">
            <div class="etds-panel">
              <h2>New Diagnostic Case</h2>
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
                  <button class="btn btn-primary" type="submit">Create Diagnostic Session</button>
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
                          <td><a class="btn btn-outline btn-sm" href="<?= etds_qc_h(site_href('/fintech/etds-qc/?ws=intake&view=session&session=' . urlencode((string) $row['session_id']))) ?>">Open Case</a></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php elseif ($workspace === 'extraction' && $activeSession): ?>
          <div class="etds-grid etds-two-col">
            <div class="etds-panel">
              <h2>Upload Case Files</h2>
              <form method="post" enctype="multipart/form-data" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload_documents">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <div class="etds-field"><label for="documents">Case Files</label><input id="documents" name="documents[]" type="file" multiple required></div>
                <div class="etds-action-row" style="margin-top:16px;">
                  <button class="btn btn-primary" type="submit">Upload Case Files</button>
                </div>
              </form>
            </div>

            <div class="etds-panel">
              <h2>Extraction Status</h2>
              <ul class="etds-mini-list">
                <li><span>Files Received</span><strong><?= count($sourceData['documents'] ?? []) ?></strong></li>
                <li><span>Extracted Records</span><strong><?= $totalRecords ?></strong></li>
                <li><span>Detected Columns</span><strong><?= $sourceColumnsCount ?></strong></li>
              </ul>
              <form method="post" action="<?= etds_qc_h(site_href('/fintech/etds-qc/')) ?>" style="margin-top:18px;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="extract_validate">
                <input type="hidden" name="session_id" value="<?= etds_qc_h($sessionId) ?>">
                <button class="btn btn-gold" type="submit">Run Extraction &amp; Send to Doctor's Bench</button>
              </form>
            </div>
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
              <div class="etds-stat"><strong><?= count($openIssues) ?></strong><span>Doctor Findings</span></div>
            </div>
            <div class="etds-grid etds-two-col">
              <div class="etds-panel">
                <h2>Doctor Findings</h2>
                <ul class="etds-mini-list">
                  <?php foreach (array_slice($openIssues, 0, 8) as $entry): ?>
                    <li>
                      <span><?= etds_qc_h((string) $entry['issue']['message']) ?><br><span class="etds-muted"><?= etds_qc_h((string) ($entry['record']['normalized']['deductee_name'] ?? 'Unknown')) ?></span></span>
                      <strong><?= etds_qc_h((string) $entry['severity_label']) ?></strong>
                    </li>
                  <?php endforeach; ?>
                  <?php if ($openIssues === []): ?>
                    <li><span>Diagnosis complete</span><strong>No health issues open</strong></li>
                  <?php endif; ?>
                </ul>
              </div>
              <div class="etds-panel">
                <h2>Health Issues Queue</h2>
                <?php if ($openIssues === []): ?>
                  <div class="etds-empty">No items are waiting in the queue.</div>
                <?php else: ?>
                  <div class="etds-issue-stack">
                    <?php foreach ($openIssues as $entry): ?>
                      <article class="etds-issue-card" data-severity="<?= etds_qc_h((string) $entry['issue']['severity']) ?>">
                        <div class="etds-chip-row" style="margin-bottom:10px;">
                          <span class="etds-status-chip" data-tone="<?= etds_qc_h((string) $entry['tone']) ?>"><?= etds_qc_h((string) $entry['severity_label']) ?></span>
                          <span class="etds-chip"><?= etds_qc_h((string) ($entry['record']['record_id'] ?? '')) ?></span>
                        </div>
                        <h4><?= etds_qc_h((string) $entry['issue']['message']) ?></h4>
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
              <h2>Treatment Queue</h2>
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
                      <p><strong>Suggested correction:</strong> <?= etds_qc_h((string) $issue['suggested_correction']) ?></p>
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
            <div class="etds-grid etds-dashboard-grid">
              <div class="etds-stat"><strong><?= $quality ?>%</strong><span>Data Health Score</span></div>
              <div class="etds-stat"><strong><?= etds_qc_h($treatmentStatusLabel) ?></strong><span>Treatment Completion Status</span></div>
              <div class="etds-stat"><strong><?= etds_qc_h($doctorCertificationLabel) ?></strong><span>Doctor Certification</span></div>
              <div class="etds-stat"><strong><?= etds_qc_h($processingResultLabel) ?></strong><span>Result</span></div>
              <div class="etds-stat"><strong><?= etds_qc_h($exportResultLabel) ?></strong><span>Export</span></div>
            </div>
          <?php endif; ?>
        <?php elseif ($workspace === 'excel' && $activeSession): ?>
          <div class="etds-grid etds-dashboard-grid">
            <div class="etds-stat"><strong><?= etds_qc_h($doctorCertificationLabel) ?></strong><span>Doctor Certification Summary</span></div>
            <div class="etds-stat"><strong><?= $quality ?>%</strong><span>Case Health Report</span></div>
            <div class="etds-stat"><strong><?= count($exportFiles) ?></strong><span>Generated Files</span></div>
            <div class="etds-stat"><strong><?= etds_qc_h($exportResultLabel) ?></strong><span>Download Centre</span></div>
            <div class="etds-stat"><strong><?= $sourceColumnsCount ?></strong><span>Output Context</span></div>
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
                <span class="etds-chip"><?= $title === 'Final Clean Excel' ? etds_qc_h($exportResultLabel) : 'Output View' ?></span>
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
