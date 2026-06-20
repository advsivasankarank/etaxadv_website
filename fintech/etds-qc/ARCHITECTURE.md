# e-TDS QC Tool Architecture

## Product Definition

`e-TDS QC Tool` is an internal office utility for E Tax Advisors Private Limited. It receives client TDS source documents, extracts structured data, highlights data quality problems, supports correction and challan reconciliation, and produces clean Excel output for downstream TaxPro use.

It is intentionally not:

- TDS filing software
- FVU generator
- Form 16 generator
- client portal
- CRM
- workflow tracker

## Hosting Model

- URL target: `/fintech/etds-qc/`
- shared site chrome: existing header, footer, navigation, branding, CSS theme
- access model: authenticated internal users only
- session model: every unit of work belongs to one QC session

## Technology Decisions

- backend: PHP 8.2+, native PHP
- architecture: lightweight MVC with service classes
- frontend: HTML5, Bootstrap 5, vanilla JavaScript, AJAX
- storage: JSON files only
- database: none
- export library: PhpSpreadsheet

## Design Principle

The UI is a `Data Quality Command Centre`, not a spreadsheet surface.

Primary focus:

- broken records
- missing fields
- duplicate records
- invalid PANs
- date and amount errors
- challan mismatches
- export blockers

Secondary focus:

- correct records
- background totals
- passive audit history

## Core Workflow

1. User signs in.
2. User creates a QC session.
3. User uploads source files.
4. System extracts and normalizes data into JSON.
5. Validation engine creates an exception queue.
6. User resolves errors and warnings.
7. User enters or imports challan data.
8. Reconciliation engine compares deductee totals against challan totals and allocations.
9. Export readiness engine decides whether output is allowed.
10. System generates clean Excel and records audit history.
11. Session moves through archive and purge lifecycle.

## Application Modules

### 1. Authentication

- session-based login
- password hashing with PHP `password_hash()`
- CSRF protection
- user store in `storage/users.json`

### 2. Dashboard

Displays:

- sessions created
- pending validation
- pending reconciliation
- ready for export
- completed
- recent sessions

### 3. QC Session Management

Responsibilities:

- create session IDs
- store session metadata
- maintain status transitions
- track creator and timestamps

### 4. Document Receipt

Responsibilities:

- multi-file upload
- extension and MIME validation
- file manifest creation
- upload delete and preview support

### 5. Extraction Pipeline

Version strategy:

- v1: Excel, XLS, XLSX, CSV
- v2: PDF
- v3: OCR-backed image extraction

Pipeline:

`upload -> extract -> normalize -> store source JSON -> validate`

### 6. Validation Engine

Checks:

- mandatory field presence
- PAN format
- amount validity
- date validity
- duplicate detection
- required column presence

Output:

- per-record issues
- issue severity
- suggested corrections
- live quality score

### 7. Error Queue Workspace

Record cards expose:

- key record snapshot
- error type
- error description
- suggested correction
- action buttons for accept, edit, ignore, resolve

### 8. Challan Reconciliation Engine

Checks:

- challan total vs deducted total
- utilized amount vs available amount
- unallocated challans
- short allocation
- excess allocation
- deductee without challan mapping

### 9. Export Readiness Engine

Export gate should check:

- no critical validation issues
- reconciliation difference equals zero
- configured quality threshold met

Default threshold: `100`

### 10. Export Engine

- uses normalized, validated records
- produces ready Excel workbook
- naming pattern: `CLIENTNAME_QTR_RETURNTYPE_READY.xlsx`

### 11. Archive and Purge Engine

Retention:

- configurable purge window
- default `PURGE_AFTER_DAYS=7`

After purge:

- remove uploads, extracted data, temp files, generated files
- keep session metadata and audit log only

### 12. Audit Engine

Tracks:

- user
- action
- session
- timestamp
- event details

## MVC Layout

### Controllers

- receive requests
- perform authentication and CSRF checks
- call services
- return HTML or JSON

### Models

- represent JSON-backed entities
- no database layer
- read/write through repository classes

### Views

- server-rendered PHP templates
- Bootstrap layout
- JS-enhanced AJAX panels for validation and reconciliation updates

### Services

- session service
- file upload service
- extraction service
- validation service
- reconciliation service
- export service
- purge service
- audit service

## Status Lifecycle

Allowed statuses:

- `draft`
- `validation`
- `reconciliation`
- `ready`
- `downloaded`
- `archived`
- `purged`

Suggested transitions:

- draft -> validation
- validation -> reconciliation
- reconciliation -> ready
- ready -> downloaded
- downloaded -> archived
- archived -> purged

## Folder Ownership

Application code lives under `fintech/etds-qc/`.

Persistent JSON storage lives under `storage/etds-qc/`.

The public website listing page may continue to link into the internal app, but the app itself must enforce authentication even when the URL is known.

## Non-Functional Requirements

- responsive desktop-first interface
- tablet compatibility
- mobile-safe viewing for summary actions
- deterministic JSON storage
- no spreadsheet-style editing grid
- production-safe file handling
- auditable user actions
- isolated session folders for support and recovery

## Implementation Sequence

1. establish folder structure and config
2. build authentication and session creation
3. build dashboard and session listing
4. build upload and manifest handling
5. build extraction and normalization for CSV/XLS/XLSX
6. build validation engine and error queue
7. build challan reconciliation console
8. build export readiness and Excel output
9. build archive and purge jobs
10. add PDF and OCR extraction in later versions
