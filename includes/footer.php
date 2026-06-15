<footer class="footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <img class="footer-logo" src="<?= htmlspecialchars(site_href('/assets/img/logo.png')) ?>" alt="E Tax Advisors footer logo" />
      <h2>E Tax Advisors Private Limited</h2>
      <p>
        Premium tax, legal, compliance and bookkeeping advisory support for founders,
        businesses, promoters, trustees and professional entities.
      </p>
      <div class="footer-contact-stack">
        <a href="tel:+919894626300">Call: +91 98946 26300</a>
        <a href="mailto:support@etaxadv.com">support@etaxadv.com</a>
        <a href="https://wa.me/919500601119" target="_blank" rel="noopener">WhatsApp Consultation</a>
      </div>
    </div>

    <div>
      <h3>Core Services</h3>
      <ul class="footer-links">
        <li><a href="<?= htmlspecialchars(site_href('/services.php#income-tax')) ?>">Income Tax Advisory</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/services.php#gst')) ?>">GST Compliance</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/services.php#tds')) ?>">TDS & Payroll</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/services.php#company')) ?>">Company / LLP Compliance</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/ekanakan.php')) ?>">e-Kanakan Bookkeeping</a></li>
      </ul>
    </div>

    <div>
      <h3>Client Actions</h3>
      <ul class="footer-links">
        <li><a href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Book Consultation</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/client-support.php')) ?>">Raise Support Ticket</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/testimonial/')) ?>">Client Testimonials</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/tools.php')) ?>">Client Tools</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/digital-signature.php')) ?>">Digital Signature Support</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/e-task.php')) ?>">e-Task Preview</a></li>
      </ul>
    </div>

    <div>
      <h3>Legal & Office</h3>
      <ul class="footer-links">
        <li><a href="<?= htmlspecialchars(site_href('/about.php')) ?>">About the Firm</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/privacy-policy.php')) ?>">Privacy Policy</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/disclaimer.php')) ?>">Website Disclaimer</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Office Address</a></li>
      </ul>
      <p class="footer-address">
        No. 234, I Floor, Lawspet Main Road,<br/>
        Pakkamudayanpet, Lawspet,<br/>
        Puducherry - 605008
      </p>
    </div>
  </div>

  <div class="container footer-bottom">
    <p>&copy; <?= date("Y") ?> E Tax Advisors Private Limited. All rights reserved.</p>
    <p>Engagement-based services. Scope, deliverables and timelines remain subject to onboarding and supporting records.</p>
  </div>
</footer>

<div class="mobile-action-bar">
  <a href="tel:+919894626300">Call</a>
  <a href="https://wa.me/919500601119" target="_blank" rel="noopener">WhatsApp</a>
  <a href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Consult</a>
</div>

<!-- Sticky WhatsApp button -->
<a href="https://wa.me/919500601119" target="_blank" rel="noopener" class="whatsapp-float" aria-label="Chat on WhatsApp">
  <svg viewBox="0 0 32 32" fill="white" width="28" height="28"><path d="M16 2C8.2 2 2 8.2 2 16c0 3.1.9 6 2.5 8.5L2 30l5.8-2.3C10.3 29.2 13 30 16 30c7.8 0 14-6.2 14-14S23.8 2 16 2zm6.5 19.8c-.4 1-1.5 1.8-2.5 2-1 .2-2 .2-3.2-.6-1.7-.8-3.2-2.4-4.4-3.8-1.2-1.4-2-3-2.3-4.5-.2-1.2.1-2.2.6-2.8.4-.6 1-.8 1.3-.8h.8c.3 0 .5 0 .8.6.3.6 1 2.2 1 2.4s0 .4-.2.6c-.2.2-.4.5-.6.7-.2.2-.4.4-.2.8.2.4 1 1.7 2 2.6 1.2 1.2 2.2 1.6 2.6 1.8.4.2.6.2.8 0 .2-.2.8-.8 1-1.2.2-.4.4-.4.6-.3.2.2 1.4.7 1.6.8.2.2.4.2.5.3 0 .4 0 1-.2 1.4z"/></svg>
</a>

<!-- Floating consultation button -->
<a href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>" class="float-consult" aria-label="Book consultation">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
  Book Consultation
</a>

<!-- Exit Intent Popup -->
<div class="popup-overlay" id="exitPopup">
  <div class="popup-modal">
    <button class="popup-close" type="button" aria-label="Close">&times;</button>
    <h3>Before you go...</h3>
    <p>Have a tax or compliance question? Our team is ready to help.</p>
    <div class="popup-actions">
      <a class="btn btn-primary" href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Book Free Consultation</a>
      <a class="btn btn-outline" href="https://wa.me/919500601119" target="_blank" rel="noopener">Chat on WhatsApp</a>
    </div>
    <p class="popup-note">Or call us directly: <a href="tel:+919894626300"><strong>+91 98946 26300</strong></a></p>
  </div>
</div>

<script src="<?= htmlspecialchars(app_href('/assets/js/main.js')) ?>"></script>
</body>
</html>
