<?php
declare(strict_types=1);

if (!function_exists('etds_rule_registry_directory')) {
  function etds_rule_registry_directory(): string {
    return defined('ETDS_QC_STORAGE_ROOT')
      ? ETDS_QC_STORAGE_ROOT . '/rules'
      : dirname(__DIR__, 2) . '/storage/etds-qc/rules';
  }
}

if (!function_exists('etds_rule_registry_files')) {
  function etds_rule_registry_files(): array {
    return [
      'pan_rules.json',
      'challan_rules.json',
      'deductee_rules.json',
      'salary_rules.json',
      'section_rules.json',
      'amount_rules.json',
      'date_rules.json',
      'return_rules.json',
      'custom_rules.json',
    ];
  }
}

if (!function_exists('etds_rule_registry_defaults')) {
  function etds_rule_registry_defaults(): array {
    $today = '2026-06-27';
    return [
      'pan_rules.json' => [
        [
          'rule_id' => 'PAN-001',
          'rule_name' => 'Deductee PAN Required',
          'category' => 'PAN',
          'severity' => 'High',
          'description' => 'Each deductee row must include a PAN value.',
          'condition' => ['scope' => 'deductee', 'field' => 'pan', 'operator' => 'required'],
          'expected_result' => 'PAN is present',
          'failure_message' => 'PAN is missing for the deductee row.',
          'suggested_treatment' => 'Review the source document and confirm the PAN for the deductee.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
        [
          'rule_id' => 'PAN-002',
          'rule_name' => 'Deductee PAN Format',
          'category' => 'PAN',
          'severity' => 'Critical',
          'description' => 'Deductee PAN must match the statutory PAN format.',
          'condition' => ['scope' => 'deductee', 'field' => 'pan', 'operator' => 'regex', 'pattern' => '^[A-Z]{5}[0-9]{4}[A-Z]$'],
          'expected_result' => 'PAN matches format',
          'failure_message' => 'PAN format is invalid.',
          'suggested_treatment' => 'Compare the extracted PAN with the document and correct the source if needed.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'challan_rules.json' => [
        [
          'rule_id' => 'CHL-001',
          'rule_name' => 'Challan Reference Required',
          'category' => 'Challan',
          'severity' => 'High',
          'description' => 'Each challan record should have a challan reference.',
          'condition' => ['scope' => 'challan', 'field' => 'challan_reference', 'operator' => 'required'],
          'expected_result' => 'Challan reference exists',
          'failure_message' => 'Challan reference is missing.',
          'suggested_treatment' => 'Review the challan source and confirm the challan reference.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
        [
          'rule_id' => 'CHL-002',
          'rule_name' => 'BSR Format',
          'category' => 'Challan',
          'severity' => 'Medium',
          'description' => 'BSR code should contain exactly seven digits when provided.',
          'condition' => ['scope' => 'challan', 'field' => 'bsr_code', 'operator' => 'regex_optional', 'pattern' => '^[0-9]{7}$'],
          'expected_result' => 'BSR code format is valid',
          'failure_message' => 'BSR code format is invalid.',
          'suggested_treatment' => 'Check the challan image or PDF and confirm the BSR code.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'deductee_rules.json' => [
        [
          'rule_id' => 'DED-001',
          'rule_name' => 'Deductee Name Required',
          'category' => 'Deductee',
          'severity' => 'High',
          'description' => 'A deductee row must include the deductee name.',
          'condition' => ['scope' => 'deductee', 'field' => 'deductee_name', 'operator' => 'required'],
          'expected_result' => 'Deductee name is present',
          'failure_message' => 'Deductee name is missing.',
          'suggested_treatment' => 'Check the extracted row against the source document.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'salary_rules.json' => [
        [
          'rule_id' => 'SAL-001',
          'rule_name' => 'Salary Amount Positive',
          'category' => 'Salary',
          'severity' => 'High',
          'description' => 'Extracted salary amount should be greater than zero.',
          'condition' => ['scope' => 'salary', 'field' => 'amount', 'operator' => 'positive_number'],
          'expected_result' => 'Salary amount is positive',
          'failure_message' => 'Salary amount is not positive.',
          'suggested_treatment' => 'Inspect the extracted amount and compare it with the source row.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'section_rules.json' => [
        [
          'rule_id' => 'SEC-001',
          'rule_name' => 'Section Code In Master',
          'category' => 'Section',
          'severity' => 'Low',
          'description' => 'Section code should exist in the section master when provided.',
          'condition' => ['scope' => 'challan', 'field' => 'section_code', 'operator' => 'in_master', 'master' => 'sections', 'master_key' => 'code', 'optional' => true],
          'expected_result' => 'Section code is mapped in master',
          'failure_message' => 'Section code is not present in the master repository.',
          'suggested_treatment' => 'Confirm whether the extracted section code is correct and update the master if approved.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'amount_rules.json' => [
        [
          'rule_id' => 'AMT-001',
          'rule_name' => 'Deductee Amount Positive',
          'category' => 'Amount',
          'severity' => 'High',
          'description' => 'TDS amount should be greater than zero for each deductee.',
          'condition' => ['scope' => 'deductee', 'field' => 'tds_amount', 'operator' => 'positive_number'],
          'expected_result' => 'TDS amount is positive',
          'failure_message' => 'TDS amount is missing or not positive.',
          'suggested_treatment' => 'Compare the amount against the extracted source row.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'date_rules.json' => [
        [
          'rule_id' => 'DAT-001',
          'rule_name' => 'Deduction Date Format',
          'category' => 'Date',
          'severity' => 'Medium',
          'description' => 'Deduction date should be in YYYY-MM-DD format when populated.',
          'condition' => ['scope' => 'deductee', 'field' => 'deduction_date', 'operator' => 'date_format_optional'],
          'expected_result' => 'Date format is valid',
          'failure_message' => 'Deduction date format is invalid.',
          'suggested_treatment' => 'Review the source date and normalize the date pattern.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
        [
          'rule_id' => 'DAT-002',
          'rule_name' => 'Deduction Date In Financial Year',
          'category' => 'Financial Year',
          'severity' => 'Medium',
          'description' => 'Deduction date should fall inside the case financial year when available.',
          'condition' => ['scope' => 'deductee', 'field' => 'deduction_date', 'operator' => 'date_in_financial_year', 'financial_year_field' => 'financial_year'],
          'expected_result' => 'Date is inside financial year',
          'failure_message' => 'Deduction date falls outside the case financial year.',
          'suggested_treatment' => 'Confirm the financial year and extracted date before moving ahead.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'return_rules.json' => [
        [
          'rule_id' => 'RET-001',
          'rule_name' => 'Case TAN Required',
          'category' => 'TAN',
          'severity' => 'Critical',
          'description' => 'A case must have a TAN before validation can proceed.',
          'condition' => ['scope' => 'case', 'field' => 'tan', 'operator' => 'required'],
          'expected_result' => 'TAN is present',
          'failure_message' => 'TAN is missing from the case profile.',
          'suggested_treatment' => 'Complete the client profile with the correct TAN.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
        [
          'rule_id' => 'RET-002',
          'rule_name' => 'Case Quarter Valid',
          'category' => 'Quarter',
          'severity' => 'Low',
          'description' => 'Quarter should be one of Q1, Q2, Q3, or Q4.',
          'condition' => ['scope' => 'case', 'field' => 'quarter', 'operator' => 'in_list', 'values' => ['Q1', 'Q2', 'Q3', 'Q4']],
          'expected_result' => 'Quarter value is valid',
          'failure_message' => 'Quarter is invalid.',
          'suggested_treatment' => 'Check the case profile and update the quarter.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
        [
          'rule_id' => 'RET-003',
          'rule_name' => 'Financial Year Format',
          'category' => 'Financial Year',
          'severity' => 'Low',
          'description' => 'Financial year should follow the YYYY-YY pattern.',
          'condition' => ['scope' => 'case', 'field' => 'financial_year', 'operator' => 'regex', 'pattern' => '^[0-9]{4}-[0-9]{2}$'],
          'expected_result' => 'Financial year format is valid',
          'failure_message' => 'Financial year format is invalid.',
          'suggested_treatment' => 'Update the case profile with a valid financial year.',
          'enabled' => true,
          'version' => '1.0.0',
          'effective_date' => $today,
        ],
      ],
      'custom_rules.json' => [],
    ];
  }
}

if (!function_exists('etds_rule_registry_bootstrap')) {
  function etds_rule_registry_bootstrap(): void {
    $directory = etds_rule_registry_directory();
    if (!is_dir($directory)) {
      mkdir($directory, 0775, true);
    }
    foreach (etds_rule_registry_defaults() as $fileName => $payload) {
      $target = $directory . '/' . $fileName;
      if (!is_file($target)) {
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (is_string($json)) {
          file_put_contents($target, $json . PHP_EOL, LOCK_EX);
        }
      }
    }
  }
}

if (!function_exists('etds_rule_registry_load')) {
  function etds_rule_registry_load(): array {
    etds_rule_registry_bootstrap();
    $rules = [];
    foreach (etds_rule_registry_files() as $fileName) {
      $payload = [];
      $file = etds_rule_registry_directory() . '/' . $fileName;
      if (is_file($file)) {
        $raw = file_get_contents($file);
        $decoded = is_string($raw) ? json_decode($raw, true) : [];
        if (is_array($decoded)) {
          $payload = $decoded;
        }
      }
      foreach ($payload as $rule) {
        if (is_array($rule)) {
          $rules[] = $rule;
        }
      }
    }
    return $rules;
  }
}
