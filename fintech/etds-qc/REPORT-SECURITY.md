# SECURITY AUDIT REPORT
## eTDSDoc V1 – Production Hardening Sprint
### Date: 2026-06-27

---

## 1. AUTHENTICATION

| Check | Status | Notes |
|-------|--------|-------|
| Password hashing | PASS | bcrypt via password_hash(), cost 12 |
| Session regeneration on login | PASS | session_regenerate_id(true) |
| Session regeneration on logout | PASS | session_regenerate_id(true) |
| Login CSRF protection | PASS | Token verified before authentication |
| Default credentials | INFO | admin@etaxadv.local – should be changed in production |
| Login rate limiting | FIXED | rate_limit_check('login', 10) added |
| Account lockout | INFO | Not implemented – acceptable for internal tool |

## 2. AUTHORIZATION

| Check | Status | Notes |
|-------|--------|-------|
| Auth check on all non-login requests | PASS | etds_qc_require_auth() called for view≠login |
| POST action auth check | PASS | $user checked before all write actions |
| Session-based authorization | PASS | $_SESSION['etds_qc_user_id'] validated |

## 3. SESSION HANDLING

| Check | Status | Notes |
|-------|--------|-------|
| Custom session name | PASS | ETDS_QC_SESSION (not default PHPSESSID) |
| Cookie HttpOnly | FIXED | Now set to true |
| Cookie Secure | FIXED | Conditional on HTTPS |
| Cookie SameSite | FIXED | Set to Lax |
| Strict mode | FIXED | use_strict_mode = 1 |
| Session timeout | FIXED | gc_maxlifetime increased to 3600s |
| Session fixation protection | PASS | regenerate_id on login/logout |

## 4. CSRF PROTECTION

| Check | Status | Notes |
|-------|--------|-------|
| Token generation | PASS | random_bytes(32) via security.php |
| Token verification | PASS | hash_equals() comparison |
| Token in all forms | PASS | csrf_field() in all form templates |
| Token in AJAX requests | PASS | Sent via _csrf parameter |
| Dual-session issue | PASS | csrf_token() reuses existing ETDS_QC_SESSION |

## 5. XSS PROTECTION

| Check | Status | Notes |
|-------|--------|-------|
| Output escaping | PASS | etds_qc_h() uses htmlspecialchars ENT_QUOTES UTF-8 |
| Content-Security-Policy | FIXED | CSP header added |
| X-XSS-Protection | PASS | 1; mode=block |
| X-Content-Type-Options | PASS | nosniff |
| Input sanitization | PASS | clean_input() strips tags, normalizes whitespace |

## 6. FILE UPLOAD VALIDATION

| Check | Status | Notes |
|-------|--------|-------|
| Extension whitelist | PASS | Configurable via config.json |
| File size validation | PASS | ETDS_QC_MAX_UPLOAD_BYTES = 15MB |
| PHP upload size | FIXED | upload_max_filesize set to 15M |
| is_uploaded_file() check | PASS | Before processing |
| Safe file renaming | PASS | DOC-NNNNNN_vNN.ext format |
| MIME type detection | PASS | mime_content_type() + finfo |
| Content hash dedup | PASS | SHA-1 hash comparison |
| basename() on filenames | PASS | Prevents directory traversal in filenames |

## 7. PATH TRAVERSAL

| Check | Status | Notes |
|-------|--------|-------|
| Case ID validation | PASS | Regex /^ETD-\d{4}-\d{6}$/ |
| basename() on user filenames | PASS | Used in download/preview |
| Session file path construction | PASS | Through validated case ID |
| Directory structure validation | PASS | etds_qc_session_file() normalizes paths |

## 8. DIRECTORY TRAVERSAL

| Check | Status | Notes |
|-------|--------|-------|
| Storage .htaccess | PASS | Denies direct access to .json/.log/.xlsx etc |
| Application .htaccess | FIXED | Created with proper access controls |
| Directory indexing disabled | PASS | Options -Indexes |

## 9. INPUT VALIDATION

| Check | Status | Notes |
|-------|--------|-------|
| TAN format validation | PASS | /^[A-Z]{4}[0-9]{5}[A-Z]$/ |
| PAN format validation | PASS | Via engine rules |
| clean_input() | PASS | Trim, strip_tags, whitespace normalize, length limit |
| clean_multiline() | PASS | Preserves newlines, limits blank lines |
| Type casting | PASS | (string), (int), (float) casts used |

## 10. OUTPUT ESCAPING

| Check | Status | Notes |
|-------|--------|-------|
| HTML output escaping | PASS | etds_qc_h() on all dynamic output |
| XML output escaping | PASS | etds_qc_xml() for XLSX generation |
| JSON response escaping | PASS | json_encode with proper flags |
| CSV output | PASS | fputcsv() handles escaping |

## 11. RATE LIMITING

| Check | Status | Notes |
|-------|--------|-------|
| rate_limit_check() function | PASS | Exists in security.php |
| Login rate limiting | FIXED | 10 attempts per hour enforced |
| POST action rate limiting | INFO | Not implemented – acceptable for internal tool |

## 12. AUDIT LOGGING

| Check | Status | Notes |
|-------|--------|-------|
| Per-case audit trail | PASS | audit.json per case |
| Event tracking | PASS | All significant actions logged |
| IP address logging | FIXED | etds_qc_client_ip() captures real IP |
| Timestamp tracking | PASS | ISO 8601 timestamps with timezone |
| Old/new value tracking | PASS | Before/after values captured |

## 13. JSON INTEGRITY

| Check | Status | Notes |
|-------|--------|-------|
| Write locking | PASS | LOCK_EX on file_put_contents |
| JSON encoding validation | PASS | json_last_error() check |
| Read fallback | PASS | Default values on read failure |
| Atomic writes | PASS | file_put_contents with LOCK_EX |

## 14. ERROR HANDLING

| Check | Status | Notes |
|-------|--------|-------|
| Global exception handler | PASS | try/catch(Throwable) in index.php |
| Generic error messages | PASS | User sees safe flash message |
| Error details logged | PASS | Written to runtime-error.log |
| display_errors Off | PASS | No信息 leakage to users |

---

## ISSUES FOUND

| # | Severity | Issue | Status |
|---|----------|-------|--------|
| S-01 | Critical | No .htaccess in application directory | FIXED |
| S-02 | Critical | session.cookie_httponly Off | FIXED |
| S-03 | Critical | Login form pre-fills admin email | FIXED |
| S-04 | High | No rate limiting on login | FIXED |
| S-05 | High | No Content-Security-Policy header | FIXED |
| S-06 | High | Audit trail missing IP addresses | FIXED |
| S-07 | High | session.cookie_secure Off | FIXED |
| S-08 | High | session.use_strict_mode Off | FIXED |
| S-09 | Medium | Security headers not sent on main app view | FIXED |
| S-10 | Medium | Default admin credentials hardcoded | ACCEPTED – Internal tool |
| S-11 | Informational | No account lockout mechanism | ACCEPTED – Internal tool |

---

**Security Audit Result: 11 issues found, 9 FIXED, 2 ACCEPTED**
