<footer class="footer">
  <div class="container footer-grid">

    <!-- COLUMN 1: Company -->
    <div class="footer-brand">
      <p class="footer-company-name">E Tax Advisors Private Limited</p>
      <p class="footer-cin">CIN: U74120PY2015PTC003005</p>
      <p class="footer-tagline">Integrated Tax, Legal &amp; Compliance Solutions</p>
      <h3 class="footer-office-heading">Office Access</h3>
      <ul class="footer-links footer-office-links">
        <li><a href="<?= htmlspecialchars(site_href('/admin/enquiries.php')) ?>">Appointment Management</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/admin/login.php')) ?>">BO Login</a></li>
        <li><a href="<?= htmlspecialchars(site_href('/admin/login.php')) ?>">Admin Login</a></li>
      </ul>
    </div>

    <!-- COLUMN 2: Useful Links -->
    <div>
      <h3>Useful Links</h3>
      <ul class="footer-links">
        <li><a href="https://www.incometax.gov.in/" target="_blank" rel="noopener noreferrer">Income Tax Dept.</a></li>
        <li><a href="https://www.gst.gov.in/" target="_blank" rel="noopener noreferrer">GST Portal</a></li>
        <li><a href="https://nclt.gov.in/" target="_blank" rel="noopener noreferrer">National Company Law Tribunal</a></li>
        <li><a href="https://nclat.nic.in/" target="_blank" rel="noopener noreferrer">National Company Law Appellate Tribunal</a></li>
        <li><a href="https://www.mca.gov.in/" target="_blank" rel="noopener noreferrer">Ministry of Corporate Affairs</a></li>
        <li><a href="https://www.sebi.gov.in/" target="_blank" rel="noopener noreferrer">Securities and Exchange Board of India</a></li>
        <li><a href="https://www.rbi.org.in/" target="_blank" rel="noopener noreferrer">Reserve Bank of India</a></li>
        <li><a href="https://ibbi.gov.in/" target="_blank" rel="noopener noreferrer">Insolvency and Bankruptcy Board of India</a></li>
      </ul>
    </div>

    <!-- COLUMN 3: Connect with Us -->
    <div>
      <h3>Connect with Us</h3>
      <ul class="footer-links">
        <!-- TODO: Replace # with actual social media URLs -->
        <li><a href="#" target="_blank" rel="noopener noreferrer">Facebook</a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer">Instagram</a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer">Telegram</a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer">Twitter / X</a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer">YouTube</a></li>
        <li><a href="#" target="_blank" rel="noopener noreferrer">Google Group</a></li>
      </ul>
    </div>

    <!-- COLUMN 4: Contact Us -->
    <div>
      <h3>Contact Us</h3>
      <p class="footer-address">No. 234, I Floor, Lawspet Main Road, Pakkamudayanpet, Lawspet, Puducherry &#8211; 605008</p>
      <div class="footer-contact-stack">
        <span>Phone: <a href="tel:+919500601119">+91-9500601119</a></span>
        <span>Founder: <a href="tel:+919894626300">+91-9894626300</a></span>
      </div>
      <p class="footer-email" style="margin-top:6px;"><a href="mailto:support@etaxadv.com">support@etaxadv.com</a></p>
      <p style="font-size:11px;color:var(--gray-400);margin-top:4px;">We feel happy to talk</p>
      <a class="footer-message-btn" href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Write Your Message</a>
    </div>

  </div>

  <div class="container footer-bottom">
    <p>&copy; 2026 E Tax Advisors Private Limited. All Rights Reserved. &nbsp;|&nbsp; CIN: U74120PY2015PTC003005</p>
    <div class="footer-bottom-links">
      <a href="<?= htmlspecialchars(site_href('/privacy-policy.php')) ?>">Privacy Policy</a>
      <a href="<?= htmlspecialchars(site_href('/disclaimer.php')) ?>">Disclaimer</a>
      <a href="<?= htmlspecialchars(site_href('/disclaimer.php')) ?>">Terms of Use</a>
    </div>
  </div>
</footer>

<div class="mobile-action-bar">
  <a href="tel:+919894626300">Call</a>
  <a href="https://wa.me/919500601119" target="_blank" rel="noopener">WhatsApp</a>
  <a href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Consult</a>
</div>

<a href="https://wa.me/919500601119" target="_blank" rel="noopener" class="whatsapp-float" aria-label="Chat on WhatsApp">
  <svg viewBox="0 0 32 32" fill="white" width="28" height="28"><path d="M16 2C8.2 2 2 8.2 2 16c0 3.1.9 6 2.5 8.5L2 30l5.8-2.3C10.3 29.2 13 30 16 30c7.8 0 14-6.2 14-14S23.8 2 16 2zm6.5 19.8c-.4 1-1.5 1.8-2.5 2-1 .2-2 .2-3.2-.6-1.7-.8-3.2-2.4-4.4-3.8-1.2-1.4-2-3-2.3-4.5-.2-1.2.1-2.2.6-2.8.4-.6 1-.8 1.3-.8h.8c.3 0 .5 0 .8.6.3.6 1 2.2 1 2.4s0 .4-.2.6c-.2.2-.4.5-.6.7-.2.2-.4.4-.2.8.2.4 1 1.7 2 2.6 1.2 1.2 2.2 1.6 2.6 1.8.4.2.6.2.8 0 .2-.2.8-.8 1-1.2.2-.4.4-.4.6-.3.2.2 1.4.7 1.6.8.2.2.4.2.5.3 0 .4 0 1-.2 1.4z"/></svg>
</a>

<script src="<?= htmlspecialchars(app_href('/assets/js/main.js')) ?>"></script>
</body>
</html>
