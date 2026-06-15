<footer class="footer">
  <div class="container">
    <div class="cols">
      <div>
        <h3>E Tax Advisors Private Limited</h3>
        <div class="muted">
          No. 234, Lawspet Main Road,<br/>
          Puducherry - 605008<br/>
          Email: <a href="mailto:support@etaxadv.com">support@etaxadv.com</a><br/>
          CEO: <a href="tel:+919894626300">98946 26300</a>
        </div>
      </div>

      <div>
        <h3>Quick Links</h3>
        <div class="muted">
          <a href="<?= htmlspecialchars(site_href('/services.php')) ?>">Services</a><br/>
          <a href="<?= htmlspecialchars(site_href('/tools.php')) ?>">Tools</a><br/>
          <a href="<?= htmlspecialchars(site_href('/client-support.php')) ?>">Client Support</a><br/><br/>

          <strong>Staff Access</strong><br/>
          <a href="<?= htmlspecialchars(site_href('/support/agent/login.php')) ?>">Back Office Login</a><br/>
          <a href="<?= htmlspecialchars(site_href('/support/admin/login.php')) ?>">Admin Login</a>
        </div>
      </div>

      <div>
        <h3>Disclaimer</h3>
        <div class="muted">
          Services are engagement-based and subject to documents furnished and applicable law.
        </div>
      </div>
    </div>

    <div class="bottom">
      &copy; <?= date("Y") ?> E Tax Advisors Private Limited
    </div>
  </div>
</footer>

<a class="wa-float" href="https://wa.me/919500601119" target="_blank" rel="noopener">
  <span class="wa-dot"></span> WhatsApp
</a>

<script src="<?= htmlspecialchars(site_href('/assets/js/main.js')) ?>"></script>
</body>
</html>
