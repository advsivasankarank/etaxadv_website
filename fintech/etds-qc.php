<?php
$page_title = "e-TDS QC Tool | E Tax Advisors";
$page_description = "e-TDS QC Tool - Data Quality Validation & Excel Preparation. Validate Excel, PDF and scanned TDS data, identify errors, rectify inconsistencies, and generate clean Excel outputs.";
$page_path = '/fintech/etds-qc.php';

require_once dirname(__DIR__) . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">TDS Automation</div>
        <h1>e-TDS QC Tool</h1>
        <p>
          <strong>Data Quality Validation &amp; Excel Preparation</strong>
        </p>
        <p>
          Validate Excel, PDF and scanned TDS data, identify errors, rectify inconsistencies,
          and generate clean Excel outputs.
        </p>
        <div class="hero-actions">
          <span class="btn btn-primary" aria-disabled="true" style="pointer-events:none;opacity:.65;cursor:not-allowed;">Coming Soon</span>
          <a class="btn btn-outline" href="<?= htmlspecialchars(site_href('/fintech-tools.php')) ?>">Back to Fintech Tools</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Coming Soon</span>
          <span class="proof-chip">TDS workflow support</span>
          <span class="proof-chip">Excel preparation</span>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-grid">
          <div class="hero-metric">
            <strong>Document Intake</strong>
            <span>Structured handling for Excel, PDF and scanned TDS data received from clients.</span>
          </div>
          <div class="hero-metric">
            <strong>Validation Focus</strong>
            <span>Early-stage checks designed to surface inconsistencies before downstream processing begins.</span>
          </div>
          <div class="hero-metric">
            <strong>Rectification Support</strong>
            <span>Planned workspace for resolving quality issues and standardising output records.</span>
          </div>
          <div class="hero-metric">
            <strong>Excel Output Readiness</strong>
            <span>Cleaned and structured output preparation for operational handoff and follow-up processing.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="card" style="max-width:900px;margin:0 auto;">
        <div class="section-header" style="margin-bottom:20px;">
          <p class="section-label">Coming Soon</p>
          <h2 class="section-title">Coming Soon</h2>
          <p class="section-intro">
            We are building a powerful data quality validation tool for TDS processing workflows. The e-TDS QC Tool will help streamline document intake, validation, error rectification and Excel preparation for downstream processing.
          </p>
        </div>
        <div class="grid-2">
          <article class="card card-muted">
            <h3>Expected Features</h3>
            <ul class="list-clean">
              <li>Excel Validation</li>
              <li>PDF Processing</li>
              <li>OCR-Based Data Extraction</li>
            </ul>
          </article>
          <article class="card card-muted">
            <h3>Planned Workflow Support</h3>
            <ul class="list-clean">
              <li>Data Quality Checks</li>
              <li>Error Rectification Workspace</li>
              <li>Excel Output Generation</li>
            </ul>
          </article>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why This Tool</p>
        <h2 class="section-title">Why e-TDS QC Tool?</h2>
        <p class="section-intro">
          The e-TDS QC Tool is being developed to simplify the collection and validation of TDS-related data received from clients in various formats. By identifying inconsistencies early and standardizing data quality checks, the tool aims to reduce manual effort and improve processing efficiency.
        </p>
      </div>

      <div class="grid-3">
        <article class="card card-muted">
          <div class="feature-icon">Q C</div>
          <h3>Earlier quality control</h3>
          <p>Surface inconsistencies before they affect spreadsheet preparation, review cycles or downstream workflow timing.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">FMT</div>
          <h3>Format flexibility</h3>
          <p>Support intake from client-submitted Excel, PDFs and scanned records without relying on one fixed source format.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">EFF</div>
          <h3>Processing efficiency</h3>
          <p>Reduce manual checking effort by creating a more consistent validation and rectification workflow.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Development Status</p>
        <h2 class="section-title">Development Status</h2>
      </div>

      <div class="grid-3">
        <article class="card">
          <h3>Project Status</h3>
          <p class="tech-subtitle">Current Standing</p>
          <p>In Development</p>
        </article>
        <article class="card">
          <h3>Current Phase</h3>
          <p class="tech-subtitle">Planning Cycle</p>
          <p>Planning &amp; Architecture</p>
        </article>
        <article class="card">
          <h3>Release</h3>
          <p class="tech-subtitle">Availability</p>
          <p>To Be Announced</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container cta-band">
      <div>
        <h2>Stay Tuned</h2>
        <p>
          The e-TDS QC Tool is currently under development and will be launched soon as part of the E Tax Advisors Fintech Tools ecosystem.
        </p>
      </div>
      <div class="card">
        <h3>Explore the ecosystem</h3>
        <ul class="list-clean">
          <li>Review the broader Fintech Tools lineup from E Tax Advisors.</li>
          <li>Book a consultation if you need interim support for TDS workflow preparation.</li>
          <li>Track future product launches through our technology and advisory pages.</li>
        </ul>
        <div class="cta-actions">
          <a class="btn btn-outline" href="<?= htmlspecialchars(site_href('/fintech-tools.php')) ?>">View Fintech Tools</a>
          <a class="btn btn-primary" href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Book Consultation</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
