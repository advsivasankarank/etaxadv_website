<?php
declare(strict_types=1);

if (!function_exists('etds_reconciliation_score_from_issues')) {
  function etds_reconciliation_score_from_issues(array $issues): int {
    $score = 100;
    foreach ($issues as $issue) {
      $severity = (string) ($issue['severity'] ?? 'Low');
      $score -= match ($severity) {
        'Critical' => 18,
        'High' => 12,
        'Medium' => 6,
        'Low' => 3,
        default => 1,
      };
    }
    return max(0, $score);
  }
}

if (!function_exists('etds_financial_health_build')) {
  function etds_financial_health_build(array $challanModule, array $deducteeModule, array $salaryModule, array $quarterModule): array {
    $challanScore = etds_reconciliation_score_from_issues((array) ($challanModule['issues'] ?? []));
    $deducteeScore = etds_reconciliation_score_from_issues((array) ($deducteeModule['issues'] ?? []));
    $salaryScore = etds_reconciliation_score_from_issues((array) ($salaryModule['issues'] ?? []));
    $quarterScore = etds_reconciliation_score_from_issues((array) ($quarterModule['issues'] ?? []));
    $overall = (int) round(($challanScore + $deducteeScore + $salaryScore + $quarterScore) / 4);
    return [
      'challan_score' => $challanScore,
      'deductee_score' => $deducteeScore,
      'salary_score' => $salaryScore,
      'quarter_score' => $quarterScore,
      'financial_health_score' => $overall,
    ];
  }
}
