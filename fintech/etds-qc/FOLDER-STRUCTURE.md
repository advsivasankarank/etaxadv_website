# e-TDS QC Tool Folder Structure

## Application Tree

```text
fintech/
  etds-qc/
    index.php
    bootstrap_runtime.php
    config/
      app.php
      validation.php
      reconciliation.php
    controllers/
      AuthController.php
      DashboardController.php
      SessionController.php
      UploadController.php
      ValidationController.php
      ReconciliationController.php
      ExportController.php
    models/
      User.php
      QcSession.php
      UploadFile.php
      ValidationIssue.php
      ChallanEntry.php
      AuditEvent.php
    repositories/
      UserRepository.php
      SessionRepository.php
      UploadRepository.php
      AuditRepository.php
    services/
      AuthService.php
      CsrfService.php
      SessionIdService.php
      UploadService.php
      ExtractionService.php
      NormalizationService.php
      ValidationService.php
      ReconciliationService.php
      QualityScoreService.php
      ExportReadinessService.php
      ExportService.php
      PurgeService.php
      AuditService.php
    helpers/
      JsonStorage.php
      FileGuard.php
      Response.php
      Validator.php
    views/
      auth/
        login.php
      dashboard/
        index.php
      sessions/
        create.php
        show.php
      partials/
        session_header.php
        error_queue.php
        summary_panel.php
        documents_panel.php
        reconciliation_panel.php
    assets/
      css/
        etds-qc.css
      js/
        etds-qc.js
    ARCHITECTURE.md
    FOLDER-STRUCTURE.md
    JSON-SCHEMAS.md
    WIREFRAMES.md
    VALIDATION-RULE-MATRIX.md
    RECONCILIATION-RULE-MATRIX.md
    SECURITY-DESIGN.md
```

## Storage Tree

```text
storage/
  etds-qc/
    users.json
    config.json
    sessions/
      QC-2026-0001/
        session.json
        source_data.json
        validated_data.json
        challans.json
        reconciliation.json
        uploads/
          original/
        output/
        audit/
          audit-log.json
        logs/
          extraction.log
          validation.log
```

## Notes

- `index.php` acts as the public entry point for the internal utility.
- `bootstrap_runtime.php` loads configuration, shared site helpers, sessions, and app services.
- repositories isolate JSON reads and writes so file storage can be audited and tested.
- `views/partials/` keeps the command-centre panels modular.
- `storage/etds-qc/sessions/` is partitioned by QC session ID to reduce cross-session risk.
- the current design package is stored at the root of `fintech/etds-qc/` for this planning phase; production code can later move docs into a dedicated `docs/` folder if preferred.
