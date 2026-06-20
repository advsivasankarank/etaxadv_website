# e-TDS QC Tool JSON Schemas

## 1. `users.json`

```json
[
  {
    "id": "USR-0001",
    "name": "Super Admin",
    "email": "admin@etaxadv.local",
    "role": "super_admin",
    "password_hash": "$2y$10$example",
    "status": "active",
    "created_on": "2026-06-20T10:00:00+05:30",
    "updated_on": "2026-06-20T10:00:00+05:30"
  }
]
```

## 2. `session.json`

```json
{
  "session_id": "QC-2026-0001",
  "client_name": "ABC Private Limited",
  "tan": "CHNA12345B",
  "financial_year": "2025-26",
  "quarter": "Q1",
  "return_type": "24Q",
  "remarks": "Initial intake from client email batch 1",
  "status": "validation",
  "quality_score": 86,
  "reconciliation_score": 72,
  "export_readiness": false,
  "created_by": "USR-0001",
  "created_on": "2026-06-20T10:30:00+05:30",
  "updated_on": "2026-06-20T12:30:00+05:30",
  "last_action": "validation_run_completed"
}
```

## 3. `source_data.json`

```json
{
  "documents": [
    {
      "file_id": "FIL-0001",
      "file_name": "deductee-q1.xlsx",
      "stored_name": "20260620_103215_deductee-q1.xlsx",
      "extension": "xlsx",
      "mime_type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      "size_bytes": 45122,
      "uploaded_on": "2026-06-20T10:32:15+05:30",
      "uploaded_by": "USR-0001"
    }
  ],
  "source_columns": [
    "deductee_name",
    "pan",
    "tds_amount",
    "deduction_date",
    "invoice_number",
    "challan_reference"
  ],
  "records": [
    {
      "record_id": "REC-0001",
      "source_file_id": "FIL-0001",
      "row_number": 2,
      "deductee_name": "Ravi Kumar",
      "pan": "ABCDE1234F",
      "tds_amount": "2500.00",
      "deduction_date": "2025-04-18",
      "invoice_number": "INV-1001",
      "challan_reference": "CIN-001"
    }
  ]
}
```

## 4. `validated_data.json`

```json
{
  "summary": {
    "total_records": 150,
    "passed_records": 120,
    "failed_records": 25,
    "warning_records": 5,
    "quality_score": 86,
    "ready_status": false,
    "last_validated_on": "2026-06-20T12:00:00+05:30"
  },
  "records": [
    {
      "record_id": "REC-0001",
      "status": "failed",
      "normalized": {
        "deductee_name": "Ravi Kumar",
        "pan": "ABCDE1234F",
        "tds_amount": 2500,
        "deduction_date": "2025-04-18",
        "invoice_number": "INV-1001",
        "challan_reference": "CIN-001"
      },
      "issues": [
        {
          "issue_id": "ISS-0001",
          "type": "duplicate_invoice",
          "severity": "warning",
          "field": "invoice_number",
          "message": "Invoice number appears more than once in this session.",
          "suggested_correction": "Verify whether this duplicate is valid or remove the repeated record.",
          "resolution_status": "open"
        }
      ]
    }
  ]
}
```

## 5. `challans.json`

```json
{
  "challans": [
    {
      "challan_id": "CHL-0001",
      "bsr_code": "1234567",
      "deposit_date": "2025-04-30",
      "challan_serial_no": "00012",
      "section_code": "94C",
      "tax_deposited": 50000,
      "interest": 0,
      "fee": 0,
      "others": 0,
      "total_available": 50000,
      "allocated_total": 47000,
      "balance_total": 3000
    }
  ]
}
```

## 6. `reconciliation.json`

```json
{
  "summary": {
    "challan_total": 50000,
    "allocated_total": 47000,
    "deductee_total": 47000,
    "balance": 3000,
    "difference": 0,
    "reconciliation_score": 94,
    "ready_status": false
  },
  "exceptions": [
    {
      "exception_id": "RECN-0001",
      "type": "unallocated_challan_balance",
      "severity": "warning",
      "challan_id": "CHL-0001",
      "message": "Challan has remaining unallocated balance of 3000.",
      "resolution_status": "open"
    }
  ]
}
```

## 7. `audit/audit-log.json`

```json
[
  {
    "event_id": "AUD-0001",
    "session_id": "QC-2026-0001",
    "user_id": "USR-0001",
    "action": "session_created",
    "event": "QC session created for ABC Private Limited",
    "meta": {
      "return_type": "24Q",
      "quarter": "Q1"
    },
    "timestamp": "2026-06-20T10:30:00+05:30"
  }
]
```

## Storage Rules

- every JSON write must be atomic: write temp file first, then rename
- every JSON file must remain UTF-8 encoded
- numeric totals should be stored as numbers after normalization
- uploaded file manifests must never trust client-supplied MIME type alone
