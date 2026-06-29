# eTDSDoc V1 - Phase 4 Validation Rules Engine

## Scope Delivered

Phase 4 introduces a reusable Validation Rules Engine without redesigning the UI shell, navigation, Case Engine, Data Intake Engine, or AI Extraction Engine.

Implemented components:

- `app/engines/rule_registry.php`
- `app/engines/rule_executor.php`
- `app/engines/rule_engine.php`
- `app/engines/validation_engine.php`
- `storage/etds-qc/rules/*.json`

## Engine Architecture

### Rule Registry

The rule registry loads modular JSON rule packs from:

- `storage/etds-qc/rules/pan_rules.json`
- `storage/etds-qc/rules/challan_rules.json`
- `storage/etds-qc/rules/deductee_rules.json`
- `storage/etds-qc/rules/salary_rules.json`
- `storage/etds-qc/rules/section_rules.json`
- `storage/etds-qc/rules/amount_rules.json`
- `storage/etds-qc/rules/date_rules.json`
- `storage/etds-qc/rules/return_rules.json`
- `storage/etds-qc/rules/custom_rules.json`

Each rule contains:

- Rule ID
- Rule Name
- Category
- Severity
- Description
- Condition
- Expected Result
- Failure Message
- Suggested Treatment
- Enable / Disable
- Version
- Effective Date

### Rule Executor

The executor currently supports these operators:

- `required`
- `regex`
- `regex_optional`
- `positive_number`
- `date_format_optional`
- `in_list`
- `in_master`
- `date_in_financial_year`

### Validation Engine

The validation engine:

- loads all enabled rules
- builds a case-aware execution context
- evaluates rules against case, deductee, challan, salary, payment, and extraction scopes
- generates structured findings
- assigns severity
- writes `validation.json`

## Validation Output Schema

`validation.json` now contains:

```json
{
  "summary": {
    "total_records": 0,
    "quality_score": 0,
    "critical": 0,
    "high": 0,
    "medium": 0,
    "low": 0,
    "information": 0,
    "total_findings": 0,
    "ready_status": false,
    "last_validated_on": null
  },
  "findings": []
}
```

Each finding includes:

- `finding_id`
- `rule_id`
- `severity`
- `record_reference`
- `message`
- `suggested_action`
- `status`
- `timestamp`
- `category`
- `rule_name`

## Application Integration

Integrated changes:

- `fintech/etds-qc/bootstrap_runtime.php`
  - boots the Phase 4 engine layer
  - routes `etds_qc_validate_session()` through the reusable validation engine
  - updates issue status directly in `validation.json`
- `fintech/etds-qc/index.php`
  - adds `run_validation` action
- `fintech/etds-qc/render_app_v3.php`
  - Validation Centre reads real validation summary and findings
  - Doctor's Bench reads `validation.json` only
  - severity cards now show `Critical`, `High`, `Medium`, `Low`, and `Information`

## Production Readiness

Ready in this phase:

- modular rule storage
- reusable execution pipeline
- structured findings output
- status-aware Doctor's Bench integration
- rule enable/disable support
- rule version and effective date fields

Deferred intentionally:

- reconciliation engine enhancements
- return preparation
- TaxPro export
- FVU
- government utility integration
- advanced cross-record rules
- custom rule authoring UI

## Verification

Syntax checks passed:

- `php -l fintech/etds-qc/bootstrap_runtime.php`
- `php -l fintech/etds-qc/index.php`
- `php -l fintech/etds-qc/render_app_v3.php`
- `php -l app/engines/rule_registry.php`
- `php -l app/engines/rule_executor.php`
- `php -l app/engines/rule_engine.php`
- `php -l app/engines/validation_engine.php`
