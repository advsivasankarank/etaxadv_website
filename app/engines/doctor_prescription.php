<?php
declare(strict_types=1);

if (!function_exists('etds_doctor_prescription_minutes')) {
  function etds_doctor_prescription_minutes(array $diagnosis): int {
    $base = match ((string) ($diagnosis['priority'] ?? 'Information')) {
      'Critical' => 4,
      'High' => 3,
      'Medium' => 2,
      'Low' => 1,
      default => 1,
    };
    return max(1, $base * max(1, (int) ($diagnosis['affected_record_count'] ?? 1)));
  }
}

if (!function_exists('etds_doctor_prescription_build')) {
  function etds_doctor_prescription_build(array $diagnoses, array $scores): array {
    $currentScore = (int) ($scores['overall_data_health_score'] ?? 0);
    $prescriptions = [];
    $runningScore = $currentScore;

    foreach ($diagnoses as $index => $diagnosis) {
      $minutes = etds_doctor_prescription_minutes($diagnosis);
      $delta = match ((string) ($diagnosis['priority'] ?? 'Information')) {
        'Critical' => 14,
        'High' => 10,
        'Medium' => 6,
        'Low' => 3,
        default => 1,
      };
      $expected = min(100, $runningScore + $delta);
      $prescriptions[] = [
        'prescription_id' => 'RX-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
        'priority_label' => 'Priority ' . ($index + 1),
        'priority' => (string) ($diagnosis['priority'] ?? 'Information'),
        'title' => 'Resolve ' . strtolower((string) ($diagnosis['diagnosis'] ?? 'diagnosis cluster')),
        'instruction' => 'Correct ' . (int) ($diagnosis['affected_record_count'] ?? 0) . ' affected record(s) in the ' . (string) ($diagnosis['diagnosis'] ?? 'current') . ' group.',
        'estimated_time_minutes' => $minutes,
        'expected_health_score_before' => $runningScore,
        'expected_health_score_after' => $expected,
        'recommended_resolution' => (string) ($diagnosis['recommended_resolution'] ?? 'Review and correct affected records.'),
      ];
      $runningScore = $expected;
    }

    return $prescriptions;
  }
}
