# Validation Rule Matrix

| Category | Rule Code | Rule | Severity | Action |
| --- | --- | --- | --- | --- |
| Mandatory | `REQ_NAME` | Deductee name must exist | critical | block export |
| Mandatory | `REQ_PAN` | PAN must exist | critical | block export |
| Mandatory | `REQ_AMOUNT` | TDS amount must exist | critical | block export |
| Mandatory | `REQ_DATE` | Deduction date must exist | critical | block export |
| Structure | `REQ_COLUMNS` | Required columns must exist in source data | critical | stop validation until fixed |
| PAN | `PAN_LEN` | PAN length must be exactly 10 | critical | require correction |
| PAN | `PAN_PATTERN` | PAN must match `AAAAA9999A` | critical | require correction |
| PAN | `PAN_SPACES` | PAN must not contain spaces | warning | auto-trim then revalidate |
| Amount | `AMT_NUMERIC` | Amount must be numeric | critical | require correction |
| Amount | `AMT_POSITIVE` | Amount must be greater than zero | critical | require correction |
| Amount | `AMT_PRECISION` | Amount must normalize to valid decimal precision | warning | normalize value |
| Date | `DATE_VALID` | Date must be a valid calendar date | critical | require correction |
| Date | `DATE_FY_RANGE` | Date must fall within selected financial year | critical | require correction |
| Date | `DATE_NOT_FUTURE` | Date must not be in the future | critical | require correction |
| Duplicate | `DUP_PAN` | Same PAN appears in duplicate pattern | warning | manual review |
| Duplicate | `DUP_NAME` | Same name appears across suspicious repeated rows | warning | manual review |
| Duplicate | `DUP_INVOICE` | Invoice number repeats unexpectedly | warning | manual review |
| Duplicate | `DUP_TRANSACTION` | Full transaction signature repeats | critical | require resolution |
| Data Quality | `EMPTY_ROW` | Source row contains no usable data | warning | ignore or drop |
| Data Quality | `BAD_REFERENCE` | Challan reference does not map to known challan | warning | reconcile later |

## Required Source Fields

- `deductee_name`
- `pan`
- `tds_amount`
- `deduction_date`

## Quality Score Guidance

- begin at `100`
- subtract `10` for each unresolved critical issue
- subtract `3` for each unresolved warning
- floor at `0`
- resolved or accepted items no longer reduce score

## Export Blockers

- any unresolved critical rule
- reconciliation difference not equal to zero
- score below configured threshold
