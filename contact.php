<?php
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Contact & Consultation | E Tax Advisors";
$page_description = "Book a consultation with E Tax Advisors Private Limited for tax, legal, compliance, bookkeeping and representation requirements.";
$page_path = '/contact.php';
$google_review_url = 'https://g.page/r/CXjrmuq5lzjxEAI/review';

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contact_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">

  <section class="founder-message">
    <div class="container">
      <div class="founder-message-shell">
        <div class="founder-profile">
          <div class="founder-profile-photo">
            <img src="/assets/img/ks-sivasankaran.jpg" alt="K. Sivasankaran" />
          </div>
          <p class="founder-profile-name">K. Sivasankaran</p>
          <p class="founder-profile-qual">B.Com., LL.B., C.T.Pr.</p>
          <p class="founder-profile-title">Founder &amp; Principal Advisor</p>
        </div>
        <div class="founder-message-content">
          <p class="founder-message-heading">From the Founder&rsquo;s Desk</p>
          <blockquote>
            &ldquo;Professional advisory is not merely about compliance. It is about helping clients make informed decisions, manage risks proactively, and build systems that support sustainable growth.&rdquo;
          </blockquote>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Consultation Areas</p>
        <h2 class="section-title">What Can We Help You With?</h2>
      </div>
      <div class="areas-grid">
        <div class="area-item"><span class="area-check">&#10003;</span> GST Notices</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Income Tax Notices</div>
        <div class="area-item"><span class="area-check">&#10003;</span> TDS &amp; Payroll Compliance</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Labour Law Compliance</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Corporate Compliance</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Trust &amp; NGO Advisory</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Business Registrations</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Project Reports &amp; CMA</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Representation Matters</div>
        <div class="area-item"><span class="area-check">&#10003;</span> Business Advisory</div>
      </div>
    </div>
  </section>

  <section class="section section-alt" id="consult">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Consultation Request</p>
        <h2 class="section-title">How Can We Assist You?</h2>
        <p class="section-intro">Select the service area and consultation mode that best suits your requirement.</p>
      </div>

      <div class="contact-review-strip">
        <div class="contact-review-copy">
          <p class="google-review-kicker">Google Reviews</p>
          <h3>Want extra confidence before you book?</h3>
          <p>Review our public Google feedback before submitting your consultation request, or leave your own review after working with us.</p>
        </div>
        <div class="contact-review-actions">
          <div class="google-review-stars" aria-label="Five star review platform">★★★★★</div>
          <a class="btn btn-outline btn-lg" href="<?= htmlspecialchars($google_review_url) ?>" target="_blank" rel="noopener noreferrer">Open Google Reviews</a>
          <div class="contact-review-qr">
            <img src="<?= htmlspecialchars(site_href('/assets/img/google-review-qr.png')) ?>" alt="QR code for Google review page" />
            <span>Scan to open the Google review page</span>
          </div>
        </div>
      </div>

      <div class="consult-shell">
        <div class="consult-info">
          <h3>Book a Consultation</h3>
          <p>Complete the form and our team will review your requirement and contact you.</p>

<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

          <form method="post" action="<?= htmlspecialchars(site_href('/contact.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="contact_consult">
            <input type="hidden" name="source_page" value="/contact.php">
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
                <select class="input" id="c_service" name="service" required>
                  <option value="">Select a service</option>
                  <option value="Income Tax Advisory">Income Tax Advisory</option>
                  <option value="GST Advisory & Representation">GST Advisory &amp; Representation</option>
                  <option value="TDS & Payroll Compliance">TDS &amp; Payroll Compliance</option>
                  <option value="Labour Law & HR Compliance">Labour Law &amp; HR Compliance</option>
                  <option value="Corporate Compliance">Corporate Compliance</option>
                  <option value="Trust & NGO Advisory">Trust &amp; NGO Advisory</option>
                  <option value="Litigation & Representation">Litigation &amp; Representation</option>
                  <option value="Project Reports & CMA">Project Reports &amp; CMA</option>
                  <option value="Business Advisory">Business Advisory</option>
                  <option value="e-Pani">e-Pani</option>
                  <option value="e-HR">e-HR</option>
                  <option value="e-Kanakan">e-Kanakan</option>
                  <option value="e-Bal">e-Bal</option>
                  <option value="SalPro">SalPro</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="field">
                <label for="c_mode">Consultation Mode</label>
                <select class="input" id="c_mode" name="consultation_mode" required>
                  <option value="">Select mode</option>
                  <option value="Online Consultation">Online Consultation</option>
                  <option value="Office Consultation">Office Consultation</option>
                </select>
              </div>
              <div class="field">
                <label for="c_date">Preferred Date</label>
                <input class="input" id="c_date" name="preferred_date" type="date" />
              </div>
              <div class="field">
                <label for="c_time">Preferred Time</label>
                <input class="input" id="c_time" name="preferred_time" type="time" />
              </div>
              <div class="field full-span">
                <label for="c_msg">Brief Requirement</label>
                <textarea class="input" id="c_msg" name="message" required></textarea>
              </div>
              <div class="field full-span">
                <button class="btn btn-primary btn-lg" type="submit">Request Consultation</button>
              </div>
            </div>
          </form>
        </div>

        <div class="consult-info">
          <div id="consult-mode-info">
            <div class="consult-mode-card" data-mode="Online Consultation">
              <h3>Online Consultation Process</h3>
              <ol class="consult-steps">
                <li><strong>1.</strong> Submit Consultation Request</li>
                <li><strong>2.</strong> Our Team Reviews the Request</li>
                <li><strong>3.</strong> Consultation Time is Confirmed</li>
                <li><strong>4.</strong> Meeting Link is Shared</li>
                <li><strong>5.</strong> Consultation Conducted through Google Meet or Zoom</li>
              </ol>
            </div>
            <div class="consult-mode-card" data-mode="Office Consultation">
              <h3>Office Consultation Process</h3>
              <ol class="consult-steps">
                <li><strong>1.</strong> Submit Appointment Request</li>
                <li><strong>2.</strong> Time Slot Confirmation</li>
                <li><strong>3.</strong> Office Visit as Scheduled</li>
                <li><strong>4.</strong> Consultation Conducted at E Tax Advisors Office</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Direct Contact</p>
        <h2 class="section-title">Reach Us Directly</h2>
      </div>
      <div class="direct-grid">
        <div class="direct-card">
          <div class="direct-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
          </div>
          <h3>Call Us</h3>
          <p>Speak directly with our team for urgent matters or quick inquiries.</p>
          <a class="direct-link" href="tel:+919894626300">+91 98946 26300</a>
        </div>
        <div class="direct-card">
          <div class="direct-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <h3>WhatsApp Consultation</h3>
          <p>Quick messaging for initial inquiries and document sharing.</p>
          <a class="direct-link" href="https://wa.me/919500601119" target="_blank" rel="noopener">Start WhatsApp Conversation</a>
        </div>
        <div class="direct-card">
          <div class="direct-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
              <polyline points="22,6 12,13 2,6"/>
            </svg>
          </div>
          <h3>Email Us</h3>
          <p>Send a detailed message and we will respond within one business day.</p>
          <a class="direct-link" href="mailto:support@etaxadv.com">support@etaxadv.com</a>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-alt">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Office Location</p>
        <h2 class="section-title">Visit Our Office</h2>
      </div>
      <div class="office-shell">
        <div class="office-map">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3917.345!2d79.808926!3d11.945321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTHCsDU2JzQzLjIiTiA3OcKwNDgnMzIuMSJF!5e0!3m2!1sen!2sin!4v1" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="office-info">
          <h3>E Tax Advisors Private Limited</h3>
          <p>
            No. 234, I Floor,<br/>
            Lawspet Main Road,<br/>
            Pakkamudayanpet,<br/>
            Lawspet,<br/>
            Puducherry – 605008
          </p>
          <a class="btn btn-primary" href="https://maps.google.com/?q=11.945321,79.808926" target="_blank" rel="noopener">Get Directions</a>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <h2>Need Professional Guidance?</h2>
      <p>Whether you require assistance with taxation, compliance, labour law, representation or business advisory matters, our team is ready to assist.</p>
      <div class="cta-contact-links">
        <a class="btn btn-gold btn-lg" href="#consult">Book Consultation</a>
        <a class="btn btn-primary btn-lg" href="https://wa.me/919500601119" target="_blank" rel="noopener">WhatsApp Consultation</a>
      </div>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
