# eTDSDoc V1 - Phase 5 e-TDS Doctor Intelligence Engine

## Scope Delivered

Phase 5 adds the e-TDS Doctor Intelligence Engine on top of the frozen UI, Case Engine, Upload Engine, AI Extraction Engine, and Validation Rules Engine.

Delivered components:

- `app/engines/doctor_engine.php`
- `app/engines/doctor_diagnosis.php`
- `app/engines/doctor_priority.php`
- `app/engines/doctor_prescription.php`
- `app/engines/doctor_score.php`

## Objective Fulfilled

The Doctor engine does not rerun validation. It consumes existing case artifacts only:

- `validation.json`
- `deductees.json`
- `challans.json`
- `salary.json`
- `payments.json`
- `case.json`

It produces:

- `doctor.json`

## Engine Design

### Diagnosis Engine

Open validation findings are grouped into diagnosis clusters such as:

- Invalid PAN Cluster
- Missing Challan Cluster
- Duplicate Deductee Cluster
- Incorrect Section Cluster
- Salary Data Issues
- Date Issues
- Amount Issues
- Return Profile Issues

Each diagnosis includes:

- likely cause
- affected records
- estimated impact
- recommended resolution

### Priority Engine

Diagnoses are sorted by business impact instead of discovery order.

Priority weights:

- Critical
- High
- Medium
- Low
- Information

The engine also considers affected record count when ranking diagnoses.

### Prescription Engine

The Doctor engine generates practical prescriptions such as:

- priority label
- instruction
- estimated time in minutes
- expected health score before correction
- expected health score after correction

### Health Score Engine

Generated scores:

- Extraction Score
- Validation Score
- Completeness Score
- Consistency Score
- Overall Data Health Score

Methodology:

- Extraction Score: from extraction confidence
- Validation Score: from validation engine quality score
- Completeness Score: based on populated deductee, challan, salary, and payment datasets
- Consistency Score: weighted reduction based on open severity mix
- Overall Data Health Score: weighted blend
  - Extraction 20%
  - Validation 40%
  - Completeness 20%
  - Consistency 20%

### Readiness Engine

Readiness outcomes:

- Not Ready
- Ready After Corrections
- Ready for QC Certification

## doctor.json Schema

`doctor.json` contains:

- `summary`
- `diagnosis`
- `priority`
- `prescription`
- `health_scores`
- `recommendations`
- `readiness`

Summary includes:

- top priority
- top diagnosis
- expected improvement
- estimated time
- readiness
- generation timestamp

## Application Integration

Integrated changes:

- `fintech/etds-qc/bootstrap_runtime.php`
  - loads the Doctor engine layer
  - creates `doctor.json` in case structure
  - exposes doctor report downloads
- `fintech/etds-qc/index.php`
  - runs Doctor intelligence after validation
  - adds a dedicated `run_doctor` action
  - regenerates Doctor output after issue status updates and record edits
- `fintech/etds-qc/render_app_v3.php`
  - Doctor's Bench now reads `doctor.json`
  - Diagnosis tab shows diagnosis summary and top priority
  - Treatment tab shows Doctor's Prescription
  - Readiness tab shows Doctor readiness and score breakdown
  - Output tab exposes Doctor reports and `doctor.json` readiness

## Reports Added

Available report types:

- Doctor Diagnosis Report
- Prescription Report
- Health Score Report
- Readiness Report

## Production Readiness

Ready in this phase:

- diagnosis clustering
- business-impact priority ordering
- root cause guidance
- operator prescriptions
- health score methodology
- readiness decisioning
- Doctor's Bench intelligence integration

Deferred intentionally:

- challan reconciliation engine
- return preparation
- TaxPro export
- government utility integration
- FVU

## Verification

Syntax checks passed:

- `php -l app/engines/doctor_diagnosis.php`
- `php -l app/engines/doctor_priority.php`
- `php -l app/engines/doctor_score.php`
- `php -l app/engines/doctor_prescription.php`
- `php -l app/engines/doctor_engine.php`
- `php -l fintech/etds-qc/bootstrap_runtime.php`
- `php -l fintech/etds-qc/index.php`
- `php -l fintech/etds-qc/render_app_v3.php`

Runtime smoke verification completed against:

- `ETD-2026-000001`

The Doctor engine successfully generated `doctor.json` and returned a readiness outcome without rerunning validation.
