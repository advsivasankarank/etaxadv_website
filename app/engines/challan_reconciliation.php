<?php
declare(strict_types=1);

if (!function_exists('etds_challan_reconciliation_run')) {
  function etds_challan_reconciliation_run(array $challans, array $deductees): array {
    $challanRows = is_array($challans) ? $challans : [];
    $deducteeRows = is_array($deductees) ? $deductees : [];
    $allocations = [];
    $issues = [];

    foreach ($deducteeRows as $deductee) {
      if (!is_array($deductee)) {
        continue;
      }
      $recordId = (string) ($deductee['deductee_id'] ?? $deductee['record_id'] ?? 'DEDUCTEE');
      $reference = trim((string) ($deductee['challan_reference'] ?? ''));
      $amount = (float) str_replace(',', '', (string) ($deductee['tds_amount'] ?? 0));
      if ($reference === '') {
        $issues[] = [
          'issue_id' => 'REC-CHL-' . substr(bin2hex(random_bytes(4)), 0, 8),
          'module' => 'challan',
          'severity' => 'High',
          'record_reference' => $recordId,
          'field' => 'challan_reference',
          'message' => 'Deductee record is missing challan allocation.',
          'suggested_action' => 'Assign the deductee row to the correct challan reference.',
          'status' => 'open',
        ];
        continue;
      }
      $allocations[$reference] = ($allocations[$reference] ?? 0.0) + $amount;
    }

    $rows = [];
    $matched = 0;
    $partial = 0;
    $unmatched = 0;
    foreach ($challanRows as $challan) {
      if (!is_array($challan)) {
        continue;
      }
      $reference = (string) ($challan['challan_reference'] ?? '');
      $available = round((float) ($challan['total_available'] ?? 0), 2);
      $allocated = round((float) ($allocations[$reference] ?? 0), 2);
      $unused = round(max(0, $available - $allocated), 2);
      $short = round(max(0, $available - $allocated), 2);
      $over = round(max(0, $allocated - $available), 2);
      $matchPercent = $available > 0 ? max(0, min(100, (int) round(($allocated / $available) * 100))) : ($allocated > 0 ? 0 : 100);
      $status = $over > 0 ? 'unmatched' : ($unused > 0 ? 'partially_matched' : 'matched');
      if ($status === 'matched') {
        $matched++;
      } elseif ($status === 'partially_matched') {
        $partial++;
      } else {
        $unmatched++;
      }

      if ($over > 0) {
        $issues[] = [
          'issue_id' => 'REC-CHL-' . substr(bin2hex(random_bytes(4)), 0, 8),
          'module' => 'challan',
          'severity' => 'Critical',
          'record_reference' => $reference !== '' ? $reference : ((string) ($challan['challan_id'] ?? 'CHALLAN')),
          'field' => 'allocated_total',
          'message' => 'Allocated amount exceeds available challan amount.',
          'suggested_action' => 'Reduce deductee allocations or correct challan amount before QC certification.',
          'status' => 'open',
        ];
      } elseif ($unused > 0) {
        $issues[] = [
          'issue_id' => 'REC-CHL-' . substr(bin2hex(random_bytes(4)), 0, 8),
          'module' => 'challan',
          'severity' => 'Medium',
          'record_reference' => $reference !== '' ? $reference : ((string) ($challan['challan_id'] ?? 'CHALLAN')),
          'field' => 'unused_amount',
          'message' => 'Challan amount is not fully utilized.',
          'suggested_action' => 'Review pending deductee allocations against this challan.',
          'status' => 'open',
        ];
      }

      $rows[] = $challan + [
        'allocated_amount' => $allocated,
        'available_amount' => $available,
        'unused_amount' => $unused,
        'short_allocation' => $short,
        'over_allocation' => $over,
        'match_percent' => $matchPercent,
        'reconciliation_status' => $status,
      ];
    }

    return [
      'rows' => $rows,
      'summary' => [
        'total_challans' => count($rows),
        'matched' => $matched,
        'partially_matched' => $partial,
        'unmatched' => $unmatched,
        'available_total' => round(array_sum(array_map(static fn(array $row): float => (float) ($row['available_amount'] ?? 0), $rows)), 2),
        'allocated_total' => round(array_sum(array_map(static fn(array $row): float => (float) ($row['allocated_amount'] ?? 0), $rows)), 2),
        'unused_total' => round(array_sum(array_map(static fn(array $row): float => (float) ($row['unused_amount'] ?? 0), $rows)), 2),
      ],
      'issues' => $issues,
    ];
  }
}
