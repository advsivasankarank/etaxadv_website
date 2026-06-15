# E Tax Advisors - Production Deployment Checklist

## Pre-Deployment Configuration

### 1. Database Configuration
- [ ] Update `support/config.php` with real MySQL credentials:
  - `DB_HOST` - usually `localhost`
  - `DB_NAME` - e.g., `etaxadv_db`
  - `DB_USER` - e.g., `etaxadv_user`
  - `DB_PASS` - strong password
- [ ] Run `install.sql` to create the `enquiries` table
- [ ] Run `support/install.sql` to create ticketing tables (if not already done)
- [ ] Verify DB connection works: check testimonials appear on homepage

### 2. Environment Configuration
- [ ] Update Google Analytics ID in `includes/header.php`:
  - Replace `G-XXXXXXXXXX` with real GA4 Measurement ID
- [ ] Update Google Search Console verification:
  - Replace `REPLACE_WITH_GSC_CODE` in `includes/header.php`
- [ ] Create OG image at `assets/img/og-image.jpg` (1200x630px recommended)

### 3. Domain & SSL
- [ ] Ensure SSL certificate is active for `https://www.etaxadv.com`
- [ ] Verify HTTPS redirect works (configured in `.htaccess`)
- [ ] Test SSL through https://www.ssllabs.com/ssltest/

### 4. File System
- [ ] Delete development artifact directories:
  - `ebal_1/`
  - `ebal_12/`
  - `e-kanakan_bef dev/`
  - `back_office/`
- [ ] Remove the `audit-report.html` file from web root
- [ ] Move error logs outside public web root OR delete them

## Security Checks

### 5. Critical Security
- [ ] DELETE all `error_log` files (they contain DB credentials):
  - `support/error_log`
  - `support/admin/error_log`
  - `support/agent/error_log`
- [ ] Verify `.htaccess` is accessible and working
- [ ] Test that `/support/config.php` returns 403 when accessed via browser
- [ ] Remove/implement `support/admin/update.php` (currently empty stub)

### 6. Security Headers
- [ ] Verify security headers are being sent (use https://securityheaders.com/)
- [ ] Test HSTS header after HTTPS is confirmed working

## SEO Checks

### 7. SEO Essentials
- [ ] Submit `sitemap.xml` to Google Search Console
- [ ] Submit `sitemap.xml` to Bing Webmaster Tools
- [ ] Verify `robots.txt` is accessible and correct
- [ ] Add `favicon.ico` to web root (32x32 or 16x16 ICO file)
- [ ] Add `apple-touch-icon.png` (180x180 PNG)

### 8. Content Verification
- [ ] Verify all 27 pages load without PHP errors
- [ ] Check all forms submit correctly
- [ ] Verify testimonials display on pages
- [ ] Test all navigation links (desktop mega menu + mobile)

## Performance Checks

### 9. Optimization
- [ ] Minify `assets/css/style.css`
- [ ] Minify `assets/js/main.js`
- [ ] Convert `assets/img/logo.png` to WebP (save as logo.webp)
- [ ] Convert `assets/img/ekanakan.png` to WebP (save as ekanakan.webp)
- [ ] Add alt text to all images
- [ ] Test with Google PageSpeed Insights

## Email Configuration

### 10. Email Setup
- [ ] Verify `support@etaxadv.com` email account exists in cPanel
- [ ] Test that form submissions generate email notifications
- [ ] Verify `info@etaxadv.com` email alias/forwarding

## Backup

### 11. Backup
- [ ] Take full backup of files via cPanel File Manager
- [ ] Export database via phpMyAdmin
- [ ] Store backup locally (outside web root)

## Post-Deployment

### 12. Monitoring
- [ ] Set up Google Analytics to track:
  - Page views per service page
  - Form submissions as conversions
  - WhatsApp click events
  - Phone call click events
- [ ] Add Google Search Console and monitor for crawl errors
- [ ] Set up uptime monitoring (e.g., UptimeRobot)

### 13. Verification
- [ ] Test complete enquiry flow:
  - Submit form → stored in DB → email sent to office
- [ ] Test support ticket flow:
  - Submit ticket → ticket created → email notification
- [ ] Test WhatsApp link works on mobile
- [ ] Test phone click-to-call on mobile
- [ ] Test exit intent popup appears and closes
- [ ] Test sticky WhatsApp and consultation buttons
