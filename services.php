<?php
$page_title = "Services | E Tax Advisors Private Limited";
include __DIR__.'/includes/header.php';
?>

<main id="main-content">
  <section class="hero" style="padding-bottom:36px;">
    <div class="container">
      <div class="grid">
        <div>
          <div class="kicker">Services with structured delivery</div>
          <h1>Our Services</h1>
          <p class="tagline">Integrated professional support across Tax, Compliance, Finance and Litigation</p>
          <p>
            We deliver services under a disciplined workflow: scope to documents to review to filing or submission
            to post-support. For service concerns, please use Client Support for ticket-based resolution.
          </p>
          <div class="actions">
            <a class="btn primary" href="contact.php#consult">Request Engagement</a>
            <a class="btn secondary" href="client-support.php">Client Support</a>
          </div>
        </div>

        <div class="heroCard">
          <div class="item">
            <div class="badge">IT</div>
            <div><b>Income Tax</b><div class="small">Returns, notices and appeals</div></div>
          </div>
          <div class="item">
            <div class="badge">GST</div>
            <div><b>GST</b><div class="small">Returns, notices and assessments</div></div>
          </div>
          <div class="item">
            <div class="badge">TDS</div>
            <div><b>TDS</b><div class="small">Returns, TRACES and Form 16</div></div>
          </div>
          <div class="item">
            <div class="badge">ROC</div>
            <div><b>Company / LLP</b><div class="small">Incorporation and annual compliance</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="grid2">
        <div class="card" id="income-tax">
          <h3>Income Tax</h3>
          <ul class="list">
            <li>ITR filing for individuals, firms and companies</li>
            <li>Notice replies and scrutiny support</li>
            <li>Tax planning and advisory on engagement basis</li>
            <li>Appeals and representation support</li>
          </ul>
        </div>

        <div class="card" id="gst">
          <h3>GST</h3>
          <ul class="list">
            <li>GST registration and amendments</li>
            <li>GSTR-1, GSTR-3B and annual return support</li>
            <li>Reconciliations between GSTR-2B and books</li>
            <li>Notices, assessments, refunds and appeals assistance</li>
          </ul>
        </div>

        <div class="card" id="tds">
          <h3>TDS & Payroll</h3>
          <ul class="list">
            <li>TDS return filing and corrections</li>
            <li>TRACES support including Form 16 and 16A downloads</li>
            <li>Payroll compliance support</li>
            <li>General advisory for deductors</li>
          </ul>
        </div>

        <div class="card">
          <h3>Accounting - e-Kanakan</h3>
          <p class="lead" style="margin-bottom:10px;"><b>Structured Accounting. Powered by Tally.</b></p>
          <ul class="list">
            <li>Bookkeeping and ledger hygiene</li>
            <li>Bank, GST and TDS reconciliations</li>
            <li>MIS summaries and periodic reviews</li>
          </ul>
          <a class="btn primary" href="ekanakan.php">Go to e-Kanakan</a>
        </div>

        <div class="card" id="company">
          <h3>Company / LLP Compliances</h3>
          <ul class="list">
            <li>Incorporation and onboarding support</li>
            <li>ROC filings and annual compliance</li>
            <li>Event-based filings as applicable</li>
          </ul>
        </div>

        <div class="card" id="trust-society">
          <h3>Trust / Society Registration & Compliance</h3>
          <ul class="list">
            <li>Registration guidance and documentation</li>
            <li>Ongoing compliance and reporting support</li>
            <li>Governance and statutory filings support</li>
          </ul>
        </div>

        <div class="card" id="project-cma">
          <h3>Bank Project Reports & CMA Preparation</h3>
          <ul class="list">
            <li>Project reports for bank funding</li>
            <li>CMA statements and financial projections</li>
            <li>Documentation and presentation support</li>
          </ul>
        </div>

        <div class="card" id="litigation">
          <h3>Litigation / Representation Support</h3>
          <ul class="list">
            <li>Replies, objections and written submissions</li>
            <li>Hearing preparation and documentation</li>
            <li>Appeal documentation assistance as applicable</li>
          </ul>
        </div>

        <div class="card" id="dsc">
          <h3>Digital Signature (DSC)</h3>
          <ul class="list">
            <li>DSC assistance and documentation guidance</li>
            <li>Issuance flow and support</li>
            <li>API integration planned as Phase 2</li>
          </ul>
          <a class="btn secondary" href="digital-signature.php">View DSC Page</a>
        </div>

        <div class="card">
          <h3>Need an assisted quote?</h3>
          <p class="lead" style="margin-bottom:12px;">
            For corporate engagements and ongoing compliance, please book a consultation.
          </p>
          <div class="actions">
            <a class="btn primary" href="contact.php#consult">Book Consultation</a>
            <a class="btn secondary" href="client-support.php">Client Support</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__.'/includes/footer.php'; ?>
