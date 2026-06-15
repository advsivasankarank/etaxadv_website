<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Expert GST Consultant in Puducherry – Returns, Notices & Compliance Support | E Tax Advisors";
$page_description = "Professional GST consultant in Puducherry offering GST registration, return filing, notice response, e-way bill, LUT and compliance support for MSMEs, manufacturers, traders and startups. Call +91 98946 26300.";
$page_path = '/gst-consultant-puducherry.php';

$gstTestimonialSummary = testimonial_get_summary();
$gstTestimonials = testimonial_get_featured(8);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'gst_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();

$local_schema = [
  '@context' => 'https://schema.org',
  '@type' => 'LocalBusiness',
  'name' => 'E Tax Advisors Private Limited - GST Consultant Puducherry',
  'url' => 'https://www.etaxadv.com/gst-consultant-puducherry.php',
  'telephone' => '+91-98946-26300',
  'email' => 'support@etaxadv.com',
  'image' => 'https://www.etaxadv.com/assets/img/og-image.jpg',
  'description' => 'Professional GST consultancy services in Puducherry including registration, return filing, notice response, e-way bill management, LUT filing and compliance support for businesses.',
  'address' => [
    '@type' => 'PostalAddress',
    'streetAddress' => 'No. 234, I Floor, Lawspet Main Road, Pakkamudayanpet, Lawspet',
    'addressLocality' => 'Puducherry',
    'postalCode' => '605008',
    'addressCountry' => 'IN',
  ],
  'geo' => [
    '@type' => 'GeoCoordinates',
    'latitude' => 11.9270,
    'longitude' => 79.8260,
  ],
  'areaServed' => ['Puducherry', 'Karaikal', 'Mahe', 'Yanam', 'Tamil Nadu'],
  'priceRange' => 'INR 500 - INR 50000',
  'openingHours' => 'Mo-Sa 09:30-18:30',
  'sameAs' => [
    'https://wa.me/919500601119',
    'https://www.etaxadv.com/',
  ],
];

$faq_schema = [
  '@context' => 'https://schema.org',
  '@type' => 'FAQPage',
  'mainEntity' => [
    [
      '@type' => 'Question',
      'name' => 'Is GST registration mandatory for a business in Puducherry?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Yes, GST registration is mandatory if your aggregate turnover exceeds Rs. 40 lakhs (Rs. 20 lakhs for service providers) in a financial year. Certain businesses such as inter-state suppliers, e-commerce operators and casual taxable persons must register regardless of turnover. We assist with the complete registration process at our Puducherry office.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'What are the GST return filing deadlines for businesses in Puducherry?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'GSTR-3B (monthly summary return) is due by the 20th of the following month. GSTR-1 (outward supply) is due by the 11th (monthly filers) or 13th (quarterly QRMP filers). Annual return GSTR-9 is due by 31st December of the following financial year. Late filing attracts interest and late fee. We help you stay compliant with all deadlines.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'How can a GST notice or summons be handled professionally?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'GST notices under Sections 73 and 74, summons under Section 70, or assessment orders require a structured response with proper documentation. Our team reviews the notice, prepares the response with supporting records, and handles authority follow-ups. We serve clients across Puducherry who need notice representation.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'What is the Composition Scheme under GST and who can opt for it in Puducherry?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'The Composition Scheme allows small businesses with turnover up to Rs. 1.5 crore (Rs. 75 lakhs for service providers) to pay GST at a lower rate and file quarterly returns. It reduces compliance burden significantly. We guide Puducherry-based businesses on eligibility, filing and scheme migration.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'Do I need an e-way bill for transporting goods within Puducherry?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'An e-way bill is mandatory for inter-state movement of goods valued above Rs. 50,000. For intra-state movement within Puducherry, e-way bill requirements apply when the consignment value exceeds Rs. 1,00,000. We assist with e-way bill generation, extension and consolidation for manufacturers and traders.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'How do I claim ITC (Input Tax Credit) on my GST returns?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'ITC can be claimed on goods and services used for business purposes, provided you hold a valid tax invoice, the supplier has filed the return, and you have received the goods/services. Reconciliation between GSTR-2B and purchase ledger is critical. Our team ensures accurate ITC claims and handles mismatch resolution for Puducherry clients.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'What is LUT under GST and when is it required?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'A Letter of Undertaking (LUT) is filed by exporters to export goods or services without payment of IGST. It is valid for one financial year and must be renewed annually. We prepare and file LUT applications for exporters based in Puducherry and surrounding regions.',
      ],
    ],
  ],
];

require_once __DIR__ . '/includes/header.php';
?>
<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">GST Consultant Puducherry</div>
        <h1>Expert GST Consultant in Puducherry – Returns, Notices &amp; Compliance Support</h1>
        <p>
          E Tax Advisors Private Limited provides end-to-end GST compliance services in Puducherry for MSMEs,
          manufacturers, traders and startups. From registration and return filing to notice response and
          representation, we offer structured advisory backed by experienced professionals.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Book Consultation</a>
          <a class="btn btn-outline" href="services.php#gst">All GST Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">GST Registration &amp; Filing</span>
          <span class="proof-chip">Notice Response &amp; Representation</span>
          <span class="proof-chip">E-way Bill &amp; LUT Support</span>
        </div>
        <p class="hero-note">Professional GST compliance support for businesses in Puducherry, Karaikal, Mahe and surrounding regions.</p>
      </div>

      <div class="hero-visual" aria-label="GST compliance services">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Complete GST compliance under one roof.</h2>
            <p>
              Our GST practice covers registration, monthly and quarterly return filing, annual return
              preparation, notice drafting, e-way bill management, LUT filing and compliance health
              reviews with documented follow-through.
            </p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>3000+</strong>
              <span>businesses assisted with GST compliance across India</span>
            </div>
            <div class="hero-metric">
              <strong>15+</strong>
              <span>years of GST and indirect tax advisory experience</span>
            </div>
            <div class="hero-metric">
              <strong>End-to-end</strong>
              <span>GST support from registration to litigation representation</span>
            </div>
            <div class="hero-metric">
              <strong>Structured</strong>
              <span>checklist-driven reviews, maker-checker controls and closure summaries</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container authority-ribbon">
      <div class="authority-pill">
        <strong>GST</strong>
        <span>Registration Support</span>
      </div>
      <div class="authority-pill">
        <strong>GSTR-1/3B</strong>
        <span>Return Filing</span>
      </div>
      <div class="authority-pill">
        <strong>GSTR-9</strong>
        <span>Annual Returns</span>
      </div>
      <div class="authority-pill">
        <strong>Notice</strong>
        <span>Response &amp; Representation</span>
      </div>
      <div class="authority-pill">
        <strong>E-way Bill</strong>
        <span>Generation &amp; Compliance</span>
      </div>
      <div class="authority-pill">
        <strong>LUT</strong>
        <span>Export Filing Support</span>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">GST Services in Puducherry</p>
        <h2 class="section-title">Complete GST compliance services tailored for Puducherry businesses.</h2>
        <p class="section-intro">
          Whether you are a manufacturer in Lawspet, a trader in Mudaliarpet, a startup in Gorimedu or a
          distributor in Karaikal, our GST practice delivers structured support with documented processes,
          review controls and professional representation.
        </p>
      </div>

      <div class="grid-3 practice-grid">
        <article class="card practice-card">
          <h3>GST Registration</h3>
          <p class="practice-problem">New business? Confused about turnover thresholds, document requirements or composition scheme eligibility?</p>
          <p class="practice-solution">We handle the complete GST registration process – application preparation, document upload, ARN tracking and certificate download. We also assist with voluntary registration, composition scheme enrolment and cancellation.</p>
          <a class="btn btn-outline" href="#consult">Enquire About Registration</a>
        </article>
        <article class="card practice-card">
          <h3>GST Return Filing</h3>
          <p class="practice-problem">Missed deadlines, mismatched data or confusion about which return to file and when?</p>
          <p class="practice-solution">We prepare and file GSTR-1, GSTR-3B, GSTR-4 (composition), GSTR-9 and GSTR-9C with data validation, reconciliation checks and deadline tracking. Monthly and quarterly QRMP filers supported.</p>
          <a class="btn btn-outline" href="#consult">Discuss Return Filing</a>
        </article>
        <article class="card practice-card">
          <h3>GST Notice Response &amp; Representation</h3>
          <p class="practice-problem">Received a notice from GST authorities? Unsure how to respond or what documents are needed?</p>
          <p class="practice-solution">Our team reviews the notice, analyses the discrepancy, prepares a structured response with supporting documentation and, where required, provides representation support before the tax authorities.</p>
          <a class="btn btn-outline" href="#consult">Review a Notice</a>
        </article>
        <article class="card practice-card">
          <h3>Input Tax Credit (ITC) Reconciliation</h3>
          <p class="practice-problem">ITC mismatches between GSTR-2B and purchase register? Rejected ITC claims or notice exposure?</p>
          <p class="practice-solution">We perform vendor-wise ITC reconciliation, identify mismatches, coordinate with suppliers for corrective action and ensure accurate credit availment with proper documentation trail.</p>
          <a class="btn btn-outline" href="#consult">Resolve ITC Issues</a>
        </article>
        <article class="card practice-card">
          <h3>E-way Bill &amp; LUT Support</h3>
          <p class="practice-problem">Transporting goods inter-state or exporting without clarity on e-way bill or LUT requirements?</p>
          <p class="practice-solution">We assist with e-way bill generation, extension, consolidation and rejection handling. For exporters, we prepare and file LUT applications for duty-free exports and track renewals.</p>
          <a class="btn btn-outline" href="#consult">Get E-way Bill Help</a>
        </article>
        <article class="card practice-card">
          <h3>Annual Return &amp; Audit</h3>
          <p class="practice-problem">GSTR-9 annual return pending? Audit under Section 35 requiring reconciliation and certification?</p>
          <p class="practice-solution">We prepare GSTR-9 and GSTR-9C with reconciliation of books, returns and ITC. Our team coordinates with your accountant or handles the preparation end-to-end for a smooth annual compliance close.</p>
          <a class="btn btn-outline" href="#consult">Plan Annual Filing</a>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why Choose Us</p>
        <h2 class="section-title">Why Puducherry businesses trust E Tax Advisors for GST compliance.</h2>
        <p class="section-intro">
          We differentiate through structured execution, visible accountability and the ability to handle
          both routine compliance and complex litigation under one advisory roof.
        </p>
      </div>

      <div class="who-grid">
        <article class="who-card">
          <div class="feature-icon">LO</div>
          <h3>Local Presence</h3>
          <p>Our office in Lawspet, Puducherry means we are accessible for in-person consultations, document handovers and urgent compliance needs.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">EX</div>
          <h3>Experienced Team</h3>
          <p>Led by professionals with 15+ years in indirect tax, our GST team includes advocates, tax consultants and trained compliance executives.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">EN</div>
          <h3>End-to-End Support</h3>
          <p>From registration to return filing, notice response to appellate representation – we cover the full GST lifecycle under one engagement.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">PR</div>
          <h3>Process-Driven Execution</h3>
          <p>Our GST practice operates on checklist-based reviews, maker-checker validation and documented closure summaries – not memory or informal follow-ups.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">RE</div>
          <h3>Responsive Support</h3>
          <p>We operate defined response timeframes for client queries, escalation handling and compliance event tracking with proactive deadline alerts.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">AF</div>
          <h3>Affordable &amp; Transparent</h3>
          <p>Our fee structure is clear, engagement-based and scaled to the size and complexity of your compliance needs – no hidden charges.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How We Work</p>
        <h2 class="section-title">Four-step GST engagement process designed for clarity and accountability.</h2>
        <p class="section-intro">
          Every GST engagement at E Tax Advisors follows a structured workflow that ensures documentation completeness,
          review rigour and timely delivery.
        </p>
      </div>

      <div class="timeline-shell">
        <div class="timeline-steps">
          <div class="timeline-step">
            <div class="step-number">01</div>
            <div class="step-body">
              <h3>Discovery &amp; Assessment</h3>
              <p>We understand your business, turnover, nature of supplies, existing compliance status and pain points. This helps us scope the engagement accurately.</p>
            </div>
          </div>
          <div class="timeline-step">
            <div class="step-number">02</div>
            <div class="step-body">
              <h3>Documentation &amp; Onboarding</h3>
              <p>We collect required GST records, reconcile data, set up compliance calendars and establish communication protocols for ongoing support.</p>
            </div>
          </div>
          <div class="timeline-step">
            <div class="step-number">03</div>
            <div class="step-body">
              <h3>Execution &amp; Review</h3>
              <p>Returns, notices or registrations are prepared with maker-checker review. Every output is validated against source records before submission.</p>
            </div>
          </div>
          <div class="timeline-step">
            <div class="step-number">04</div>
            <div class="step-body">
              <h3>Closure &amp; Follow-Through</h3>
              <p>We provide a closure summary, pending action list and follow-up schedule. Post-submission tracking and escalation support continue until the matter is closed.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs on GST Compliance in Puducherry</p>
        <h2 class="section-title">Common GST questions answered by our Puducherry team.</h2>
        <p class="section-intro">
          Practical answers to the most frequent GST compliance questions we receive from businesses in and around Puducherry.
        </p>
      </div>

      <div class="faq-list">
        <details class="faq-item" open>
          <summary>
            <h3>Is GST registration mandatory for a business in Puducherry?</h3>
          </summary>
          <p>Yes, GST registration is mandatory if your aggregate turnover exceeds Rs. 40 lakhs (Rs. 20 lakhs for service providers) in a financial year. Certain businesses such as inter-state suppliers, e-commerce operators and casual taxable persons must register regardless of turnover. We assist with the complete registration process at our Puducherry office.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>What are the GST return filing deadlines for businesses in Puducherry?</h3>
          </summary>
          <p>GSTR-3B (monthly summary return) is due by the 20th of the following month. GSTR-1 (outward supply) is due by the 11th (monthly filers) or 13th (quarterly QRMP filers). Annual return GSTR-9 is due by 31st December of the following financial year. Late filing attracts interest at 18% per annum plus a late fee of Rs. 50 per day (Rs. 25 each under CGST and SGST). We help you stay compliant with all deadlines through proactive tracking.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>How can a GST notice or summons be handled professionally?</h3>
          </summary>
          <p>GST notices under Sections 73 and 74, summons under Section 70, or assessment orders require a structured response with proper documentation. Our team reviews the notice, analyses the discrepancy, prepares a response with supporting records, and handles authority follow-ups. We serve clients across Puducherry who need notice representation at the assistant commissioner or appellate level.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>What is the Composition Scheme under GST and who can opt for it in Puducherry?</h3>
          </summary>
          <p>The Composition Scheme allows small businesses with turnover up to Rs. 1.5 crore (Rs. 75 lakhs for service providers, Rs. 50 lakhs for certain special category states) to pay GST at a lower rate (1% for traders, 6% for service providers) and file quarterly returns (GSTR-4). It significantly reduces compliance burden. We guide Puducherry-based businesses on eligibility, scheme migration, filing and the conditions to remain in the scheme.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>Do I need an e-way bill for transporting goods within Puducherry?</h3>
          </summary>
          <p>An e-way bill is mandatory for inter-state movement of goods valued above Rs. 50,000. For intra-state movement within Puducherry, e-way bill requirements apply when the consignment value exceeds Rs. 1,00,000. Certain exempted goods and transport modes may not require an e-way bill. We assist with e-way bill generation, extension (Part-B), consolidation and rejection handling for manufacturers and traders.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>How do I claim ITC (Input Tax Credit) on my GST returns?</h3>
          </summary>
          <p>ITC can be claimed on goods and services used for business purposes, provided you hold a valid tax invoice, the supplier has filed GSTR-3B, and you have received the goods or services. Reconciliation between GSTR-2B and purchase ledger is critical to avoid mismatches and notice exposure. Our team ensures accurate ITC claims, handles vendor follow-ups for missing invoices and resolves mismatch issues before they escalate.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>What is LUT under GST and when is it required for exporters in Puducherry?</h3>
          </summary>
          <p>A Letter of Undertaking (LUT) is filed by exporters to export goods or services without payment of IGST. It is valid for one financial year and must be renewed annually before the start of the new financial year. We prepare and file LUT applications in Form GST RFD-11 for exporters based in Puducherry and surrounding regions, and track renewals to prevent lapses.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">What Our Clients Say</p>
        <h2 class="section-title">Feedback from businesses we have served with GST compliance in Puducherry.</h2>
<?php if ($gstTestimonials): ?>
        <p class="section-intro">
          Average approved rating: <strong><?= h(number_format($gstTestimonialSummary['average_rating'], 1)) ?>/5</strong>
          across <strong><?= h((string)$gstTestimonialSummary['total_reviews']) ?></strong> published client reviews.
        </p>
<?php endif; ?>
      </div>

<?php if ($gstTestimonials): ?>
      <div class="testimonial-controls">
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('prev')" aria-label="Previous testimonials">Previous</button>
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('next')" aria-label="Next testimonials">Next</button>
      </div>

      <div class="testimonial-track" id="testimonialTrack">
<?php foreach ($gstTestimonials as $item): ?>
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
        <a class="btn btn-primary" href="<?= htmlspecialchars(app_href('/testimonial/#share-review')) ?>">Share Your GST Experience</a>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="consult">
    <div class="container consult-shell">
      <div class="section-header">
        <p class="section-label">Book a GST Consultation</p>
        <h2 class="section-title">Discuss your GST compliance needs with our Puducherry team.</h2>
        <p class="section-intro">
          Use this form for GST registration support, return filing assistance, notice response help,
          e-way bill queries or any GST compliance requirement. We will review your requirement and get back to you.
        </p>
      </div>

      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>How we can help you with GST</h3>
          <ul class="list-clean">
            <li>New GST registration or composition scheme migration.</li>
            <li>Monthly / quarterly return filing with reconciliation support.</li>
            <li>Notice or summons response drafting and authority follow-through.</li>
            <li>ITC reconciliation, mismatch resolution and vendor coordination.</li>
            <li>E-way bill generation, LUT filing and annual return preparation.</li>
          </ul>
          <div class="consult-note">
            <strong>Call us directly</strong>
            <span><a href="tel:+919894626300">+91 98946 26300</a> for urgent GST matters.</span>
          </div>
        </div>

        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

          <form method="post" action="<?= htmlspecialchars(site_href('/gst-consultant-puducherry.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="gst_consult">
            <input type="hidden" name="source_page" value="/gst-consultant-puducherry.php">
            <div class="form-grid">
              <div class="field">
                <label for="gst_name">Name</label>
                <input class="input" id="gst_name" name="name" required />
              </div>
              <div class="field">
                <label for="gst_mobile">Mobile</label>
                <input class="input" id="gst_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="gst_email">Email</label>
                <input class="input" id="gst_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="gst_org">Organisation</label>
                <input class="input" id="gst_org" name="organisation" />
              </div>
              <div class="field">
                <label for="gst_service">GST Service Required</label>
                <input class="input" id="gst_service" name="service" placeholder="GST registration / return filing / notice response / e-way bill / LUT / ITC reconciliation" required />
              </div>
              <div class="field">
                <label for="gst_time">Preferred Consultation Time</label>
                <input class="input" id="gst_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="gst_msg">Describe Your GST Requirement</label>
                <textarea class="input" id="gst_msg" name="message" required></textarea>
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

  <section class="section">
    <div class="container cta-band">
      <div>
        <h2>Need immediate GST compliance support in Puducherry?</h2>
        <p>
          If you have a pending notice, an approaching return deadline, or need clarity on your GST obligations,
          reach out to our team for a structured consultation.
        </p>
        <div class="cta-actions">
          <a class="btn btn-primary" href="tel:+919894626300">Call +91 98946 26300</a>
          <a class="btn btn-outline" href="https://wa.me/919500601119" target="_blank" rel="noopener">Chat on WhatsApp</a>
        </div>
      </div>
      <div class="card">
        <h3>Also explore</h3>
        <ul class="list-clean">
          <li><a href="services.php#gst">Complete GST Service Overview</a></li>
          <li><a href="services.php#income-tax">Income Tax Advisory</a></li>
          <li><a href="services.php#company">Company &amp; LLP Compliance</a></li>
          <li><a href="ekanakan.php">e-Kanakan Bookkeeping Support</a></li>
        </ul>
      </div>
    </div>
  </section>
</main>

<script type="application/ld+json"><?= json_encode($local_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<script type="application/ld+json"><?= json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
