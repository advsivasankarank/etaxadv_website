<?php
$page_title = "Fintech Tools — E Tax Advisors Private Limited";
$page_description = "Technology-enabled solutions developed by E Tax Advisors Private Limited for compliance, accounting, HR management, financial reporting and professional learning.";
$page_path = '/fintech-tools.php';

require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">

  <section class="section">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Fintech Tools</p>
        <h2 class="section-title">Technology Enabled Services</h2>
        <p class="section-intro">Technology-enabled solutions developed by E Tax Advisors Private Limited for compliance, accounting, HR management, financial reporting and professional learning.</p>
      </div>

      <div class="ft-grid">
        <a class="ft-card" href="/e-pani.php">
          <h3>e-Pani</h3>
          <p class="ft-subtitle">Office Management Suite</p>
          <p class="ft-desc">Manage service orders, tasks, reminders, documents and workflow management.</p>
          <span class="ft-status">IN DEVELOPMENT</span>
          <span class="ft-btn">View Product</span>
        </a>

        <a class="ft-card" href="/e-hr.php">
          <h3>e-HR</h3>
          <p class="ft-subtitle">HR &amp; Labour Compliance Platform</p>
          <p class="ft-desc">Manage employee lifecycle, HR records and labour law compliance.</p>
          <span class="ft-status">IN DEVELOPMENT</span>
          <span class="ft-btn">View Product</span>
        </a>

        <a class="ft-card" href="/ekanakan.php">
          <h3>e-Kanakan</h3>
          <p class="ft-subtitle">Bookkeeping Automation</p>
          <p class="ft-desc">Convert accounting data into structured bookkeeping workflows.</p>
          <span class="ft-status">IN DEVELOPMENT</span>
          <span class="ft-btn">View Product</span>
        </a>

        <a class="ft-card" href="/e-bal.php">
          <h3>e-Bal</h3>
          <p class="ft-subtitle">Financial Reporting Automation</p>
          <p class="ft-desc">Preparation of Schedule III compliant financial statements under the Companies Act, 2013.</p>
          <span class="ft-status">IN DEVELOPMENT</span>
          <span class="ft-btn">View Product</span>
        </a>

        <a class="ft-card" href="/salpro.php">
          <h3>SalPro</h3>
          <p class="ft-subtitle">Salary Tax Planning</p>
          <p class="ft-desc">Employee salary declaration and tax planning platform.</p>
          <span class="ft-status">IN DEVELOPMENT</span>
          <span class="ft-btn">View Product</span>
        </a>

        <a class="ft-card" href="#/etds-qc">
          <span class="ft-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" focusable="false">
              <path d="M8 3.75h6.75L20.25 9v9.75A1.5 1.5 0 0 1 18.75 20.25H8a1.5 1.5 0 0 1-1.5-1.5v-13.5A1.5 1.5 0 0 1 8 3.75Z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.6"/>
              <path d="M14.75 3.75V9h5.25" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="1.6"/>
              <path d="m9.5 14.25 1.75 1.75 3.75-4.25" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
            </svg>
          </span>
          <span class="ft-tag">TDS Automation</span>
          <h3>e-TDS QC Tool</h3>
          <p class="ft-subtitle">Data Quality Validation &amp; Excel Preparation</p>
          <p class="ft-desc">Validate Excel, PDF and scanned TDS data, identify errors, rectify inconsistencies, and generate clean TaxPro-ready Excel outputs.</p>
          <ul class="ft-feature-list" aria-label="e-TDS QC Tool capabilities">
            <li>Excel Validation</li>
            <li>PDF &amp; OCR Extraction</li>
            <li>PAN Verification</li>
            <li>Duplicate Detection</li>
            <li>Error Rectification</li>
            <li>TaxPro Ready Export</li>
          </ul>
          <span class="ft-status">Coming Soon</span>
          <span class="ft-btn">Learn More</span>
        </a>

        <a class="ft-card" href="/etax-academy.php">
          <h3>E Tax Academy</h3>
          <p class="ft-subtitle">Professional Learning Platform</p>
          <p class="ft-desc">Training, certification and professional skill development.</p>
          <span class="ft-status">IN DEVELOPMENT</span>
          <span class="ft-btn">View Product</span>
        </a>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <h2>Need a Product Demonstration?</h2>
      <p>Book a consultation to understand how our technology solutions can help your organisation improve efficiency, compliance and operational control.</p>
      <div class="cta-contact-links">
        <a class="btn btn-gold btn-lg" href="contact.php">Book Consultation</a>
        <a class="btn btn-primary btn-lg" href="contact.php">Contact Us</a>
      </div>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
