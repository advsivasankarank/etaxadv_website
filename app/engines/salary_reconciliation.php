<?php
declare(strict_types=1);

if (!function_exists('etds_salary_reconciliation_run')) {
  function etds_salary_reconciliation_run(array $salaryRows, array $deductees, array $challans): array {
    $salaryRows = is_array($salaryRows) ? $salaryRows : [];
    $deductees = is_array($deductees) ? $deductees : [];
    $challans = is_array($challans) ? $challans : [];

    $salaryTotal = array_sum(array_map(static fn(array $row): float => (float) str_replace(',', '', (string) ($row['amount'] ?? 0)), $salaryRows));
    $tdsDeducted = array_sum(array_map(static fn(array $row): float => (float) str_replace(',', '', (string) ($row['tds_amount'] ?? 0)), $deductees));
    $tdsDeposited = array_sum(array_map(static fn(array $row): float => (float) ($row['total_available'] ?? $row['available_amount'] ?? 0), $challans));
    $variance = round($tdsDeposited - $tdsDeducted, 2);

    $issues = [];
    if ($variance !== 0.0) {
      $issues[] = [
        'issue_id' => 'REC-SAL-' . substr(bin2hex(random_bytes(4)), 0, 8),
        'module' => 'salary',
        'severity' => abs($variance) > 1 ? 'High' : 'Medium',
        'record_reference' => 'SALARY-SUMMARY',
        'field' => 'variance',
        'message' => 'Salary-related TDS deducted and deposited totals are not aligned.',
        'suggested_action' => 'Compare salary rows, deductee totals, and challan totals before QC certification.',
        'status' => 'open',
      ];
    }

    return [
      'summary' => [
        'salary_total' => round($salaryTotal, 2),
        'tax_deducted' => round($tdsDeducted, 2),
        'tax_deposited' => round($tdsDeposited, 2),
        'variance' => $variance,
      ],
      'issues' => $issues,
    ];
  }
}
