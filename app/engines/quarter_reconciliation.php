<?php
declare(strict_types=1);

if (!function_exists('etds_quarter_months')) {
  function etds_quarter_months(string $quarter): array {
    return match (strtoupper($quarter)) {
      'Q1' => [4, 5, 6],
      'Q2' => [7, 8, 9],
      'Q3' => [10, 11, 12],
      'Q4' => [1, 2, 3],
      default => [],
    };
  }
}

if (!function_exists('etds_quarter_reconciliation_run')) {
  function etds_quarter_reconciliation_run(array $deductees, array $case): array {
    $quarter = (string) ($case['quarter'] ?? '');
    $allowedMonths = etds_quarter_months($quarter);
    $monthlyTotals = [];
    $issues = [];
    foreach ($deductees as $deductee) {
      if (!is_array($deductee)) {
        continue;
      }
      $date = trim((string) ($deductee['deduction_date'] ?? ''));
      $recordId = (string) ($deductee['deductee_id'] ?? $deductee['record_id'] ?? 'DEDUCTEE');
      $amount = (float) str_replace(',', '', (string) ($deductee['tds_amount'] ?? 0));
      $month = null;
      if ($date !== '') {
        $time = strtotime($date);
        if ($time !== false) {
          $month = (int) date('n', $time);
          $monthlyTotals[$month] = ($monthlyTotals[$month] ?? 0.0) + $amount;
        }
      }
      if ($month !== null && $allowedMonths !== [] && !in_array($month, $allowedMonths, true)) {
        $issues[] = [
          'issue_id' => 'REC-QTR-' . substr(bin2hex(random_bytes(4)), 0, 8),
          'module' => 'quarter',
          'severity' => 'High',
          'record_reference' => $recordId,
          'field' => 'deduction_date',
          'message' => 'Deductee deduction date falls outside the selected return quarter.',
          'suggested_action' => 'Review the deduction date or move the record to the correct quarter case.',
          'status' => 'open',
        ];
      }
    }

    return [
      'summary' => [
        'quarter' => $quarter,
        'monthly_totals' => $monthlyTotals,
        'quarter_total' => round(array_sum($monthlyTotals), 2),
        'cross_quarter_consistency' => count($issues) === 0 ? 'stable' : 'review_required',
      ],
      'issues' => $issues,
    ];
  }
}
