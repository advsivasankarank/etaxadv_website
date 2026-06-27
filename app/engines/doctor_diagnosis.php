<?php
declare(strict_types=1);

if (!function_exists('etds_doctor_cluster_name')) {
  function etds_doctor_cluster_name(array $finding): string {
    $ruleName = strtolower((string) ($finding['rule_name'] ?? ''));
    $message = strtolower((string) ($finding['message'] ?? ''));
    $category = strtolower((string) ($finding['category'] ?? ''));

    return match (true) {
      str_contains($ruleName, 'pan') || str_contains($message, 'pan') => 'Invalid PAN Cluster',
      str_contains($ruleName, 'challan') || str_contains($message, 'challan') => 'Missing Challan Cluster',
      str_contains($ruleName, 'duplicate') || str_contains($message, 'duplicate') => 'Duplicate Deductee Cluster',
      str_contains($ruleName, 'section') || $category === 'section' => 'Incorrect Section Cluster',
      str_contains($ruleName, 'salary') || $category === 'salary' => 'Salary Data Issues',
      str_contains($ruleName, 'date') || $category === 'date' || $category === 'financial year' => 'Date Issues',
      str_contains($ruleName, 'amount') || $category === 'amount' => 'Amount Issues',
      str_contains($ruleName, 'quarter') || str_contains($ruleName, 'financial year') => 'Return Profile Issues',
      default => 'General Data Health Issues',
    };
  }
}

if (!function_exists('etds_doctor_likely_cause')) {
  function etds_doctor_likely_cause(string $cluster): string {
    return match ($cluster) {
      'Invalid PAN Cluster' => 'Extraction output or source working papers contain incomplete or misread PAN values.',
      'Missing Challan Cluster' => 'Supporting challan references were not captured in the uploaded bundle or mapped during extraction.',
      'Duplicate Deductee Cluster' => 'Repeated rows or merged source sheets introduced duplicate deductee entries.',
      'Incorrect Section Cluster' => 'Section coding differs between source files and the current master configuration.',
      'Salary Data Issues' => 'Salary-specific fields are incomplete or extracted into the wrong columns.',
      'Date Issues' => 'Date values are inconsistent across source files or not normalized to the case financial year.',
      'Amount Issues' => 'Numeric extraction is incomplete, non-positive, or formatted inconsistently in source records.',
      'Return Profile Issues' => 'Case profile values such as TAN, quarter, or financial year require correction before QC certification.',
      default => 'Multiple source and extraction inconsistencies are affecting the case health profile.',
    };
  }
}

if (!function_exists('etds_doctor_resolution')) {
  function etds_doctor_resolution(string $cluster): string {
    return match ($cluster) {
      'Invalid PAN Cluster' => 'Correct the PAN values in source records, then rerun validation and Doctor intelligence.',
      'Missing Challan Cluster' => 'Confirm challan references from the client bundle and update affected records before review.',
      'Duplicate Deductee Cluster' => 'Identify duplicate rows, keep the valid record, and remove or correct the redundant entries.',
      'Incorrect Section Cluster' => 'Compare extracted section codes with source documents and the section master before approval.',
      'Salary Data Issues' => 'Review salary rows in the extracted dataset and complete missing salary-specific fields.',
      'Date Issues' => 'Normalize date formats and ensure deduction dates fall inside the selected financial year.',
      'Amount Issues' => 'Verify the extracted amounts against source documents and correct numeric formatting issues.',
      'Return Profile Issues' => 'Update the case profile and return setup values so the case can proceed to QC certification.',
      default => 'Review the affected records, correct source data, and rerun the validation workflow.',
    };
  }
}

if (!function_exists('etds_doctor_diagnosis_build')) {
  function etds_doctor_diagnosis_build(array $validation, array $context): array {
    $findings = array_values(array_filter(
      is_array($validation['findings'] ?? null) ? $validation['findings'] : [],
      static fn(array $finding): bool => (string) ($finding['status'] ?? 'open') === 'open'
    ));

    $clusters = [];
    foreach ($findings as $finding) {
      $cluster = etds_doctor_cluster_name($finding);
      if (!isset($clusters[$cluster])) {
        $clusters[$cluster] = [
          'diagnosis_id' => 'DX-' . str_pad((string) (count($clusters) + 1), 4, '0', STR_PAD_LEFT),
          'diagnosis' => $cluster,
          'priority' => (string) ($finding['severity'] ?? 'Information'),
          'severity_counts' => ['Critical' => 0, 'High' => 0, 'Medium' => 0, 'Low' => 0, 'Information' => 0],
          'affected_records' => [],
          'findings' => [],
          'likely_cause' => etds_doctor_likely_cause($cluster),
          'recommended_resolution' => etds_doctor_resolution($cluster),
        ];
      }

      $severity = (string) ($finding['severity'] ?? 'Information');
      if (!array_key_exists($severity, $clusters[$cluster]['severity_counts'])) {
        $clusters[$cluster]['severity_counts'][$severity] = 0;
      }
      $clusters[$cluster]['severity_counts'][$severity]++;
      $clusters[$cluster]['affected_records'][] = (string) ($finding['record_reference'] ?? 'N/A');
      $clusters[$cluster]['findings'][] = [
        'finding_id' => (string) ($finding['finding_id'] ?? ''),
        'rule_id' => (string) ($finding['rule_id'] ?? ''),
        'rule_name' => (string) ($finding['rule_name'] ?? ''),
        'message' => (string) ($finding['message'] ?? ''),
        'severity' => $severity,
        'record_reference' => (string) ($finding['record_reference'] ?? 'N/A'),
        'suggested_action' => (string) ($finding['suggested_action'] ?? ''),
      ];
    }

    foreach ($clusters as &$cluster) {
      $cluster['affected_records'] = array_values(array_unique($cluster['affected_records']));
      $cluster['affected_record_count'] = count($cluster['affected_records']);
      $cluster['estimated_impact'] = $cluster['affected_record_count'] . ' record(s) are affected by this diagnosis cluster.';
      foreach (['Critical', 'High', 'Medium', 'Low', 'Information'] as $priority) {
        if (($cluster['severity_counts'][$priority] ?? 0) > 0) {
          $cluster['priority'] = $priority;
          break;
        }
      }
    }
    unset($cluster);

    return array_values($clusters);
  }
}
