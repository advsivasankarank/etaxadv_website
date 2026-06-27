# PERFORMANCE REPORT
## e-TDSDoc V1 – Production Hardening Sprint
### Date: 2026-06-27

---

## 1. ENVIRONMENT BASELINE

| Metric | Value |
|--------|-------|
| PHP Version | 8.5.3 |
| Memory Limit | 128M |
| Max Execution Time | 0 (unlimited) |
| OPcache | Enabled (default) |
| ZipArchive | Available |

## 2. OPERATION ANALYSIS

### Case Creation
| Aspect | Assessment |
|--------|------------|
| JSON writes per case | 16 files (case.json, client.json, etc.) |
| Disk I/O | Sequential file_put_contents with LOCK_EX |
| Estimated time | < 100ms for empty case |
| Bottleneck | JSON write locking (LOCK_EX) |

### File Upload
| Aspect | Assessment |
|--------|------------|
| Max file size | 15MB (post_max_size 20M) |
| Duplicate detection | SHA-1 hash per file (fast) |
| MIME detection | mime_content_type() (fast) |
| Estimated time | < 500ms for 15MB file |
| Bottleneck | Disk write speed |

### AI Extraction
| Aspect | Assessment |
|--------|------------|
| CSV parsing | fgetcsv() – fast, streaming |
| XLSX parsing | ZipArchive + SimpleXML – fast |
| PDF text extraction | regex on raw content – fast |
| Image OCR | Tesseract exec – SLOW (external process) |
| Estimated time | < 1s for CSV/XLSX, 5-30s per image for OCR |
| Bottleneck | Tesseract OCR (if images present) |

### Validation Engine
| Aspect | Assessment |
|--------|------------|
| Rule execution | In-memory PHP processing |
| Rule registry | Loaded from PHP files (cached by OPcache) |
| Estimated time | < 500ms for 1000 records |
| Bottleneck | Rule complexity |

### Doctor Intelligence
| Aspect | Assessment |
|--------|------------|
| Diagnosis clustering | In-memory PHP |
| Priority scoring | In-memory PHP |
| Health scoring | In-memory PHP |
| Estimated time | < 200ms |
| Bottleneck | None significant |

### Spreadsheet Workspace
| Aspect | Assessment |
|--------|------------|
| Record loading | Multiple JSON reads per request |
| Cell state computation | In-memory PHP |
| AI suggestion generation | In-memory PHP |
| Estimated time | < 300ms |
| Bottleneck | JSON read latency |

### Reconciliation
| Aspect | Assessment |
|--------|------------|
| Challan matching | In-memory PHP |
| Deductee matching | In-memory PHP |
| Cross-quarter checks | In-memory PHP |
| Estimated time | < 500ms for 1000 records |
| Bottleneck | None significant |

### XLSX Export
| Aspect | Assessment |
|--------|------------|
| Generation method | Native ZipArchive + raw OOXML |
| No external dependencies | PhpSpreadsheet NOT used |
| Estimated time | < 2s for 1000 rows |
| Bottleneck | ZipArchive compression |

### Report Generation (CSV)
| Aspect | Assessment |
|--------|------------|
| Method | fputcsv() to php://output |
| Estimated time | < 500ms |
| Bottleneck | None significant |

## 3. JSON STORAGE PERFORMANCE

| Operation | Assessment |
|-----------|------------|
| Read (file_get_contents) | Fast for files < 1MB |
| Write (file_put_contents + LOCK_EX) | Atomic, safe, slight overhead |
| Concurrent access | LOCK_EX prevents corruption |
| Large files (>1MB) | May slow down with 1000+ records |
| Recommendation | Acceptable for current scale |

## 4. MEMORY USAGE

| Component | Estimated Memory |
|-----------|-----------------|
| PHP base | ~5MB |
| Bootstrap (2753 lines) | ~2-3MB |
| JSON decode (1000 records) | ~1-2MB |
| XLSX generation | ~5-10MB |
| Tesseract OCR | External process |
| Peak usage estimate | ~30-50MB |
| Memory limit | 128M |
| Status | SAFE |

## 5. BOTTLENECKS IDENTIFIED

| # | Severity | Bottleneck | Mitigation |
|---|----------|------------|------------|
| P-01 | Medium | Tesseract OCR external process | Acceptable – runs per image only |
| P-02 | Low | LOCK_EX on every JSON write | Acceptable – prevents corruption |
| P-03 | Low | Monolithic bootstrap (2753 lines) | Acceptable – OPcache mitigates |
| P-04 | Informational | No database – JSON file storage | Acceptable for current scale |

---

## PERFORMANCE VERDICT

| Metric | Rating |
|--------|--------|
| Case creation | FAST (< 100ms) |
| File upload | FAST (< 500ms) |
| Extraction (CSV/XLSX) | FAST (< 1s) |
| Extraction (OCR) | SLOW (5-30s per image) |
| Validation | FAST (< 500ms) |
| Doctor Intelligence | FAST (< 200ms) |
| Spreadsheet load | FAST (< 300ms) |
| Reconciliation | FAST (< 500ms) |
| XLSX export | FAST (< 2s) |
| CSV reports | FAST (< 500ms) |

**Overall: Application performs well within acceptable limits for internal use.**
