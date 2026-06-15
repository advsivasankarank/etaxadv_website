<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Trust & Society Registration Consultants | E Tax Advisors Puducherry";
$page_description = "Trust registration, society registration, 80G/12A, FCRA and NGO compliance services.";
$page_path = '/trust-society-registration.php';
$homepageTestimonialSummary = testimonial_get_summary();
$homepageTestimonials = testimonial_get_featured(8);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'trust_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Trust & Society Registration</div>
        <h1>Trust and society registration, 80G/12A and FCRA compliance services.</h1>
        <p>
          We assist charitable institutions, NGOs, religious trusts and societies with registration under the Indian Trusts Act,
          Societies Registration Act, income tax approvals for 80G and 12A, FCRA registration and annual compliance.
          Our team provides end-to-end support from trust deed drafting to regulatory filings.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Discuss Trust Registration</a>
          <a class="btn btn-outline" href="services.php">Explore Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Trust & Society Registration</span>
          <span class="proof-chip">80G & 12A Approval</span>
          <span class="proof-chip">FCRA & NGO Compliance</span>
        </div>
        <p class="hero-note">Dedicated NGO compliance and registration support for charitable organisations across India, with office presence in Puducherry.</p>
      </div>
      <div class="hero-visual" aria-label="Trust and society registration overview">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Built on regulatory experience and documentation discipline.</h2>
            <p>Trust and society formation requires careful deed drafting, regulatory clarity and structured compliance. We bring all three to every engagement.</p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>Trust Registration</strong>
              <span>private and public trust formation with deed drafting and income tax approvals</span>
            </div>
            <div class="hero-metric">
              <strong>Society Registration</strong>
              <span>memorandum, by-laws and registration under the Societies Registration Act</span>
            </div>
            <div class="hero-metric">
              <strong>80G & 12A</strong>
              <span>income tax exemption approvals and renewal support for charitable institutions</span>
            </div>
            <div class="hero-metric">
              <strong>FCRA</strong>
              <span>registration, renewal and annual return filing for foreign contribution receipt</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container authority-ribbon">
      <div class="authority-pill"><strong>3000+</strong><span>Clients Served</span></div>
      <div class="authority-pill"><strong>15+</strong><span>Years Experience</span></div>
      <div class="authority-pill"><strong>Trust Deeds</strong><span>Drafted</span></div>
      <div class="authority-pill"><strong>80G/12A</strong><span>Approvals</span></div>
      <div class="authority-pill"><strong>FCRA</strong><span>Support</span></div>
      <div class="authority-pill"><strong>Puducherry</strong><span>Office</span></div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Trust & NGO Services</p>
        <h2 class="section-title">Complete registration and compliance support for charitable organisations.</h2>
        <p class="section-intro">From trust formation to FCRA compliance, we offer structured services for NGOs and charitable institutions.</p>
      </div>
      <div class="grid-3">
        <article class="card">
          <div class="feature-icon">TR</div>
          <h3>Trust Registration</h3>
          <p>Private and public charitable trust registration with trust deed drafting, settlor and trustee documentation and income tax PAN application.</p>
        </article>
        <article class="card">
          <div class="feature-icon">SR</div>
          <h3>Society Registration</h3>
          <p>Society formation under the Societies Registration Act with memorandum, by-laws, governing body composition and registration filing.</p>
        </article>
        <article class="card">
          <div class="feature-icon">80</div>
          <h3>80G & 12A Approval</h3>
          <p>Income tax exemption registration under section 12A and 80G of the Income Tax Act for donor tax benefit eligibility.</p>
        </article>
        <article class="card">
          <div class="feature-icon">FC</div>
          <h3>FCRA Compliance</h3>
          <p>FCRA registration, renewal, annual return (FC-4/FC-6) filing and foreign contribution receipt compliance for NGOs.</p>
        </article>
        <article class="card">
          <div class="feature-icon">AC</div>
          <h3>Annual NGO Compliance</h3>
          <p>Annual return filing, income tax return for trusts, audit documentation and governing body meeting compliance.</p>
        </article>
        <article class="card">
          <div class="feature-icon">DE</div>
          <h3>Deed Drafting & Amendments</h3>
          <p>Trust deed drafting, society by-laws, amendment deeds, scheme alteration and object clause modifications.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Benefits</p>
        <h2 class="section-title">Why proper registration and compliance strengthen your charitable mission.</h2>
      </div>
      <div class="grid-2">
        <article class="card card-muted">
          <h3>Tax Exemption for Donors</h3>
          <p>80G approval allows donors to claim a deduction on their contributions, making it easier to raise funds for your charitable activities.</p>
        </article>
        <article class="card card-muted">
          <h3>Regulatory Credibility</h3>
          <p>Registered trusts and societies enjoy greater credibility with government agencies, donors, grant-making bodies and the public.</p>
        </article>
        <article class="card card-muted">
          <h3>FCRA Compliance Readiness</h3>
          <p>Organisations receiving foreign contributions must maintain FCRA compliance. Our process ensures timely filings and renewal management.</p>
        </article>
        <article class="card card-muted">
          <h3>Perpetual Succession</h3>
          <p>Registered trusts and societies enjoy perpetual succession, meaning the organisation continues regardless of changes in trustees or members.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Our trust and society engagement process in four steps.</h2>
      </div>
      <div class="process-timeline">
        <div class="timeline-step">
          <div class="timeline-number">1</div>
          <h3>Consultation & Structure</h3>
          <p>We discuss your objectives, suggest the appropriate structure (trust, society or section 8 company) and outline the documentation pathway.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">2</div>
          <h3>Deed & Document Preparation</h3>
          <p>Trust deed or society memorandum is drafted, reviewed and finalised. Supporting documents are prepared for registration.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">3</div>
          <h3>Registration Filing</h3>
          <p>Documents are filed with the relevant authority. We follow up on the application and address any queries from the registrar.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">4</div>
          <h3>Tax Approvals & Compliance Setup</h3>
          <p>Post-registration, we file for 12A, 80G and PAN. A compliance calendar is established for returns, audits and FCRA management.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Frequently asked questions about trust and society registration.</h2>
      </div>
      <div class="faq-list" itemscope itemtype="https://schema.org/FAQPage">
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is the difference between a trust and a society?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">A trust is created through a trust deed and is managed by trustees. It is governed by the Indian Trusts Act 1882 for private trusts or general principles for public trusts. A society is governed by the Societies Registration Act 1860, has a governing body and a membership-based structure.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is 80G and 12A approval?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Section 12A registration provides income tax exemption to the trust or society on its income. Section 80G approval allows donors to claim a deduction on their contributions to the organisation. Both are granted by the Income Tax Department and require proper application documentation.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Who can register under FCRA?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Any trust, society or section 8 company with a proven track record of at least three years in charitable activities can apply for FCRA registration. Certain categories of persons are prohibited from receiving foreign contributions under the FCRA Act.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What documents are needed for trust registration?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Key documents include the trust deed, settlor and trustee identity and address proofs, PAN cards, passport-size photographs and proof of registered office address. The specific requirements vary by state.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Can an existing trust apply for 80G approval?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Yes. An existing registered trust or society with a proven track record of charitable activities can apply for 80G and 12A approval at any time, provided it meets the conditions specified under the Income Tax Act.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Do you provide annual compliance support for NGOs?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Yes. We offer annual compliance services including income tax return filing for trusts, FCRA annual returns (FC-4/FC-6), 80G renewal, audit documentation and governing body meeting compliance.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">What our clients say about our trust and society services.</h2>
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
        <h2 class="section-title">Discuss your trust or society registration requirements.</h2>
        <p class="section-intro">
          Use this form to request a consultation for trust registration, society formation, 80G/12A approval, FCRA compliance or NGO annual compliance support.
        </p>
      </div>
      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What you will get from this consultation</h3>
          <ul class="list-clean">
            <li>Guidance on the most suitable legal structure for your charitable objectives.</li>
            <li>A complete checklist of documents required for registration and tax approvals.</li>
            <li>A clear roadmap from registration to FCRA compliance and annual reporting.</li>
          </ul>
          <div class="consult-note">
            <strong>Preferred for</strong>
            <span>Founders of charitable institutions, NGO promoters, trustees, religious organisations and educational trusts seeking registration and compliance support.</span>
          </div>
        </div>
        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>
          <form method="post" action="<?= htmlspecialchars(site_href('/trust-society-registration.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="trust_consult">
            <input type="hidden" name="source_page" value="/trust-society-registration.php">
            <div class="form-grid">
              <div class="field">
                <label for="trust_name">Name</label>
                <input class="input" id="trust_name" name="name" required />
              </div>
              <div class="field">
                <label for="trust_mobile">Mobile</label>
                <input class="input" id="trust_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="trust_email">Email</label>
                <input class="input" id="trust_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="trust_org">Organisation</label>
                <input class="input" id="trust_org" name="organisation" />
              </div>
              <div class="field">
                <label for="trust_service">Service Required</label>
                <input class="input" id="trust_service" name="service" placeholder="Trust registration, society registration, 80G/12A, FCRA, annual compliance, etc." required />
              </div>
              <div class="field">
                <label for="trust_time">Preferred Consultation Time</label>
                <input class="input" id="trust_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="trust_msg">Brief Requirement</label>
                <textarea class="input" id="trust_msg" name="message" required></textarea>
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
      "name": "What is the difference between a trust and a society?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "A trust is created through a trust deed and is managed by trustees. It is governed by the Indian Trusts Act 1882 for private trusts or general principles for public trusts. A society is governed by the Societies Registration Act 1860, has a governing body and a membership-based structure."
      }
    },
    {
      "@type": "Question",
      "name": "What is 80G and 12A approval?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Section 12A registration provides income tax exemption to the trust or society on its income. Section 80G approval allows donors to claim a deduction on their contributions to the organisation. Both are granted by the Income Tax Department and require proper application documentation."
      }
    },
    {
      "@type": "Question",
      "name": "Who can register under FCRA?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Any trust, society or section 8 company with a proven track record of at least three years in charitable activities can apply for FCRA registration. Certain categories of persons are prohibited from receiving foreign contributions under the FCRA Act."
      }
    },
    {
      "@type": "Question",
      "name": "What documents are needed for trust registration?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Key documents include the trust deed, settlor and trustee identity and address proofs, PAN cards, passport-size photographs and proof of registered office address. The specific requirements vary by state."
      }
    },
    {
      "@type": "Question",
      "name": "Can an existing trust apply for 80G approval?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. An existing registered trust or society with a proven track record of charitable activities can apply for 80G and 12A approval at any time, provided it meets the conditions specified under the Income Tax Act."
      }
    },
    {
      "@type": "Question",
      "name": "Do you provide annual compliance support for NGOs?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. We offer annual compliance services including income tax return filing for trusts, FCRA annual returns (FC-4/FC-6), 80G renewal, audit documentation and governing body meeting compliance."
      }
    }
  ]
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
