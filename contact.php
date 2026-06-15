<?php
$page_title = "Contact | E Tax Advisors Private Limited";
include __DIR__.'/includes/header.php';
?>

<main id="main-content">
  <section class="hero" style="padding-bottom:36px;">
    <div class="container">
      <div class="grid">
        <div>
          <div class="kicker">Contact and consultation</div>
          <h1>Connect With Us</h1>
          <p class="tagline">For engagements, quotations and consultations</p>
          <p>
            For service-related issues or feedback, please use <a href="client-support.php"><b>Client Support</b></a>
            to raise a ticket for structured resolution.
          </p>
          <div class="actions">
            <a class="btn primary" href="#consult">Book Consultation</a>
            <a class="btn secondary" href="client-support.php">Client Support</a>
          </div>
        </div>

        <div class="heroCard">
          <div class="item">
            <div class="badge">LOC</div>
            <div><b>Office</b><div class="small">Puducherry</div></div>
          </div>
          <div class="item">
            <div class="badge">EML</div>
            <div><b>Email</b><div class="small">support@etaxadv.com</div></div>
          </div>
          <div class="item">
            <div class="badge">CEO</div>
            <div><b>CEO</b><div class="small">98946 26300</div></div>
          </div>
          <div class="item">
            <div class="badge">HRS</div>
            <div><b>Hours</b><div class="small">As per appointment</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="consult">
    <div class="container">
      <div class="grid2">
        <div class="card">
          <h3>Office Address</h3>
          <p class="lead" style="margin-bottom:10px;">
            No. 234, I Floor, Lawspet Main Road,<br/>
            Pakkamudayanpet, Lawspet,<br/>
            Puducherry - 605008
          </p>
          <p><b>Email:</b> <a href="mailto:support@etaxadv.com">support@etaxadv.com</a></p>
          <p><b>CEO:</b> <a href="tel:+919894626300">98946 26300</a></p>
          <p class="note">
            For faster resolution of service-related matters, please use Client Support ticketing.
          </p>
          <a class="btn secondary" href="client-support.php">Open Client Support</a>
        </div>

        <div class="card">
          <h3>Consultation Request</h3>
          <form class="form" onsubmit="return sendConsult(event)">
            <input class="input" id="c_name" placeholder="Name" required />
            <input class="input" id="c_mobile" placeholder="Mobile Number" required />
            <input class="input" id="c_service" placeholder="Service required (Income Tax / GST / TDS / e-Kanakan / etc.)" required />
            <textarea class="input" id="c_msg" placeholder="Brief requirement" required></textarea>
            <button class="btn primary" type="submit">Send Request</button>
          </form>
          <p class="note" style="margin-top:10px;">
            This opens your email client for sending the request. We can connect an integrated PHP form later if needed.
          </p>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
  function sendConsult(e){
    e.preventDefault();
    const n = document.getElementById('c_name').value.trim();
    const m = document.getElementById('c_mobile').value.trim();
    const s = document.getElementById('c_service').value.trim();
    const msg = document.getElementById('c_msg').value.trim();
    const subject = encodeURIComponent("Consultation Request");
    const body = encodeURIComponent("Name: " + n + "\nMobile: " + m + "\nService: " + s + "\n\nRequirement:\n" + msg);
    window.location.href = "mailto:support@etaxadv.com?subject=" + subject + "&body=" + body;
    return false;
  }
</script>

<?php include __DIR__.'/includes/footer.php'; ?>
