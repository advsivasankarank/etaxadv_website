<?php
declare(strict_types=1);

if (!function_exists('etds_validation_output_schema')) {
  function etds_validation_output_schema(): array {
    return [
      'summary' => [
        'total_records' => 0,
        'quality_score' => 0,
        'critical' => 0,
        'high' => 0,
        'medium' => 0,
        'low' => 0,
        'information' => 0,
        'total_findings' => 0,
        'ready_status' => false,
        'last_validated_on' => null,
      ],
      'findings' => [],
    ];
  }
}

if (!function_exists('etds_validation_engine_run')) {
  function etds_validation_engine_run(string $sessionId, array $user): array {
    $rules = function_exists('etds_rule_registry_load') ? etds_rule_registry_load() : [];
    $context = function_exists('etds_rule_engine_context') ? etds_rule_engine_context($sessionId) : [];
    $findings = function_exists('etds_rule_executor_run') ? etds_rule_executor_run($rules, $context) : [];
    $summary = function_exists('etds_rule_engine_summary') ? etds_rule_engine_summary($findings, $context) : etds_validation_output_schema()['summary'];

    $payload = [
      'summary' => $summary,
      'findings' => $findings,
    ];

    if (function_exists('etds_qc_write_json')) {
      etds_qc_write_json(etds_qc_session_file($sessionId, 'validation.json'), $payload);
    }

    $session = function_exists('etds_qc_find_session') ? etds_qc_find_session($sessionId) : null;
    if (is_array($session) && function_exists('etds_qc_save_session')) {
      $session['quality_score'] = (int) ($summary['quality_score'] ?? 0);
      $session['status'] = ((bool) ($summary['ready_status'] ?? false)) ? 'qc_in_progress' : 'validation_running';
      $session['last_action'] = 'validation_completed';
      etds_qc_save_session($session);
    }

    if (function_exists('etds_qc_audit')) {
      etds_qc_audit($sessionId, $user, 'validation_completed', 'Validation engine executed', [], [
        'quality_score' => $summary['quality_score'] ?? 0,
        'total_findings' => $summary['total_findings'] ?? 0,
      ]);
    }

    return $payload;
  }
}
