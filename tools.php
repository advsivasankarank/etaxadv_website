<?php
$page_title = "Client Tools & Working Sheets | E Tax Advisors";
$page_description = "Access client-side tools, working sheets and internal advisory utilities provided by E Tax Advisors Private Limited.";
$page_path = '/tools.php';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Tools</div>
        <h1>Practical tools that support compliance readiness and structured client collaboration.</h1>
        <p>
          These utilities are designed to accelerate working preparation, internal review and service coordination.
          They complement advisory engagement and do not replace professional review where fact patterns matter.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="tools/tax-working-ay2026-27.php" target="_blank" rel="noopener">Open Tax Working Tool</a>
          <a class="btn btn-outline" href="contact.php#consult">Request Assisted Support</a>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Built for working clarity.</h2>
            <p>Client tools are intended to reduce document friction and improve information quality before advisory review.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Available Utilities</p>
        <h2 class="section-title">Current tools and structured support channels.</h2>
      </div>

      <div class="grid-3">
        <article class="card service-card">
          <div class="feature-icon">TAX</div>
          <h3>Income Tax Working</h3>
          <p>Prepare salary-oriented tax workings in a structured format with printable output.</p>
          <a class="service-link" href="tools/tax-working-ay2026-27.php" target="_blank" rel="noopener">Open tool</a>
        </article>
        <article class="card service-card">
          <div class="feature-icon">EB</div>
          <h3>e-BAL</h3>
          <p>Internal balance sheet, profit and loss, PDF output and XML-linked trial balance support.</p>
          <a class="service-link" href="tools/e-bal/">Open e-BAL</a>
        </article>
        <article class="card service-card">
          <div class="feature-icon">SUP</div>
          <h3>Client Support Ticketing</h3>
          <p>Raise documented concerns, feedback and escalation requests through the support desk.</p>
          <a class="service-link" href="client-support.php">Raise ticket</a>
        </article>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
