<?php
declare(strict_types=1);

if (!function_exists('etds_deductee_reconciliation_run')) {
  function etds_deductee_reconciliation_run(array $deductees, array $payments, array $challans): array {
    $deducteeRows = is_array($deductees) ? $deductees : [];
    $paymentRows = is_array($payments) ? $payments : [];
    $challanRows = is_array($challans) ? $challans : [];
    $paymentMap = [];
    $challanMap = [];
    foreach ($paymentRows as $payment) {
      if (!is_array($payment)) {
        continue;
      }
      $paymentMap[strtoupper(trim((string) ($payment['pan'] ?? '')))][] = $payment;
    }
    foreach ($challanRows as $challan) {
      if (!is_array($challan)) {
        continue;
      }
      $challanMap[trim((string) ($challan['challan_reference'] ?? ''))] = $challan;
    }

    $rows = [];
    $issues = [];
    $matched = 0;
    $partial = 0;
    $unmatched = 0;
    foreach ($deducteeRows as $deductee) {
      if (!is_array($deductee)) {
        continue;
      }
      $recordId = (string) ($deductee['deductee_id'] ?? $deductee['record_id'] ?? 'DEDUCTEE');
      $pan = strtoupper(trim((string) ($deductee['pan'] ?? '')));
      $amount = (float) str_replace(',', '', (string) ($deductee['tds_amount'] ?? 0));
      $challanReference = trim((string) ($deductee['challan_reference'] ?? ''));
      $paymentStatus = isset($paymentMap[$pan]) && $pan !== '' ? 'matched' : 'unmatched';
      $challanStatus = $challanReference !== '' && isset($challanMap[$challanReference]) ? 'matched' : ($challanReference === '' ? 'missing' : 'unmatched');
      $status = ($paymentStatus === 'matched' && $challanStatus === 'matched' && $amount > 0) ? 'matched' : (($paymentStatus === 'matched' || $challanStatus === 'matched') ? 'partially_matched' : 'unmatched');
      if ($status === 'matched') {
        $matched++;
      } elseif ($status === 'partially_matched') {
        $partial++;
      } else {
        $unmatched++;
      }

      if ($paymentStatus !== 'matched') {
        $issues[] = [
          'issue_id' => 'REC-DED-' . substr(bin2hex(random_bytes(4)), 0, 8),
          'module' => 'deductee',
          'severity' => 'Medium',
          'record_reference' => $recordId,
          'field' => 'pan',
          'message' => 'No supporting payment record was matched to the deductee PAN.',
          'suggested_action' => 'Confirm payment source data or correct the deductee PAN mapping.',
          'status' => 'open',
        ];
      }
      if ($challanStatus !== 'matched') {
        $issues[] = [
          'issue_id' => 'REC-DED-' . substr(bin2hex(random_bytes(4)), 0, 8),
          'module' => 'deductee',
          'severity' => $challanStatus === 'missing' ? 'High' : 'Medium',
          'record_reference' => $recordId,
          'field' => 'challan_reference',
          'message' => $challanStatus === 'missing' ? 'Deductee record is missing challan mapping.' : 'Deductee challan reference does not match any challan register entry.',
          'suggested_action' => 'Map the deductee row to the correct challan reference.',
          'status' => 'open',
        ];
      }
      if ($amount <= 0) {
        $issues[] = [
          'issue_id' => 'REC-DED-' . substr(bin2hex(random_bytes(4)), 0, 8),
          'module' => 'deductee',
          'severity' => 'Medium',
          'record_reference' => $recordId,
          'field' => 'tds_amount',
          'message' => 'Deductee TDS amount is zero or negative.',
          'suggested_action' => 'Review the TDS amount and confirm the corrected value.',
          'status' => 'open',
        ];
      }

      $rows[] = $deductee + [
        'payment_status' => $paymentStatus,
        'challan_status' => $challanStatus,
        'reconciliation_status' => $status,
        'matched_payment_count' => count($paymentMap[$pan] ?? []),
      ];
    }

    return [
      'rows' => $rows,
      'summary' => [
        'total_deductees' => count($rows),
        'matched' => $matched,
        'partially_matched' => $partial,
        'unmatched' => $unmatched,
      ],
      'issues' => $issues,
    ];
  }
}
