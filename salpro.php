<?php
require_once __DIR__ . '/includes/contact-handler.php';
$page_title = "SalPro – Salary Tax Planning System | E Tax Advisors";
$page_description = "SalPro is a structured salary tax planning framework for employee tax optimization, TDS accuracy and working sheet automation.";
$page_path = '/salpro.php';
$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'product_consult') {
  $consult_result = contact_process_submission();
}
contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">SalPro</div>
        <h1>A salary tax planning framework for organisations that want tax-optimised payroll, TDS accuracy and structured working sheets.</h1>
        <p>
          SalPro is the salary tax planning system from E Tax Advisors Private Limited. It provides a structured framework
          for employee tax optimisation, TDS compliance, perquisite valuation, investment declaration processing and
          working sheet automation — built for finance, payroll and HR teams.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Request Demo</a>
          <a class="btn btn-outline" href="contact.php#consult">Book Consultation</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Tax optimisation</span>
          <span class="proof-chip">TDS accuracy</span>
          <span class="proof-chip">Working sheet automation</span>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-grid">
          <div class="hero-metric">
            <strong>Tax Optimisation</strong>
            <span>Structured planning to help employees optimise tax within legal framework while reducing employer liability risk.</span>
          </div>
          <div class="hero-metric">
            <strong>TDS Accuracy</strong>
            <span>Section-wise TDS computation, declaration validation, perquisite valuation and form 16 readiness checks.</span>
          </div>
          <div class="hero-metric">
            <strong>Working Sheet Automation</strong>
            <span>Automated salary working sheets with scenario modelling, cost-to-company views and year-end reconciliation.</span>
          </div>
          <div class="hero-metric">
            <strong>Compliance Integration</strong>
            <span>Data handoff to TDS return preparation, form 12BA, form 16 and audit-ready salary records.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Platform Features</p>
        <h2 class="section-title">SalPro covers the full salary tax planning and compliance lifecycle.</h2>
      </div>

      <div class="grid-3">
        <article class="card card-muted">
          <div class="feature-icon">OPT</div>
          <h3>Tax Optimisation Engine</h3>
          <p>Structure salary components, allowances and perquisites for optimal tax outcomes under the Income Tax Act, 1961.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">DEC</div>
          <h3>Investment Declaration Processing</h3>
          <p>Employee declarations, proof verification, section 80C to 80U tracking and year-end reconciliation with actuals.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">PER</div>
          <h3>Perquisite Valuation & Reporting</h3>
          <p>Valuation of taxable perquisites — housing, car, concessional loans, stock options and other fringe benefits.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">TDS</div>
          <h3>TDS Computation & Accuracy</h3>
          <p>Section-wise TDS calculation, surcharge and education cess application, marginal relief check and form 26Q preparation.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">WS</div>
          <h3>Working Sheet Automation</h3>
          <p>Dynamic salary working sheets with real-time tax calculations, employee-wise views and scenario comparison.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">F16</div>
          <h3>Form 16 & Audit Readiness</h3>
          <p>Automated form 16, form 12BA generation and structured data sets for salary audit and tax audit requirements.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why SalPro</p>
        <h2 class="section-title">Built for finance and HR teams that demand precision in salary tax planning.</h2>
      </div>

      <div class="grid-2">
        <article class="card">
          <h3>For employers</h3>
          <ul class="list-clean">
            <li>Reduces TDS deduction errors and associated interest or penalty exposure</li>
            <li>Structured framework for perquisite reporting and form 12BA compliance</li>
            <li>Employee tax planning support improves satisfaction and reduces queries during filing season</li>
            <li>Automated working sheets eliminate manual errors in salary structuring</li>
            <li>Audit-ready records for tax audit, salary audit and transfer pricing documentation</li>
          </ul>
        </article>
        <article class="card">
          <h3>For payroll teams</h3>
          <ul class="list-clean">
            <li>End-to-end declaration cycle from submission to verification and reconciliation</li>
            <li>Scenario modelling before finalising salary structure changes</li>
            <li>Seamless data handoff to payroll processing and TDS return software</li>
            <li>Year-end reconciliation between declared and actual investments</li>
            <li>Centralised repository of all tax-related employee records for reference and audit</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Four steps to salary tax planning and TDS accuracy.</h2>
      </div>

      <div class="process-timeline">
        <article class="timeline-step">
          <span class="timeline-number">1</span>
          <h3>Salary structure review</h3>
          <p>Existing salary components, allowances, perquisites and reimbursement policies are reviewed for tax efficiency and compliance.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">2</span>
          <h3>Declaration and proof collection</h3>
          <p>Employee investment declarations are collected, proofs are verified and section-wise tracking is set up for the financial year.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">3</span>
          <h3>TDS computation and payroll handoff</h3>
          <p>Monthly TDS is computed, perquisites are valued and structured data is handed off to payroll processing and TDS return systems.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">4</span>
          <h3>Year-end reconciliation and form 16</h3>
          <p>Actual investments are reconciled against declarations, form 16 and form 12BA are generated and audit-ready data packs are prepared.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Screenshots</p>
        <h2 class="section-title">A preview of the SalPro platform interface.</h2>
      </div>

      <div class="grid-3">
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Salary Tax Planning Dashboard</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Working Sheet View</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>TDS & Form 16 Module</span>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Pricing</p>
        <h2 class="section-title">Transparent pricing built around your employee base.</h2>
      </div>

      <div class="card" style="max-width:480px;margin:0 auto;text-align:center;padding:48px 32px;">
        <h3 style="margin-bottom:12px;">Contact for Pricing</h3>
        <p style="color:var(--muted);margin-bottom:24px;">
          SalPro pricing is based on employee count, salary structure complexity and perquisite reporting requirements. Contact us for a custom quote.
        </p>
        <a class="btn btn-primary" href="#consult">Request Pricing</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Common questions about SalPro.</h2>
      </div>

      <div class="faq-list">
        <details class="faq-item">
          <summary>How does SalPro differ from standard payroll software?</summary>
          <p>Payroll software processes salary and deductions. SalPro focuses on the tax planning layer — structuring salary components for tax efficiency, computing TDS accurately across all sections, valuing perquisites and generating audit-ready form 16 and form 12BA outputs.</p>
        </details>
        <details class="faq-item">
          <summary>Can SalPro handle employees across multiple tax regimes?</summary>
          <p>Yes. SalPro supports both the old tax regime (with deductions and exemptions) and the new tax regime under section 115BAC. Employees can choose their preferred regime and the system computes tax accordingly.</p>
        </details>
        <details class="faq-item">
          <summary>Does SalPro integrate with our existing payroll system?</summary>
          <p>SalPro accepts structured payroll data and can hand off computed TDS, perquisite values and declaration data to most standard payroll systems. Our team works with your payroll provider to set up the data flow.</p>
        </details>
        <details class="faq-item">
          <summary>What perquisites can SalPro value?</summary>
          <p>SalPro handles valuation of accommodation, motor car, concessional loans, stock options (ESOP/ESPP), leave travel concession, medical reimbursements, gifts, club memberships and other fringe benefits as per income tax rules.</p>
        </details>
        <details class="faq-item">
          <summary>Does SalPro handle TDS return filing?</summary>
          <p>SalPro prepares TDS computation data in a format that can be directly used for form 24Q and form 26Q preparation. TDS return filing is handled through our compliance team using the data generated by SalPro.</p>
        </details>
        <details class="faq-item">
          <summary>What is the typical onboarding timeline for SalPro?</summary>
          <p>Onboarding typically takes 2–3 weeks including salary structure mapping, employee data import, declaration workflow setup and team training. Onboarding is planned before the start of the financial year for optimal use.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="consult">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Get Started</p>
        <h2 class="section-title">Ready to bring precision to your salary tax planning?</h2>
      </div>

      <div class="contact-card consult-form-card" style="max-width:720px;margin:0 auto;">
        <p>Fill in your details and our tax team will reach out with a personalised demo and pricing for SalPro.</p>

<?php if ($consult_result && $consult_result['success']): ?>
        <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
        <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(site_href('/salpro.php')) ?>#consult">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="product_consult">
          <input type="hidden" name="service" value="SalPro - Salary Tax Planning System">
          <input type="hidden" name="source_page" value="/salpro.php">
          <div class="form-grid">
            <div class="field">
              <label for="sal_name">Name</label>
              <input class="input" id="sal_name" name="name" required />
            </div>
            <div class="field">
              <label for="sal_mobile">Mobile</label>
              <input class="input" id="sal_mobile" name="mobile" required />
            </div>
            <div class="field">
              <label for="sal_email">Email</label>
              <input class="input" id="sal_email" name="email" type="email" required />
            </div>
            <div class="field">
              <label for="sal_org">Organisation</label>
              <input class="input" id="sal_org" name="organisation" />
            </div>
            <div class="field">
              <label for="sal_time">Preferred Contact Time</label>
              <input class="input" id="sal_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date" />
            </div>
            <div class="field full-span">
              <label for="sal_msg">Message / Requirement</label>
              <textarea class="input" id="sal_msg" name="message" placeholder="Tell us about your salary tax planning requirements..." required></textarea>
            </div>
            <div class="field full-span">
              <button class="btn btn-primary" type="submit">Submit Enquiry</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
