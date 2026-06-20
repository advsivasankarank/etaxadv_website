# Security Design

## Security Objectives

- restrict the tool to authenticated internal staff
- protect session data and uploaded documents
- prevent tampering with JSON storage
- ensure every sensitive action is auditable

## Authentication

- login required for every app route except the login screen
- session fixation protection through session ID regeneration after login
- password storage only via `password_hash()`
- failed login attempts should be logged

## Authorization

- current roles: `super_admin`, `operator`
- both roles currently share identical permissions
- role field still exists for future expansion

## CSRF Protection

- generate CSRF token per login session
- validate token on all POST, PUT, PATCH, and DELETE style requests
- reject invalid token with 419-style application response

## Input Validation

- sanitize text input with trim and length checks
- whitelist return types: `24Q`, `26Q`, `27Q`, `27EQ`
- whitelist quarter values: `Q1`, `Q2`, `Q3`, `Q4`
- validate TAN format before session save
- validate dates against selected financial year

## File Upload Protection

- allow only approved extensions: `xlsx`, `xls`, `csv`, `pdf`, `png`, `jpg`, `jpeg`
- validate MIME type and extension together
- rename uploads to generated storage names
- store outside direct public listing paths where possible
- never execute uploaded files
- deny double extensions such as `file.csv.php`
- cap file size in config

## JSON Storage Protection

- use fixed storage base path
- resolve and verify canonical path before read or write
- never trust path fragments from request input
- use atomic writes to avoid partial corruption
- lock writes when updating shared JSON files

## XSS Prevention

- escape all output in PHP views
- sanitize user-entered notes and remarks on output
- do not render uploaded content inline without safe preview strategy

## Path Traversal Prevention

- derive session folder from validated session ID only
- reject `..`, slashes, null bytes, and unexpected separators in any file parameter
- use lookup by known file ID instead of accepting raw filenames from the browser

## Audit and Monitoring

Log these events:

- login success and failure
- session creation
- metadata updates
- upload create and delete
- extraction runs
- validation runs
- issue resolution
- reconciliation edits
- export generation
- archive and purge

## Session Hardening

- set `httponly` cookie flag
- set `secure` cookie flag when served over HTTPS
- set `samesite=lax` or stricter
- expire inactive sessions automatically

## Deployment Controls

- keep storage directories non-indexed
- deny direct web access to JSON and upload folders with server rules where possible
- disable verbose PHP error display in production
- keep a recoverable backup policy for storage before purge jobs
