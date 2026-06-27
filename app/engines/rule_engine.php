<?php
declare(strict_types=1);

if (!function_exists('etds_rule_engine_context')) {
  function etds_rule_engine_context(string $sessionId): array {
    $case = function_exists('etds_qc_find_session') ? (etds_qc_find_session($sessionId) ?? []) : [];
    $deductor = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'deductor.json'), []) : [];
    $deducteesPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => []]) : ['deductees' => []];
    $challansPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []]) : ['challans' => []];
    $salaryPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => []]) : ['rows' => []];
    $paymentsPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'payments.json'), ['payments' => []]) : ['payments' => []];
    $extractionPayload = function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'extraction.json'), ['summary' => []]) : ['summary' => []];

    return [
      'case' => is_array($case) ? $case : [],
      'deductor' => is_array($deductor) ? $deductor : [],
      'deductees' => is_array($deducteesPayload['deductees'] ?? null) ? $deducteesPayload['deductees'] : [],
      'challans' => is_array($challansPayload['challans'] ?? null) ? $challansPayload['challans'] : [],
      'salary' => is_array($salaryPayload['rows'] ?? null) ? $salaryPayload['rows'] : [],
      'payments' => is_array($paymentsPayload['payments'] ?? null) ? $paymentsPayload['payments'] : [],
      'extraction_summary' => is_array($extractionPayload['summary'] ?? null) ? $extractionPayload['summary'] : [],
    ];
  }
}

if (!function_exists('etds_rule_engine_summary')) {
  function etds_rule_engine_summary(array $findings, array $context): array {
    $counts = ['Critical' => 0, 'High' => 0, 'Medium' => 0, 'Low' => 0, 'Information' => 0];
    foreach ($findings as $finding) {
      $severity = (string) ($finding['severity'] ?? 'Information');
      if (!array_key_exists($severity, $counts)) {
        $counts[$severity] = 0;
      }
      $counts[$severity]++;
    }

    $score = 100;
    $weights = ['Critical' => 15, 'High' => 10, 'Medium' => 5, 'Low' => 2, 'Information' => 1];
    foreach ($counts as $severity => $count) {
      $score -= ((int) ($weights[$severity] ?? 1)) * $count;
    }
    $totalRecords = count((array) ($context['deductees'] ?? [])) + count((array) ($context['challans'] ?? [])) + count((array) ($context['salary'] ?? [])) + count((array) ($context['payments'] ?? []));

    return [
      'total_records' => $totalRecords,
      'quality_score' => max(0, $score),
      'critical' => $counts['Critical'],
      'high' => $counts['High'],
      'medium' => $counts['Medium'],
      'low' => $counts['Low'],
      'information' => $counts['Information'],
      'total_findings' => array_sum($counts),
      'ready_status' => $counts['Critical'] === 0 && $counts['High'] === 0,
      'last_validated_on' => function_exists('etds_qc_now') ? etds_qc_now() : date(DATE_ATOM),
    ];
  }
}
