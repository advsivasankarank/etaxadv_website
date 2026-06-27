<?php
declare(strict_types=1);

if (!function_exists('etds_doctor_output_schema')) {
  function etds_doctor_output_schema(): array {
    return [
      'summary' => [
        'top_priority' => 'Information',
        'top_diagnosis' => 'Diagnosis Pending',
        'expected_improvement' => '0 -> 0',
        'estimated_time_minutes' => 0,
        'readiness' => 'Not Ready',
        'last_generated_on' => null,
      ],
      'diagnosis' => [],
      'priority' => [],
      'prescription' => [],
      'health_scores' => [
        'extraction_score' => 0,
        'validation_score' => 0,
        'completeness_score' => 0,
        'consistency_score' => 0,
        'overall_data_health_score' => 0,
        'methodology' => [],
      ],
      'recommendations' => [],
      'readiness' => [
        'status' => 'Not Ready',
        'reason' => 'Validation findings are not available yet.',
      ],
    ];
  }
}

if (!function_exists('etds_doctor_engine_context')) {
  function etds_doctor_engine_context(string $sessionId): array {
    return [
      'case' => function_exists('etds_qc_load_json') ? etds_qc_load_json(etds_qc_session_file($sessionId, 'case.json'), []) : [],
      'deductees' => function_exists('etds_qc_load_json') ? (array) (etds_qc_load_json(etds_qc_session_file($sessionId, 'deductees.json'), ['deductees' => []])['deductees'] ?? []) : [],
      'challans' => function_exists('etds_qc_load_json') ? (array) (etds_qc_load_json(etds_qc_session_file($sessionId, 'challans.json'), ['challans' => []])['challans'] ?? []) : [],
      'salary' => function_exists('etds_qc_load_json') ? (array) (etds_qc_load_json(etds_qc_session_file($sessionId, 'salary.json'), ['rows' => []])['rows'] ?? []) : [],
      'payments' => function_exists('etds_qc_load_json') ? (array) (etds_qc_load_json(etds_qc_session_file($sessionId, 'payments.json'), ['payments' => []])['payments'] ?? []) : [],
      'extraction' => function_exists('etds_qc_load_json') ? (array) (etds_qc_load_json(etds_qc_session_file($sessionId, 'extraction.json'), ['summary' => []])['summary'] ?? []) : [],
    ];
  }
}

if (!function_exists('etds_doctor_readiness_build')) {
  function etds_doctor_readiness_build(array $validation, array $scores): array {
    $summary = is_array($validation['summary'] ?? null) ? $validation['summary'] : [];
    $critical = (int) ($summary['critical'] ?? 0);
    $high = (int) ($summary['high'] ?? 0);
    $medium = (int) ($summary['medium'] ?? 0);
    $overall = (int) ($scores['overall_data_health_score'] ?? 0);

    if ($critical > 0 || $high > 0) {
      return [
        'status' => 'Not Ready',
        'reason' => 'Critical or high-severity findings are still open in the validation output.',
      ];
    }
    if ($medium > 0 || $overall < 85) {
      return [
        'status' => 'Ready After Corrections',
        'reason' => 'High-severity findings are clear, but moderate findings or health score thresholds still need attention.',
      ];
    }
    return [
      'status' => 'Ready for QC Certification',
      'reason' => 'The current Doctor intelligence profile shows no blocking severity and healthy score coverage.',
    ];
  }
}

if (!function_exists('etds_doctor_engine_run')) {
  function etds_doctor_engine_run(string $sessionId, array $user): array {
    $validation = function_exists('etds_qc_load_json')
      ? etds_qc_load_json(etds_qc_session_file($sessionId, 'validation.json'), function_exists('etds_qc_default_validation') ? etds_qc_default_validation() : ['summary' => [], 'findings' => []])
      : ['summary' => [], 'findings' => []];
    $context = etds_doctor_engine_context($sessionId);
    $diagnoses = function_exists('etds_doctor_diagnosis_build') ? etds_doctor_diagnosis_build($validation, $context) : [];
    $prioritized = function_exists('etds_doctor_priority_sort') ? etds_doctor_priority_sort($diagnoses) : $diagnoses;
    $scores = function_exists('etds_doctor_score_build') ? etds_doctor_score_build($context, $validation) : etds_doctor_output_schema()['health_scores'];
    $prescriptions = function_exists('etds_doctor_prescription_build') ? etds_doctor_prescription_build($prioritized, $scores) : [];
    $readiness = etds_doctor_readiness_build($validation, $scores);

    $topDiagnosis = $prioritized[0] ?? null;
    $topPrescription = $prescriptions[0] ?? null;
    $payload = [
      'summary' => [
        'top_priority' => (string) ($topDiagnosis['priority'] ?? 'Information'),
        'top_diagnosis' => (string) ($topDiagnosis['diagnosis'] ?? 'Diagnosis Complete'),
        'expected_improvement' => $topPrescription ? ((string) ($topPrescription['expected_health_score_before'] ?? 0) . ' -> ' . (string) ($topPrescription['expected_health_score_after'] ?? 0)) : ((string) ($scores['overall_data_health_score'] ?? 0) . ' -> ' . (string) ($scores['overall_data_health_score'] ?? 0)),
        'estimated_time_minutes' => (int) ($topPrescription['estimated_time_minutes'] ?? 0),
        'readiness' => (string) ($readiness['status'] ?? 'Not Ready'),
        'last_generated_on' => function_exists('etds_qc_now') ? etds_qc_now() : date(DATE_ATOM),
      ],
      'diagnosis' => $prioritized,
      'priority' => array_map(static fn(array $diagnosis): array => [
        'diagnosis_id' => (string) ($diagnosis['diagnosis_id'] ?? ''),
        'diagnosis' => (string) ($diagnosis['diagnosis'] ?? ''),
        'priority' => (string) ($diagnosis['priority'] ?? 'Information'),
        'priority_rank' => (int) ($diagnosis['priority_rank'] ?? 0),
        'business_impact_score' => (int) ($diagnosis['business_impact_score'] ?? 0),
      ], $prioritized),
      'prescription' => $prescriptions,
      'health_scores' => $scores,
      'recommendations' => array_map(static fn(array $diagnosis): array => [
        'diagnosis' => (string) ($diagnosis['diagnosis'] ?? ''),
        'recommended_resolution' => (string) ($diagnosis['recommended_resolution'] ?? ''),
        'likely_cause' => (string) ($diagnosis['likely_cause'] ?? ''),
      ], $prioritized),
      'readiness' => $readiness,
    ];

    if (function_exists('etds_qc_write_json')) {
      etds_qc_write_json(etds_qc_session_file($sessionId, 'doctor.json'), $payload);
      $qcPayload = function_exists('etds_qc_load_json')
        ? etds_qc_load_json(etds_qc_session_file($sessionId, 'qc.json'), function_exists('etds_qc_default_qc') ? etds_qc_default_qc() : ['status' => 'not_started', 'certificate' => null, 'exports' => []])
        : ['status' => 'not_started', 'certificate' => null, 'exports' => []];
      $qcPayload['status'] = $readiness['status'] === 'Ready for QC Certification' ? 'ready_for_qc_certification' : 'doctor_review_required';
      $qcPayload['certificate'] = [
        'status' => $readiness['status'],
        'reason' => $readiness['reason'],
        'generated_on' => $payload['summary']['last_generated_on'],
        'overall_data_health_score' => $scores['overall_data_health_score'] ?? 0,
      ];
      etds_qc_write_json(etds_qc_session_file($sessionId, 'qc.json'), $qcPayload);
    }

    if (function_exists('etds_qc_find_session') && function_exists('etds_qc_save_session')) {
      $session = etds_qc_find_session($sessionId);
      if (is_array($session)) {
        $session['last_action'] = 'doctor_intelligence_completed';
        if (($scores['overall_data_health_score'] ?? 0) > 0) {
          $session['quality_score'] = (int) ($scores['overall_data_health_score'] ?? 0);
        }
        $session['status'] = $readiness['status'] === 'Ready for QC Certification' ? 'qc_completed' : 'qc_in_progress';
        etds_qc_save_session($session);
      }
    }

    if (function_exists('etds_qc_audit')) {
      etds_qc_audit($sessionId, $user, 'doctor_intelligence_completed', 'Doctor intelligence generated', [], [
        'top_priority' => $payload['summary']['top_priority'],
        'readiness' => $payload['summary']['readiness'],
        'overall_data_health_score' => $scores['overall_data_health_score'] ?? 0,
      ]);
    }

    return $payload;
  }
}
