<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Project Reports & CMA Data Preparation | E Tax Advisors";
$page_description = "Professional project report preparation, CMA data, DPR and bank loan documentation support.";
$page_path = '/project-report-cma.php';
$homepageTestimonialSummary = testimonial_get_summary();
$homepageTestimonials = testimonial_get_featured(8);

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'project_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Project Reports & CMA</div>
        <h1>Professional project report preparation, CMA data and bank loan documentation.</h1>
        <p>
          We assist businesses, startups and entrepreneurs with detailed project report (DPR) preparation,
          CMA (Credit Monitoring Arrangement) data formulation, financial projections and bank loan documentation.
          Our reports are structured to meet the requirements of banks, financial institutions and government agencies.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Discuss Project Report</a>
          <a class="btn btn-outline" href="services.php">Explore Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">CMA Data & Projections</span>
          <span class="proof-chip">Detailed Project Reports</span>
          <span class="proof-chip">Bank Loan Documentation</span>
        </div>
        <p class="hero-note">Structured project report and financial documentation support for term loans, working capital and funding proposals.</p>
      </div>
      <div class="hero-visual" aria-label="Project report services overview">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Built around lender and institutional requirements.</h2>
            <p>Every project report and CMA statement we prepare follows the format prescribed by banks, RBI guidelines and funding institution requirements.</p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>CMA Data</strong>
              <span>operating statement, balance sheet projections and fund flow analysis</span>
            </div>
            <div class="hero-metric">
              <strong>DPR</strong>
              <span>detailed project reports with technical, financial and market feasibility analysis</span>
            </div>
            <div class="hero-metric">
              <strong>Projections</strong>
              <span>multi-year financial projections, profitability analysis and working capital assessment</span>
            </div>
            <div class="hero-metric">
              <strong>Bank Docs</strong>
              <span>loan application, term loan memorandum and working capital limit documentation</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container authority-ribbon">
      <div class="authority-pill"><strong>3000+</strong><span>Clients Served</span></div>
      <div class="authority-pill"><strong>15+</strong><span>Years Experience</span></div>
      <div class="authority-pill"><strong>CMA</strong><span>Data Experts</span></div>
      <div class="authority-pill"><strong>DPR</strong><span>Preparation</span></div>
      <div class="authority-pill"><strong>Bank</strong><span>Documentation</span></div>
      <div class="authority-pill"><strong>Projections</strong><span>Accurate</span></div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Project Report Services</p>
        <h2 class="section-title">Complete project report and financial documentation support.</h2>
        <p class="section-intro">From CMA data to detailed project reports, we offer end-to-end documentation support for funding and approval requirements.</p>
      </div>
      <div class="grid-3">
        <article class="card">
          <div class="feature-icon">CM</div>
          <h3>CMA Data Preparation</h3>
          <p>Credit Monitoring Arrangement statements including operating statement, balance sheet projections, fund flow statement and working capital assessment.</p>
        </article>
        <article class="card">
          <div class="feature-icon">DP</div>
          <h3>Detailed Project Reports</h3>
          <p>Comprehensive DPR covering project overview, market analysis, technical specifications, financial feasibility and implementation timeline.</p>
        </article>
        <article class="card">
          <div class="feature-icon">FI</div>
          <h3>Financial Projections</h3>
          <p>Multi-year projected profit and loss, balance sheet, cash flow and ratio analysis for loan proposals and investor presentations.</p>
        </article>
        <article class="card">
          <div class="feature-icon">BL</div>
          <h3>Bank Loan Documentation</h3>
          <p>Term loan memorandum, working capital limit proposal, project viability summary and supporting document compilation.</p>
        </article>
        <article class="card">
          <div class="feature-icon">TE</div>
          <h3>Technical Feasibility</h3>
          <p>Assessment of technical parameters, plant capacity, machinery specifications, raw material availability and production process.</p>
        </article>
        <article class="card">
          <div class="feature-icon">MA</div>
          <h3>Market Assessment</h3>
          <p>Demand-supply analysis, pricing strategy, competitor assessment and market penetration planning for project proposals.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Benefits</p>
        <h2 class="section-title">Why professionally prepared project reports make a difference.</h2>
      </div>
      <div class="grid-2">
        <article class="card card-muted">
          <h3>Higher Loan Approval Probability</h3>
          <p>Banks and financial institutions evaluate project reports and CMA data rigorously. Professionally prepared documentation improves your loan approval prospects.</p>
        </article>
        <article class="card card-muted">
          <h3>RBI-Compliant CMA Format</h3>
          <p>Our CMA statements follow the standard format prescribed by the RBI and individual bank requirements, reducing queries and rejection risk.</p>
        </article>
        <article class="card card-muted">
          <h3>Investor-Ready Documentation</h3>
          <p>Project reports prepared by our team are structured to meet the review standards of investors, PE funds and government subsidy agencies.</p>
        </article>
        <article class="card card-muted">
          <h3>Time & Cost Efficiency</h3>
          <p>We manage the entire documentation process, saving you weeks of effort and reducing the cost of repeated revisions and bank queries.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Our project report engagement process in four steps.</h2>
      </div>
      <div class="process-timeline">
        <div class="timeline-step">
          <div class="timeline-number">1</div>
          <h3>Requirement Assessment</h3>
          <p>We understand your project scope, funding requirement, lender expectations and timelines to plan the documentation approach.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">2</div>
          <h3>Data Collection & Analysis</h3>
          <p>Historical financials, projections, market data, technical specifications and supporting records are gathered and analysed.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">3</div>
          <h3>Report Preparation</h3>
          <p>CMA data, DPR or loan memorandum is prepared, reviewed internally and aligned with the specific format required by the lender.</p>
        </div>
        <div class="timeline-step">
          <div class="timeline-number">4</div>
          <h3>Submission & Query Support</h3>
          <p>The final report is submitted. We provide ongoing support for bank queries, revision requests and documentation follow-ups.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Testimonials</p>
        <h2 class="section-title">What our clients say about our project report services.</h2>
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
        <h2 class="section-title">Discuss your project report or CMA data requirements.</h2>
        <p class="section-intro">
          Use this form to request a consultation for project report preparation, CMA data, DPR, bank loan documentation or financial projections.
        </p>
      </div>
      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What you will get from this consultation</h3>
          <ul class="list-clean">
            <li>A review of your project scope and documentation requirements.</li>
            <li>Guidance on the specific format and data needed for your lender or institution.</li>
            <li>A clear timeline and fee estimate for report preparation and submission support.</li>
          </ul>
          <div class="consult-note">
            <strong>Preferred for</strong>
            <span>Entrepreneurs seeking term loans, businesses requiring working capital enhancement, startup founders preparing investor reports and companies applying for government subsidies.</span>
          </div>
        </div>
        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>
          <form method="post" action="<?= htmlspecialchars(site_href('/project-report-cma.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="project_consult">
            <input type="hidden" name="source_page" value="/project-report-cma.php">
            <div class="form-grid">
              <div class="field">
                <label for="project_name">Name</label>
                <input class="input" id="project_name" name="name" required />
              </div>
              <div class="field">
                <label for="project_mobile">Mobile</label>
                <input class="input" id="project_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="project_email">Email</label>
                <input class="input" id="project_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="project_org">Organisation</label>
                <input class="input" id="project_org" name="organisation" />
              </div>
              <div class="field">
                <label for="project_service">Service Required</label>
                <input class="input" id="project_service" name="service" placeholder="Project report, CMA data, DPR, bank loan documentation, projections, etc." required />
              </div>
              <div class="field">
                <label for="project_time">Preferred Consultation Time</label>
                <input class="input" id="project_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="project_msg">Brief Requirement</label>
                <textarea class="input" id="project_msg" name="message" required></textarea>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
