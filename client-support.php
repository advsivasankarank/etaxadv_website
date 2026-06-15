<?php
$page_title = "Client Support & Escalation | E Tax Advisors";
$page_description = "Raise service concerns, feedback, suggestions and escalation requests through the structured support and ticketing desk of E Tax Advisors.";
$page_path = '/client-support.php';
include __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Client Support</div>
        <h1>Structured support, documented escalation and accountable follow-through.</h1>
        <p>
          Use the support desk for existing service concerns, feedback, suggestions and escalation requests.
          Tickets help preserve context, priority and accountability across the response cycle.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#ticket">Raise Ticket</a>
          <a class="btn btn-outline" href="/support/status.php">Track Existing Ticket</a>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-grid">
          <div class="hero-metric">
            <strong>24 hrs</strong>
            <span>level 1 working-hour response commitment</span>
          </div>
          <div class="hero-metric">
            <strong>48 hrs</strong>
            <span>level 2 escalation review commitment</span>
          </div>
          <div class="hero-metric">
            <strong>Ticket ID</strong>
            <span>documented tracking after submission</span>
          </div>
          <div class="hero-metric">
            <strong>Confidential</strong>
            <span>client records handled with controlled access discipline</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container grid-2">
      <article class="card">
        <h3>Functional contact points</h3>
        <ul class="list-clean">
          <li>Client Relations Manager - <a href="tel:+919500601119">9500601119</a></li>
          <li>Back Office - <a href="tel:+919500727863">9500727863</a></li>
          <li>Accounts Manager - <a href="tel:+919092026018">9092026018</a></li>
          <li>Managing Director - <a href="tel:+919500601188">9500601188</a></li>
          <li>Chief Executive Officer - <a href="tel:+919894626300">9894626300</a></li>
        </ul>
      </article>
      <article class="card">
        <h3>Escalation framework</h3>
        <ul class="list-clean">
          <li>Level 1 - Accounts Manager review path</li>
          <li>Level 2 - Managing Director escalation review</li>
          <li>Track through your Ticket ID and registered mobile number</li>
        </ul>
      </article>
    </div>
  </section>

  <section class="section section-muted" id="ticket">
    <div class="container contact-card">
      <h3>Raise a Ticket</h3>
      <p>Use this form for service concerns, feedback, suggestions or escalation requests.</p>

      <form method="post" action="/support/submit.php">
        <div class="form-grid">
          <div class="field">
            <label for="category">Category</label>
            <select class="input" id="category" name="category" required>
              <option value="">Select Category</option>
              <option>Service Concern</option>
              <option>Feedback</option>
              <option>Suggestion</option>
              <option>Escalation Request</option>
            </select>
          </div>
          <div class="field">
            <label for="priority">Priority</label>
            <select class="input" id="priority" name="priority" required>
              <option>Normal</option>
              <option>High</option>
              <option>Urgent</option>
            </select>
          </div>
          <div class="field">
            <label for="name">Client Name</label>
            <input class="input" id="name" name="name" />
          </div>
          <div class="field">
            <label for="mobile">Registered Mobile Number</label>
            <input class="input" id="mobile" name="mobile" required />
          </div>
          <div class="field full-span">
            <label for="email">Email Address</label>
            <input class="input" id="email" name="email" />
          </div>
          <div class="field full-span">
            <label for="subject">Subject</label>
            <input class="input" id="subject" name="subject" required />
          </div>
          <div class="field full-span">
            <label for="message">Describe the issue or feedback</label>
            <textarea class="input" id="message" name="message" required></textarea>
          </div>
          <div class="field full-span">
            <button class="btn btn-primary" type="submit">Submit Ticket</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
