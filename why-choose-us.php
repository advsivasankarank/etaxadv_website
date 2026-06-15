<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Why Choose E Tax Advisors – Integrated Advisory Model";
$page_description = "Discover why businesses choose E Tax Advisors for integrated tax, compliance and bookkeeping advisory over traditional accounting models.";
$page_path = '/why-choose-us.php';
$page_og_image = '/assets/img/og-image.jpg';

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'wcu_consult') {
  $consult_result = contact_process_submission();
}

$wcu_testimonial_summary = testimonial_get_summary();
$wcu_testimonials = testimonial_get_featured(6);

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Why Choose Us</div>
        <h1>An advisory model built for clients who need more than just filing support.</h1>
        <p>
          Businesses choose E Tax Advisors when they want a single desk for tax, compliance, bookkeeping
          and representation matters. Our structure reduces coordination gaps, improves documentation
          quality and builds accountability into every engagement.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="contact.php#consult">Book Free Consultation</a>
          <a class="btn btn-outline" href="team.php">Meet Our Team</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Integrated advisory model</span>
          <span class="proof-chip">Review-controlled outputs</span>
          <span class="proof-chip">Single-point accountability</span>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Structured differently from traditional accounting practices.</h2>
            <p>
              We treat compliance, advisory and documentation as connected services rather than isolated
              transactions. The difference is visible in how we communicate, document and follow through.
            </p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>8</strong>
              <span>integrated service domains under one advisory umbrella</span>
            </div>
            <div class="hero-metric">
              <strong>3000+</strong>
              <span>businesses and entities served across India</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Comparison</p>
        <h2 class="section-title">Why businesses choose E Tax Advisors over a traditional accountant-led model.</h2>
        <p class="section-intro">
          The current market often forces clients to coordinate between separate accountants, tax consultants
          and compliance handlers. Our model closes that gap through one integrated workflow.
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

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Key Differentiators</p>
        <h2 class="section-title">Six structural advantages that define how we work.</h2>
        <p class="section-intro">
          These differentiators are not marketing claims. They are built into our engagement workflow,
          team structure and client communication standards.
        </p>
      </div>

      <div class="grid-3">
        <article class="card card-muted">
          <div class="feature-icon">01</div>
          <h3>Integrated advisory under one desk</h3>
          <p>Tax, compliance, bookkeeping, legal and representation support are coordinated within the firm rather than split across separate advisors. This reduces gaps, miscommunication and delays.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">02</div>
          <h3>Review-controlled delivery</h3>
          <p>Every filing, response and advisory note passes through internal verification before it reaches the client or the authority. Maker-checker review is standard, not optional.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">03</div>
          <h3>Documentation as a service standard</h3>
          <p>Scope notes, record checklists, review observations, closure summaries and pending action lists are treated as part of the service rather than as exceptions.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">04</div>
          <h3>Technology-enabled execution</h3>
          <p>Workflow platforms, tracking tools and accounting automation support are used to improve consistency, visibility and response time across engagements.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">05</div>
          <h3>Leadership visibility and continuity</h3>
          <p>Clients interact with the same practice leads throughout the engagement. Matters are not handed off between unfamiliar team members without structured transition.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">06</div>
          <h3>Structured escalation and support routing</h3>
          <p>Client queries, follow-ups and escalation requests are logged, routed and responded to within documented timeframes rather than depending on memory or individual availability.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Clients By The Numbers</p>
        <h2 class="section-title">Consistent delivery across a growing client base.</h2>
        <p class="section-intro">These metrics reflect the firm's operating experience and the breadth of matters handled across service domains.</p>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <strong>3000+</strong>
          <span>Clients served across India</span>
        </div>
        <div class="stat-card">
          <strong>15+</strong>
          <span>Years of professional advisory experience</span>
        </div>
        <div class="stat-card">
          <strong>50+</strong>
          <span>Professionals across teams</span>
        </div>
        <div class="stat-card">
          <strong>8</strong>
          <span>Integrated service domains</span>
        </div>
        <div class="stat-card">
          <strong>24-48 hrs</strong>
          <span>Standard client response window</span>
        </div>
        <div class="stat-card">
          <strong>98%</strong>
          <span>Client satisfaction rating</span>
        </div>
        <div class="stat-card">
          <strong>1000+</strong>
          <span>Matters handled annually</span>
        </div>
        <div class="stat-card">
          <strong>Dedicated</strong>
          <span>Practice teams per domain</span>
        </div>
      </div>
    </div>
  </section>

<?php if ($wcu_testimonials): ?>
  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">What clients say about working with E Tax Advisors.</h2>
<?php if ($wcu_testimonial_summary['average_rating'] > 0): ?>
        <p class="section-intro">
          Average approved rating: <strong><?= number_format($wcu_testimonial_summary['average_rating'], 1) ?>/5</strong>
          across <strong><?= (int)$wcu_testimonial_summary['total_reviews'] ?></strong> published client reviews.
        </p>
<?php endif; ?>
      </div>

      <div class="testimonial-controls">
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('prev')" aria-label="Previous testimonials">Previous</button>
        <button class="testimonial-nav" type="button" onclick="scrollTestimonialTrack('next')" aria-label="Next testimonials">Next</button>
      </div>

      <div class="testimonial-track" id="wcuTestimonialTrack">
<?php foreach ($wcu_testimonials as $item): ?>
        <article class="testimonial-card premium">
          <div class="testimonial-meta">
            <span class="verified-badge">Verified Client</span>
            <span class="star-rating"><?= testimonial_issue_stars((int)$item['rating']) ?></span>
          </div>
          <span class="testimonial-type"><?= htmlspecialchars($item['service_availed']) ?></span>
          <p><?= htmlspecialchars($item['testimonial_text']) ?></p>
          <div class="testimonial-footer">
            <strong><?= htmlspecialchars($item['client_name']) ?></strong>
            <span><?= htmlspecialchars($item['company_name']) ?><?php if ($item['city']): ?>, <?= htmlspecialchars($item['city']) ?><?php endif; ?></span>
          </div>
        </article>
<?php endforeach; ?>
      </div>

      <div class="section-actions">
        <a class="btn btn-outline" href="<?= htmlspecialchars(app_href('/testimonial/')) ?>">View All Testimonials</a>
        <a class="btn btn-primary" href="<?= htmlspecialchars(app_href('/testimonial/#share-review')) ?>">Share Your Experience</a>
      </div>
    </div>
  </section>
<?php endif; ?>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Industries We Serve</p>
        <h2 class="section-title">Our advisory model is relevant across a wide range of business contexts.</h2>
      </div>

      <div class="industry-grid">
        <span class="industry-pill">MSMEs</span>
        <span class="industry-pill">Manufacturers</span>
        <span class="industry-pill">Traders &amp; Distributors</span>
        <span class="industry-pill">Startups</span>
        <span class="industry-pill">Educational Institutions</span>
        <span class="industry-pill">NGOs &amp; Trusts</span>
        <span class="industry-pill">Professionals</span>
        <span class="industry-pill">Family-Owned Businesses</span>
        <span class="industry-pill">Hospitals &amp; Clinics</span>
        <span class="industry-pill">Real Estate Developers</span>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container cta-band">
      <div>
        <h2>Ready to experience a more structured advisory relationship?</h2>
        <p>
          Schedule a consultation to discuss how our integrated model can support your tax, compliance
          and bookkeeping requirements with professional accountability.
        </p>
        <div class="cta-actions">
          <a class="btn btn-primary" href="contact.php#consult">Book Free Consultation</a>
          <a class="btn btn-outline" href="team.php">Meet Our Team</a>
        </div>
      </div>
      <div class="card">
        <h3>Contact Information</h3>
        <ul class="list-clean">
          <li>Call: <strong>+91 98946 26300</strong> for immediate routing</li>
          <li>Email: <strong>support@etaxadv.com</strong> for written enquiries</li>
          <li>WhatsApp: <strong>+91 95006 01119</strong> for quick connect</li>
        </ul>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
