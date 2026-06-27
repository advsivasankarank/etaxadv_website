# ENVIRONMENT AUDIT REPORT
## e-TDSDoc V1 – Production Hardening Sprint
### Date: 2026-06-27

---

## 1. PHP CONFIGURATION

| Setting | Expected | Actual | Status |
|---------|----------|--------|--------|
| PHP Version | ≥ 8.1 | 8.5.3 | PASS |
| upload_max_filesize | ≥ 15M | 2M (FIXED via .htaccess → 15M) | FIXED |
| post_max_size | ≥ 20M | 8M (FIXED via .htaccess → 20M) | FIXED |
| max_execution_time | ≥ 120 | 0 (unlimited) | PASS |
| memory_limit | ≥ 128M | 128M | PASS |
| display_errors | Off | Off | PASS |
| log_errors | On | On | PASS |
| error_log | configured | empty (FIXED → storage/php-error.log) | FIXED |
| error_reporting | E_ALL & ~E_DEPRECATED | 22527 (E_ALL) | PASS |

## 2. SESSION CONFIGURATION

| Setting | Expected | Actual | Status |
|---------|----------|--------|--------|
| session.name | custom | ETDS_QC_SESSION | PASS |
| session.cookie_httponly | 1 | 0 (FIXED → 1) | FIXED |
| session.cookie_secure | 1 (HTTPS) | 0 (FIXED → conditional on HTTPS) | FIXED |
| session.use_strict_mode | 1 | 0 (FIXED → 1) | FIXED |
| session.gc_maxlifetime | ≥ 3600 | 1440 (FIXED → 3600) | FIXED |
| session.save_path | configured | empty (system default) | INFO |
| Session fixation protection | regenerate_id on login/logout | Present | PASS |

## 3. HTTPS CONFIGURATION

| Check | Status | Notes |
|-------|--------|-------|
| HTTPS available | INFO | Depends on XAMPP/Apache config |
| HSTS header | PASS | Set when HTTPS active |
| Secure cookies | FIXED | Now conditional on HTTPS |

## 4. UPLOAD DIRECTORY PERMISSIONS

| Directory | Permission | Status |
|-----------|------------|--------|
| storage/etds-qc/ | drwxr-xr-x | PASS |
| storage/etds-qc/cases/ | drwxr-xr-x | PASS |
| storage/etds-qc/uploads/ | drwxr-xr-x | PASS |
| storage/etds-qc/settings/ | drwxr-xr-x | PASS |

## 5. JSON STORAGE PERMISSIONS

| File | Permission | Status |
|------|------------|--------|
| users.json | rw-r--r-- | PASS |
| config.json | rw-r--r-- | PASS |
| settings/counters.json | rw-r--r-- | PASS |

## 6. ERROR LOGGING

| Check | Status | Notes |
|-------|--------|-------|
| PHP error_log | FIXED | Now configured to storage/php-error.log |
| Runtime error log | PASS | storage/etds-qc/runtime-error.log |
| display_errors | PASS | Off (production-safe) |
| Exception handling | PASS | Global try/catch in index.php |

## 7. REQUIRED PHP EXTENSIONS

| Extension | Status |
|-----------|--------|
| ZipArchive | YES |
| finfo | YES |
| mime_content_type | YES |
| json_encode | YES |
| session_start | YES |
| random_bytes | YES |
| password_hash | YES |

---

## ISSUES FOUND

| # | Severity | Issue | Status |
|---|----------|-------|--------|
| E-01 | Critical | PHP upload_max_filesize (2M) < app limit (15M) | FIXED |
| E-02 | Critical | PHP post_max_size (8M) < app limit (15M) | FIXED |
| E-03 | High | session.cookie_httponly = Off | FIXED |
| E-04 | High | session.cookie_secure = Off | FIXED |
| E-05 | High | session.use_strict_mode = Off | FIXED |
| E-06 | High | error_log not configured | FIXED |
| E-07 | Medium | session.gc_maxlifetime too short (1440s) | FIXED |
| E-08 | Informational | Session save_path uses system default | ACCEPTED |

---

**Environment Audit Result: 8 issues found, 7 FIXED, 1 ACCEPTED**
