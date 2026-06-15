<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Income Tax Consultant in Puducherry – Returns, Notices & Planning | E Tax Advisors";
$page_description = "Professional income tax advisory, return preparation, notice response and tax planning in Puducherry. Led by Advocate K. Sivasankaran. Income tax consultant Puducherry.";
$page_path = '/income-tax-consultant-puducherry.php';

$serviceTestimonials = testimonial_get_featured(6);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'it_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();

$localbusiness_schema = [
  '@context' => 'https://schema.org',
  '@type' => 'LocalBusiness',
  '@id' => 'https://www.etaxadv.com/income-tax-consultant-puducherry.php',
  'name' => 'E Tax Advisors Private Limited – Income Tax Consultant Puducherry',
  'description' => 'Professional income tax advisory, return preparation, notice response and tax planning services in Puducherry.',
  'url' => 'https://www.etaxadv.com/income-tax-consultant-puducherry.php',
  'telephone' => '+91-98946-26300',
  'email' => 'support@etaxadv.com',
  'image' => 'https://www.etaxadv.com/assets/img/og-image.jpg',
  'address' => [
    '@type' => 'PostalAddress',
    'streetAddress' => 'No. 234, I Floor, Lawspet Main Road, Pakkamudayanpet, Lawspet',
    'addressLocality' => 'Puducherry',
    'postalCode' => '605008',
    'addressCountry' => 'IN',
  ],
  'areaServed' => [
    '@type' => 'City',
    'name' => 'Puducherry',
  ],
  'priceRange' => '$$',
  'openingHours' => 'Mo-Sa 09:30-18:30',
];

$faq_schema = [
  '@context' => 'https://schema.org',
  '@type' => 'FAQPage',
  'mainEntity' => [
    [
      '@type' => 'Question',
      'name' => 'What income tax services do you offer in Puducherry?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'We offer end-to-end income tax services including return preparation and filing for individuals, businesses, trusts and NRIs; tax planning and structuring; notice response and representation; scrutiny assessment support; TDS compliance and reconciliation; and advisory on capital gains, business income and professional earnings.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'When is the deadline for filing income tax returns in India?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'The due date for individual taxpayers is 31 July of the assessment year. For businesses requiring audit, the due date is 31 October, and for taxpayers requiring transfer pricing audit, it is 30 November. We recommend early filing to avoid last-minute errors and compliance pressure.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'Can you help with income tax notices from the Income Tax Department?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Yes. We routinely assist clients with notice response for u/s 139(9), 142(1), 143(1), 143(2), 148, 245 and other provisions. Our process includes notice analysis, document collection, response drafting and filing through the Income Tax portal, followed by follow-up until the matter is closed.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'Do you offer tax planning for salaried employees and businesses?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Yes. We provide structured tax planning for salaried professionals, business owners and corporate entities. This includes investment planning under 80C to 80U, capital gains optimisation, salary restructuring, advance tax estimation and compliance with alternative minimum tax provisions where applicable.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'What is the process for scrutiny assessment support?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Our scrutiny support process involves: (1) review of the scrutiny notice and identifying specific issues raised, (2) collection and organisation of supporting documents and reconciliations, (3) drafting detailed written submissions with case references where applicable, (4) coordinating with the Assessing Officer and attending hearings, and (5) follow-through until the assessment order is finalised.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'Why choose E Tax Advisors as your income tax consultant in Puducherry?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'E Tax Advisors is led by Advocate K. Sivasankaran with over 15 years of experience in tax advisory and litigation. We offer a structured, process-driven approach with dedicated teams for return filing, notice response and compliance. Our clients benefit from integrated support across tax, company compliance, labour law and bookkeeping under one roof.',
      ],
    ],
  ],
];
require_once __DIR__ . '/includes/header.php';
?>
<main id="main-content">
  <script type="application/ld+json"><?= json_encode($localbusiness_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
  <script type="application/ld+json"><?= json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Income Tax Consultant</div>
        <h1>Income Tax Consultant in Puducherry – Returns, Notices &amp; Strategic Planning</h1>
        <p>
          Professional income tax advisory, return preparation, notice response and tax planning for individuals,
          businesses, trusts and NRIs in Puducherry. Led by Advocate K. Sivasankaran with a structured,
          process-driven delivery model.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Book Consultation</a>
          <a class="btn btn-outline" href="services.php#income-tax">All Tax Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Return Filing &amp; Planning</span>
          <span class="proof-chip">Notice Response &amp; Scrutiny</span>
          <span class="proof-chip">TDS Compliance &amp; Reconciliation</span>
        </div>
        <p class="hero-note">Engagement-based income tax services for residents and businesses across Puducherry.</p>
      </div>

      <div class="hero-visual" aria-label="Income tax advisory overview">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Structured income tax advisory for every filing season.</h2>
            <p>
              From return preparation to notice response and scrutiny representation, every engagement follows
              documented review, validation and follow-through protocols rather than one-time compliance alone.
            </p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>3000+</strong>
              <span>returns filed across individual, business and trust categories</span>
            </div>
            <div class="hero-metric">
              <strong>24-48 hrs</strong>
              <span>notice response and escalation support windows</span>
            </div>
            <div class="hero-metric">
              <strong>Integrated</strong>
              <span>tax, compliance, bookkeeping and representation coordination</span>
            </div>
            <div class="hero-metric">
              <strong>Structured</strong>
              <span>checklists, maker-checker review and closure summaries</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container authority-ribbon">
      <div class="authority-pill">
        <strong>3000+</strong>
        <span>Clients Served</span>
      </div>
      <div class="authority-pill">
        <strong>15+</strong>
        <span>Years Experience</span>
      </div>
      <div class="authority-pill">
        <strong>50+</strong>
        <span>Professionals</span>
      </div>
      <div class="authority-pill">
        <strong>Dedicated</strong>
        <span>Compliance Team</span>
      </div>
      <div class="authority-pill">
        <strong>Technology</strong>
        <span>Enabled Processes</span>
      </div>
      <div class="authority-pill">
        <strong>Confidential</strong>
        <span>&amp; Secure Handling</span>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Income Tax Services</p>
        <h2 class="section-title">Complete income tax solutions for individuals, businesses and institutions in Puducherry.</h2>
        <p class="section-intro">
          Every engagement is supported by document validation, exception handling, review notes and response
          protocols. We do not treat compliance as a one-time transaction but as a recurring responsibility.
        </p>
      </div>

      <div class="grid-3 practice-grid">
        <article class="card practice-card">
          <h3>Income Tax Return Filing</h3>
          <p class="practice-problem">Common problems: missed deadlines, incorrect computations, mismatched form selection and notice risks.</p>
          <p class="practice-solution">We handle ITR preparation and e-filing for individuals, HUFs, partnerships, LLPs, companies, trusts and NRIs with thorough document review and computation validation.</p>
          <a class="btn btn-outline" href="#consult">File Your Return</a>
        </article>
        <article class="card practice-card">
          <h3>Tax Planning &amp; Structuring</h3>
          <p class="practice-problem">Common problems: missed deductions, poor investment alignment, unplanned tax outflows and last-minute rush.</p>
          <p class="practice-solution">We provide structured tax planning under 80C to 80U, capital gains optimisation, salary restructuring, advance tax estimation and business income alignment.</p>
          <a class="btn btn-outline" href="#consult">Plan Your Taxes</a>
        </article>
        <article class="card practice-card">
          <h3>Notice Response &amp; Representation</h3>
          <p class="practice-problem">Common problems: unexplained mismatches, unclear notice language, missing backup and submission anxiety.</p>
          <p class="practice-solution">We analyse notices, organise supporting records, draft precise responses and file them through the Income Tax portal with follow-up until closure.</p>
          <a class="btn btn-outline" href="#consult">Respond to Notice</a>
        </article>
        <article class="card practice-card">
          <h3>Scrutiny Assessment Support</h3>
          <p class="practice-problem">Common problems: incomplete reconciliations, weak submission drafting and prolonged assessment cycles.</p>
          <p class="practice-solution">We coordinate scrutiny responses from notice analysis through document collation, written submissions and hearing representation to final order review.</p>
          <a class="btn btn-outline" href="#consult">Scrutiny Support</a>
        </article>
        <article class="card practice-card">
          <h3>TDS Compliance &amp; Reconciliation</h3>
          <p class="practice-problem">Common problems: incorrect deductions, delayed deposits, form 26AS mismatches and return filing errors.</p>
          <p class="practice-solution">We manage TDS return filing, form 16/16A generation, 26AS reconciliation, correction statements and quarterly compliance tracking.</p>
          <a class="btn btn-outline" href="#consult">TDS Support</a>
        </article>
        <article class="card practice-card">
          <h3>NRI &amp; International Tax Advisory</h3>
          <p class="practice-problem">Common problems: residency confusion, dual taxation, foreign asset disclosure and DTAA interpretation.</p>
          <p class="practice-solution">We advise on residential status determination, foreign income reporting, DTAA benefits, foreign asset schedules and NRI return filing.</p>
          <a class="btn btn-outline" href="#consult">NRI Advisory</a>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why Choose Us</p>
        <h2 class="section-title">What sets E Tax Advisors apart as your income tax consultant in Puducherry.</h2>
        <p class="section-intro">
          We combine technical expertise with process discipline. Every engagement benefits from documented workflows,
          review controls and accountable communication.
        </p>
      </div>

      <div class="who-grid">
        <article class="who-card">
          <div class="feature-icon">AL</div>
          <h3>Advocate-Led Advisory</h3>
          <p>Led by Advocate K. Sivasankaran with 15+ years of experience in tax advisory, litigation and compliance across multiple jurisdictions.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">PR</div>
          <h3>Process-Driven Execution</h3>
          <p>Every filing, notice response or planning engagement follows documented checklists, maker-checker review and closure summaries.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">IN</div>
          <h3>Integrated Advisory Model</h3>
          <p>Tax, company compliance, labour law and bookkeeping support under one roof. No need to coordinate between separate consultants.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">TE</div>
          <h3>Technology-Enabled Delivery</h3>
          <p>Workflow tracking, client portals, reconciliation tools and accounting automation improve turnaround time and accuracy.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">TR</div>
          <h3>Timely Response Commitment</h3>
          <p>24 to 48 hour response windows for notices and escalation-sensitive matters. No delays in critical compliance situations.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">CO</div>
          <h3>Confidentiality &amp; Security</h3>
          <p>All client data, financial records and documents are handled with strict confidentiality and secure processing protocols.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Our Process</p>
        <h2 class="section-title">A structured 4-step engagement model for income tax advisory.</h2>
        <p class="section-intro">
          From initial consultation to final closure, every engagement follows a repeatable process designed for
          clarity, accuracy and accountability.
        </p>
      </div>

      <div class="grid-4 insight-grid">
        <article class="card insight-card">
          <span class="insight-tag" style="font-size:1.4rem;font-weight:700;">01</span>
          <h3>Consultation &amp; Scope</h3>
          <p>We review your requirement, assess your current tax position and define the scope of work, deliverables and timeline during an initial consultation.</p>
        </article>
        <article class="card insight-card">
          <span class="insight-tag" style="font-size:1.4rem;font-weight:700;">02</span>
          <h3>Documentation &amp; Review</h3>
          <p>You share the required documents through secure channels. We verify completeness, flag exceptions and prepare a review-ready working file.</p>
        </article>
        <article class="card insight-card">
          <span class="insight-tag" style="font-size:1.4rem;font-weight:700;">03</span>
          <h3>Execution &amp; Validation</h3>
          <p>We execute the agreed scope whether filing, notice response or planning report. Every output undergoes maker-checker review before submission.</p>
        </article>
        <article class="card insight-card">
          <span class="insight-tag" style="font-size:1.4rem;font-weight:700;">04</span>
          <h3>Delivery &amp; Follow-Through</h3>
          <p>We share the completed output with a closure summary, document the next actions and remain available for follow-up, queries or escalation if needed.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Frequently Asked Questions</p>
        <h2 class="section-title">Common questions about income tax consultancy in Puducherry.</h2>
      </div>

      <div class="who-grid" style="max-width:900px;margin:0 auto;">
        <article class="who-card" style="grid-column:1/-1;">
          <h3>What income tax services do you offer in Puducherry?</h3>
          <p>We offer end-to-end income tax services including return preparation and filing for individuals, businesses, trusts and NRIs; tax planning and structuring; notice response and representation; scrutiny assessment support; TDS compliance and reconciliation; and advisory on capital gains, business income and professional earnings.</p>
        </article>
        <article class="who-card" style="grid-column:1/-1;">
          <h3>When is the deadline for filing income tax returns in India?</h3>
          <p>The due date for individual taxpayers is 31 July of the assessment year. For businesses requiring audit, the due date is 31 October, and for taxpayers requiring transfer pricing audit, it is 30 November. We recommend early filing to avoid last-minute errors and compliance pressure.</p>
        </article>
        <article class="who-card" style="grid-column:1/-1;">
          <h3>Can you help with income tax notices from the Income Tax Department?</h3>
          <p>Yes. We routinely assist clients with notice response under sections 139(9), 142(1), 143(1), 143(2), 148, 245 and other provisions. Our process includes notice analysis, document collection, response drafting and filing through the Income Tax portal, followed by follow-up until the matter is closed.</p>
        </article>
        <article class="who-card" style="grid-column:1/-1;">
          <h3>Do you offer tax planning for salaried employees and businesses?</h3>
          <p>Yes. We provide structured tax planning for salaried professionals, business owners and corporate entities. This includes investment planning under 80C to 80U, capital gains optimisation, salary restructuring, advance tax estimation and compliance with alternative minimum tax provisions where applicable.</p>
        </article>
        <article class="who-card" style="grid-column:1/-1;">
          <h3>What is the process for scrutiny assessment support?</h3>
          <p>Our scrutiny support process involves: review of the scrutiny notice and identifying specific issues raised, collection and organisation of supporting documents and reconciliations, drafting detailed written submissions with case references where applicable, coordinating with the Assessing Officer and attending hearings, and follow-through until the assessment order is finalised.</p>
        </article>
        <article class="who-card" style="grid-column:1/-1;">
          <h3>Why choose E Tax Advisors as your income tax consultant in Puducherry?</h3>
          <p>E Tax Advisors is led by Advocate K. Sivasankaran with over 15 years of experience in tax advisory and litigation. We offer a structured, process-driven approach with dedicated teams for return filing, notice response and compliance. Our clients benefit from integrated support across tax, company compliance, labour law and bookkeeping under one roof.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">What our clients say about our income tax advisory services.</h2>
      </div>

<?php if ($serviceTestimonials): ?>
      <div class="testimonial-controls">
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('prev')" aria-label="Previous testimonials">Previous</button>
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('next')" aria-label="Next testimonials">Next</button>
      </div>

      <div class="testimonial-track" id="testimonialTrack">
<?php foreach ($serviceTestimonials as $item): ?>
        <article class="testimonial-card premium">
          <div class="testimonial-meta">
            <span class="verified-badge">Verified Client</span>
            <span class="star-rating"><?= h(testimonial_issue_stars((int)$item['rating'])) ?></span>
          </div>
          <span class="testimonial-type"><?= h($item['service_availed']) ?></span>
          <p><?= h($item['testimonial_text']) ?></p>
          <div class="testimonial-footer">
            <strong><?= h($item['client_name']) ?></strong>
            <span><?= h($item['company_name']) ?><?php if ($item['city']): ?>, <?= h($item['city']) ?><?php endif; ?></span>
          </div>
        </article>
<?php endforeach; ?>
      </div>
<?php else: ?>
      <div class="card">
        <h3>Approved testimonials will appear here.</h3>
        <p>Reviews are displayed only after approval and only when the client has granted publish permission.</p>
      </div>
<?php endif; ?>

      <div class="section-actions">
        <a class="btn btn-outline" href="<?= htmlspecialchars(app_href('/testimonial/')) ?>">View All Testimonials</a>
        <a class="btn btn-primary" href="<?= htmlspecialchars(app_href('/testimonial/#share-review')) ?>">Share Your Experience</a>
      </div>
    </div>
  </section>

  <section class="section" id="consult">
    <div class="container consult-shell">
      <div class="section-header">
        <p class="section-label">Book A Consultation</p>
        <h2 class="section-title">Discuss your income tax requirements with our team.</h2>
        <p class="section-intro">
          Use this route when you need professional assistance with return filing, a tax notice, planning advice,
          scrutiny support or any income-tax related matter.
        </p>
      </div>

      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What this consultation helps you do</h3>
          <ul class="list-clean">
            <li>Clarify your tax requirement, timeline and risk level before action is taken.</li>
            <li>Identify what records, reconciliations or supporting documents are missing.</li>
            <li>Route the matter to the right return filing, notice response or planning path.</li>
          </ul>
          <div class="consult-note">
            <strong>Preferred matters</strong>
            <span>Income tax return filing, notice response, scrutiny assessment, tax planning, TDS compliance and NRI tax advisory.</span>
          </div>
        </div>

        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

          <form method="post" action="<?= htmlspecialchars(site_href('/income-tax-consultant-puducherry.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="it_consult">
            <input type="hidden" name="source_page" value="/income-tax-consultant-puducherry.php">
            <div class="form-grid">
              <div class="field">
                <label for="it_name">Name</label>
                <input class="input" id="it_name" name="name" required />
              </div>
              <div class="field">
                <label for="it_mobile">Mobile</label>
                <input class="input" id="it_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="it_email">Email</label>
                <input class="input" id="it_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="it_org">Organisation</label>
                <input class="input" id="it_org" name="organisation" />
              </div>
              <div class="field">
                <label for="it_service">Service Required</label>
                <input class="input" id="it_service" name="service" placeholder="Return filing, notice response, tax planning, TDS, scrutiny, etc." required />
              </div>
              <div class="field">
                <label for="it_time">Preferred Consultation Time</label>
                <input class="input" id="it_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="it_msg">Brief Requirement</label>
                <textarea class="input" id="it_msg" name="message" required></textarea>
              </div>
              <div class="field full-span">
                <button class="btn btn-primary" type="submit">Submit Enquiry</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container cta-band">
      <div>
        <h2>Need professional income tax assistance in Puducherry?</h2>
        <p>
          Whether you need to file your return, respond to a notice, plan your taxes or handle a scrutiny matter,
          our team is ready to help with structured, process-driven advisory.
        </p>
        <div class="cta-actions">
          <a class="btn btn-primary" href="#consult">Request Consultation</a>
          <a class="btn btn-outline" href="tel:+919894626300">Call Now</a>
        </div>
      </div>
      <div class="card">
        <h3>Direct response channels</h3>
        <ul class="list-clean">
          <li>Call for immediate advisory routing and consultation booking.</li>
          <li>WhatsApp for quick contact and discussion initiation.</li>
          <li>Client Support for documented concerns, feedback and escalation.</li>
        </ul>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
