<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "GST Consultant in Chennai – Returns, Notices & Compliance | E Tax Advisors";
$page_description = "GST compliance, return filing, notice response and advisory services in Chennai. E Tax Advisors serves Chennai businesses with structured GST support.";
$page_path = '/gst-consultant-chennai.php';

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
  'name' => 'E Tax Advisors Private Limited - GST Consultant Chennai',
  'url' => 'https://www.etaxadv.com/gst-consultant-chennai.php',
  'telephone' => '+91-98946-26300',
  'email' => 'support@etaxadv.com',
  'image' => 'https://www.etaxadv.com/assets/img/og-image.jpg',
  'description' => 'Professional GST consultancy services in Chennai including return filing, notice response, registration, e-way bill management and compliance support for businesses across T. Nagar, Mount Road, OMR, Guindy and surrounding districts.',
  'address' => [
    '@type' => 'PostalAddress',
    'streetAddress' => 'No. 234, I Floor, Lawspet Main Road, Pakkamudayanpet, Lawspet',
    'addressLocality' => 'Puducherry',
    'postalCode' => '605008',
    'addressCountry' => 'IN',
  ],
  'geo' => [
    '@type' => 'GeoCoordinates',
    'latitude' => 13.0827,
    'longitude' => 80.2707,
  ],
  'areaServed' => ['Chennai', 'T. Nagar', 'Mount Road', 'OMR', 'Guindy', 'Ambattur', 'Porur', 'Tambaram', 'Adyar', 'Velachery', 'Tamil Nadu'],
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
      'name' => 'What is the GST registration threshold for businesses in Chennai?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'GST registration is mandatory when aggregate turnover exceeds Rs. 40 lakhs for goods suppliers and Rs. 20 lakhs for service providers in a financial year. Businesses operating in Chennai commercial districts such as T. Nagar, Mount Road and OMR must also register if they engage in inter-state supply, e-commerce operations or casual taxable person activities regardless of turnover.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'How does e-way bill compliance work for goods movement within Chennai?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'For inter-state movement of goods valued above Rs. 50,000, e-way bill generation is mandatory. Intra-state movement within Tamil Nadu requires an e-way bill when the consignment value exceeds Rs. 1,00,000. Businesses in industrial areas like Guindy, Ambattur and Porur frequently require e-way bill support for raw material and finished goods transport. We assist with generation, Part-B extension, consolidation and rejection handling.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'What are the key GST return deadlines Chennai businesses must track?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'GSTR-3B is due by the 20th of each month. GSTR-1 is due by the 11th for monthly filers and 13th for quarterly QRMP filers. The annual return GSTR-9 must be filed by 31st December of the following financial year. Late filing attracts interest at 18% per annum plus a late fee of Rs. 50 per day. Our team provides proactive deadline tracking for Chennai-based businesses to avoid penalties.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'Can you help with GST notice response for Chennai-based businesses?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Yes. GST notices under Sections 73 and 74, summons under Section 70, and assessment orders require a structured response with proper documentation. We review the notice, analyse the discrepancy, prepare a response with supporting records and, where required, provide representation before the Chennai GST Commissionerate at Nungambakkam. Our team has experience handling notices for traders in T. Nagar, manufacturers in Ambattur and IT firms on OMR.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'How is ITC reconciliation handled for businesses with multiple vendors in Chennai?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'ITC reconciliation involves matching GSTR-2B data with purchase ledgers vendor by vendor. Businesses in Chennai often deal with numerous suppliers across T. Nagar, Parrys and Koyambedu markets, making reconciliation critical. Mismatches if unresolved lead to notices and interest demands. We perform vendor-wise reconciliation, coordinate with suppliers for missing invoices and ensure accurate credit availment.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'What is the composition scheme and who can opt for it in Chennai?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'The Composition Scheme is available for businesses with turnover up to Rs. 1.5 crore (Rs. 75 lakhs for service providers). It allows GST payment at lower rates — 1% for traders, 6% for service providers — with quarterly return filing. Small retailers in Chennai markets such as Ranganathan Street, Pondy Bazaar and Sowcarpet often benefit from this scheme. We assist with eligibility evaluation, migration and quarterly compliance.',
      ],
    ],
    [
      '@type' => 'Question',
      'name' => 'Do exporters in Chennai need LUT filing under GST?',
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => 'Yes, exporters must file a Letter of Undertaking in Form GST RFD-11 to export goods or services without paying IGST. The LUT is valid for one financial year and must be renewed annually. Exporters in Chennai, particularly those in the MEPZ Special Economic Zone and industrial estates in Ambattur and Guindy, rely on timely LUT filing to maintain duty-free export benefits. We prepare and file LUT applications and track renewals.',
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
        <div class="eyebrow">GST Consultant Chennai</div>
        <h1>GST Consultant in Chennai – Returns, Notices &amp; Compliance Support</h1>
        <p>
          E Tax Advisors Private Limited provides end-to-end GST compliance services for businesses
          across Chennai. From return filing and notice response to registration and e-way bill management,
          we serve clients in T. Nagar, Mount Road, OMR, Guindy, Ambattur, Porur and all major
          commercial districts with structured, process-driven advisory.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Book Consultation</a>
          <a class="btn btn-outline" href="services.php#gst">All GST Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">GST Return Filing</span>
          <span class="proof-chip">Notice Response &amp; Representation</span>
          <span class="proof-chip">ITC Reconciliation &amp; Compliance</span>
        </div>
        <p class="hero-note">Structured GST advisory for businesses across Chennai, Tambaram, Ambattur, OMR and surrounding areas.</p>
      </div>

      <div class="hero-visual" aria-label="GST compliance services">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>End-to-end GST compliance for Chennai businesses.</h2>
            <p>
              Our GST practice covers return preparation and filing, notice drafting, ITC reconciliation,
              e-way bill management, LUT filing, annual return preparation and compliance health reviews
              with documented follow-through and maker-checker controls.
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
              <strong>All districts</strong>
              <span>service coverage across Chennai, OMR, Tambaram & Ambattur</span>
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
        <strong>Returns</strong>
        <span>GSTR-1 / 3B / 9</span>
      </div>
      <div class="authority-pill">
        <strong>ITC</strong>
        <span>Reconciliation &amp; Claims</span>
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
        <p class="section-label">GST Services in Chennai</p>
        <h2 class="section-title">Complete GST compliance services tailored for Chennai businesses.</h2>
        <p class="section-intro">
          Whether you are a retailer on Ranganathan Street in T. Nagar, a manufacturer in Ambattur
          Industrial Estate, an IT firm on OMR, a distributor in Guindy or a professional practice
          in Mount Road — our GST practice delivers structured support with documented processes
          and professional accountability.
        </p>
      </div>

      <div class="grid-3 practice-grid">
        <article class="card practice-card">
          <h3>GST Registration &amp; Migration</h3>
          <p class="practice-problem">New business in Chennai? Unsure if you need voluntary registration or composition scheme migration?</p>
          <p class="practice-solution">We handle new GST registration, composition scheme enrolment, voluntary registration and GST cancellation. Our team manages the complete process including document preparation, ARN tracking and certificate download for businesses across Chennai.</p>
          <a class="btn btn-outline" href="#consult">Enquire About Registration</a>
        </article>
        <article class="card practice-card">
          <h3>GST Return Filing</h3>
          <p class="practice-problem">Filing delays, data mismatches or confusion about monthly vs quarterly return options?</p>
          <p class="practice-solution">We prepare and file GSTR-1, GSTR-3B, GSTR-4, GSTR-9 and GSTR-9C with data validation, reconciliation checks and deadline tracking. Monthly and quarterly QRMP filers across Chennai are supported with proactive compliance calendars.</p>
          <a class="btn btn-outline" href="#consult">Discuss Return Filing</a>
        </article>
        <article class="card practice-card">
          <h3>ITC Reconciliation &amp; Optimisation</h3>
          <p class="practice-problem">ITC mismatches between GSTR-2B and purchase register? Vendor not filing returns?</p>
          <p class="practice-solution">We perform detailed vendor-wise ITC reconciliation, identify mismatches early, coordinate with suppliers for corrective filing and ensure maximum eligible credit is claimed with proper documentation. Critical for Chennai businesses with large vendor bases.</p>
          <a class="btn btn-outline" href="#consult">Resolve ITC Issues</a>
        </article>
        <article class="card practice-card">
          <h3>GST Notice Response &amp; Representation</h3>
          <p class="practice-problem">Received a notice from the Chennai GST Commissionerate? Unsure how to respond before the deadline?</p>
          <p class="practice-solution">Our team reviews the notice, analyses the discrepancy, prepares a structured response with supporting documentation and provides representation support before the tax authorities at Nungambakkam or the respective CGST jurisdiction.</p>
          <a class="btn btn-outline" href="#consult">Review a Notice</a>
        </article>
        <article class="card practice-card">
          <h3>E-way Bill &amp; LUT Support</h3>
          <p class="practice-problem">Transporting goods within Tamil Nadu or inter-state without e-way bill clarity?</p>
          <p class="practice-solution">We provide end-to-end e-way bill support including generation, Part-B extension, consolidation and rejection handling. For exporters in Chennai MEPZ and industrial estates, we prepare and file LUT applications for duty-free exports with annual renewal tracking.</p>
          <a class="btn btn-outline" href="#consult">Get E-way Bill Help</a>
        </article>
        <article class="card practice-card">
          <h3>Annual Return &amp; GST Audit</h3>
          <p class="practice-problem">GSTR-9 annual return pending? Audit under Section 35 requiring reconciliation and certification?</p>
          <p class="practice-solution">We prepare GSTR-9 and GSTR-9C with comprehensive reconciliation of books, returns and ITC. Our team coordinates with your accountant or handles the preparation end-to-end for a smooth annual compliance close.</p>
          <a class="btn btn-outline" href="#consult">Plan Annual Filing</a>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why Choose Us</p>
        <h2 class="section-title">Why Chennai businesses trust E Tax Advisors for GST compliance.</h2>
        <p class="section-intro">
          We differentiate through structured execution, visible accountability and the ability to handle
          both routine compliance and complex litigation under one advisory roof.
        </p>
      </div>

      <div class="who-grid">
        <article class="who-card">
          <div class="feature-icon">CC</div>
          <h3>Chennai-Centric Expertise</h3>
          <p>We understand the specific compliance patterns of Chennai's business ecosystem — from T. Nagar retail to OMR IT services and Ambattur manufacturing — and tailor our GST approach accordingly.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">EX</div>
          <h3>Experienced Team</h3>
          <p>Led by professionals with 15+ years in indirect tax, our GST team includes advocates, tax consultants and trained compliance executives who understand Chennai's regulatory environment.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">EN</div>
          <h3>End-to-End Support</h3>
          <p>From registration to return filing, notice response to appellate representation — we cover the full GST lifecycle under one engagement without handing you off between multiple vendors.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">PR</div>
          <h3>Process-Driven Execution</h3>
          <p>Our GST practice operates on checklist-based reviews, maker-checker validation and documented closure summaries — not memory or informal follow-ups.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">RE</div>
          <h3>Responsive Support</h3>
          <p>We maintain defined response timeframes for client queries, escalation handling and compliance event tracking with proactive deadline alerts.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">AF</div>
          <h3>Affordable &amp; Transparent</h3>
          <p>Our fee structure is clear, engagement-based and scaled to the size and complexity of your compliance needs — no hidden charges or surprise billing.</p>
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
              <p>We understand your business, turnover, nature of supplies, existing compliance status and pain points. This helps us scope the engagement accurately for your Chennai operations.</p>
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
        <p class="section-label">FAQs on GST Compliance in Chennai</p>
        <h2 class="section-title">Common GST questions answered by our team.</h2>
        <p class="section-intro">
          Practical answers to the most frequent GST compliance questions we receive from businesses across Chennai.
        </p>
      </div>

      <div class="faq-list">
        <details class="faq-item" open>
          <summary>
            <h3>What is the GST registration threshold for businesses in Chennai?</h3>
          </summary>
          <p>GST registration is mandatory when aggregate turnover exceeds Rs. 40 lakhs for goods suppliers and Rs. 20 lakhs for service providers in a financial year. Businesses operating in Chennai commercial districts such as T. Nagar, Mount Road and OMR must also register if they engage in inter-state supply, e-commerce operations or casual taxable person activities regardless of turnover. We assist with the complete registration process.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>How does e-way bill compliance work for goods movement within Chennai?</h3>
          </summary>
          <p>For inter-state movement of goods valued above Rs. 50,000, e-way bill generation is mandatory. Intra-state movement within Tamil Nadu requires an e-way bill when the consignment value exceeds Rs. 1,00,000. Businesses in industrial areas like Guindy, Ambattur and Porur frequently require e-way bill support for raw material and finished goods transport. We assist with generation, Part-B extension, consolidation and rejection handling.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>What are the key GST return deadlines Chennai businesses must track?</h3>
          </summary>
          <p>GSTR-3B is due by the 20th of each month. GSTR-1 is due by the 11th for monthly filers and 13th for quarterly QRMP filers. The annual return GSTR-9 must be filed by 31st December of the following financial year. Late filing attracts interest at 18% per annum plus a late fee of Rs. 50 per day (Rs. 25 each under CGST and SGST). Our team provides proactive deadline tracking for Chennai-based businesses to avoid penalties.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>Can you help with GST notice response for Chennai-based businesses?</h3>
          </summary>
          <p>Yes. GST notices under Sections 73 and 74, summons under Section 70, and assessment orders require a structured response with proper documentation. We review the notice, analyse the discrepancy, prepare a response with supporting records and, where required, provide representation before the Chennai GST Commissionerate at Nungambakkam. Our team has experience handling notices for traders in T. Nagar, manufacturers in Ambattur and IT firms on OMR.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>How is ITC reconciliation handled for businesses with multiple vendors in Chennai?</h3>
          </summary>
          <p>ITC reconciliation involves matching GSTR-2B data with purchase ledgers vendor by vendor. Businesses in Chennai often deal with numerous suppliers across T. Nagar, Parrys and Koyambedu markets, making reconciliation critical. Mismatches if unresolved lead to notices and interest demands. We perform vendor-wise reconciliation, coordinate with suppliers for missing invoices and ensure accurate credit availment with proper documentation trail.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>What is the composition scheme and who can opt for it in Chennai?</h3>
          </summary>
          <p>The Composition Scheme is available for businesses with turnover up to Rs. 1.5 crore (Rs. 75 lakhs for service providers). It allows GST payment at lower rates — 1% for traders, 6% for service providers — with quarterly return filing. Small retailers in Chennai markets such as Ranganathan Street, Pondy Bazaar and Sowcarpet often benefit from this scheme. We assist with eligibility evaluation, scheme migration and quarterly compliance under the composition scheme.</p>
        </details>
        <details class="faq-item">
          <summary>
            <h3>Do exporters in Chennai need LUT filing under GST?</h3>
          </summary>
          <p>Yes, exporters must file a Letter of Undertaking in Form GST RFD-11 to export goods or services without paying IGST. The LUT is valid for one financial year and must be renewed annually before the start of the new financial year. Exporters in Chennai, particularly those in the MEPZ Special Economic Zone and industrial estates in Ambattur and Guindy, rely on timely LUT filing to maintain duty-free export benefits. We prepare and file LUT applications and track renewals to prevent lapses.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">What Our Clients Say</p>
        <h2 class="section-title">Feedback from businesses we have served with GST compliance.</h2>
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
        <h2 class="section-title">Discuss your GST compliance needs with our team.</h2>
        <p class="section-intro">
          Use this form for GST registration support, return filing assistance, notice response help,
          e-way bill queries or any GST compliance requirement. We will review your requirement and get back to you.
        </p>
      </div>

      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>How we can help you with GST</h3>
          <ul class="list-clean">
            <li>New GST registration or composition scheme migration for Chennai businesses.</li>
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

          <form method="post" action="<?= htmlspecialchars(site_href('/gst-consultant-chennai.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="gst_consult">
            <input type="hidden" name="source_page" value="/gst-consultant-chennai.php">
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
        <h2>Need immediate GST compliance support in Chennai?</h2>
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
