<?php
$page_title = "Home | E Tax Advisors Private Limited";
include __DIR__.'/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container">
      <div class="grid">
        <div>
          <div class="kicker">Built for <b>corporate-grade</b> tax and compliance delivery</div>
          <h1>Integrated Tax, Compliance, Financial Advisory Solutions and Litigation Under One Roof</h1>
          <p class="tagline">Tax &middot; Compliance &middot; Finance &middot; Litigation under one roof</p>
          <p>
            We provide structured professional services across Income Tax, GST, TDS & payroll compliance,
            corporate and entity compliance, bookkeeping through <b>e-Kanakan</b>, project reports and CMA,
            and representation support for notices, assessments and appeals.
          </p>

          <div class="actions">
            <a class="btn primary" href="services.php">Explore Services</a>
            <a class="btn secondary" href="tools.php">Use Tools</a>
            <a class="btn secondary" href="client-support.php">Client Support</a>
          </div>

          <p class="small" style="margin-top:10px;">
            Service delivery is engagement-based and subject to documents furnished and applicable law.
          </p>
        </div>

        <div class="heroCard" aria-label="Service strengths">
          <div class="item">
            <div class="badge">&#10003;</div>
            <div><b>Process Discipline</b><div class="small">Checklist-driven execution and validation</div></div>
          </div>
          <div class="item">
            <div class="badge">SLA</div>
            <div><b>Due-date Control</b><div class="small">Timelines, reminders and closure tracking</div></div>
          </div>
          <div class="item">
            <div class="badge">SEC</div>
            <div><b>Confidential Handling</b><div class="small">Secure document handling practices</div></div>
          </div>
          <div class="item">
            <div class="badge">LIT</div>
            <div><b>Representation Support</b><div class="small">Replies, submissions and hearing assistance</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <h2>Service Lines</h2>
      <p class="lead">Quick access to our core service offerings.</p>

      <div class="tiles">
        <div class="tile">
          <div class="title">Income Tax</div>
          <div class="desc">ITR filing, notices, scrutiny support, appeals and advisory.</div>
          <a class="link" href="services.php#income-tax">View details &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">GST</div>
          <div class="desc">Registration, returns, reconciliations, notices and assessments.</div>
          <a class="link" href="services.php#gst">View details &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">TDS & Payroll</div>
          <div class="desc">TDS returns, TRACES, Form 16/16A support and payroll compliance.</div>
          <a class="link" href="services.php#tds">View details &rarr;</a>
        </div>

        <div class="tile emph">
          <div class="title">Accounting - e-Kanakan</div>
          <div class="desc">Structured Accounting. Powered by Tally. Bookkeeping and MIS discipline.</div>
          <a class="link" href="ekanakan.php">Go to e-Kanakan &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">Company / LLP Compliances</div>
          <div class="desc">Incorporation support, ROC filings and annual compliance.</div>
          <a class="link" href="services.php#company">View details &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">Trust / Society</div>
          <div class="desc">Registration and compliance support for trusts and societies.</div>
          <a class="link" href="services.php#trust-society">View details &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">Project Reports & CMA</div>
          <div class="desc">Bank project reports, CMA statements and financial documentation.</div>
          <a class="link" href="services.php#project-cma">View details &rarr;</a>
        </div>

        <div class="tile">
          <div class="title">Digital Signature (DSC)</div>
          <div class="desc">DSC assistance and documentation guidance. API integration planned.</div>
          <a class="link" href="digital-signature.php">View details &rarr;</a>
        </div>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0;">
    <div class="container">
      <h2>How We Work</h2>
      <p class="lead">A structured delivery model to ensure traceability and timely compliance.</p>

      <div class="process">
        <div class="step"><b>1. Onboarding</b><span>Scope, checklist and timelines</span></div>
        <div class="step"><b>2. Documents</b><span>Collection and validation</span></div>
        <div class="step"><b>3. Review</b><span>Internal checks and exceptions</span></div>
        <div class="step"><b>4. Filing</b><span>Submission and acknowledgements</span></div>
        <div class="step"><b>5. Support</b><span>Post-filing follow-up</span></div>
      </div>

      <div class="card" style="margin-top:18px;">
        <h3>Need quick assistance?</h3>
        <p class="lead" style="margin-bottom:12px;">
          Use our Tools page or raise a service ticket through Client Support for structured resolution.
        </p>
        <div class="actions">
          <a class="btn primary" href="tools.php">Open Tools</a>
          <a class="btn secondary" href="client-support.php">Raise Ticket</a>
          <a class="btn secondary" href="contact.php#consult">Book Consultation</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__.'/includes/footer.php'; ?>
