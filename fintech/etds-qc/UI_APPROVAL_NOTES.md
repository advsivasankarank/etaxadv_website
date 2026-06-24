# e-TDS QC Tool UI Approval Notes

## Scope

This revision is a UI-only prototype aligned to the approved e-BAL-inspired workflow model.

No backend workflow changes were introduced in this approval pass.

## Approved Navigation

1. Case Overview
2. Intake Centre
3. Extraction Centre
4. Doctor's Bench
5. Final Excel Advice

Bottom fixed action:

- Logout

## Shell Direction

- Sidebar follows the e-BAL V2 single-level navigation philosophy.
- Header is reinterpreted as a case command header with three zones:
  - Left: ETA brand plus e-TDS QC Tool identity
  - Centre: Client Name, TAN, FY, Quarter
  - Right: e-TDS Doctor status, user profile, logout
- Doctor's Bench remains the primary workspace and uses tabs:
  - Diagnosis
  - Reconciliation
  - Treatment
  - Readiness

## Case Overview

Case Overview is now the default workspace.

It surfaces:

- Client Name
- TAN
- FY
- Quarter
- Case Status
- Data Health Score
- Documents Received
- Health Issues Count
- Export Readiness
- Next Recommended Action

It also includes:

- Progress Banner
- Completed Tasks
- Pending Tasks
- Quick Launch Cards

## Final Excel Advice

This workspace is positioned as an output centre only.

It highlights:

- Doctor Certification
- Case Health Report
- Generated Files
- Download Centre
- Output Readiness
- Blocked Issues

## Files In This UI Pass

- `fintech/etds-qc/render_app_v2.php`
- `fintech/etds-qc/index.php`
- `fintech/etds-qc/assets/css/etds-qc.css`

## Pending Before Backend Work

- Visual review of header density and spacing
- Confirmation that Case Overview metrics are the right priority order
- Confirmation that Doctor's Bench tab labels and card grouping feel final
- Confirmation that Final Excel Advice should remain this compact or become more deliverables-heavy
