<?php
declare(strict_types=1);

if (!function_exists('etds_reconciliation_output_schema')) {
  function etds_reconciliation_output_schema(): array {
    return [
      'summary' => [
        'challan_score' => 0,
        'deductee_score' => 0,
        'salary_score' => 0,
        'quarter_score' => 0,
        'financial_health_score' => 0,
        'reconciliation_score' => 0,
        'ready_status' => false,
        'total_issues' => 0,
        'difference' => 0.0,
        'balance' => 0.0,
        'last_reconciled_on' => null,
      ],
      'challan_reconciliation' => ['summary' => [], 'rows' => [], 'issues' => []],
      'deductee_reconciliation' => ['summary' => [], 'rows' => [], 'issues' => []],
      'salary_reconciliation' => ['summary' => [], 'issues' => []],
      'quarter_reconciliation' => ['summary' => [], 'issues' => []],
      'document_reconciliation' => ['summary' => []],
      'issues' => [],
      'exceptions' => [],
    ];
  }
}

if (!function_exists('etds_reconciliation_engine_run')) {
  function etds_reconciliation_engine_run(string $sessionId, array $user): array {
    if (function_exists('etds_qc_workspace_sync_case_data')) {
      etds_qc_workspace_sync_case_data($sessionId);
    }
    $challansPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]) : ['challans' => []];
    $deducteesPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => []]) : ['deductees' => []];
    $salaryPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => []]) : ['rows' => []];
    $paymentsPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'payments.json'), ['payments' => []]) : ['payments' => []];
    $validation = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'validation.json'), function_exists('etds_qc_default_validation') ? etds_qc_default_validation() : ['summary' => [], 'findings' => []]) : ['summary' => [], 'findings' => []];
    $doctor = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'doctor.json'), function_exists('etds_qc_default_doctor') ? etds_qc_default_doctor() : []) : [];
    $corrections = function_exists('etds_qc_workspace_corrections') ? etds_qc_workspace_corrections($sessionId) : ['history' => [], 'cell_states' => []];
    $case = function_exists('etds_qc_find_session') ? (etds_qc_find_session($sessionId) ?? []) : [];

    $challanModule = function_exists('etds_challan_reconciliation_run') ? etds_challan_reconciliation_run((array) ($challansPayload['challans'] ?? []), (array) ($deducteesPayload['deductees'] ?? [])) : ['summary' => [], 'rows' => [], 'issues' => []];
    $deducteeModule = function_exists('etds_deductee_reconciliation_run') ? etds_deductee_reconciliation_run((array) ($deducteesPayload['deductees'] ?? []), (array) ($paymentsPayload['payments'] ?? []), (array) ($challansPayload['challans'] ?? [])) : ['summary' => [], 'rows' => [], 'issues' => []];
    $salaryModule = function_exists('etds_salary_reconciliation_run') ? etds_salary_reconciliation_run((array) ($salaryPayload['rows'] ?? []), (array) ($deducteesPayload['deductees'] ?? []), (array) ($challansPayload['challans'] ?? [])) : ['summary' => [], 'issues' => []];
    $quarterModule = function_exists('etds_quarter_reconciliation_run') ? etds_quarter_reconciliation_run((array) ($deducteesPayload['deductees'] ?? []), is_array($case) ? $case : []) : ['summary' => [], 'issues' => []];
    $health = function_exists('etds_financial_health_build') ? etds_financial_health_build($challanModule, $deducteeModule, $salaryModule, $quarterModule) : ['challan_score' => 0, 'deductee_score' => 0, 'salary_score' => 0, 'quarter_score' => 0, 'financial_health_score' => 0];

    $documentSummary = [
      'extracted_records' => count((array) ($deducteesPayload['deductees'] ?? [])),
      'corrected_cells' => count((array) ($corrections['cell_states'] ?? [])),
      'validation_findings' => (int) (($validation['summary']['total_findings'] ?? 0) ?: count((array) ($validation['findings'] ?? []))),
      'doctor_readiness' => (string) (($doctor['readiness']['status'] ?? 'Not Ready')),
      'ready_records' => max(0, count((array) ($deducteesPayload['deductees'] ?? [])) - count((array) ($validation['findings'] ?? []))),
    ];

    $allIssues = array_merge(
      (array) ($challanModule['issues'] ?? []),
      (array) ($deducteeModule['issues'] ?? []),
      (array) ($salaryModule['issues'] ?? []),
      (array) ($quarterModule['issues'] ?? [])
    );
    usort($allIssues, static function (array $left, array $right): int {
      $weight = ['Critical' => 5, 'High' => 4, 'Medium' => 3, 'Low' => 2, 'Information' => 1];
      return (($weight[(string) ($right['severity'] ?? 'Low')] ?? 1) <=> ($weight[(string) ($left['severity'] ?? 'Low')] ?? 1));
    });

    $difference = round((float) (($challanModule['summary']['available_total'] ?? 0) - ($challanModule['summary']['allocated_total'] ?? 0)), 2);
    $payload = [
      'summary' => [
        'challan_score' => (int) ($health['challan_score'] ?? 0),
        'deductee_score' => (int) ($health['deductee_score'] ?? 0),
        'salary_score' => (int) ($health['salary_score'] ?? 0),
        'quarter_score' => (int) ($health['quarter_score'] ?? 0),
        'financial_health_score' => (int) ($health['financial_health_score'] ?? 0),
        'reconciliation_score' => (int) ($health['financial_health_score'] ?? 0),
        'ready_status' => $allIssues === [],
        'total_issues' => count($allIssues),
        'difference' => $difference,
        'balance' => (float) ($challanModule['summary']['unused_total'] ?? 0),
        'last_reconciled_on' => function_exists('etds_qc_now') ? etds_qc_now() : date(DATE_ATOM),
      ],
      'challan_reconciliation' => $challanModule,
      'deductee_reconciliation' => $deducteeModule,
      'salary_reconciliation' => $salaryModule,
      'quarter_reconciliation' => $quarterModule,
      'document_reconciliation' => ['summary' => $documentSummary],
      'issues' => $allIssues,
      'exceptions' => $allIssues,
    ];

    if (function_exists('etds_qc_write_json')) {
      etds_qc_write_json(etds_qc_session_file($sessionId, 'reconciliation.json'), $payload);
    }
    if (function_exists('etds_qc_find_session') && function_exists('etds_qc_save_session')) {
      $session = etds_qc_find_session($sessionId);
      if (is_array($session)) {
        $session['reconciliation_score'] = (int) ($payload['summary']['financial_health_score'] ?? 0);
        $session['status'] = $payload['summary']['ready_status'] ? 'qc_in_progress' : 'reconciliation_pending';
        $session['last_action'] = 'reconciliation_run_completed';
        $session['export_readiness'] = function_exists('etds_qc_export_readiness') ? etds_qc_export_readiness($sessionId) : false;
        etds_qc_save_session($session);
      }
    }
    if (function_exists('etds_qc_audit')) {
      etds_qc_audit($sessionId, $user, 'reconciliation_completed', 'Enterprise reconciliation completed', [], [
        'financial_health_score' => $payload['summary']['financial_health_score'] ?? 0,
        'total_issues' => $payload['summary']['total_issues'] ?? 0,
      ]);
    }
    return $payload;
  }
}
