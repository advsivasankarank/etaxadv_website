<?php
declare(strict_types=1);

if (!function_exists('etds_doctor_priority_weight')) {
  function etds_doctor_priority_weight(string $priority): int {
    return match ($priority) {
      'Critical' => 500,
      'High' => 300,
      'Medium' => 150,
      'Low' => 60,
      default => 20,
    };
  }
}

if (!function_exists('etds_doctor_priority_sort')) {
  function etds_doctor_priority_sort(array $diagnoses): array {
    usort($diagnoses, static function (array $left, array $right): int {
      $leftScore = etds_doctor_priority_weight((string) ($left['priority'] ?? 'Information')) + ((int) ($left['affected_record_count'] ?? 0) * 10);
      $rightScore = etds_doctor_priority_weight((string) ($right['priority'] ?? 'Information')) + ((int) ($right['affected_record_count'] ?? 0) * 10);
      return $rightScore <=> $leftScore;
    });

    foreach ($diagnoses as $index => &$diagnosis) {
      $diagnosis['priority_rank'] = $index + 1;
      $diagnosis['business_impact_score'] = etds_doctor_priority_weight((string) ($diagnosis['priority'] ?? 'Information')) + ((int) ($diagnosis['affected_record_count'] ?? 0) * 10);
    }
    unset($diagnosis);

    return $diagnoses;
  }
}
