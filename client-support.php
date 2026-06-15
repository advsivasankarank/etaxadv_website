<?php
$page_title = "Client Support | E Tax Advisors Private Limited";
include __DIR__.'/includes/header.php';
?>

<main id="main-content">
  <section class="hero" style="padding-bottom:36px;">
    <div class="container">
      <div class="grid">
        <div>
          <div class="kicker">Client support and escalation</div>
          <h1>Client Support & Escalation Matrix</h1>
          <p class="tagline">Structured ticketing for service concerns, feedback and suggestions</p>
          <p>
            Please raise a ticket for service concerns, feedback or suggestions. Tickets ensure documented tracking
            and timely resolution. You may track status using your Ticket ID and registered mobile number.
          </p>
          <div class="actions">
            <a class="btn primary" href="#ticket">Raise Ticket</a>
            <a class="btn secondary" href="<?= htmlspecialchars(site_href('/support/status.php')) ?>">Track Ticket</a>
          </div>
        </div>

        <div class="heroCard">
          <div class="item">
            <div class="badge">24h</div>
            <div><b>Level 1</b><div class="small">Response commitment within 24 working hours</div></div>
          </div>
          <div class="item">
            <div class="badge">48h</div>
            <div><b>Level 2</b><div class="small">Escalation review within 48 working hours</div></div>
          </div>
          <div class="item">
            <div class="badge">ID</div>
            <div><b>Ticket ID</b><div class="small">Generated upon submission</div></div>
          </div>
          <div class="item">
            <div class="badge">SEC</div>
            <div><b>Confidential</b><div class="small">Handled with confidentiality</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="grid2">
        <div class="card">
          <h3>Functional Contacts</h3>
          <p><strong>Client Relations Manager</strong><br/><a href="tel:+919500601119">9500601119</a></p>
          <p><strong>Back Office</strong><br/><a href="tel:+919500727863">9500727863</a></p>
          <p><strong>Accounts Manager</strong><br/><a href="tel:+919092026018">9092026018</a></p>
          <p><strong>Managing Director (MD)</strong><br/><a href="tel:+919500601188">9500601188</a></p>
          <p><strong>Chief Executive Officer (CEO)</strong><br/><a href="tel:+919894626300">98946 26300</a></p>
        </div>

        <div class="card">
          <h3>Escalation Matrix</h3>
          <p><strong>Level 1 - Accounts Manager</strong><br/><a href="tel:+919092026018">9092026018</a></p>
          <p><strong>Level 2 - Managing Director</strong><br/><a href="tel:+919500601188">9500601188</a></p>
          <p class="note" style="margin-top:12px;">
            Clients are requested to follow the above escalation hierarchy for structured resolution.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="ticket" style="padding-top:0;">
    <div class="container">
      <div class="card">
        <h3>Raise a Ticket (Concern / Feedback / Suggestion)</h3>
        <form class="form" method="post" action="<?= htmlspecialchars(site_href('/support/submit.php')) ?>">
          <select class="input" name="category" required>
            <option value="">Select Category</option>
            <option>Service Concern</option>
            <option>Feedback</option>
            <option>Suggestion</option>
            <option>Escalation Request</option>
          </select>

          <select class="input" name="priority" required>
            <option>Normal</option>
            <option>High</option>
            <option>Urgent</option>
          </select>

          <input class="input" name="name" placeholder="Client Name (optional)" />
          <input class="input" name="mobile" placeholder="Registered Mobile Number" required />
          <input class="input" name="email" placeholder="Email (optional)" />
          <input class="input" name="subject" placeholder="Subject" required />
          <textarea class="input" name="message" placeholder="Describe the issue, feedback or suggestion" required></textarea>

          <button class="btn primary" type="submit">Submit Ticket</button>

          <div class="note">
            After submission, you will receive a Ticket ID. Track at: <b><?= htmlspecialchars(site_href('/support/status.php')) ?></b>
          </div>
        </form>

        <div class="actions" style="margin-top:14px;">
          <a class="btn secondary" href="<?= htmlspecialchars(site_href('/support/status.php')) ?>">Track Ticket Status</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__.'/includes/footer.php'; ?>
