<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Labour Law Compliance Services – HR & Payroll Compliance | E Tax Advisors";
$page_description = "Labour law compliance, HR compliance, payroll advisory under various labour codes for businesses in India.";
$page_path = '/labour-law-compliance.php';
$homepageTestimonialSummary = testimonial_get_summary();
$homepageTestimonials = testimonial_get_featured(8);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'labour_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Labour Law Compliance</div>
        <h1>Labour law, HR and payroll compliance built for Indian employers.</h1>
        <p>
          We help businesses navigate the evolving labour compliance landscape under the Code on Wages, Industrial Relations Code,
          Social Security Code and OSH Code. Our services span ESI, PF, bonus, gratuity, contract labour regulation,
          factory compliance and structured HR policy support.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Discuss Labour Compliance</a>
          <a class="btn btn-outline" href="services.php#labour">View All Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">ESI & PF compliance</span>
          <span class="proof-chip">Bonus & Gratuity advisory</span>
          <span class="proof-chip">Contract labour registration</span>
        </div>
        <p class="hero-note">Structured labour law support for manufacturing units, offices, startups and establishments across India.</p>
      </div>
      <div class="hero-visual" aria-label="Labour law compliance overview">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Designed for operational compliance clarity.</h2>
            <p>Labour compliance is not limited to PF and ESI filings. It encompasses registers, returns, policies, contractor management and factory-level obligations.</p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>ESI & PF</strong>
              <span>monthly returns, annual statements and inspection readiness support</span>
            </div>
            <div class="hero-metric">
              <strong>Bonus & Gratuity</strong>
              <span>computation, policy design and payment compliance under applicable acts</span>
            </div>
            <div class="hero-metric">
              <strong>Contract Labour</strong>
              <span>registration, licence management and principal employer obligations</span>
            </div>
            <div class="hero-metric">
              <strong>Factory Act</strong>
              <span>registrations, annual returns, registers and safety compliance support</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container authority-ribbon">
      <div class="authority-pill"><strong>3000+</strong><span>Clients Served</span></div>
      <div class="authority-pill"><strong>15+</strong><span>Years Experience</span></div>
      <div class="authority-pill"><strong>ESI</strong><span>Return Support</span></div>
      <div class="authority-pill"><strong>PF</strong><span>Compliance</span></div>
      <div class="authority-pill"><strong>HR Policies</strong><span>Drafted</span></div>
      <div class="authority-pill"><strong>Inspections</strong><span>Ready Support</span></div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Labour Law Services</p>
        <h2 class="section-title">Comprehensive HR and labour compliance services for employers.</h2>
        <p class="section-intro">We support businesses in meeting their obligations under central and state labour enactments through structured compliance workflows.</p>
      </div>
      <div class="grid-3">
        <article class="card">
          <div class="feature-icon">EP</div>
          <h3>ESI Compliance</h3>
          <p>Employee State Insurance registration, monthly returns, contribution statements and inspection readiness support.</p>
        </article>
        <article class="card">
          <div class="feature-icon">PF</div>
          <h3>PF Compliance</h3>
          <p>Provident Fund registration, monthly ECR filing, annual returns, employee transfer and withdrawal support.</p>
        </article>
        <article class="card">
          <div class="feature-icon">BN</div>
          <h3>Bonus Act Compliance</h3>
          <p>Payment of Bonus Act applicability, computation, annual return filing and register maintenance.</p>
        </article>
        <article class="card">
          <div class="feature-icon">GR</div>
          <h3>Gratuity Advisory</h3>
          <p>Payment of Gratuity Act coverage, calculation, policy drafting, fund setup and annual return support.</p>
        </article>
        <article class="card">
          <div class="feature-icon">CL</div>
          <h3>Contract Labour Compliance</h3>
          <p>Principal employer registration, contractor licence management, returns and compliance monitoring.</p>
        </article>
        <article class="card">
          <div class="feature-icon">FA</div>
          <h3>Factory Act Compliance</h3>
          <p>Factory registration, annual returns, register maintenance, safety compliance and inspector liaison.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Benefits</p>
        <h2 class="section-title">Why proactive labour law compliance strengthens your business.</h2>
      </div>
      <div class="grid-2">
        <article class="card card-muted">
          <h3>Avoid Penalties & Prosecution</h3>
          <p>Non-compliance under labour laws can result in show-cause notices, inspections, fines and in serious cases, prosecution. Our compliance calendar helps you stay ahead of due dates.</p>
        </article>
        <article class="card card-muted">
          <h3>Inspection-Ready Records</h3>
          <p>Labour inspectors may visit with little notice. We ensure your registers, returns and policies are organised and readily presentable.</p>
        </article>
        <article class="card card-muted">
          <h3>HR Policy Alignment</h3>
          <p>Employee handbooks, standing orders and HR policies must align with the new labour codes. We review and update these documents to reflect current law.</p>
        </article>
        <article class="card card-muted">
          <h3>Contractor Risk Management</h3>
          <p>Principal employers bear significant liability for contractor non-compliance. We monitor contractor licences, returns and compliance status to reduce this risk.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Our labour law engagement process in four steps.</h2>
      </div>
      <div class="process-timeline">
        <div class="timeline-step">
          <div class="timeline-number">1</div>
          <h3>Compliance Audit</h3>
          <p>We review your current labour law coverage, registrations, returns and policy documents to identify gaps.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">2</div>
          <h3>Registration & Setup</h3>
          <p>Missing registrations under ESI, PF, factory act, contract labour or shops and establishment are completed.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">3</div>
          <h3>Periodic Compliance</h3>
          <p>Monthly returns, annual filings, register updates and policy reviews are managed on a recurring schedule.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">4</div>
          <h3>Inspection & Notice Support</h3>
          <p>We assist with inspector visits, notice responses, documentation and follow-through for any labour department matters.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Frequently asked questions about labour law compliance.</h2>
      </div>
      <div class="faq-list" itemscope itemtype="https://schema.org/FAQPage">
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Which businesses need labour law compliance in India?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Any establishment employing individuals is covered under one or more labour enactments. Applicability depends on headcount, wage threshold, industry type and location. Even a single employee can trigger ESI or PF obligations in certain cases.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What is the difference between ESI and PF?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">ESI provides medical and sickness benefits to employees earning up to INR 21,000 per month. PF is a retirement benefit scheme applicable to establishments with 20 or more employees. Both require monthly contributions and periodic return filings.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What are the new labour codes in India?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">The four new labour codes are the Code on Wages 2019, Industrial Relations Code 2020, Social Security Code 2020 and the Occupational Safety, Health and Working Conditions Code 2020. These consolidate 29 central labour laws into a simplified framework.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">Do contractors need separate labour law compliance?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Yes. Contractors must obtain their own licences, maintain registers and file returns. The principal employer is also required to register and ensure contractor compliance, failing which liability may fall on the principal employer.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">What registers are required under factory law?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">Key registers include the muster roll, wage register, overtime register, inspection book, accident register and hazardous process register. The specific list depends on the state and the nature of manufacturing activity.</div>
          </div>
        </div>
        <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <h3 itemprop="name">How do you handle labour inspections?</h3>
          <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">We prepare your documentation, registers and returns for inspection. We also support your team during inspector visits and assist with any follow-up notices or compliance directions issued post-inspection.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">What our clients say about our labour law compliance services.</h2>
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
        <h2 class="section-title">Discuss your labour law compliance requirements.</h2>
        <p class="section-intro">
          Use this form to request a consultation for ESI, PF, bonus, gratuity, contract labour, factory compliance or HR policy support.
        </p>
      </div>
      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What you will get from this consultation</h3>
          <ul class="list-clean">
            <li>An assessment of your current labour law coverage and compliance gaps.</li>
            <li>Guidance on registrations, returns and policy documents required for your establishment.</li>
            <li>A roadmap for ongoing compliance management and inspection readiness.</li>
          </ul>
          <div class="consult-note">
            <strong>Preferred for</strong>
            <span>Manufacturing units, factories, offices, startups, HR teams and business owners with employee compliance obligations.</span>
          </div>
        </div>
        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>
          <form method="post" action="<?= htmlspecialchars(site_href('/labour-law-compliance.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="labour_consult">
            <input type="hidden" name="source_page" value="/labour-law-compliance.php">
            <div class="form-grid">
              <div class="field">
                <label for="labour_name">Name</label>
                <input class="input" id="labour_name" name="name" required />
              </div>
              <div class="field">
                <label for="labour_mobile">Mobile</label>
                <input class="input" id="labour_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="labour_email">Email</label>
                <input class="input" id="labour_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="labour_org">Organisation</label>
                <input class="input" id="labour_org" name="organisation" />
              </div>
              <div class="field">
                <label for="labour_service">Service Required</label>
                <input class="input" id="labour_service" name="service" placeholder="ESI, PF, bonus, gratuity, contract labour, factory act, HR policy, etc." required />
              </div>
              <div class="field">
                <label for="labour_time">Preferred Consultation Time</label>
                <input class="input" id="labour_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="labour_msg">Brief Requirement</label>
                <textarea class="input" id="labour_msg" name="message" required></textarea>
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
      "name": "Which businesses need labour law compliance in India?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Any establishment employing individuals is covered under one or more labour enactments. Applicability depends on headcount, wage threshold, industry type and location. Even a single employee can trigger ESI or PF obligations in certain cases."
      }
    },
    {
      "@type": "Question",
      "name": "What is the difference between ESI and PF?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "ESI provides medical and sickness benefits to employees earning up to INR 21,000 per month. PF is a retirement benefit scheme applicable to establishments with 20 or more employees. Both require monthly contributions and periodic return filings."
      }
    },
    {
      "@type": "Question",
      "name": "What are the new labour codes in India?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The four new labour codes are the Code on Wages 2019, Industrial Relations Code 2020, Social Security Code 2020 and the Occupational Safety, Health and Working Conditions Code 2020. These consolidate 29 central labour laws into a simplified framework."
      }
    },
    {
      "@type": "Question",
      "name": "Do contractors need separate labour law compliance?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. Contractors must obtain their own licences, maintain registers and file returns. The principal employer is also required to register and ensure contractor compliance, failing which liability may fall on the principal employer."
      }
    },
    {
      "@type": "Question",
      "name": "What registers are required under factory law?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Key registers include the muster roll, wage register, overtime register, inspection book, accident register and hazardous process register. The specific list depends on the state and the nature of manufacturing activity."
      }
    },
    {
      "@type": "Question",
      "name": "How do you handle labour inspections?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "We prepare your documentation, registers and returns for inspection. We also support your team during inspector visits and assist with any follow-up notices or compliance directions issued post-inspection."
      }
    }
  ]
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
