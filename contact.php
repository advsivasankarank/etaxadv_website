<?php
$page_title = "Contact & Consultation | E Tax Advisors";
$page_description = "Book a consultation with E Tax Advisors Private Limited for tax, legal, compliance, bookkeeping and representation requirements.";
$page_path = '/contact.php';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Contact</div>
        <h1>Start with a structured consultation, not a fragmented conversation.</h1>
        <p>
          Use this channel for consultation requests, quotations and first-stage advisory discussions. For existing service issues,
          feedback or escalation, please use Client Support so the matter remains documented.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Request Consultation</a>
          <a class="btn btn-outline" href="client-support.php">Client Support</a>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Preferred channels for faster routing.</h2>
            <p>Call for urgent conversations, WhatsApp for quick initiation and the form below for structured consultation intake.</p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>Consult First</strong>
              <span>Useful for notices, representation, compliance setup and decision-sensitive reviews.</span>
            </div>
            <div class="hero-metric">
              <strong>Structured Intake</strong>
              <span>Service line, timing, organisation and requirement captured in one consultation brief.</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="consult">
    <div class="container contact-grid">
      <div class="contact-card">
        <h3>Direct contact options</h3>
        <p>Use the route that best matches the urgency and nature of your requirement.</p>
        <div class="contact-stack">
          <div class="contact-item">
            <strong>Call</strong>
            <a href="tel:+919894626300">+91 98946 26300</a>
          </div>
          <div class="contact-item">
            <strong>WhatsApp</strong>
            <a href="https://wa.me/919500601119" target="_blank" rel="noopener">Start WhatsApp consultation</a>
          </div>
          <div class="contact-item">
            <strong>Email</strong>
            <a href="mailto:support@etaxadv.com">support@etaxadv.com</a>
          </div>
          <div class="contact-item">
            <strong>Office</strong>
            No. 234, I Floor, Lawspet Main Road, Pakkamudayanpet, Lawspet, Puducherry - 605008
          </div>
        </div>

        <div class="consult-note">
          <strong>Recommended for</strong>
          <span>GST notices, income tax notices, labour compliance matters, company &amp; LLP compliance, bookkeeping cleanup and representation support.</span>
        </div>
      </div>

      <div class="contact-card consult-form-card">
        <h3>Book a Consultation</h3>
        <p>Complete the form and we will open your preferred email client with a structured consultation draft.</p>

        <form class="js-consult-form">
          <div class="form-grid">
            <div class="field">
              <label for="c_name">Name</label>
              <input class="input" id="c_name" name="name" required />
            </div>
            <div class="field">
              <label for="c_mobile">Mobile</label>
              <input class="input" id="c_mobile" name="mobile" required />
            </div>
            <div class="field">
              <label for="c_email">Email</label>
              <input class="input" id="c_email" name="email" type="email" required />
            </div>
            <div class="field">
              <label for="c_org">Organisation</label>
              <input class="input" id="c_org" name="organisation" />
            </div>
            <div class="field">
              <label for="c_service">Service Required</label>
              <input class="input" id="c_service" name="service" placeholder="Income Tax, GST, TDS, e-Kanakan, company compliance, notices, DSC, etc." required />
            </div>
            <div class="field">
              <label for="c_time">Preferred Consultation Time</label>
              <input class="input" id="c_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" required />
            </div>
            <div class="field full-span">
              <label for="c_msg">Brief Requirement</label>
              <textarea class="input" id="c_msg" name="message" required></textarea>
            </div>
            <div class="field full-span">
              <button class="btn btn-primary" type="submit">Open Consultation Draft</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="grid-3">
        <article class="card card-muted">
          <h3>For new mandates</h3>
          <p>Use consultation booking for business planning, compliance onboarding, notice review and advisory scoping.</p>
        </article>
        <article class="card card-muted">
          <h3>For existing matters</h3>
          <p>Use Client Support to raise service concerns, feedback or escalation requests with ticket tracking.</p>
        </article>
        <article class="card card-muted">
          <h3>For urgent discussions</h3>
          <p>Use call or WhatsApp first, then formalise the matter through consultation or ticketing as required.</p>
        </article>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
