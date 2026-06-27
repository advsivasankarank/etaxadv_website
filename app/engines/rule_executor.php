<?php
declare(strict_types=1);

if (!function_exists('etds_rule_executor_normalize_value')) {
  function etds_rule_executor_normalize_value(mixed $value): string {
    if (is_array($value)) {
      if (array_key_exists('value', $value)) {
        return trim((string) $value['value']);
      }
      return trim((string) json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
    return trim((string) $value);
  }
}

if (!function_exists('etds_rule_executor_extract_field')) {
  function etds_rule_executor_extract_field(array $target, string $field): string {
    if (array_key_exists($field, $target)) {
      return etds_rule_executor_normalize_value($target[$field]);
    }
    if (isset($target['fields']) && is_array($target['fields'])) {
      foreach ($target['fields'] as $item) {
        if (($item['field'] ?? '') === $field) {
          return etds_rule_executor_normalize_value($item['value'] ?? '');
        }
      }
    }
    if (isset($target['values']) && is_array($target['values']) && array_key_exists($field, $target['values'])) {
      return etds_rule_executor_normalize_value($target['values'][$field]);
    }
    return '';
  }
}

if (!function_exists('etds_rule_executor_resolve_targets')) {
  function etds_rule_executor_resolve_targets(array $context, string $scope): array {
    return match ($scope) {
      'case' => [$context['case'] ?? []],
      'deductor' => [$context['deductor'] ?? []],
      'deductee' => is_array($context['deductees'] ?? null) ? $context['deductees'] : [],
      'challan' => is_array($context['challans'] ?? null) ? $context['challans'] : [],
      'salary' => is_array($context['salary'] ?? null) ? $context['salary'] : [],
      'payment' => is_array($context['payments'] ?? null) ? $context['payments'] : [],
      'extraction' => [$context['extraction_summary'] ?? []],
      default => [],
    };
  }
}

if (!function_exists('etds_rule_executor_record_reference')) {
  function etds_rule_executor_record_reference(string $scope, array $target): string {
    return match ($scope) {
      'case', 'deductor' => (string) ($target['session_id'] ?? $target['case_id'] ?? 'CASE'),
      'deductee' => (string) ($target['deductee_id'] ?? $target['document_id'] ?? 'DEDUCTEE'),
      'challan' => (string) ($target['challan_id'] ?? $target['document_id'] ?? 'CHALLAN'),
      'salary' => (string) ($target['salary_id'] ?? $target['document_id'] ?? 'SALARY'),
      'payment' => (string) ($target['payment_id'] ?? $target['document_id'] ?? 'PAYMENT'),
      'extraction' => 'EXTRACTION-SUMMARY',
      default => 'RECORD',
    };
  }
}

if (!function_exists('etds_rule_executor_evaluate')) {
  function etds_rule_executor_evaluate(array $rule, array $target, array $context): bool {
    $condition = is_array($rule['condition'] ?? null) ? $rule['condition'] : [];
    $field = (string) ($condition['field'] ?? '');
    $operator = (string) ($condition['operator'] ?? 'required');
    $value = $field !== '' ? etds_rule_executor_extract_field($target, $field) : '';
    $optional = (bool) ($condition['optional'] ?? false);

    if ($optional && $value === '') {
      return true;
    }

    return match ($operator) {
      'required' => $value !== '',
      'regex' => $value !== '' && preg_match('/' . ($condition['pattern'] ?? '') . '/', $value) === 1,
      'regex_optional' => $value === '' || preg_match('/' . ($condition['pattern'] ?? '') . '/', $value) === 1,
      'positive_number' => is_numeric(str_replace(',', '', $value)) && (float) str_replace(',', '', $value) > 0,
      'date_format_optional' => $value === '' || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1,
      'in_list' => in_array(strtoupper($value), array_map('strtoupper', (array) ($condition['values'] ?? [])), true),
      'in_master' => (function () use ($condition, $value): bool {
        if ($value === '') {
          return true;
        }
        if (!function_exists('etds_qc_master')) {
          return false;
        }
        $master = etds_qc_master((string) ($condition['master'] ?? ''), []);
        $key = (string) ($condition['master_key'] ?? 'code');
        foreach ($master as $item) {
          if (strcasecmp((string) ($item[$key] ?? ''), $value) === 0) {
            return true;
          }
        }
        return false;
      })(),
      'date_in_financial_year' => (function () use ($target, $context, $field): bool {
        $dateValue = etds_rule_executor_extract_field($target, $field);
        $fy = (string) (($context['case']['financial_year'] ?? '') ?: ($target['financial_year'] ?? ''));
        if ($dateValue === '' || $fy === '') {
          return true;
        }
        if (!function_exists('etds_qc_date_in_financial_year')) {
          return false;
        }
        try {
          $date = new DateTimeImmutable($dateValue, new DateTimeZone('Asia/Calcutta'));
        } catch (Throwable) {
          return false;
        }
        return etds_qc_date_in_financial_year($date, $fy);
      })(),
      default => true,
    };
  }
}

if (!function_exists('etds_rule_executor_build_finding')) {
  function etds_rule_executor_build_finding(array $rule, string $recordReference): array {
    $ruleId = (string) ($rule['rule_id'] ?? '');
    return [
      'finding_id' => $ruleId !== '' ? $ruleId . '::' . $recordReference : ('FINDING::' . $recordReference),
      'rule_id' => $ruleId,
      'severity' => (string) ($rule['severity'] ?? 'Information'),
      'record_reference' => $recordReference,
      'message' => (string) ($rule['failure_message'] ?? 'Validation rule failed.'),
      'suggested_action' => (string) ($rule['suggested_treatment'] ?? 'Review the extracted data.'),
      'status' => 'open',
      'timestamp' => function_exists('etds_qc_now') ? etds_qc_now() : date(DATE_ATOM),
      'category' => (string) ($rule['category'] ?? ''),
      'rule_name' => (string) ($rule['rule_name'] ?? ''),
    ];
  }
}

if (!function_exists('etds_rule_executor_run')) {
  function etds_rule_executor_run(array $rules, array $context): array {
    $findings = [];
    foreach ($rules as $rule) {
      if (!is_array($rule) || (($rule['enabled'] ?? true) !== true)) {
        continue;
      }
      $scope = (string) (($rule['condition']['scope'] ?? 'case'));
      $targets = etds_rule_executor_resolve_targets($context, $scope);
      if ($targets === []) {
        continue;
      }
      foreach ($targets as $target) {
        if (!is_array($target)) {
          continue;
        }
        if (!etds_rule_executor_evaluate($rule, $target, $context)) {
          $findings[] = etds_rule_executor_build_finding($rule, etds_rule_executor_record_reference($scope, $target));
        }
      }
    }
    return $findings;
  }
}
