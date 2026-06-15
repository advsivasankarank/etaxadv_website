<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';
require_once __DIR__ . '/content/trust-sections.php';

$page_title = "Premium Tax, Legal & Compliance Advisory | E Tax Advisors Private Limited";
$page_description = "Integrated tax, legal, compliance and bookkeeping advisory for businesses, founders, trustees and promoters seeking structured execution and professional representation.";
$page_path = '/index.php';
$homepageTestimonialSummary = testimonial_get_summary();
$homepageTestimonials = testimonial_get_featured(8);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'home_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="hero-eyebrow">Premium Tax & Legal Advisory</div>
        <h1>Advisory that protects your business, your compliance, and your confidence.</h1>
        <p>
          E Tax Advisors Private Limited brings together tax, regulatory, bookkeeping and representation support 
          under one disciplined delivery model. We help managing directors, CFOs, factory owners, trust founders 
          and business promoters move from uncertainty to decision-ready action.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary btn-lg" href="contact.php#consult">Book Free Consultation</a>
          <a class="btn btn-secondary btn-lg" href="services.php">Explore Services</a>
        </div>
      </div>
      <div class="hero-image">
        <div class="hero-image-placeholder">
          Professional advisory consultation setting<br/>
          <span style="font-size:14px;opacity:.6;margin-top:8px;display:block;">[Boardroom imagery — replace with premium photography]</span>
        </div>
      </div>
    </div>
  </section>

  <section class="credibility-strip">
    <div class="container">
      <div class="credibility-grid">
        <div class="credibility-item"><strong>1000+</strong><span>Clients Served</span></div>
        <div class="credibility-item"><strong>15+</strong><span>Years Experience</span></div>
        <div class="credibility-item"><strong>5+</strong><span>Advisory Professionals</span></div>
        <div class="credibility-item"><strong>8</strong><span>Core Practice Areas</span></div>
        <div class="credibility-item"><strong>500+</strong><span>Notice &amp; Compliance Matters</span></div>
        <div class="credibility-item"><strong>PAN India</strong><span>Service Coverage</span></div>
      </div>
    </div>
  </section>

  <section class="section section-alt">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Leadership</p>
        <h2 class="section-title">Visible leadership for matters where experience, judgment and accountability matter most.</h2>
        <p class="section-intro">Premium advisory relationships are built on who leads the matter, how decisions are reviewed, and whether clients can rely on experienced hands when timing, compliance and representation risk are at stake.</p>
      </div>

      <div class="leadership-grid">
        <article class="leader-card">
          <a href="/team/ks-sivasankaran.php" class="leader-photo" aria-label="View full profile of K. Sivasankaran" style="display:block;text-decoration:none;">
            <div class="leader-photo-placeholder">KS</div>
          </a>
          <div class="leader-body">
            <h3><a href="/team/ks-sivasankaran.php" style="color:inherit;text-decoration:none;">K. Sivasankaran</a></h3>
            <p class="leader-role">Advocate | Tax Consultant</p>
            <p class="leader-credentials">B.Com., LL.B. — 15+ years in tax, litigation and corporate compliance advisory.</p>
            <ul class="leader-expertise">
              <li>GST Advisory &amp; Litigation</li>
              <li>Income Tax Advisory &amp; Representation</li>
              <li>Corporate &amp; Labour Law Compliance</li>
              <li>Commercial Documentation &amp; Contracts</li>
            </ul>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <a class="btn btn-outline btn-sm" href="/team/ks-sivasankaran.php">View Full Profile</a>
              <a class="btn btn-primary btn-sm" href="contact.php#consult">Schedule Consultation</a>
            </div>
          </div>
        </article>

        <article class="leader-card">
          <div class="leader-photo">
            <div class="leader-photo-placeholder">SM</div>
          </div>
          <div class="leader-body">
            <h3>S. Muthulakshmi</h3>
            <p class="leader-role">Managing Director</p>
            <p class="leader-credentials">Leading client-facing operations, compliance delivery and business development.</p>
            <ul class="leader-expertise">
              <li>Client Relations &amp; Engagement Management</li>
              <li>Operations &amp; Compliance Delivery Oversight</li>
              <li>Business Development &amp; Strategic Advisory</li>
              <li>Technology Integration &amp; Process Design</li>
            </ul>
            <a class="btn btn-primary btn-sm" href="contact.php#consult">Schedule Consultation</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="section" id="practice-areas">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Practice Areas</p>
        <h2 class="section-title">Focused advisory support where the cost of delay, error or weak follow-up is highest.</h2>
        <p class="section-intro">Each practice area is built around the specific risks, documentation requirements, and decision timelines our clients face daily.</p>
      </div>

      <div class="practice-grid">
        <article class="practice-card">
          <h3>GST Notices &amp; Appeals</h3>
          <p>Notice anxiety, incomplete records, weak response framing and timeline pressure are common. We support matter review, documentation readiness, drafting and authority-facing follow-through.</p>
          <a class="btn btn-outline btn-sm" href="contact.php#consult">Discuss GST matter</a>
        </article>
        <article class="practice-card">
          <h3>Income Tax Advisory</h3>
          <p>Unexplained mismatches, scrutiny concerns, missing backup and unclear submission strategy require structured handling. We help organise facts, records, response notes and escalation-sensitive submissions.</p>
          <a class="btn btn-outline btn-sm" href="contact.php#consult">Review notice</a>
        </article>
        <article class="practice-card">
          <h3>Labour Law Compliance</h3>
          <p>Fragmented HR processes, missed filings and weak documentation discipline create employer risk. We align labour compliance with recurring controls, operational coordination and reporting discipline.</p>
          <a class="btn btn-outline btn-sm" href="contact.php#consult">Discuss labour compliance</a>
        </article>
        <article class="practice-card">
          <h3>Company &amp; LLP Compliance</h3>
          <p>Promoter-side confusion, due-date pressure and incomplete filing support are recurring issues. We coordinate entity compliance, event-based filings and management documentation follow-through.</p>
          <a class="btn btn-outline btn-sm" href="services.php#company">Explore support</a>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-alt">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Why Choose Us</p>
        <h2 class="section-title">Four reasons businesses choose E Tax Advisors over a traditional accountant-led model.</h2>
      </div>

      <div class="reasons-grid">
        <div class="reason-card">
          <div class="reason-icon">01</div>
          <h3>Integrated Advisory Model</h3>
          <p>Tax, compliance, bookkeeping and representation under one accountable desk — no more coordinating between independent advisors.</p>
        </div>
        <div class="reason-card">
          <div class="reason-icon">02</div>
          <h3>Technology-Enabled Execution</h3>
          <p>Platform-driven workflows, tracking and accounting tools ensure visibility, consistency and timely follow-through on every engagement.</p>
        </div>
        <div class="reason-card">
          <div class="reason-icon">03</div>
          <h3>Leadership-Led Relationships</h3>
          <p>Every engagement is supervised by a senior professional. Clients speak to decision-makers, not juniors handling volume.</p>
        </div>
        <div class="reason-card">
          <div class="reason-icon">04</div>
          <h3>Representation Readiness</h3>
          <p>When matters reach notices, appeals or litigation, our documentation and response protocols ensure you are never under-prepared.</p>
        </div>
      </div>
    </div>
  </section>

<?php render_membership_section(); ?>

  <section class="section" id="who-we-advise">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Industries We Serve</p>
        <h2 class="section-title">We work with businesses and institutions that carry real operating responsibility.</h2>
        <p class="section-intro">Our advisory is designed for organisations where compliance gaps, filing delays or weak documentation carry measurable business risk.</p>
      </div>

      <div class="industry-grid">
        <div class="industry-card">
          <h3>MSMEs</h3>
          <p>Compliance, notices, documentation and periodic controls for growing enterprises.</p>
        </div>
        <div class="industry-card">
          <h3>Manufacturers</h3>
          <p>GST, labour, entity and record discipline for operationally intensive businesses.</p>
        </div>
        <div class="industry-card">
          <h3>Traders &amp; Distributors</h3>
          <p>Return support, reconciliations and documentation for moving-volume businesses.</p>
        </div>
        <div class="industry-card">
          <h3>Startups &amp; Entrepreneurs</h3>
          <p>Formation, compliance setup, bookkeeping and promoter-side advisory.</p>
        </div>
        <div class="industry-card">
          <h3>Educational Institutions</h3>
          <p>Governance-sensitive support for entity, payroll and tax compliance.</p>
        </div>
        <div class="industry-card">
          <h3>NGOs &amp; Trusts</h3>
          <p>Registration, governance, reporting and compliance for public trust institutions.</p>
        </div>
        <div class="industry-card">
          <h3>Professionals</h3>
          <p>Tax planning, compliance and documentation-sensitive support for practitioners.</p>
        </div>
        <div class="industry-card">
          <h3>Family-Owned Businesses</h3>
          <p>Integrated advisory where operations, promoter obligations and compliance intersect.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-alt" id="technology-driven-advisory">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Technology Ecosystem</p>
        <h2 class="section-title">A digital ecosystem designed to strengthen execution quality, client visibility and responsiveness.</h2>
        <p class="section-intro">Our proprietary platforms extend advisory beyond consultation into structured workflow, tracking and control.</p>
      </div>

      <div class="tech-grid">
        <div class="tech-card">
          <div class="tech-screenshot">[ e-Pani screenshot ]</div>
          <h3>e-Pani</h3>
          <p class="tech-subtitle">Office Management Suite</p>
          <p>Internal office coordination, work routing, task visibility and operating discipline for engagement execution.</p>
          <ul>
            <li>Operational control &amp; workflow visibility</li>
            <li>Task routing &amp; accountability</li>
            <li>Service delivery tracking</li>
          </ul>
        </div>
        <div class="tech-card">
          <div class="tech-screenshot">[ e-HR screenshot ]</div>
          <h3>e-HR</h3>
          <p class="tech-subtitle">HR &amp; Labour Compliance Platform</p>
          <p>Structured support for labour law administration, people-process coordination and compliance delivery.</p>
          <ul>
            <li>Labour compliance workflows</li>
            <li>HR coordination &amp; documentation</li>
            <li>Regulatory reporting support</li>
          </ul>
        </div>
        <div class="tech-card">
          <div class="tech-screenshot">[ e-Kanakan screenshot ]</div>
          <h3>e-Kanakan</h3>
          <p class="tech-subtitle">Bookkeeping Automation</p>
          <p>Bookkeeping and reconciliation support designed to improve record quality and downstream compliance readiness.</p>
          <ul>
            <li>Ledger discipline &amp; reconciliation</li>
            <li>MIS-ready management outputs</li>
            <li>Compliance-aligned accounting</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Client Testimonials</p>
        <h2 class="section-title">How clients describe the value of a structured advisory relationship.</h2>
<?php if ($homepageTestimonialSummary): ?>
        <p class="section-intro">Average rating: <strong><?= h(number_format($homepageTestimonialSummary['average_rating'], 1)) ?>/5</strong> across <strong><?= h((string)$homepageTestimonialSummary['total_reviews']) ?></strong> published reviews.</p>
<?php endif; ?>
      </div>

<?php if ($homepageTestimonials): ?>
      <div class="testimonial-controls">
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('prev')" aria-label="Previous testimonials">&#8592;</button>
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('next')" aria-label="Next testimonials">&#8594;</button>
      </div>

      <div class="testimonial-track" id="testimonialTrack">
<?php foreach ($homepageTestimonials as $item): ?>
        <article class="testimonial-card">
          <div class="rating"><?= h(testimonial_issue_stars((int)$item['rating'])) ?></div>
          <h3><?= h($item['client_name']) ?></h3>
          <p class="company"><?= h($item['company_name']) ?><?= $item['city'] ? ', ' . h($item['city']) : '' ?></p>
          <blockquote><?= h($item['testimonial_text']) ?></blockquote>
        </article>
<?php endforeach; ?>
      </div>
<?php else: ?>
      <div class="section-intro">
        <p>Approved testimonials will appear here. Reviews are displayed only with client permission.</p>
      </div>
<?php endif; ?>

      <div style="display:flex;gap:12px;margin-top:32px;flex-wrap:wrap;">
        <a class="btn btn-outline" href="<?= htmlspecialchars(app_href('/testimonial/')) ?>">View All Testimonials</a>
        <a class="btn btn-primary" href="<?= htmlspecialchars(app_href('/testimonial/#share-review')) ?>">Share Your Experience</a>
      </div>
    </div>
  </section>

  <section class="section section-alt" id="insights-updates">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Insights &amp; Updates</p>
        <h2 class="section-title">Practical guidance for the compliance and tax decisions that matter most to your business.</h2>
      </div>

      <div class="insight-grid">
        <article class="insight-card">
          <span class="insight-tag">GST Updates</span>
          <h3>Periodic GST interpretation notes</h3>
          <p>Filing changes, notice trends, reconciliation risks and practical guidance for GST compliance.</p>
          <a class="insight-link" href="contact.php#consult">Request update brief</a>
        </article>
        <article class="insight-card">
          <span class="insight-tag">Income Tax</span>
          <h3>Assessment and planning developments</h3>
          <p>Income tax interpretations, filing alerts and representation-sensitive updates for businesses.</p>
          <a class="insight-link" href="contact.php#consult">Request update brief</a>
        </article>
        <article class="insight-card">
          <span class="insight-tag">Compliance Alerts</span>
          <h3>Due-date and governance reminders</h3>
          <p>Company, LLP, TDS, payroll and recurring compliance action alerts for your entity.</p>
          <a class="insight-link" href="contact.php#consult">Request update brief</a>
        </article>
        <article class="insight-card">
          <span class="insight-tag">Labour Law</span>
          <h3>HR and employment compliance developments</h3>
          <p>Labour compliance interpretations, process changes and employer response guidance.</p>
          <a class="insight-link" href="contact.php#consult">Request update brief</a>
        </article>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <h2>Need a senior review before you file, reply or commit?</h2>
      <p>Use a consultation call when the matter involves notices, deadlines, representations, entity compliance or documentation-sensitive decisions.</p>
      <div class="cta-actions">
        <a class="btn btn-gold btn-lg" href="contact.php#consult">Request Consultation</a>
        <a class="btn btn-secondary" href="client-support.php">Raise Support Ticket</a>
      </div>
    </div>
  </section>

<?php render_service_guarantee_section(); ?>

  <section class="section" id="consult">
    <div class="container consult-shell">
      <div class="consult-info">
        <h3>Start with a structured advisory discussion.</h3>
        <p>Use this route when you need a professional review of a notice, entity compliance issue, labour law requirement, bookkeeping challenge or documentation-sensitive business decision.</p>
        <ul>
          <li>Clarify the issue, timeline and risk level before action is taken.</li>
          <li>Identify what records, reconciliations or supporting papers are missing.</li>
          <li>Route the matter to the right advisory, compliance or representation path.</li>
        </ul>
      </div>

      <div>
<?php if ($consult_result && $consult_result['success']): ?>
        <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
        <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(site_href('/index.php')) ?>#consult">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="home_consult">
          <input type="hidden" name="source_page" value="/index.php">
          <div class="form-grid">
            <div class="field">
              <label for="home_name">Name</label>
              <input class="input" id="home_name" name="name" required />
            </div>
            <div class="field">
              <label for="home_mobile">Mobile</label>
              <input class="input" id="home_mobile" name="mobile" required />
            </div>
            <div class="field">
              <label for="home_email">Email</label>
              <input class="input" id="home_email" name="email" type="email" required />
            </div>
            <div class="field">
              <label for="home_org">Organisation</label>
              <input class="input" id="home_org" name="organisation" />
            </div>
            <div class="field">
              <label for="home_service">Service Required</label>
              <input class="input" id="home_service" name="service" placeholder="GST, income tax, labour, company, bookkeeping, litigation, etc." required />
            </div>
            <div class="field">
              <label for="home_time">Preferred Consultation Time</label>
              <input class="input" id="home_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
            </div>
            <div class="field full-span">
              <label for="home_msg">Brief Requirement</label>
              <textarea class="input" id="home_msg" name="message" required></textarea>
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
