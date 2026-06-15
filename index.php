<?php
require_once __DIR__ . '/includes/testimonials.php';

$page_title = "Premium Tax, Legal & Compliance Advisory | E Tax Advisors Private Limited";
$page_description = "Integrated tax, legal, compliance and bookkeeping advisory for businesses, founders, trustees and promoters seeking structured execution and professional representation.";
$page_path = '/index.php';
$homepageTestimonialSummary = testimonial_get_summary();
$homepageTestimonials = testimonial_get_featured(8);
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Professional Advisory Firm</div>
        <h1>Tax, legal and compliance advisory built for confident business decisions.</h1>
        <p>
          E Tax Advisors Private Limited brings together tax, regulatory, bookkeeping and representation support
          under one disciplined delivery model. We help clients move from uncertainty to decision-ready action with
          structured documentation, review controls and accountable communication.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="contact.php#consult">Book Free Consultation</a>
          <a class="btn btn-outline" href="services.php">Explore Service Lines</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Income Tax, GST, TDS & payroll support</span>
          <span class="proof-chip">Company, LLP and trust compliance</span>
          <span class="proof-chip">Structured bookkeeping through e-Kanakan</span>
        </div>
        <p class="hero-note">Engagement-based professional services for businesses, promoters, trustees and professional entities across India.</p>
      </div>

      <div class="hero-visual" aria-label="Advisory positioning">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Built around disciplined execution.</h2>
            <p>
              Advisory engagement is supported by document validation, exception handling, review notes,
              response protocols and follow-through support rather than one-time filing alone.
            </p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>8</strong>
              <span>core service domains under one advisory umbrella</span>
            </div>
            <div class="hero-metric">
              <strong>24-48 hrs</strong>
              <span>documented client support and escalation response windows</span>
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
        <p class="section-label">Leadership Team</p>
        <h2 class="section-title">Visible leadership for clients who need experience, accountability and judgment.</h2>
        <p class="section-intro">
          Premium advisory relationships are built on who leads the matter, how decisions are reviewed and whether
          clients can rely on experienced hands when timing, compliance and representation risk matter.
        </p>
      </div>

      <div class="leadership-grid">
        <article class="leader-card">
          <div class="leader-portrait" aria-label="Placeholder portrait for K. Sivasankaran">KS</div>
          <div class="leader-body">
            <p class="leader-role">Advocate | Tax Consultant</p>
            <h3>K. Sivasankaran</h3>
            <p class="leader-credentials">B.Com., LL.B.</p>
            <ul class="leader-expertise">
              <li>GST Advisory &amp; Litigation</li>
              <li>Income Tax Advisory</li>
              <li>Corporate Compliance</li>
              <li>Labour Law Advisory</li>
              <li>Commercial Documentation</li>
            </ul>
            <a class="btn btn-primary" href="contact.php#consult">Schedule a Consultation</a>
          </div>
        </article>

        <article class="leader-card">
          <div class="leader-portrait" aria-label="Placeholder portrait for S. Muthulakshmi">SM</div>
          <div class="leader-body">
            <p class="leader-role">Managing Director</p>
            <h3>S. Muthulakshmi</h3>
            <p class="leader-credentials">Client-facing operations and compliance leadership</p>
            <ul class="leader-expertise">
              <li>Client Relations</li>
              <li>Operations Management</li>
              <li>Compliance Delivery</li>
              <li>Business Development</li>
            </ul>
            <a class="btn btn-primary" href="contact.php#consult">Schedule a Consultation</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Who We Advise</p>
        <h2 class="section-title">Structured advisory support for businesses and institutions that carry real operating responsibility.</h2>
      </div>

      <div class="who-grid">
        <article class="who-card">
          <div class="feature-icon">MS</div>
          <h3>MSMEs</h3>
          <p>Compliance, notices, documentation and periodic controls for growing small and medium enterprises.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">MF</div>
          <h3>Manufacturers</h3>
          <p>GST, labour, entity and record discipline support for operationally intensive businesses.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">TD</div>
          <h3>Traders &amp; Distributors</h3>
          <p>Return support, reconciliations, documentation and commercial control for moving-volume businesses.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">ST</div>
          <h3>Startups &amp; Entrepreneurs</h3>
          <p>Formation, compliance setup, bookkeeping discipline and promoter-side advisory for scaling teams.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">ED</div>
          <h3>Educational Institutions</h3>
          <p>Governance-sensitive support for entity, payroll, tax and administrative compliance requirements.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">NG</div>
          <h3>NGOs &amp; Trusts</h3>
          <p>Registration, governance, reporting and compliance handling for institutions built on public trust.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">PR</div>
          <h3>Professionals</h3>
          <p>Individual and practice-entity support for tax planning, compliance and documentation-sensitive matters.</p>
        </article>
        <article class="who-card">
          <div class="feature-icon">FB</div>
          <h3>Family-Owned Businesses</h3>
          <p>Integrated advisory where business operations, promoter obligations and entity compliance intersect.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why E Tax Advisors</p>
        <h2 class="section-title">Why businesses choose E Tax Advisors over a traditional accountant-led model.</h2>
        <p class="section-intro">
          The current market often forces clients to coordinate between separate accountants, tax consultants and compliance handlers.
          Our model closes that gap through one integrated workflow.
        </p>
      </div>

      <div class="comparison-shell">
        <div class="comparison-head">
          <div>Decision Area</div>
          <div>Traditional Accountant</div>
          <div>E Tax Advisors</div>
        </div>
        <div class="comparison-row">
          <div>Compliance</div>
          <div>Periodic filing focus</div>
          <div>Filing plus review controls, documentation and follow-through</div>
        </div>
        <div class="comparison-row">
          <div>Advisory</div>
          <div>Mostly transaction-based guidance</div>
          <div>Structured interpretation, risk view and action planning</div>
        </div>
        <div class="comparison-row">
          <div>Technology</div>
          <div>Low-process dependence on manual coordination</div>
          <div>Platform-led workflows, tracking and accounting support tools</div>
        </div>
        <div class="comparison-row">
          <div>Reporting</div>
          <div>Submission-focused</div>
          <div>Management-ready clarity, next actions and closure summaries</div>
        </div>
        <div class="comparison-row">
          <div>Follow-up</div>
          <div>Reactive response after filing</div>
          <div>Documented escalation, support routing and response timelines</div>
        </div>
        <div class="comparison-row">
          <div>Litigation Support</div>
          <div>Limited notice handling depth</div>
          <div>Notice response, representation support and matter coordination</div>
        </div>
        <div class="comparison-row">
          <div>Dedicated Team</div>
          <div>Single-handler dependency</div>
          <div>Leadership oversight plus compliance and support coordination</div>
        </div>
        <div class="comparison-row">
          <div>Business Insights</div>
          <div>Compliance viewed in isolation</div>
          <div>Books, tax, labour and entity issues viewed together</div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Technology Driven Advisory</p>
        <h2 class="section-title">A digital ecosystem designed to strengthen execution, visibility and client responsiveness.</h2>
      </div>

      <div class="grid-3 tech-grid">
        <article class="card tech-card">
          <div class="feature-icon">EP</div>
          <h3>e-Pani</h3>
          <p class="tech-subtitle">Office Management Suite</p>
          <p>Internal office coordination, work routing, task visibility and operating discipline for engagement execution.</p>
          <ul class="list-clean">
            <li>Operational control</li>
            <li>Workflow visibility</li>
            <li>Accountability support</li>
          </ul>
          <a class="service-link" href="contact.php#consult">Learn more</a>
        </article>
        <article class="card tech-card">
          <div class="feature-icon">HR</div>
          <h3>e-HR</h3>
          <p class="tech-subtitle">HR &amp; Labour Compliance Platform</p>
          <p>Structured support for labour law administration, people-process coordination and compliance delivery.</p>
          <ul class="list-clean">
            <li>Labour compliance workflows</li>
            <li>HR coordination support</li>
            <li>Documentation discipline</li>
          </ul>
          <a class="service-link" href="contact.php#consult">Learn more</a>
        </article>
        <article class="card tech-card">
          <div class="feature-icon">EK</div>
          <h3>e-Kanakan</h3>
          <p class="tech-subtitle">Accounting Automation</p>
          <p>Bookkeeping and reconciliation support designed to improve record quality and downstream compliance readiness.</p>
          <ul class="list-clean">
            <li>Ledger discipline</li>
            <li>Reconciliation control</li>
            <li>MIS-ready support</li>
          </ul>
          <a class="service-link" href="ekanakan.php">Learn more</a>
        </article>
        <article class="card tech-card">
          <div class="feature-icon">SP</div>
          <h3>SalPro</h3>
          <p class="tech-subtitle">Salary Tax Planning System</p>
          <p>A structured framework for salary-oriented tax planning, employee support and working-sheet efficiency.</p>
          <ul class="list-clean">
            <li>Employee tax clarity</li>
            <li>Working automation support</li>
            <li>Repeatable planning workflows</li>
          </ul>
          <a class="service-link" href="tools.php">Learn more</a>
        </article>
        <article class="card tech-card">
          <div class="feature-icon">AC</div>
          <h3>E Tax Academy</h3>
          <p class="tech-subtitle">Professional Training &amp; Certification</p>
          <p>Capability-building initiatives designed to improve technical execution, delivery consistency and professional readiness.</p>
          <ul class="list-clean">
            <li>Training and upskilling</li>
            <li>Delivery consistency</li>
            <li>Professional knowledge culture</li>
          </ul>
          <a class="service-link" href="contact.php#consult">Learn more</a>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Practice Area Highlights</p>
        <h2 class="section-title">Focused support where the cost of delay, error or weak follow-up is high.</h2>
      </div>

      <div class="grid-3 practice-grid">
        <article class="card practice-card">
          <h3>GST Notices &amp; Appeals</h3>
          <p class="practice-problem">Common problems: notice anxiety, incomplete records, poor response framing and timeline pressure.</p>
          <p class="practice-solution">We support matter review, documentation readiness, drafting support and authority-facing follow-through.</p>
          <a class="btn btn-outline" href="contact.php#consult">Discuss GST matter</a>
        </article>
        <article class="card practice-card">
          <h3>Income Tax Notices</h3>
          <p class="practice-problem">Common problems: unexplained mismatches, scrutiny concerns, missing backup and unclear submission strategy.</p>
          <p class="practice-solution">We help structure facts, records, response notes and escalation-sensitive handling.</p>
          <a class="btn btn-outline" href="contact.php#consult">Review notice</a>
        </article>
        <article class="card practice-card">
          <h3>Labour Law Compliance</h3>
          <p class="practice-problem">Common problems: fragmented HR processes, missed filings and weak documentation discipline.</p>
          <p class="practice-solution">We align labour compliance with recurring controls, operational coordination and reporting discipline.</p>
          <a class="btn btn-outline" href="contact.php#consult">Discuss labour compliance</a>
        </article>
        <article class="card practice-card">
          <h3>Company &amp; LLP Compliance</h3>
          <p class="practice-problem">Common problems: promoter-side confusion, due-date pressure and incomplete filing support.</p>
          <p class="practice-solution">We coordinate entity compliance, event-based filings and management documentation follow-through.</p>
          <a class="btn btn-outline" href="services.php#company">Explore support</a>
        </article>
        <article class="card practice-card">
          <h3>Litigation &amp; Representation</h3>
          <p class="practice-problem">Common problems: weak draft responses, inconsistent records and escalation risk.</p>
          <p class="practice-solution">We support representation-sensitive matters with clearer documentation and structured response preparation.</p>
          <a class="btn btn-outline" href="contact.php#consult">Schedule representation review</a>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">How clients describe the value of a more structured advisory relationship.</h2>
        <p class="section-intro">
          Average approved rating: <strong><?= h(number_format($homepageTestimonialSummary['average_rating'], 1)) ?>/5</strong>
          across <strong><?= h((string)$homepageTestimonialSummary['total_reviews']) ?></strong> published client reviews.
        </p>
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

  <section class="section">
    <div class="container consult-shell">
      <div class="section-header">
        <p class="section-label">Book A Consultation</p>
        <h2 class="section-title">Start with a structured advisory discussion.</h2>
        <p class="section-intro">
          Use this route when you need a professional review of a notice, entity compliance issue, labour law requirement,
          bookkeeping challenge or documentation-sensitive business decision.
        </p>
      </div>

      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What this consultation helps you do</h3>
          <ul class="list-clean">
            <li>Clarify the issue, timeline and risk level before action is taken.</li>
            <li>Identify what records, reconciliations or supporting papers are missing.</li>
            <li>Route the matter to the right advisory, compliance or representation path.</li>
          </ul>
          <div class="consult-note">
            <strong>Preferred matters</strong>
            <span>GST notices, income tax notices, labour compliance, promoter issues, company compliance and accounting clean-up.</span>
          </div>
        </div>

        <div class="contact-card">
          <form class="js-consult-form">
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
                <input class="input" id="home_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" required />
              </div>
              <div class="field full-span">
                <label for="home_msg">Brief Requirement</label>
                <textarea class="input" id="home_msg" name="message" required></textarea>
              </div>
              <div class="field full-span">
                <button class="btn btn-primary" type="submit">Open Consultation Draft</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Insights &amp; Updates</p>
        <h2 class="section-title">A reusable structure for thought leadership, compliance alerts and client-facing updates.</h2>
      </div>

      <div class="grid-4 insight-grid">
        <article class="card insight-card">
          <span class="insight-tag">GST Updates</span>
          <h3>Periodic GST interpretation notes</h3>
          <p>Use this space for filing changes, notice trends, reconciliation risks and practical GST guidance.</p>
          <a class="service-link" href="contact.php#consult">Request update brief</a>
        </article>
        <article class="card insight-card">
          <span class="insight-tag">Income Tax Updates</span>
          <h3>Assessment and planning developments</h3>
          <p>Use this block for income tax interpretations, filing alerts and representation-sensitive updates.</p>
          <a class="service-link" href="contact.php#consult">Request update brief</a>
        </article>
        <article class="card insight-card">
          <span class="insight-tag">Compliance Alerts</span>
          <h3>Due-date and governance reminders</h3>
          <p>Use this block for company, LLP, TDS, payroll and recurring compliance action alerts.</p>
          <a class="service-link" href="contact.php#consult">Request update brief</a>
        </article>
        <article class="card insight-card">
          <span class="insight-tag">Labour Law Updates</span>
          <h3>Labour and HR compliance developments</h3>
          <p>Use this space for labour compliance interpretations, process changes and employer response notes.</p>
          <a class="service-link" href="contact.php#consult">Request update brief</a>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container cta-band">
      <div>
        <h2>Need a senior review before you file, reply or commit?</h2>
        <p>
          Use a consultation call when the matter involves notices, deadlines, representations, entity compliance,
          bookkeeping cleanup or documentation-sensitive decisions.
        </p>
        <div class="cta-actions">
          <a class="btn btn-primary" href="contact.php#consult">Request Consultation</a>
          <a class="btn btn-outline" href="client-support.php">Raise Support Ticket</a>
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
