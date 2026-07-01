<?php
$page_title = "E Tax Advisors Private Limited — Tax, Legal & Compliance Advisory";
$page_description = "Integrated Tax, Legal, Compliance and Business Advisory services across India. Led by K. Sivasankaran with nearly three decades of experience. Serving 1000+ clients with PAN India support.";
$page_path = '/index.php';

require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">

  <!-- ============================================
       HERO HOME – Premium Advisory Portal
       ============================================ -->
  <section class="hero-home">
    <div class="container">
      <div class="hero-home-shell">
        <div class="hero-home-copy">
          <span class="hero-eyebrow">Trusted by 1000+ Clients Across India</span>
          <h1>Integrated Tax, Legal &amp; Compliance Advisory for Businesses, Professionals and Institutions</h1>
          <p>We assist clients across India with Income Tax, GST, TDS, Corporate Compliance, Labour Law, Trust/NGO Advisory, Litigation Support and business advisory services through a structured, technology-enabled professional delivery model.</p>
          <div class="hero-actions">
            <a class="btn btn-primary btn-lg" href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Book Consultation</a>
            <a class="btn btn-secondary btn-lg" href="<?= htmlspecialchars(site_href('/services.php')) ?>">Explore Services</a>
            <a class="btn btn-outline btn-lg" href="https://wa.me/919500601119" target="_blank" rel="noopener noreferrer">WhatsApp Us</a>
          </div>
        </div>
        <div class="hero-home-panel">
          <div class="hero-stats-grid">
            <div class="hero-stat-card">
              <strong>1000+</strong>
              <span>Clients Served</span>
            </div>
            <div class="hero-stat-card">
              <strong>Nearly 30 Years</strong>
              <span>Professional Experience</span>
            </div>
            <div class="hero-stat-card">
              <strong>PAN India</strong>
              <span>Support Coverage</span>
            </div>
            <div class="hero-stat-card">
              <strong>GST &amp; Income Tax</strong>
              <span>Representation</span>
            </div>
          </div>
          <p class="hero-cin">CIN: U74120PY2015PTC003005</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================
       FOUNDER MESSAGE – Executive Layout
       ============================================ -->
  <section class="founder-message">
    <div class="container">
      <div class="founder-message-shell">
        <div class="founder-profile">
          <div class="founder-profile-photo">
            <img src="/assets/img/ks-sivasankaran.jpg" alt="K. Sivasankaran" />
          </div>
          <p class="founder-profile-name">FCTPr. K. Sivasankaran</p>
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

  <!-- ============================================
       QUICK SERVICE GRID
       ============================================ -->
  <section class="section quick-services-section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Our Services</p>
        <h2 class="section-title">Comprehensive professional services across practice areas</h2>
      </div>
      <div class="quick-services-grid">

        <div class="quick-service-card">
          <div class="quick-service-icon">IT</div>
          <h3>Income Tax Advisory</h3>
          <p>Tax planning, return filing, assessment support and representation</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/income-tax-consultant-puducherry.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">GS</div>
          <h3>GST Advisory &amp; Representation</h3>
          <p>Registration, return filing, notice response and appellate support</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/gst-consultant-puducherry.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">TD</div>
          <h3>TDS &amp; Payroll Compliance</h3>
          <p>TDS return filing, reconciliation, payroll processing and corrections</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/tds-return-filing.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">LL</div>
          <h3>Labour Law &amp; HR Compliance</h3>
          <p>Statutory registrations, returns, audits and HR policy advisory</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/labour-law-hr-compliance.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">RC</div>
          <h3>Corporate / ROC Compliance</h3>
          <p>Company incorporation, annual filings, event-based filings and secretarial support</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/roc-company-compliance.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">TN</div>
          <h3>Trust &amp; NGO Advisory</h3>
          <p>Registration, 12A/80G compliance, FCRA and governance support</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/trust-ngo-advisory.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">LR</div>
          <h3>Litigation &amp; Representation</h3>
          <p>Reply drafting, appellate representation and litigation support</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/litigation-representation.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">PR</div>
          <h3>Project Reports / CMA</h3>
          <p>Project feasibility reports, CMA data preparation and financial modelling</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/project-report-cma.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">AB</div>
          <h3>Accounting &amp; Bookkeeping</h3>
          <p>Statutory books, management accounts and financial statement preparation</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/services.php')) ?>">Learn More &rarr;</a>
        </div>

        <div class="quick-service-card">
          <div class="quick-service-icon">BR</div>
          <h3>Business Registrations</h3>
          <p>Company, LLP, partnership, trust/society, GST, IEC, MSME and trade licences</p>
          <a class="quick-service-link" href="<?= htmlspecialchars(site_href('/services.php')) ?>">Learn More &rarr;</a>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================
       ABOUT TILES – About Us & Why Choose Us
       ============================================ -->
  <section class="section section-alt" id="about-tiles">
    <div class="container about-tiles-grid">
      <div class="about-tile">
        <h3 class="about-tile-title">About E Tax Advisors</h3>
        <p class="about-tile-text">E Tax Advisors Private Limited is a multidisciplinary professional services company providing integrated Tax, Legal, Compliance, Payroll, HR and Technology-enabled solutions to businesses, professionals, trusts, educational institutions and entrepreneurs across India.</p>
        <ul class="about-tile-list">
          <li><span class="about-tile-check">&#10003;</span> Income Tax Advisory</li>
          <li><span class="about-tile-check">&#10003;</span> GST Advisory &amp; Representation</li>
          <li><span class="about-tile-check">&#10003;</span> TDS &amp; Payroll</li>
          <li><span class="about-tile-check">&#10003;</span> Labour Law Compliance</li>
          <li><span class="about-tile-check">&#10003;</span> Corporate Compliance</li>
          <li><span class="about-tile-check">&#10003;</span> Trust &amp; NGO Advisory</li>
          <li><span class="about-tile-check">&#10003;</span> Litigation Support</li>
          <li><span class="about-tile-check">&#10003;</span> Business Advisory</li>
        </ul>
      </div>
      <div class="about-tile">
        <h3 class="about-tile-title">Why Clients Choose Us</h3>
        <ul class="about-tile-list">
          <li><span class="about-tile-check">&#10003;</span> Nearly 30 Years Experience</li>
          <li><span class="about-tile-check">&#10003;</span> Integrated Tax &amp; Legal Expertise</li>
          <li><span class="about-tile-check">&#10003;</span> Technology-enabled Service Delivery</li>
          <li><span class="about-tile-check">&#10003;</span> PAN India Client Support</li>
          <li><span class="about-tile-check">&#10003;</span> Single Point Professional Coordination</li>
          <li><span class="about-tile-check">&#10003;</span> Practical Business-Oriented Solutions</li>
          <li><span class="about-tile-check">&#10003;</span> Trusted by Businesses, Professionals, Trusts and Institutions</li>
          <li><span class="about-tile-check">&#10003;</span> Focused on Compliance, Governance and Growth</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- ============================================
       REGULATORY LINKS
       ============================================ -->
  <section class="section regulatory-section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Important Links</p>
        <h2 class="section-title">Regulatory &amp; Professional Resources</h2>
        <p class="section-intro">Quick access to key regulatory portals and professional bodies for your reference.</p>
      </div>
      <div class="regulatory-grid">
        <a class="regulatory-card" href="https://www.incometax.gov.in/" target="_blank" rel="noopener noreferrer">
          <div class="regulatory-card-icon">IT</div>
          <span>Income Tax Department</span>
        </a>
        <a class="regulatory-card" href="https://www.gst.gov.in/" target="_blank" rel="noopener noreferrer">
          <div class="regulatory-card-icon">GS</div>
          <span>GST Portal</span>
        </a>
        <a class="regulatory-card" href="https://www.mca.gov.in/" target="_blank" rel="noopener noreferrer">
          <div class="regulatory-card-icon">MC</div>
          <span>Ministry of Corporate Affairs</span>
        </a>
        <a class="regulatory-card" href="https://nclt.gov.in/" target="_blank" rel="noopener noreferrer">
          <div class="regulatory-card-icon">NC</div>
          <span>National Company Law Tribunal</span>
        </a>
        <a class="regulatory-card" href="https://www.rbi.org.in/" target="_blank" rel="noopener noreferrer">
          <div class="regulatory-card-icon">RB</div>
          <span>Reserve Bank of India</span>
        </a>
        <a class="regulatory-card" href="https://www.sebi.gov.in/" target="_blank" rel="noopener noreferrer">
          <div class="regulatory-card-icon">SE</div>
          <span>Securities and Exchange Board of India</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ============================================
       CTA BAND
       ============================================ -->
  <section class="cta-band">
    <div class="container">
      <h2>Need Professional Guidance?</h2>
      <p>Whether you require tax filing support, GST or Income Tax representation, statutory compliance management, business advisory or regulatory reply drafting, our team can assist you with a structured and professional approach.</p>
      <div class="cta-contact-links">
        <a class="btn btn-primary btn-lg" href="tel:+919894626300">Call</a>
        <a class="btn btn-primary btn-lg" href="mailto:support@etaxadv.com">Email</a>
        <a class="btn btn-primary btn-lg" href="https://wa.me/919500601119" target="_blank" rel="noopener">WhatsApp</a>
        <a class="btn btn-gold btn-lg" href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Book Consultation</a>
      </div>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
