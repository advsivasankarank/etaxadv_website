# e-TDS QC Tool Testing Report

## Test Date

- 2026-06-20

## Environment

- workspace: `C:\xampp\htdocs\etaxadv_website`
- PHP CLI: `8.5.3`
- OCR binary detected: `tesseract.exe`

## Validation Performed

### Syntax Validation

Executed:

- `php -l fintech/etds-qc/bootstrap_runtime.php`
- `php -l fintech/etds-qc/index.php`
- `php -l includes/header.php`
- `php -l fintech/etds-qc.php`

Result:

- all passed without syntax errors

### Functional Coverage

Verified in implementation and CLI smoke flow:

- session bootstrap and JSON storage initialization
- session ID generation
- session creation
- CSV extraction path
- XLSX extraction path
- image OCR extraction path wiring
- validation score calculation
- issue resolution status updates
- challan reconciliation summary generation
- XLSX export generation
- archive and purge transitions

### CLI Smoke Results

Observed outcomes:

- `QC-2026-0001`: validation score `100`, reconciliation difference `0`, OCR mode `ocr_text`
- `QC-2026-0002`: export file `SMOKE_TEST_CLIENT_TWO_Q1_24Q_READY.xlsx` created successfully, session status moved to `ready`

## Manual QA Checklist

- login page renders with shared branding
- dashboard shows summary cards
- session workspace keeps error queue central
- upload panel shows file metadata and preview links
- reconciliation panel shows challan totals and differences
- export panel blocks output until quality and reconciliation conditions are met

## Known Constraints

- legacy `.xls` parsing is not automated in this build and is marked for manual conversion review
- PDF extraction is best-effort for text-based PDFs; scanned PDFs may still require manual OCR preparation
- browser-based UI automation was not run in this session

## Recommended UAT Scenarios

1. upload a clean CSV and confirm the tool reaches ready state
2. upload a file with invalid PANs and duplicate invoices and resolve them from the error queue
3. load challans that produce short and excess utilization and confirm exceptions appear
4. generate export and confirm the workbook opens in Excel
5. archive a session and confirm later purge preserves metadata and audit log
