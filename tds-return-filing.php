<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "TDS Return Filing Services – TDS Compliance & Advisory | E Tax Advisors";
$page_description = "Professional TDS return filing, correction, TRACES support and payroll compliance services for businesses and deductors.";
$page_path = '/tds-return-filing.php';
$homepageTestimonialSummary = testimonial_get_summary();
$homepageTestimonials = testimonial_get_featured(8);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tds_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">TDS Return Filing</div>
        <h1>Professional TDS return filing, corrections and deductor compliance support.</h1>
        <p>
          We assist businesses, deductors and payroll handlers with accurate TDS return preparation, quarterly filing,
          Form 16/16A issuance, correction statements, TRACES reconciliation and deductor advisory.
          Our process ensures that every return is validated before submission and each notice is handled with proper documentation.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Discuss TDS Requirement</a>
          <a class="btn btn-outline" href="services.php#tds">Explore TDS Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Quarterly TDS return filing</span>
          <span class="proof-chip">Form 16 & 16A issuance</span>
          <span class="proof-chip">TRACES reconciliation</span>
        </div>
        <p class="hero-note">End-to-end TDS compliance support for corporate deductors, firms, proprietors and individual taxpayers across India.</p>
      </div>
      <div class="hero-visual" aria-label="TDS compliance overview">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Built on accuracy and timely delivery.</h2>
            <p>TDS errors, late filing and mismatched challans can lead to notices and disallowances. We bring a structured review process to every return engagement.</p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>Quarterly</strong>
              <span>return cycles managed across Form 24Q, 26Q, 27Q and 27EQ</span>
            </div>
            <div class="hero-metric">
              <strong>Corrections</strong>
              <span>belated or revised return support with challan reconciliation</span>
            </div>
            <div class="hero-metric">
              <strong>TRACES</strong>
              <span>downloads, mismatch resolution and Form 16 generation support</span>
            </div>
            <div class="hero-metric">
              <strong>Payroll</strong>
              <span>linked TDS compliance and employee-wise statement preparation</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container authority-ribbon">
      <div class="authority-pill"><strong>3000+</strong><span>Clients Served</span></div>
      <div class="authority-pill"><strong>15+</strong><span>Years Experience</span></div>
      <div class="authority-pill"><strong>All Forms</strong><span>24Q, 26Q, 27Q, 27EQ</span></div>
      <div class="authority-pill"><strong>PAN</strong><span>Based Validation</span></div>
      <div class="authority-pill"><strong>TRACES</strong><span>Integrated Support</span></div>
      <div class="authority-pill"><strong>Due Date</strong><span>Monitoring</span></div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">TDS Services</p>
        <h2 class="section-title">Complete TDS compliance support under one advisory desk.</h2>
        <p class="section-intro">From return preparation to notice handling, we offer structured TDS services for all types of deductors.</p>
      </div>
      <div class="grid-3">
        <article class="card">
          <div class="feature-icon">RF</div>
          <h3>Return Preparation & Filing</h3>
          <p>Accurate preparation and timely filing of Form 24Q, 26Q, 27Q and 27EQ with challan and PAN validation.</p>
        </article>
        <article class="card">
          <div class="feature-icon">CR</div>
          <h3>Correction Statements</h3>
          <p>Revised or belated returns, challan mismatch resolution and deductor-side adjustments.</p>
        </article>
        <article class="card">
          <div class="feature-icon">F16</div>
          <h3>Form 16 & 16A Issuance</h3>
          <p>Generation and distribution of Form 16 for salaried employees and Form 16A for non-salary deductees.</p>
        </article>
        <article class="card">
          <div class="feature-icon">TR</div>
          <h3>TRACES Support</h3>
          <p>Downloading TDS certificates, challan reconciliation, mismatch identification and correction filing.</p>
        </article>
        <article class="card">
          <div class="feature-icon">AD</div>
          <h3>Deductor Advisory</h3>
          <p>Guidance on TDS applicability, rate determination, threshold monitoring and compliance calendar management.</p>
        </article>
        <article class="card">
          <div class="feature-icon">NO</div>
          <h3>Notice Handling</h3>
          <p>Response preparation for TDS-related notices u/s 201, 271C and 272A with supporting documentation.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Benefits</p>
        <h2 class="section-title">Why structured TDS compliance matters for your business.</h2>
      </div>
      <div class="grid-2">
        <article class="card card-muted">
          <h3>Avoid Penalty & Interest</h3>
          <p>Late filing, incorrect deductor details or PAN mismatches can attract penalties under section 271C and interest under section 201. Our review process helps minimise these risks.</p>
        </article>
        <article class="card card-muted">
          <h3>Simplify Payroll Compliance</h3>
          <p>For organisations with employees, TDS compliance is directly linked to payroll accuracy. We help align your payroll outputs with quarterly return obligations.</p>
        </article>
        <article class="card card-muted">
          <h3>Timely Form 16 Issuance</h3>
          <p>Form 16 delays can affect employee tax filing and create dissatisfaction. Our process ensures certificates are generated and distributed well within due dates.</p>
        </article>
        <article class="card card-muted">
          <h3>Notice Prevention</h3>
          <p>Most TDS notices arise from unreconciled challans, incorrect PANs or missing returns. Our validation steps address these before submission.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Our TDS engagement process in four steps.</h2>
      </div>
      <div class="process-timeline">
        <div class="timeline-step">
          <div class="timeline-number">1</div>
          <h3>Submit Records</h3>
          <p>Share your challan details, PAN data, salary registers and previous return records with our team.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">2</div>
          <h3>Validation & Reconciliation</h3>
          <p>We validate PANs, reconcile challan amounts and match deductee records against TRACES data.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">3</div>
          <h3>Return Preparation & Filing</h3>
          <p>Return is prepared, reviewed internally and filed before the due date. Acknowledgment is shared with you.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">4</div>
          <h3>Certificate Issuance & Closure</h3>
          <p>Form 16/16A is generated and distributed. Any follow-up correction or notice is handled post-filing.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Frequently asked questions about TDS return filing.</h2>
      </div>
      <div class="faq-list" itemscope itemtype="https://schema.org/FAQPage">
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What are the different TDS return forms?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Form 24Q is for salaries, Form 26Q for all non-salary payments to residents, Form 27Q for payments to non-residents and Form 27EQ for tax collection at source. Each form has a quarterly filing cycle.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Can we file a belated or revised TDS return?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Yes. Correction statements can be filed for any quarter of a financial year to add, delete or modify deductee records or challan details. Late fees may apply for belated filings.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is the due date for TDS return filing?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">TDS returns for each quarter must be filed by the 31st day of the month following the quarter. For the fourth quarter (January to March), the due date is 31st May.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is TRACES and how does it help?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">TRACES is the TDS Reconciliation, Analysis and Correction Enabling System by the Income Tax Department. It allows deductors to view challan status, download Form 16/16A and identify mismatches between returned and paid amounts.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What penalties apply for late TDS filing?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Late filing attracts a fee of INR 200 per day under section 234E, in addition to interest under section 201 for delayed payment of TDS. Persistent non-compliance may lead to prosecution under section 276B.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Do you support TDS notice handling?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Yes. We assist with notice responses for sections 201, 271C, 272A and other TDS-related matters, including documentation, reply drafting and representation support.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">What our clients say about our TDS compliance services.</h2>
        <p class="section-intro">Average approved rating: <strong><?= h(number_format($homepageTestimonialSummary['average_rating'], 1)) ?>/5</strong> across <strong><?= h((string)$homepageTestimonialSummary['total_reviews']) ?></strong> published client reviews.</p>
      </div>
<?php if ($homepageTestimonials): ?>
      <div class="testimonial-controls">
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('prev')" aria-label="Previous testimonials">Previous</button>
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('next')" aria-label="Next testimonials">Next</button>
      </div>
      <div class="testimonial-track" id="testimonialTrack">
<?php foreach ($homepageTestimonials as $item): ?>
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

  <section class="section section-muted" id="consult">
    <div class="container consult-shell">
      <div class="section-header">
        <p class="section-label">Book A Consultation</p>
        <h2 class="section-title">Discuss your TDS compliance requirements with our team.</h2>
        <p class="section-intro">
          Use this form to request a consultation for TDS return filing, correction statements, Form 16/16A, TRACES support or deductor advisory.
        </p>
      </div>
      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What you will get from this consultation</h3>
          <ul class="list-clean">
            <li>A review of your current TDS filing status and pending obligations.</li>
            <li>Identification of challan mismatches, PAN errors or missed returns.</li>
            <li>A clear plan for return preparation, corrections and compliance follow-through.</li>
          </ul>
          <div class="consult-note">
            <strong>Preferred for</strong>
            <span>Corporate deductors, partnership firms, proprietors, payroll handlers and individual taxpayers requiring structured TDS support.</span>
          </div>
        </div>
        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>
          <form method="post" action="<?= htmlspecialchars(site_href('/tds-return-filing.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="tds_consult">
            <input type="hidden" name="source_page" value="/tds-return-filing.php">
            <div class="form-grid">
              <div class="field">
                <label for="tds_name">Name</label>
                <input class="input" id="tds_name" name="name" required />
              </div>
              <div class="field">
                <label for="tds_mobile">Mobile</label>
                <input class="input" id="tds_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="tds_email">Email</label>
                <input class="input" id="tds_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="tds_org">Organisation</label>
                <input class="input" id="tds_org" name="organisation" />
              </div>
              <div class="field">
                <label for="tds_service">Service Required</label>
                <input class="input" id="tds_service" name="service" placeholder="TDS return, correction, Form 16, TRACES, notice handling, etc." required />
              </div>
              <div class="field">
                <label for="tds_time">Preferred Consultation Time</label>
                <input class="input" id="tds_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="tds_msg">Brief Requirement</label>
                <textarea class="input" id="tds_msg" name="message" required></textarea>
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
</main>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "What are the different TDS return forms?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Form 24Q is for salaries, Form 26Q for all non-salary payments to residents, Form 27Q for payments to non-residents and Form 27EQ for tax collection at source. Each form has a quarterly filing cycle."
      }
    },
    {
      "@type": "Question",
      "name": "Can we file a belated or revised TDS return?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. Correction statements can be filed for any quarter of a financial year to add, delete or modify deductee records or challan details. Late fees may apply for belated filings."
      }
    },
    {
      "@type": "Question",
      "name": "What is the due date for TDS return filing?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "TDS returns for each quarter must be filed by the 31st day of the month following the quarter. For the fourth quarter (January to March), the due date is 31st May."
      }
    },
    {
      "@type": "Question",
      "name": "What is TRACES and how does it help?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "TRACES is the TDS Reconciliation, Analysis and Correction Enabling System by the Income Tax Department. It allows deductors to view challan status, download Form 16/16A and identify mismatches between returned and paid amounts."
      }
    },
    {
      "@type": "Question",
      "name": "What penalties apply for late TDS filing?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Late filing attracts a fee of INR 200 per day under section 234E, in addition to interest under section 201 for delayed payment of TDS. Persistent non-compliance may lead to prosecution under section 276B."
      }
    },
    {
      "@type": "Question",
      "name": "Do you support TDS notice handling?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. We assist with notice responses for sections 201, 271C, 272A and other TDS-related matters, including documentation, reply drafting and representation support."
      }
    }
  ]
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
