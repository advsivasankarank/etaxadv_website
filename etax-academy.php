<?php
require_once __DIR__ . '/includes/contact-handler.php';
$page_title = "E Tax Academy – Professional Training & Certification | E Tax Advisors";
$page_description = "E Tax Academy offers professional training programs in tax, compliance and accounting for students, professionals and corporate teams.";
$page_path = '/etax-academy.php';
$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'product_consult') {
  $consult_result = contact_process_submission();
}
contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">E Tax Academy</div>
        <h1>Professional training and certification programs in tax, compliance and accounting for career growth and team capability.</h1>
        <p>
          E Tax Academy is the training and certification division of E Tax Advisors Private Limited. We offer programs
          for students entering the tax profession, professionals seeking structured upskilling and corporate teams
          that need customised compliance training for their workforce.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Enquire Now</a>
          <a class="btn btn-outline" href="contact.php#consult">Book Consultation</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Tax training programs</span>
          <span class="proof-chip">Compliance certification</span>
          <span class="proof-chip">Corporate team training</span>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-grid">
          <div class="hero-metric">
            <strong>Live Online Classes</strong>
            <span>Instructor-led sessions with real-time interaction, case studies and practical exercises.</span>
          </div>
          <div class="hero-metric">
            <strong>Structured Curriculum</strong>
            <span>Programs designed by practising professionals with current regulatory and industry relevance.</span>
          </div>
          <div class="hero-metric">
            <strong>Certification</strong>
            <span>Certificate of completion issued by E Tax Advisors Private Limited for each program.</span>
          </div>
          <div class="hero-metric">
            <strong>Corporate Programs</strong>
            <span>Customised training for corporate teams on specific compliance, tax and accounting topics.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Programs</p>
        <h2 class="section-title">Training programs designed for different career stages and organisational needs.</h2>
      </div>

      <div class="grid-3">
        <article class="card card-muted">
          <div class="feature-icon">GST</div>
          <h3>GST Practitioner Program</h3>
          <p>Comprehensive training on GST return filing, reconciliation, notice response, input tax credit optimisation and audit support. Ideal for students and early-career professionals.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">IT</div>
          <h3>Income Tax & TDS Program</h3>
          <p>Covers income tax return preparation, TDS compliance, form 16, salary tax planning, perquisite reporting and assessment procedures for individuals and businesses.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">LAB</div>
          <h3>Labour Law & HR Compliance Program</h3>
          <p>Training on statutory registers, payroll compliance, inspection readiness, contract labour management and employee life-cycle documentation for HR and compliance teams.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">ACC</div>
          <h3>Practical Accounting & Bookkeeping Program</h3>
          <p>Hands-on training in ledger management, bank reconciliation, GST data alignment, TDS mapping, MIS reporting and month-end closure procedures.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">COM</div>
          <h3>Company & LLP Compliance Program</h3>
          <p>Covers annual filings, board meeting procedures, director compliance, MCA return preparation and secretarial standards for company secretaries and compliance officers.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">CORP</div>
          <h3>Corporate Training Programs</h3>
          <p>Custom-designed programs for corporate teams covering tax compliance, regulatory updates, payroll management, audit readiness and industry-specific compliance topics.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why E Tax Academy</p>
        <h2 class="section-title">Training that bridges the gap between theory and compliance practice.</h2>
      </div>

      <div class="grid-2">
        <article class="card">
          <h3>For students & professionals</h3>
          <ul class="list-clean">
            <li>Curriculum designed and delivered by practising tax and compliance professionals</li>
            <li>Real-world case studies, return preparation exercises and compliance scenario work</li>
            <li>Small batch sizes for personalised attention and doubt resolution</li>
            <li>Certificate of completion with practical skill assessment</li>
            <li>Placement support and internship referrals for meritorious participants</li>
          </ul>
        </article>
        <article class="card">
          <h3>For corporate teams</h3>
          <ul class="list-clean">
            <li>Customised curriculum aligned to your industry and compliance obligations</li>
            <li>Flexible delivery — online, on-site or hybrid as per team availability</li>
            <li>Post-training support materials and reference documentation</li>
            <li>Assessment and feedback reports for management review</li>
            <li>Periodic refresher sessions on regulatory changes and updates</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Four steps to enrolling in a program or scheduling corporate training.</h2>
      </div>

      <div class="process-timeline">
        <article class="timeline-step">
          <span class="timeline-number">1</span>
          <h3>Choose your program</h3>
          <p>Browse our program catalogue, review the curriculum and select the program that matches your career or team requirements.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">2</span>
          <h3>Register and enrol</h3>
          <p>Complete the enrolment form, make the program fee payment and receive your training schedule, study materials and access credentials.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">3</span>
          <h3>Attend sessions and practice</h3>
          <p>Participate in live sessions, complete practical exercises, work on case studies and take assessments designed to test applied knowledge.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">4</span>
          <h3>Certification and beyond</h3>
          <p>Receive your certificate of completion, access post-training reference materials and explore internship or placement opportunities.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Program Schedule</p>
        <h2 class="section-title">Upcoming batch schedules and program formats.</h2>
      </div>

      <div class="grid-3">
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Program Calendar View</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Learning Dashboard</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Certificate Sample</span>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Pricing</p>
        <h2 class="section-title">Affordable programs with transparent fee structure.</h2>
      </div>

      <div class="card" style="max-width:480px;margin:0 auto;text-align:center;padding:48px 32px;">
        <h3 style="margin-bottom:12px;">Contact for Program Fees</h3>
        <p style="color:var(--muted);margin-bottom:24px;">
          Program fees vary by curriculum duration and delivery format. Corporate training pricing is customised based on team size and scope. Contact us for details.
        </p>
        <a class="btn btn-primary" href="#consult">Request Fee Details</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Common questions about E Tax Academy programs.</h2>
      </div>

      <div class="faq-list">
        <details class="faq-item">
          <summary>Who are the instructors at E Tax Academy?</summary>
          <p>All programs are delivered by practising professionals from E Tax Advisors Private Limited — chartered accountants, company secretaries, tax advisors and compliance specialists with hands-on experience in their respective domains.</p>
        </details>
        <details class="faq-item">
          <summary>Are the programs conducted online or in-person?</summary>
          <p>Programs are primarily conducted through live online sessions with interactive components. In-person and on-site corporate training options are available based on location and team requirements.</p>
        </details>
        <details class="faq-item">
          <summary>Do you offer placement assistance after program completion?</summary>
          <p>Yes. We provide placement support and internship referrals for meritorious participants. Our placement team works with tax firms, compliance departments and corporate finance teams to identify suitable opportunities.</p>
        </details>
        <details class="faq-item">
          <summary>Can corporate teams get customised training programs?</summary>
          <p>Absolutely. We design custom training programs based on the organisation's industry, compliance obligations and team skill gaps. Content, duration and delivery format are tailored to your requirements.</p>
        </details>
        <details class="faq-item">
          <summary>Is there an assessment or examination at the end of the program?</summary>
          <p>Each program includes periodic assessments and a final evaluation to test practical understanding. Participants who meet the passing criteria receive a certificate of completion from E Tax Advisors Private Limited.</p>
        </details>
        <details class="faq-item">
          <summary>Do you provide study materials and recordings?</summary>
          <p>Yes. Participants receive structured study materials, reference documents and access to session recordings for the duration of the program.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="consult">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Get Started</p>
        <h2 class="section-title">Interested in a program? Let us help you choose the right path.</h2>
      </div>

      <div class="contact-card consult-form-card" style="max-width:720px;margin:0 auto;">
        <p>Fill in your details and our academy team will reach out with program details, fees and batch schedules.</p>

<?php if ($consult_result && $consult_result['success']): ?>
        <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
        <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(site_href('/etax-academy.php')) ?>#consult">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="product_consult">
          <input type="hidden" name="service" value="E Tax Academy - Professional Training & Certification">
          <input type="hidden" name="source_page" value="/etax-academy.php">
          <div class="form-grid">
            <div class="field">
              <label for="acad_name">Name</label>
              <input class="input" id="acad_name" name="name" required />
            </div>
            <div class="field">
              <label for="acad_mobile">Mobile</label>
              <input class="input" id="acad_mobile" name="mobile" required />
            </div>
            <div class="field">
              <label for="acad_email">Email</label>
              <input class="input" id="acad_email" name="email" type="email" required />
            </div>
            <div class="field">
              <label for="acad_org">Organisation (if applicable)</label>
              <input class="input" id="acad_org" name="organisation" />
            </div>
            <div class="field">
              <label for="acad_time">Preferred Contact Time</label>
              <input class="input" id="acad_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date" />
            </div>
            <div class="field full-span">
              <label for="acad_msg">Program Interest / Requirement</label>
              <textarea class="input" id="acad_msg" name="message" placeholder="Tell us which program interests you or describe your corporate training needs..." required></textarea>
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
