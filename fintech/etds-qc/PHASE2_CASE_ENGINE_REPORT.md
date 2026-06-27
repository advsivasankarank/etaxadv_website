# e-TDSDoc V1 Phase 2 Report

## Scope Delivered

Phase 2 implements the file-based business foundation for:

1. Case Engine
2. Client Engine
3. Upload Engine
4. Document Register
5. JSON Storage Architecture
6. Audit Engine
7. Search Engine
8. Dynamic Dashboard
9. File Structure Documentation
10. Production Readiness Report

The existing UI shell, navigation, and workspace architecture were retained.

## JSON Storage Architecture

Root:

- `storage/etds-qc/cases`
- `storage/etds-qc/documents`
- `storage/etds-qc/uploads`
- `storage/etds-qc/masters`
- `storage/etds-qc/users`
- `storage/etds-qc/logs`
- `storage/etds-qc/audit`
- `storage/etds-qc/settings`

Per case folder:

- `case.json`
- `client.json`
- `deductor.json`
- `documents.json`
- `deductees.json`
- `challans.json`
- `salary.json`
- `validation.json`
- `reconciliation.json`
- `qc.json`
- `audit.json`
- `uploads/`
- `exports/`

Case number format:

- `ETD-YYYY-NNNNNN`

The sequence is maintained through `settings/counters.json` and is never reused.

## Case Engine

Implemented:

- Create Case
- Open Case
- Close Case
- Archive Case
- Delete Case (soft delete)
- Duplicate Case
- Recent Cases
- Favourite Cases
- Search Cases
- Case Timeline
- Case Status
- Case Progress

Status model:

- `draft`
- `documents_received`
- `extraction_running`
- `validation_running`
- `reconciliation_pending`
- `qc_in_progress`
- `qc_completed`
- `ready_for_return_preparation`
- `archived`
- `deleted`

## Client Engine

Stored in `client.json` and summarized in `case.json`:

- Client Name
- Client Code
- TAN
- PAN
- Address
- Contact Person
- Mobile
- Email
- Financial Year
- Quarter
- Entity Type

## Upload Engine

Implemented:

- Browse upload
- Multiple upload
- Drag and drop interaction
- Upload progress bar
- Duplicate detection by content hash
- Safe file naming
- Version tracking
- Document preview for previewable file types
- Document removal

Document register fields:

- Document ID
- File Name
- Original Name
- Document Type
- Upload Time
- Uploaded By
- OCR Status
- Extraction Status
- Validation Status
- Remarks

## Master Data

Reusable JSON masters are bootstrapped for:

- Sections
- Nature of Payment
- States
- Banks
- Document Types
- Validation Rules
- Financial Years
- Quarters

## Audit Engine

Every case action is written to `audit.json` with:

- Date
- Time
- User
- Action
- Old Value
- New Value
- IP placeholder

## Dynamic Dashboard

Dashboard metrics are now driven from case JSON state:

- Open Cases
- QC Completed
- Pending Validation
- Pending Reconciliation
- Ready for Return Preparation

The dashboard also supports:

- Search by case number, client name, TAN, PAN, quarter, and financial year
- Recent case listing
- Favourite visibility
- Current case actions
- Audit-driven case timeline

## Phase Restrictions Respected

Not implemented in Phase 2:

- OCR
- AI Extraction logic
- Validation rule execution
- Reconciliation engine logic
- TaxPro export
- Return preparation
- Government utility generation
- FVU

The related workspaces remain present, but Phase 2 uses them as status-aware placeholders rather than activating those engines.

## Files Modified

- `fintech/etds-qc/bootstrap_runtime.php`
- `fintech/etds-qc/index.php`
- `fintech/etds-qc/render_app_v3.php`
- `fintech/etds-qc/assets/js/etds-qc.js`
- `fintech/etds-qc/assets/css/etds-qc.css`

## Production Readiness Report

Completed:

- Modular JSON storage foundation established
- Case numbering and counter persistence implemented
- Legacy session storage migration path introduced
- File-based case, client, document, and audit engines integrated
- Upload safety, duplicate detection, and version tracking implemented
- Search and reporting endpoints added
- UI shell preserved

Recommended next phase checks:

- Browser QA for upload progress and drag-drop on localhost
- Seeded-user password rotation before deployment
- Phase 3 extraction engine activation using the new case/document structure
- Phase 4 validation and reconciliation engines using the bootstrapped masters
