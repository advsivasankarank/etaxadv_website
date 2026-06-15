<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "ROC Compliance & Company Registration Services | E Tax Advisors";
$page_description = "Company incorporation, ROC filing, annual compliance, LLP registration and corporate governance support.";
$page_path = '/roc-company-compliance.php';
$homepageTestimonialSummary = testimonial_get_summary();
$homepageTestimonials = testimonial_get_featured(8);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'roc_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">ROC & Company Compliance</div>
        <h1>Company incorporation, ROC filing and corporate governance support.</h1>
        <p>
          We assist businesses with company and LLP incorporation, annual ROC filings, DIN and DPT-3 compliance, board meeting documentation,
          director changes and corporate governance advisory. Our team ensures that every filing is accurate, timely and compliant with the
          Companies Act 2013 and LLP Act 2008.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Discuss ROC Compliance</a>
          <a class="btn btn-outline" href="services.php#company">Explore Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Private & Public Company Incorporation</span>
          <span class="proof-chip">Annual ROC Filings (AOC-4, MGT-7)</span>
          <span class="proof-chip">LLP Registration & Compliance</span>
        </div>
        <p class="hero-note">End-to-end corporate compliance support for companies, LLPs and startups across India.</p>
      </div>
      <div class="hero-visual" aria-label="ROC compliance overview">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Corporate compliance built on accuracy and accountability.</h2>
            <p>ROC filings, board resolutions and annual returns require careful documentation. We bring structured processes to every corporate compliance engagement.</p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>Incorporation</strong>
              <span>company and LLP registration with DIN, PAN, TAN and bank account setup support</span>
            </div>
            <div class="hero-metric">
              <strong>Annual Filings</strong>
              <span>AOC-4, MGT-7, LLP Form 8 and income tax return coordination</span>
            </div>
            <div class="hero-metric">
              <strong>Event Based</strong>
              <span>director changes, registered office shift, DPT-3, charge filing and board resolutions</span>
            </div>
            <div class="hero-metric">
              <strong>Governance</strong>
              <span>board meeting minutes, register maintenance and secretarial documentation support</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container authority-ribbon">
      <div class="authority-pill"><strong>3000+</strong><span>Clients Served</span></div>
      <div class="authority-pill"><strong>15+</strong><span>Years Experience</span></div>
      <div class="authority-pill"><strong>Incorporation</strong><span>Support</span></div>
      <div class="authority-pill"><strong>Annual ROC</strong><span>Filings</span></div>
      <div class="authority-pill"><strong>LLP</strong><span>Compliance</span></div>
      <div class="authority-pill"><strong>DPT-3</strong><span>Filing</span></div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">ROC Services</p>
        <h2 class="section-title">Complete company and LLP compliance support under one desk.</h2>
        <p class="section-intro">From incorporation to annual filings and event-based compliance, we offer end-to-end corporate compliance services.</p>
      </div>
      <div class="grid-3">
        <article class="card">
          <div class="feature-icon">IN</div>
          <h3>Company Incorporation</h3>
          <p>Private limited, public limited and one person company registration with DIN, PAN, TAN, bank account and registered office support.</p>
        </article>
        <article class="card">
          <div class="feature-icon">LL</div>
          <h3>LLP Registration</h3>
          <p>Limited Liability Partnership incorporation including DPIN, PAN, TAN, LLP agreement drafting and initial compliance setup.</p>
        </article>
        <article class="card">
          <div class="feature-icon">AF</div>
          <h3>Annual ROC Filings</h3>
          <p>Preparation and filing of AOC-4, MGT-7, LLP Form 8 and 11 with accurate financial and management data.</p>
        </article>
        <article class="card">
          <div class="feature-icon">DI</div>
          <h3>DIN & Director Changes</h3>
          <p>Director identification number application, director appointment, resignation, KYC filing and DIN change support.</p>
        </article>
        <article class="card">
          <div class="feature-icon">DP</div>
          <h3>DPT-3 & Charge Filing</h3>
          <p>Deposit and charge filing compliance under sections 73 and 77 of the Companies Act, including return of deposits and charge creation.</p>
        </article>
        <article class="card">
          <div class="feature-icon">BG</div>
          <h3>Board Meeting & Governance</h3>
          <p>Board meeting minutes, resolutions, statutory register maintenance, secretarial documentation and compliance calendar management.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Benefits</p>
        <h2 class="section-title">Why structured corporate compliance is critical for your business.</h2>
      </div>
      <div class="grid-2">
        <article class="card card-muted">
          <h3>Avoid Penalty & Strike-Off</h3>
          <p>Non-filing of annual returns or late filing can attract heavy penalties under the Companies Act and may lead to company strike-off by the ROC. Our calendar helps you stay compliant.</p>
        </article>
        <article class="card card-muted">
          <h3>Investor & Bank Readiness</h3>
          <p>Investors and financial institutions review compliance history before funding or lending. A clean ROC compliance record improves your credibility and transaction readiness.</p>
        </article>
        <article class="card card-muted">
          <h3>Director Protection</h3>
          <p>Directors are personally liable for certain defaults under the Companies Act. Proper documentation, board resolutions and timely filings help protect director interests.</p>
        </article>
        <article class="card card-muted">
          <h3>Smooth Transitions</h3>
          <p>Changes in directors, registered office, shareholding or capital structure require proper documentation. We manage the paperwork and ROC filings for smooth transitions.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Our corporate compliance engagement process.</h2>
      </div>
      <div class="process-timeline">
        <div class="timeline-step">
          <div class="timeline-number">1</div>
          <h3>Kick-off & Document Collection</h3>
          <p>We gather incorporation documents, PAN, Aadhaar, address proof and previous filing records to assess compliance status.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">2</div>
          <h3>Preparation & Review</h3>
          <p>Documents are drafted, reviewed internally and shared for your approval before submission to the MCA portal.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">3</div>
          <h3>Filing & Acknowledgment</h3>
          <p>Forms are filed on the MCA portal. Acknowledgment receipts are shared and any ROC queries are addressed promptly.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">4</div>
          <h3>Compliance Closure</h3>
          <p>Filing confirmations, updated registers and a compliance summary are delivered. Follow-up events are tracked on the compliance calendar.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Frequently asked questions about ROC company compliance.</h2>
      </div>
      <div class="faq-list" itemscope itemtype="https://schema.org/FAQPage">
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is annual ROC compliance for a private company?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Every private company must file AOC-4 (financial statements) and MGT-7 (annual return) with the ROC within 30 days of the AGM. Additionally, income tax return and auditor appointment must be completed. Non-filing attracts penalties up to INR 1,00,000.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is the difference between a company and an LLP?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">A company is a separate legal entity with limited liability, governed by the Companies Act 2013. An LLP combines limited liability with partnership flexibility and is governed by the LLP Act 2008. LLPs have fewer compliance requirements compared to private companies.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is DPT-3 filing and who needs to file it?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">DPT-3 is the return of deposits or outstanding money received by a company that is not considered a deposit under the Companies Act. Every company accepting deposits or having outstanding amounts from shareholders, directors or others must file this annually by 30th June.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Can a foreign company register a subsidiary in India?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Yes. A foreign company can incorporate a wholly owned subsidiary as a private limited company in India. The process requires director DINs, digital signatures, registered office address and capital infusion as per FEMA guidelines.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What are the penalties for late ROC filing?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Late filing of annual returns attracts a fee of INR 100 per day per form. Additional penalties may apply under section 454 of the Companies Act. Persistent default can lead to director disqualification and company strike-off.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Do you support director KYC and DIN changes?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Yes. We handle DIR-3 KYC filing, director appointment (DIR-12), resignation, DIN change and related board resolution documentation for all types of companies.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">What our clients say about our ROC compliance services.</h2>
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
        <h2 class="section-title">Discuss your company or LLP compliance requirements.</h2>
        <p class="section-intro">
          Use this form to request a consultation for incorporation, annual ROC filing, DPT-3, director changes or corporate governance support.
        </p>
      </div>
      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What you will get from this consultation</h3>
          <ul class="list-clean">
            <li>An assessment of your current corporate compliance status and pending filings.</li>
            <li>Guidance on incorporation structure, annual obligations and event-based compliance.</li>
            <li>A clear compliance roadmap with timelines, document checklists and fee estimates.</li>
          </ul>
          <div class="consult-note">
            <strong>Preferred for</strong>
            <span>Founders, company directors, startup teams, LLP partners and business owners requiring ROC compliance support.</span>
          </div>
        </div>
        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>
          <form method="post" action="<?= htmlspecialchars(site_href('/roc-company-compliance.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="roc_consult">
            <input type="hidden" name="source_page" value="/roc-company-compliance.php">
            <div class="form-grid">
              <div class="field">
                <label for="roc_name">Name</label>
                <input class="input" id="roc_name" name="name" required />
              </div>
              <div class="field">
                <label for="roc_mobile">Mobile</label>
                <input class="input" id="roc_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="roc_email">Email</label>
                <input class="input" id="roc_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="roc_org">Organisation</label>
                <input class="input" id="roc_org" name="organisation" />
              </div>
              <div class="field">
                <label for="roc_service">Service Required</label>
                <input class="input" id="roc_service" name="service" placeholder="Incorporation, annual ROC filing, DPT-3, director change, LLP compliance, etc." required />
              </div>
              <div class="field">
                <label for="roc_time">Preferred Consultation Time</label>
                <input class="input" id="roc_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="roc_msg">Brief Requirement</label>
                <textarea class="input" id="roc_msg" name="message" required></textarea>
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
      "name": "What is annual ROC compliance for a private company?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Every private company must file AOC-4 (financial statements) and MGT-7 (annual return) with the ROC within 30 days of the AGM. Additionally, income tax return and auditor appointment must be completed. Non-filing attracts penalties up to INR 1,00,000."
      }
    },
    {
      "@type": "Question",
      "name": "What is the difference between a company and an LLP?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "A company is a separate legal entity with limited liability, governed by the Companies Act 2013. An LLP combines limited liability with partnership flexibility and is governed by the LLP Act 2008. LLPs have fewer compliance requirements compared to private companies."
      }
    },
    {
      "@type": "Question",
      "name": "What is DPT-3 filing and who needs to file it?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "DPT-3 is the return of deposits or outstanding money received by a company that is not considered a deposit under the Companies Act. Every company accepting deposits or having outstanding amounts from shareholders, directors or others must file this annually by 30th June."
      }
    },
    {
      "@type": "Question",
      "name": "Can a foreign company register a subsidiary in India?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. A foreign company can incorporate a wholly owned subsidiary as a private limited company in India. The process requires director DINs, digital signatures, registered office address and capital infusion as per FEMA guidelines."
      }
    },
    {
      "@type": "Question",
      "name": "What are the penalties for late ROC filing?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Late filing of annual returns attracts a fee of INR 100 per day per form. Additional penalties may apply under section 454 of the Companies Act. Persistent default can lead to director disqualification and company strike-off."
      }
    },
    {
      "@type": "Question",
      "name": "Do you support director KYC and DIN changes?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. We handle DIR-3 KYC filing, director appointment (DIR-12), resignation, DIN change and related board resolution documentation for all types of companies."
      }
    }
  ]
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
