<?php
$page_title = "Tools | E Tax Advisors Private Limited";
include __DIR__.'/includes/header.php';
?>

<main id="main-content">
  <section class="hero" style="padding-bottom:36px;">
    <div class="container">
      <div class="grid">
        <div>
          <div class="kicker">Tools for clients</div>
          <h1>Client Tools</h1>
          <p class="tagline">Utilities and working sheets for faster compliance</p>
          <p>
            These tools are provided for client convenience. For assisted computation or guidance,
            please book a consultation.
          </p>
          <div class="actions">
            <a class="btn primary" href="tools/tax-working-ay2026-27.php" target="_blank" rel="noopener">Open Tax Working Tool</a>
            <a class="btn secondary" href="contact.php#consult">Assisted Support</a>
          </div>
        </div>

        <div class="heroCard">
          <div class="item">
            <div class="badge">AY</div>
            <div><b>Income Tax Working</b><div class="small">Salary, deductions and rebate under Section 87A</div></div>
          </div>
          <div class="item">
            <div class="badge">PDF</div>
            <div><b>PDF Output</b><div class="small">Generate and save your working</div></div>
          </div>
          <div class="item">
            <div class="badge">FMT</div>
            <div><b>Structured Format</b><div class="small">Employer-ready presentation</div></div>
          </div>
          <div class="item">
            <div class="badge">NOTE</div>
            <div><b>Disclaimer</b><div class="small">Final tax depends on facts and applicable law</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <h2>Available Tools</h2>
      <p class="lead">We will add more tools progressively.</p>

      <div class="tiles">
        <div class="tile emph">
          <div class="title">SalPro (Staff Login)</div>
          <div class="desc">Internal Salary Proof Pack processing dashboard for authorized staff.</div>
          <a class="link" href="tools/salpro/login.php">Login &rarr;</a>
        </div>

        <div class="tile emph">
          <div class="title">Income Tax Working (AY 2026-27)</div>
          <div class="desc">Enter salary details and generate a structured working with PDF output.</div>
          <a class="link" href="tools/tax-working-ay2026-27.php" target="_blank" rel="noopener">Open tool &rarr;</a>
        </div>

        <div class="tile emph">
          <div class="title">e-BAL</div>
          <div class="desc">Balance sheet, profit and loss, PDF generation, and XML-based trial balance processing.</div>
          <a class="link" href="tools/e-bal/">Open tool &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">Upcoming Tools</div>
          <div class="desc">GST reconciliation helper, TDS due-date checker and compliance calendars.</div>
          <a class="link" href="contact.php#consult">Request a tool &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">Client Support Ticket</div>
          <div class="desc">Raise a ticket for service issues, feedback or suggestions.</div>
          <a class="link" href="client-support.php">Raise ticket &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">e-Task (Preview)</div>
          <div class="desc">Task and compliance tracking web app currently under development.</div>
          <a class="link" href="e-task.php">View &rarr;</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__.'/includes/footer.php'; ?>
