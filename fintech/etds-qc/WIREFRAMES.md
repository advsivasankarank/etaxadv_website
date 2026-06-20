# e-TDS QC Tool Screen Wireframes

## 1. Login

```text
+---------------------------------------------------------------+
| E Tax Advisors Header                                         |
+---------------------------------------------------------------+
| Internal Utility Access                                       |
| e-TDS QC Tool                                                 |
| Data Quality Validation, Reconciliation & Excel Preparation   |
|                                                               |
|  Email / User ID                                              |
|  Password                                                     |
|  [ Sign In ]                                                  |
|                                                               |
|  Security note: authorised internal users only                |
+---------------------------------------------------------------+
```

## 2. Dashboard

```text
+----------------------------------------------------------------------------------+
| Header / Navigation                                                              |
+----------------------------------------------------------------------------------+
| e-TDS QC Tool Dashboard                         [ New Session ] [ Continue Work ] |
|----------------------------------------------------------------------------------|
| Sessions Created | Pending Validation | Pending Reconciliation | Ready | Complete|
|----------------------------------------------------------------------------------|
| Recent Sessions                                                                  |
| QC-2026-0001 | ABC Pvt Ltd | 24Q | Q1 | Validation | 86% | Open                 |
| QC-2026-0002 | XYZ LLP     | 26Q | Q1 | Ready      | 100%| Export               |
+----------------------------------------------------------------------------------+
```

## 3. Create QC Session

```text
+-----------------------------------------------------------------------+
| Create QC Session                                                     |
|-----------------------------------------------------------------------|
| Client Name                 [______________________________]           |
| TAN                         [______________________________]           |
| Financial Year              [ 2025-26 v ]                             |
| Quarter                     [ Q1 v ]                                  |
| Return Type                 [ 24Q v ]                                 |
| Remarks                     [______________________________]           |
|                                                                       |
| Generated Session ID: QC-2026-0003                                    |
|                                                                       |
| [ Create Session ]    [ Cancel ]                                      |
+-----------------------------------------------------------------------+
```

## 4. Session Workspace

```text
+--------------------------------------------------------------------------------------------------+
| Session: QC-2026-0001 | ABC Pvt Ltd | TAN | FY 2025-26 | Q1 | 24Q | Status: Validation         |
| Quality Score: 86% | Reconciliation Score: 72% | Export Readiness: Blocked                      |
+--------------------------------------------------------------------------------------------------+
| Documents Panel         | Error Queue / Command Centre                 | Summary Panel           |
|-------------------------|----------------------------------------------|-------------------------|
| Uploaded Files          | [ Invalid PAN ] Ravi Kumar                  | Total Records: 150      |
| - deductee-q1.xlsx      | PAN must match AAAAA9999A                   | Passed: 120             |
| - challans.csv          | Suggested: remove spaces / check source      | Failed: 25              |
|                         | [ Accept ] [ Edit ] [ Ignore ] [ Resolve ]   | Warnings: 5             |
| Source Information      |----------------------------------------------| Ready Status: No        |
| Extraction Status       | [ Missing Amount ] Sangeetha A              |                         |
| Last Upload Time        | Amount is blank or zero                      | Key Blockers            |
|                         | [ Accept ] [ Edit ] [ Ignore ] [ Resolve ]   | - 2 critical errors     |
|                         |----------------------------------------------| - challan diff exists   |
+--------------------------------------------------------------------------------------------------+
```

## 5. Reconciliation Console

```text
+---------------------------------------------------------------------------------------------+
| Reconciliation Dashboard                                                                    |
|---------------------------------------------------------------------------------------------|
| Challan Total | Allocated Total | Deductee Total | Balance | Difference | Status           |
| 50,000        | 47,000          | 47,000         | 3,000   | 0          | Review Required  |
|---------------------------------------------------------------------------------------------|
| Unmatched / Review Queue                                                                    |
| - CHL-0001 has 3,000 remaining balance                                                      |
| - 4 deductees not mapped to any challan                                                     |
|---------------------------------------------------------------------------------------------|
| Matched Items | Unmatched Items | Remaining Balance | Review Flags                           |
+---------------------------------------------------------------------------------------------+
```

## 6. Export Ready View

```text
+----------------------------------------------------------------------------+
| Export Readiness                                                           |
|----------------------------------------------------------------------------|
| Validation Score        100%                                               |
| Reconciliation Difference 0                                                |
| Critical Errors         0                                                  |
| Status                  Ready for Export                                   |
|                                                                            |
| Output File Name: ABC_Q1_24Q_READY.xlsx                                    |
|                                                                            |
| [ Generate Excel ]   [ Download ]   [ Archive Session ]                    |
+----------------------------------------------------------------------------+
```

## UX Notes

- keep correct records out of the primary viewport
- use colored severity chips for `critical`, `warning`, and `resolved`
- make the centre panel the visual anchor of the workspace
- reserve modal editing for targeted corrections only
