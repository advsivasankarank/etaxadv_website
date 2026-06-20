# e-TDS QC Tool Security Review Report

## Review Scope

- authentication
- session handling
- upload processing
- JSON storage safety
- request validation
- export and preview endpoints

## Implemented Controls

### Authentication

- login required for all app views except the login screen
- password hashes stored instead of plain text
- session ID regenerated on successful login

### CSRF

- CSRF token validated on state-changing requests
- invalid tokens are rejected before mutation

### Output Escaping

- user-facing values are HTML-escaped in app views

### Upload Controls

- extension whitelist enforced
- upload size limit enforced
- uploaded files renamed before storage
- files stored under dedicated internal storage path

### Path Safety

- preview and download use `basename()` for file parameters
- session lookup is constrained to `QC-YYYY-NNNN` pattern

### Storage Safety

- JSON writes are atomic through temp-file replacement
- direct web access to storage files is blocked with `.htaccess`

### Auditing

- session creation, upload, extraction, validation, reconciliation, export, archive, and purge events are logged

## Residual Risks

- bootstrap admin credentials must be rotated immediately after deployment
- uploaded MIME validation is basic and should be strengthened further if hostile uploads are expected
- PDF extraction fallback is not a hardened parser for every PDF encoding strategy
- image OCR depends on the local Tesseract binary remaining trusted and available

## Recommendations

1. rotate the default admin password before production use
2. move uploads outside the web root entirely if hosting structure permits
3. add inactivity timeout enforcement for long-running operator sessions
4. add server-side rate limiting for repeated login failures
5. add a second operator account and validate audit separation in UAT
