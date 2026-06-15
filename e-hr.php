<?php
require_once __DIR__ . '/includes/contact-handler.php';
$page_title = "e-HR – HR & Labour Compliance Platform | E Tax Advisors";
$page_description = "e-HR is a structured HR and labour law compliance platform for businesses managing payroll, statutory registers and employee compliance.";
$page_path = '/e-hr.php';
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
        <div class="eyebrow">e-HR</div>
        <h1>A structured HR and labour law compliance platform for businesses that want regulatory certainty, payroll alignment and employee record discipline.</h1>
        <p>
          e-HR is the HR and labour compliance platform from E Tax Advisors Private Limited. It helps businesses manage
          statutory registers, payroll-linked compliance, employee life-cycle documentation and inspection readiness
          through a structured operational framework.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Request Demo</a>
          <a class="btn btn-outline" href="contact.php#consult">Book Consultation</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Labour law compliance</span>
          <span class="proof-chip">Payroll coordination</span>
          <span class="proof-chip">Statutory register management</span>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-grid">
          <div class="hero-metric">
            <strong>Statutory Registers</strong>
            <span>Digital maintenance of registers under Factories Act, Shops Act, Contract Labour and other applicable laws.</span>
          </div>
          <div class="hero-metric">
            <strong>Payroll Integration</strong>
            <span>Structured data handoff between payroll processing and compliance reporting for TDS, PF, ESI and bonus.</span>
          </div>
          <div class="hero-metric">
            <strong>Inspection Readiness</strong>
            <span>Records organised, indexed and updated for inspection by labour authorities or third-party auditors.</span>
          </div>
          <div class="hero-metric">
            <strong>Employee Lifecycle</strong>
            <span>Offer letters, appointment letters, attendance, leave, separation and full-exit documentation in one workflow.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Platform Features</p>
        <h2 class="section-title">e-HR covers the full spectrum of HR and labour compliance operations.</h2>
      </div>

      <div class="grid-3">
        <article class="card card-muted">
          <div class="feature-icon">REG</div>
          <h3>Statutory Register Management</h3>
          <p>Digital registers for all applicable labour laws — attendance, wages, overtime, leave, contract labour, maternity benefit and more.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">PAY</div>
          <h3>Payroll Compliance Linking</h3>
          <p>Coordination between payroll data and statutory obligations — TDS, PF, ESI, bonus calculation, gratuity and professional tax.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">DOC</div>
          <h3>Employee Documentation Hub</h3>
          <p>Centralised repository for appointment letters, contracts, ID proofs, address proofs, educational certificates and experience records.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">AUD</div>
          <h3>Audit & Inspection Support</h3>
          <p>Structured data packs for labour department inspections, statutory audits and compliance reviews with notice-response tracking.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">LIC</div>
          <h3>Licence & Registration Tracking</h3>
          <p>Renewal and compliance tracking for factory licence, shop establishment registration, contract labour licence and other registrations.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">DAS</div>
          <h3>Dashboard & Reporting</h3>
          <p>Compliance dashboards, register status views and exception reports for management review and decision support.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why e-HR</p>
        <h2 class="section-title">Built for businesses that take employee compliance seriously.</h2>
      </div>

      <div class="grid-2">
        <article class="card">
          <h3>For employers</h3>
          <ul class="list-clean">
            <li>Reduces risk of non-compliance under labour laws and state-specific regulations</li>
            <li>Centralised record-keeping improves inspection response time</li>
            <li>Payroll and compliance teams work from the same structured data set</li>
            <li>Automated tracking of renewal dates, return due dates and submission deadlines</li>
            <li>Management visibility into compliance status across multiple locations or units</li>
          </ul>
        </article>
        <article class="card">
          <h3>For compliance teams</h3>
          <ul class="list-clean">
            <li>Structured intake of employee data and statutory document collection</li>
            <li>Checklist-led validation before statutory submissions</li>
            <li>Exception flagging and escalation for missing or delayed compliance items</li>
            <li>Coordination with legal and tax teams when labour matters affect filing or liability</li>
            <li>Audit trail for every compliance action taken on the platform</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Four steps to HR and labour compliance readiness.</h2>
      </div>

      <div class="process-timeline">
        <article class="timeline-step">
          <span class="timeline-number">1</span>
          <h3>Assessment and scope</h3>
          <p>Applicable labour laws, number of employees, locations, existing registers and current compliance status are reviewed first.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">2</span>
          <h3>Data collection and setup</h3>
          <p>Employee records, statutory documents, payroll reports and existing registers are collected, digitised and organised on the platform.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">3</span>
          <h3>Register maintenance and filing</h3>
          <p>Statutory registers are maintained on a continuous basis, returns are prepared and filings are completed within prescribed timelines.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">4</span>
          <h3>Reporting and inspection readiness</h3>
          <p>Monthly compliance status reports, exception summaries and inspection-ready data packs are shared with management.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Screenshots</p>
        <h2 class="section-title">A preview of the e-HR platform interface.</h2>
      </div>

      <div class="grid-3">
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>e-HR Dashboard View</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Statutory Register Module</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Compliance Dashboard</span>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Pricing</p>
        <h2 class="section-title">Simple, transparent pricing tailored to your business size.</h2>
      </div>

      <div class="card" style="max-width:480px;margin:0 auto;text-align:center;padding:48px 32px;">
        <h3 style="margin-bottom:12px;">Contact for Pricing</h3>
        <p style="color:var(--muted);margin-bottom:24px;">
          e-HR pricing depends on employee count, number of locations and applicable labour laws. Contact us for a custom quote.
        </p>
        <a class="btn btn-primary" href="#consult">Request Pricing</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Common questions about e-HR.</h2>
      </div>

      <div class="faq-list">
        <details class="faq-item">
          <summary>What labour laws does e-HR cover?</summary>
          <p>e-HR covers the Factories Act, Shops and Establishment Act, Contract Labour (R&A) Act, Maternity Benefit Act, Payment of Wages Act, Minimum Wages Act, Payment of Bonus Act, Payment of Gratuity Act, Employees' State Insurance Act, Employees' Provident Fund Act and Professional Tax acts, along with state-specific rules where applicable.</p>
        </details>
        <details class="faq-item">
          <summary>Is e-HR suitable for small businesses with fewer than 10 employees?</summary>
          <p>Yes. e-HR is designed to scale. For smaller teams, we offer a streamlined compliance module covering essential registers, payroll linking and inspection preparation without overcomplicating the process.</p>
        </details>
        <details class="faq-item">
          <summary>Can e-HR integrate with existing payroll software?</summary>
          <p>e-HR accepts structured payroll data exports from standard payroll software. Our team works with your payroll provider to set up the data handoff so that compliance registers stay updated without manual re-entry.</p>
        </details>
        <details class="faq-item">
          <summary>How does e-HR handle inspection readiness?</summary>
          <p>All statutory registers are maintained in a format aligned with inspection requirements. Data packs can be generated on request with indexed sections covering each applicable labour law, employee rosters and compliance filings.</p>
        </details>
        <details class="faq-item">
          <summary>Does e-HR include legal support for labour notices or disputes?</summary>
          <p>e-HR focuses on compliance infrastructure and preventive record-keeping. Where a labour notice, inspection or dispute arises, our legal advisory team handles the matter separately through the E Tax Advisors litigation desk.</p>
        </details>
        <details class="faq-item">
          <summary>What is the onboarding timeline for e-HR?</summary>
          <p>Typical onboarding takes 2–4 weeks depending on data availability, number of employees and the scope of applicable labour laws. A dedicated compliance manager is assigned for the setup phase.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="consult">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Get Started</p>
        <h2 class="section-title">Interested in e-HR? Let us walk you through the platform.</h2>
      </div>

      <div class="contact-card consult-form-card" style="max-width:720px;margin:0 auto;">
        <p>Fill in your details and our compliance team will reach out with a personalised demo and pricing.</p>

<?php if ($consult_result && $consult_result['success']): ?>
        <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
        <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(site_href('/e-hr.php')) ?>#consult">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="product_consult">
          <input type="hidden" name="service" value="e-HR - HR & Labour Compliance Platform">
          <input type="hidden" name="source_page" value="/e-hr.php">
          <div class="form-grid">
            <div class="field">
              <label for="hr_name">Name</label>
              <input class="input" id="hr_name" name="name" required />
            </div>
            <div class="field">
              <label for="hr_mobile">Mobile</label>
              <input class="input" id="hr_mobile" name="mobile" required />
            </div>
            <div class="field">
              <label for="hr_email">Email</label>
              <input class="input" id="hr_email" name="email" type="email" required />
            </div>
            <div class="field">
              <label for="hr_org">Organisation</label>
              <input class="input" id="hr_org" name="organisation" />
            </div>
            <div class="field">
              <label for="hr_time">Preferred Contact Time</label>
              <input class="input" id="hr_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date" />
            </div>
            <div class="field full-span">
              <label for="hr_msg">Message / Requirement</label>
              <textarea class="input" id="hr_msg" name="message" placeholder="Tell us about your HR and labour compliance requirements..." required></textarea>
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
