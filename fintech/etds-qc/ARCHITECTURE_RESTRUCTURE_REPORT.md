# e-TDSDoc V1 Architecture Restructure Report

## Updated Information Architecture

Primary workspaces:

1. Case Dashboard
2. Data Centre
3. Return Centre
4. Report Centre

Bottom action:

- Logout

## Updated Navigation

Sidebar navigation is now limited to the four approved primary workspaces.

No nested sidebar menus were added.

Internal workspace navigation now lives inside:

- Data Centre
- Doctor's Bench
- Report Centre

## Updated Workspace Structure

### Case Dashboard

Purpose:

- Case management
- Current case visibility
- Workflow progress
- Notifications
- Recent cases
- Case timeline

### Data Centre

Internal tabs:

- Overview
- Upload
- Extraction
- Spreadsheet
- Validation
- Reconciliation
- Doctor's Bench
- QC Output

Doctor's Bench tabs:

- Diagnosis
- Reconciliation
- Treatment
- Readiness
- QC Certification

### Return Centre

Version 2 placeholder only.

Displayed as:

- Available in Version 2
- Roadmap cards
- Disabled future modules

### Report Centre

Implemented tabs:

- QC Reports
- Exception Reports
- Analytics
- Audit Logs
- System Reports
- Administration

Future placeholders:

- Form 16
- Form 16A
- Form 27D

## Files Modified

- `fintech/etds-qc/index.php`
- `fintech/etds-qc/render_app_v3.php`
- `fintech/etds-qc/ARCHITECTURE_RESTRUCTURE_REPORT.md`

## UI Screenshots

Pending capture:

- Case Dashboard
- Data Centre
- Return Centre
- Report Centre

## Version 1 Scope Validation

Enabled in Version 1:

- Document intake
- AI extraction
- Spreadsheet review
- Validation
- Reconciliation
- Doctor's Bench diagnosis and treatment flow
- QC certification
- Clean QC output generation
- Basic reporting

Explicitly not implemented in Version 1:

- Return preparation
- Correction returns
- Government utility export
- RPU
- FVU
- TaxPro integration
- Filing centre
- Acknowledgements

## Version 2 Placeholder Validation

The Return Centre is now preserved as a stable workspace shell for future expansion.

The current navigation and application shell can support Version 2 without introducing new primary sidebar items.

## Production Readiness Report

Completed:

- Primary navigation simplified to enterprise-scale structure
- Existing app shell retained
- Responsive shell preserved
- Existing workflow actions remapped into Data Centre architecture
- Return logic kept out of Version 1 workspace behavior

Remaining non-architecture follow-up:

- Capture refreshed UI screenshots for the new four-workspace structure
- Perform visual QA in browser for dashboard, data centre, return centre, and report centre
- Review any remaining copy refinements against branding language
