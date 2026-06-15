<?php
$page_title = "K. Sivasankaran | Founder & Principal Advisor | E Tax Advisors Private Limited";
$page_description = "Profile of K. Sivasankaran — Advocate, Income Tax Practitioner, GST Practitioner and Founder of E Tax Advisors Private Limited with nearly three decades of experience.";
$page_path = '/team/ks-sivasankaran.php';
require_once __DIR__ . '/../includes/header.php';
?>
<style>
.profile-hero-img {
  width: 100%; height: 100%; object-fit: cover;
  border-radius: var(--radius-lg);
}
.profile-hero .hero-image {
  aspect-ratio: 1/1; max-width: 500px; margin-left: auto;
}
.hero-titles { margin: 4px 0 16px; }
.hero-titles h1 {
  margin: 0 0 8px; font-family: var(--font-display);
  font-size: clamp(32px, 4.5vw, 48px); line-height: 1.05; letter-spacing: -.02em;
  font-weight: 700; color: var(--navy);
}
.hero-titles .title-role {
  font-family: var(--font-display); font-size: 18px; font-weight: 600;
  color: var(--gold); margin: 0 0 4px;
}
.hero-titles .title-credentials {
  display: flex; flex-wrap: wrap; gap: 6px; margin: 8px 0 16px;
}
.hero-titles .title-credentials span {
  padding: 4px 12px; border-radius: 999px; background: var(--navy-50);
  color: var(--navy); font-size: 12px; font-weight: 600;
}
.hero-titles .title-summary {
  color: var(--gray-600); font-size: 16px; line-height: 1.7; margin: 0 0 24px;
}
.profile-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.profile-actions .btn { min-height: 48px; padding: 0 24px; }
.timeline {
  position: relative; padding: 20px 0; margin: 0 auto; max-width: 1000px;
}
.timeline::before {
  content: ""; position: absolute; left: 50%; top: 0; bottom: 0;
  width: 2px; background: var(--gray-200); transform: translateX(-50%);
}
.timeline-item {
  position: relative; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;
  padding-bottom: 48px;
}
.timeline-item:last-child { padding-bottom: 0; }
.timeline-item:nth-child(odd) .timeline-content { grid-column: 1; }
.timeline-item:nth-child(odd) .timeline-date { grid-column: 2; text-align: left; }
.timeline-item:nth-child(even) .timeline-date { grid-column: 1; text-align: right; }
.timeline-item:nth-child(even) .timeline-content { grid-column: 2; }
.timeline-dot {
  position: absolute; left: 50%; top: 8px;
  width: 16px; height: 16px; border-radius: 50%;
  background: var(--gold); border: 3px solid var(--white);
  box-shadow: 0 0 0 2px var(--gold);
  transform: translateX(-50%); z-index: 1;
}
.timeline-date h3 {
  font-family: var(--font-display); font-size: 28px; font-weight: 700;
  color: var(--gold); margin: 0 0 4px; line-height: 1.1;
}
.timeline-date p {
  color: var(--gray-600); font-size: 14px; margin: 0;
}
.timeline-content { padding-top: 4px; }
.timeline-content h4 {
  font-family: var(--font-display); font-size: 16px; font-weight: 700;
  color: var(--navy); margin: 0 0 8px;
}
.timeline-content p {
  color: var(--gray-600); font-size: 14px; line-height: 1.7; margin: 0;
}
.expertise-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
.expertise-card {
  padding: 20px; border: 1px solid var(--gray-100); border-radius: var(--radius-sm);
  background: var(--white); font-size: 14px; font-weight: 600; color: var(--navy);
  display: flex; align-items: center; gap: 10px; transition: box-shadow .2s;
}
.expertise-card:hover { box-shadow: var(--shadow-xs); }
.expertise-card::before {
  content: ""; width: 6px; height: 6px; border-radius: 50%;
  background: var(--gold); flex-shrink: 0;
}
.credentials-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
.credential-item {
  padding: 20px; border: 1px solid var(--gray-100); border-radius: var(--radius-sm);
  background: var(--white); text-align: center; transition: box-shadow .2s;
}
.credential-item:hover { box-shadow: var(--shadow-xs); }
.credential-item strong {
  display: block; font-family: var(--font-display); font-size: 15px;
  font-weight: 700; color: var(--navy); margin-bottom: 4px;
}
.credential-item span {
  color: var(--gray-600); font-size: 12px; line-height: 1.5; display: block;
}
.philosophy-block {
  max-width: 800px; margin: 0 auto; padding: 48px;
  border: 1px solid var(--gray-100); border-radius: var(--radius-lg);
  background: var(--white); box-shadow: var(--shadow-md);
  text-align: center;
}
.philosophy-block blockquote {
  margin: 0; font-family: var(--font-display);
  font-size: clamp(20px, 2.5vw, 28px); line-height: 1.4;
  font-weight: 600; color: var(--navy); font-style: italic;
}
.consult-reasons-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
}
.consult-reason-card {
  padding: 28px; border: 1px solid var(--gray-100);
  border-radius: var(--radius-md); background: var(--white);
  transition: box-shadow .3s, transform .3s;
}
.consult-reason-card:hover { box-shadow: var(--shadow-sm); transform: translateY(-2px); }
.consult-reason-card h4 {
  font-family: var(--font-display); font-size: 16px; font-weight: 700;
  color: var(--navy); margin: 0 0 8px;
}
.consult-reason-card p {
  color: var(--gray-600); font-size: 13px; line-height: 1.6; margin: 0;
}
.authority-section {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
}
.authority-card {
  padding: 28px; border: 1px solid var(--gray-100);
  border-radius: var(--radius-md); background: var(--white);
  text-align: center; transition: box-shadow .3s;
}
.authority-card:hover { box-shadow: var(--shadow-sm); }
.authority-card .authority-icon {
  width: 48px; height: 48px; margin: 0 auto 16px; display: grid; place-items: center;
  border-radius: 50%; background: var(--gold-soft); color: var(--gold);
  font-family: var(--font-display); font-size: 20px; font-weight: 700;
}
.authority-card h4 {
  font-family: var(--font-display); font-size: 14px; font-weight: 700;
  color: var(--navy); margin: 0 0 6px;
}
.authority-card p {
  color: var(--gray-600); font-size: 12px; line-height: 1.5; margin: 0;
}
.final-cta-shell {
  display: grid; grid-template-columns: 1fr 1fr; gap: 40px;
  padding: 56px; border: 1px solid var(--gray-100);
  border-radius: var(--radius-lg); background: var(--white); box-shadow: var(--shadow-sm);
}
.final-cta-shell h3 {
  margin: 0 0 8px; font-family: var(--font-display); font-size: 24px; font-weight: 700;
}
.final-cta-shell .cta-intro { color: var(--gray-600); margin: 0 0 20px; font-size: 15px; }
.final-cta-topics { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 0 0 24px; padding: 0; list-style: none; }
.final-cta-topics li { padding-left: 18px; position: relative; color: var(--gray-600); font-size: 14px; }
.final-cta-topics li::before { content: ""; position: absolute; left: 0; top: 9px; width: 5px; height: 5px; border-radius: 50%; background: var(--gold); }
.final-cta-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.cta-contact-links { display: grid; gap: 14px; align-content: center; padding: 20px; }
.cta-contact-links a {
  display: flex; align-items: center; gap: 12px;
  padding: 16px 20px; border: 1px solid var(--gray-100); border-radius: var(--radius-sm);
  font-size: 16px; font-weight: 600; color: var(--navy); transition: box-shadow .2s;
}
.cta-contact-links a:hover { box-shadow: var(--shadow-xs); }
@media (max-width: 960px) {
  .profile-hero .hero-image { max-width: 100%; }
  .timeline::before { left: 20px; }
  .timeline-item { grid-template-columns: 1fr; gap: 4px; padding-left: 48px; }
  .timeline-item:nth-child(odd) .timeline-content,
  .timeline-item:nth-child(odd) .timeline-date,
  .timeline-item:nth-child(even) .timeline-date,
  .timeline-item:nth-child(even) .timeline-content { grid-column: 1; text-align: left; }
  .timeline-dot { left: 20px; }
  .expertise-grid { grid-template-columns: 1fr 1fr; }
  .credentials-grid { grid-template-columns: 1fr 1fr; }
  .consult-reasons-grid { grid-template-columns: 1fr 1fr; }
  .authority-section { grid-template-columns: 1fr 1fr; }
  .final-cta-shell { grid-template-columns: 1fr; padding: 32px; }
}
@media (max-width: 620px) {
  .expertise-grid { grid-template-columns: 1fr; }
  .credentials-grid { grid-template-columns: 1fr; }
  .consult-reasons-grid { grid-template-columns: 1fr; }
  .authority-section { grid-template-columns: 1fr; }
  .final-cta-topics { grid-template-columns: 1fr; }
  .philosophy-block { padding: 32px 24px; }
}
</style>
<main id="main-content">
  <section class="hero profile-hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="hero-eyebrow">Leadership Profile</div>
        <div class="hero-titles">
          <h1>K. Sivasankaran</h1>
          <p class="title-role">Founder &amp; Principal Advisor</p>
          <div class="title-credentials">
            <span>Advocate</span>
            <span>Income Tax Practitioner</span>
            <span>GST Practitioner</span>
            <span>Certified POSH Trainer</span>
          </div>
          <p class="title-summary">
            Nearly Three Decades of Professional Excellence in Taxation, Compliance, Business Advisory and Legal Practice.
          </p>
          <p style="color:var(--gray-600);font-size:15px;line-height:1.7;margin:0 0 24px;">
            K. Sivasankaran is an Advocate, Income Tax Practitioner, GST Practitioner and Founder of E Tax Advisors Private Limited, bringing nearly three decades of experience in taxation, regulatory compliance, litigation support, labour law advisory and business consulting.
          </p>
        </div>
        <div class="profile-actions">
          <a class="btn btn-primary btn-lg" href="/contact.php#consult">Book Consultation</a>
          <a class="btn btn-secondary btn-lg" href="tel:+919894626300">Contact Directly</a>
          <a class="btn btn-outline btn-lg" href="#credentials">Download Profile</a>
        </div>
      </div>
      <div class="hero-image">
        <img class="profile-hero-img" src="<?= htmlspecialchars(site_href('/assets/img/ks-sivasankaran.jpg')) ?>" alt="K. Sivasankaran — Founder & Principal Advisor, E Tax Advisors Private Limited" width="500" height="500" />
      </div>
    </div>
  </section>

  <section class="credibility-strip">
    <div class="container">
      <div class="credibility-grid">
        <div class="credibility-item"><strong>1000+</strong><span>Clients Served</span></div>
        <div class="credibility-item"><strong>Nearly 30</strong><span>Years of Experience</span></div>
        <div class="credibility-item"><strong>5+</strong><span>Advisory Professionals</span></div>
        <div class="credibility-item"><strong>8</strong><span>Core Practice Areas</span></div>
        <div class="credibility-item"><strong>GST &amp; Income Tax</strong><span>Representation</span></div>
        <div class="credibility-item"><strong>PAN India</strong><span>Service Coverage</span></div>
      </div>
    </div>
  </section>

  <section class="section section-alt" id="journey">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Professional Journey</p>
        <h2 class="section-title">Nearly three decades of continuous professional development, practice expansion and advisory leadership.</h2>
      </div>
      <div class="timeline">
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>1996</h3><p>Foundation Years</p></div>
          <div class="timeline-content">
            <h4>Commenced Professional Career as Audit Assistant</h4>
            <p>Built strong foundations in accounting, auditing, taxation, statutory compliance and financial reporting through structured training and hands-on audit assignments across diverse business entities.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2001</h3><p>Practice Establishment</p></div>
          <div class="timeline-content">
            <h4>Established Independent Consultancy Practice</h4>
            <p>Started advising businesses on taxation, accounting, compliance management and business advisory matters. Built a client base across manufacturing, trading and service sector enterprises in the Puducherry region.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2007</h3><p>Government Recognition</p></div>
          <div class="timeline-content">
            <h4>Appointed as Tax Return Preparer (TRP) by CBDT, Government of India</h4>
            <p>Provided Income Tax Return preparation and taxpayer assistance services under the Government of India initiative, building deep expertise in direct tax compliance and taxpayer representation.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2009</h3><p>Service Tax Recognition</p></div>
          <div class="timeline-content">
            <h4>Appointed as Service Tax Return Preparer by CBEC, Government of India</h4>
            <p>Provided Service Tax compliance and return preparation services, strengthening indirect tax expertise and regulatory liaison capabilities.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2011</h3><p>Tax Practice Expansion</p></div>
          <div class="timeline-content">
            <h4>Appointed as Income Tax Practitioner</h4>
            <p>Began representing taxpayers before the Income Tax Department and handling assessment proceedings, scrutiny matters and compliance-related representations with increasing complexity and volume.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2015</h3><p>Firm Foundation</p></div>
          <div class="timeline-content">
            <h4>Founded E Tax Advisors Private Limited</h4>
            <p>Established a multidisciplinary professional services organisation focused on Tax, Legal, Compliance, HR, Payroll and Technology-enabled advisory services. The firm was built to consolidate fragmented advisory under one accountable desk.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2017</h3><p>GST Era</p></div>
          <div class="timeline-content">
            <h4>Enrolled as Goods and Services Tax Practitioner (GSTP)</h4>
            <p>Started providing GST advisory, compliance and representation services under the GST regime. Developed specialised expertise in notice response, assessment representation and appellate proceedings under GST laws.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2018</h3><p>Legal Practice</p></div>
          <div class="timeline-content">
            <h4>Graduated LL.B. and Commenced Advocacy</h4>
            <p>Expanded practice into litigation, legal advisory, labour law, commercial documentation and dispute resolution. Enrolled as an Advocate with the Bar Council of Tamil Nadu &amp; Puducherry.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2022</h3><p>Workplace Governance</p></div>
          <div class="timeline-content">
            <h4>Certified POSH Internal Committee Trainer</h4>
            <p>Successfully completed certification through National HRD Network (NHRD) and began advising organisations on POSH compliance, Internal Committee constitution and workplace governance frameworks.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2026</h3><p>Professional Certification</p></div>
          <div class="timeline-content">
            <h4>Awarded Consultant Chartered Tax Practitioner (CTPr)</h4>
            <p>Obtained NSQF Level 5 certification recognised through MEPSC and NCVET, confirming advanced competency in tax practice at nationally recognised standards.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>2026</h3><p>Industry Recognition</p></div>
          <div class="timeline-content">
            <h4>Recognised as Tally Tech Innovator</h4>
            <p>Honoured for innovation in technology-enabled advisory services, compliance automation and workflow management solutions that bridge the gap between traditional professional practice and modern service delivery.</p>
          </div>
        </div>
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-date"><h3>Present</h3><p>Leadership</p></div>
          <div class="timeline-content">
            <h4>Founder &amp; Principal Advisor</h4>
            <p>Leading integrated Tax, Legal, Compliance, Labour Law, Litigation Support, HR and Technology-enabled advisory services across India. Personally overseeing client relationships, matter strategy and practice development.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="profile">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Professional Profile</p>
        <h2 class="section-title">Nearly three decades of integrated tax, legal and compliance advisory experience.</h2>
      </div>
      <div class="card" style="padding:40px;">
        <p style="color:var(--gray-600);font-size:15px;line-height:1.8;margin:0 0 20px;">
          K. Sivasankaran brings nearly three decades of professional experience spanning taxation, regulatory compliance, litigation support, labour law advisory and business consulting. His career reflects a continuous arc of professional development — from audit assistant and government-recognised Tax Return Preparer to Advocate, GST Practitioner, Income Tax Practitioner, certified POSH trainer and Founder of a multi-disciplinary advisory firm.
        </p>
        <p style="color:var(--gray-600);font-size:15px;line-height:1.8;margin:0 0 20px;">
          As an Advocate enrolled with the Bar Council of Tamil Nadu &amp; Puducherry and a registered GST Practitioner under the CGST Act, he combines legal authority with deep regulatory knowledge. His practice spans the full spectrum of tax advisory — from routine compliance and return preparation to complex notice response, assessment representation and appellate proceedings before tax authorities.
        </p>
        <p style="color:var(--gray-600);font-size:15px;line-height:1.8;margin:0 0 20px;">
          In the domain of labour law and corporate compliance, he advises employers on statutory obligations under labour codes, factory compliance, ESI/PF management, contract labour governance and POSH compliance. His corporate compliance practice covers company and LLP incorporations, annual ROC filings, board governance, event-based statutory filings and trust/society registration and compliance.
        </p>
        <p style="color:var(--gray-600);font-size:15px;line-height:1.8;margin:0 0 20px;">
          K. Sivasankaran also advises on business structuring, commercial documentation, partnership and dispute resolution, regulatory risk management and strategic advisory for business owners. His experience includes representing clients before the Income Tax Department, GST authorities, labour departments and other regulatory bodies across South India and PAN India service coverage.
        </p>
        <p style="color:var(--gray-600);font-size:15px;line-height:1.8;margin:0;">
          As a technology innovator recognised by Tally, he has championed the integration of technology-enabled workflows into professional advisory — developing platforms and processes that improve execution quality, client visibility and compliance discipline. He leads E Tax Advisors Private Limited with a focus on structured delivery, review controls, professional accountability and long-term client relationships built on trust, not transactions.
        </p>
      </div>
    </div>
  </section>

  <section class="section section-alt" id="expertise">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Areas of Expertise</p>
        <h2 class="section-title">Comprehensive advisory capability across tax, legal and compliance domains.</h2>
        <p class="section-intro">Each practice area is led directly by K. Sivasankaran, ensuring senior-level attention on every matter.</p>
      </div>
      <div class="expertise-grid">
        <div class="expertise-card">GST Advisory &amp; Litigation</div>
        <div class="expertise-card">Income Tax Advisory &amp; Representation</div>
        <div class="expertise-card">GST Notices, Appeals &amp; Assessments</div>
        <div class="expertise-card">Income Tax Notices &amp; Assessments</div>
        <div class="expertise-card">TDS &amp; Payroll Compliance</div>
        <div class="expertise-card">Labour Law &amp; HR Compliance</div>
        <div class="expertise-card">Corporate &amp; ROC Compliance</div>
        <div class="expertise-card">Trust, Society &amp; NGO Advisory</div>
        <div class="expertise-card">Commercial Documentation</div>
        <div class="expertise-card">Business Structuring &amp; Governance</div>
        <div class="expertise-card">Tax Planning &amp; Compliance Management</div>
        <div class="expertise-card">Regulatory Advisory Services</div>
        <div class="expertise-card">POSH Compliance &amp; Workplace Governance</div>
      </div>
    </div>
  </section>

  <section class="section" id="credentials">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Professional Credentials</p>
        <h2 class="section-title">Formal qualifications, government recognitions and professional certifications.</h2>
        <p class="section-intro">Every credential represents a milestone in a career dedicated to professional excellence in tax, legal and compliance advisory.</p>
      </div>
      <div class="credentials-grid">
        <div class="credential-item"><strong>B.Com.</strong><span>Bachelor of Commerce</span></div>
        <div class="credential-item"><strong>LL.B.</strong><span>Bachelor of Laws</span></div>
        <div class="credential-item"><strong>Advocate</strong><span>Bar Council of Tamil Nadu &amp; Puducherry</span></div>
        <div class="credential-item"><strong>Income Tax Practitioner</strong><span>Authorised to represent before IT Department</span></div>
        <div class="credential-item"><strong>GST Practitioner</strong><span>Registered under CGST Act, 2017</span></div>
        <div class="credential-item"><strong>Tax Return Preparer</strong><span>Former TRP — CBDT, Government of India</span></div>
        <div class="credential-item"><strong>Service Tax Return Preparer</strong><span>Former STR — CBEC, Government of India</span></div>
        <div class="credential-item"><strong>Consultant Chartered Tax Practitioner</strong><span>CTPr — NSQF Level 5, MEPSC &amp; NCVET</span></div>
        <div class="credential-item"><strong>Certified POSH Trainer</strong><span>NHRD Network Certified</span></div>
        <div class="credential-item"><strong>Tally Tech Innovator</strong><span>Awardee — Technology-Enabled Advisory</span></div>
        <div class="credential-item"><strong>Founder &amp; Principal Advisor</strong><span>E Tax Advisors Private Limited</span></div>
        <div class="credential-item"><strong>Founder</strong><span>E Tax Academy</span></div>
      </div>
    </div>
  </section>

  <section class="section section-alt" id="industries">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Industries Served</p>
        <h2 class="section-title">Advisory experience across sectors where compliance quality directly impacts business outcomes.</h2>
        <p class="section-intro">K. Sivasankaran has advised businesses and institutions across a broad range of industries, bringing domain-specific understanding to every engagement.</p>
      </div>
      <div class="industry-grid">
        <div class="industry-card"><h3>Manufacturing</h3><p>Factory compliance, GST input credit, labour law, ESI/PF and inspection readiness.</p></div>
        <div class="industry-card"><h3>Trading</h3><p>GST return filing, e-way bill compliance, ITC reconciliation and notice response.</p></div>
        <div class="industry-card"><h3>MSMEs</h3><p>Compliance, documentation, tax planning and periodic controls for growing enterprises.</p></div>
        <div class="industry-card"><h3>Educational Institutions</h3><p>Trust registration, 12A/80G, FCRA, payroll and staff compliance.</p></div>
        <div class="industry-card"><h3>Trusts &amp; NGOs</h3><p>Registration, tax exemption, FCRA compliance, board governance and annual returns.</p></div>
        <div class="industry-card"><h3>Professionals</h3><p>Tax planning, compliance and documentation support for individual practitioners.</p></div>
        <div class="industry-card"><h3>Startups</h3><p>Formation, compliance setup, bookkeeping and promoter-side advisory.</p></div>
        <div class="industry-card"><h3>Family-Owned Businesses</h3><p>Integrated advisory where operations, promoter obligations and compliance intersect.</p></div>
        <div class="industry-card"><h3>Healthcare</h3><p>Entity compliance, ESI/PF, contract labour and clinical establishment registration.</p></div>
        <div class="industry-card"><h3>Service Sector</h3><p>GST, TDS, payroll, company compliance and regulatory advisory for service enterprises.</p></div>
      </div>
    </div>
  </section>

  <section class="section" id="philosophy">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Leadership Philosophy</p>
      </div>
      <div class="philosophy-block">
        <blockquote>
          "Professional advisory is not merely about compliance. It is about helping clients make informed decisions, manage risks proactively, and build systems that support sustainable growth."
        </blockquote>
        <p style="color:var(--gray-600);font-size:14px;margin:24px 0 0;font-weight:600;">— K. Sivasankaran, Founder &amp; Principal Advisor</p>
      </div>
    </div>
  </section>

  <section class="section section-alt" id="why-consult">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why Clients Consult Him</p>
        <h2 class="section-title">Matters where clients seek K. Sivasankaran's direct involvement.</h2>
        <p class="section-intro">Across these areas, clients value the combination of technical depth, representation readiness and structured follow-through that comes with nearly three decades of experience.</p>
      </div>
      <div class="consult-reasons-grid">
        <div class="consult-reason-card">
          <h4>GST Notices &amp; Show Cause Notices</h4>
          <p>Notice anxiety, incomplete records and weak response framing are common. He provides structured review, documentation readiness and authority-facing follow-through.</p>
        </div>
        <div class="consult-reason-card">
          <h4>Income Tax Assessments &amp; Appeals</h4>
          <p>Scrutiny assessments, Section 148 re-openings and appellate proceedings require experienced handling of facts, records and submission strategy.</p>
        </div>
        <div class="consult-reason-card">
          <h4>GST Litigation &amp; Representation</h4>
          <p>Appeal drafting, departmental hearing representation and coordinate representation before appellate authorities.</p>
        </div>
        <div class="consult-reason-card">
          <h4>Labour Law Compliance</h4>
          <p>ESI/PF, contract labour, factory act, shop &amp; establishment and inspection readiness for operationally intensive businesses.</p>
        </div>
        <div class="consult-reason-card">
          <h4>Corporate Compliance &amp; Governance</h4>
          <p>Company/LLP incorporation, annual ROC filings, board governance and event-based statutory compliance for entities of all sizes.</p>
        </div>
        <div class="consult-reason-card">
          <h4>Trust &amp; Society Advisory</h4>
          <p>Registration under Trust/Society acts, 12A/80G approvals, FCRA compliance and governance support for charitable institutions.</p>
        </div>
        <div class="consult-reason-card">
          <h4>Business Structuring</h4>
          <p>Entity selection, ownership structuring, promoter agreements and governance frameworks for new and existing businesses.</p>
        </div>
        <div class="consult-reason-card">
          <h4>Regulatory Risk Management</h4>
          <p>Proactive identification of compliance gaps, documentation weaknesses and regulatory exposure across tax, labour and corporate domains.</p>
        </div>
        <div class="consult-reason-card">
          <h4>Strategic Advisory for Business Owners</h4>
          <p>Integrated guidance on tax strategy, compliance systems, business documentation and long-term advisory relationship for decision-makers.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="authority">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Why Businesses Trust K. Sivasankaran</p>
        <h2 class="section-title">Nearly three decades of professional integrity, government recognition and integrated expertise.</h2>
      </div>
      <div class="authority-section">
        <div class="authority-card">
          <div class="authority-icon">30</div>
          <h4>Nearly 30 Years of Experience</h4>
          <p>Continuous professional practice since 1996 spanning audit, taxation, compliance, legal advisory and business consulting.</p>
        </div>
        <div class="authority-card">
          <div class="authority-icon">G</div>
          <h4>Government-Recognised Practitioner</h4>
          <p>Former TRP under CBDT, former STR under CBEC, registered GST Practitioner and authorised Income Tax Practitioner.</p>
        </div>
        <div class="authority-card">
          <div class="authority-icon">A</div>
          <h4>Advocate &amp; Legal Advisor</h4>
          <p>Enrolled Advocate with the Bar Council of Tamil Nadu &amp; Puducherry, authorised to represent clients before courts, tribunals and departmental authorities.</p>
        </div>
        <div class="authority-card">
          <div class="authority-icon">P</div>
          <h4>Certified POSH Trainer</h4>
          <p>Certified by NHRD Network to train Internal Committee members and advise organisations on POSH compliance and workplace governance.</p>
        </div>
        <div class="authority-card">
          <div class="authority-icon">G</div>
          <h4>GST Practitioner</h4>
          <p>Registered under the CGST Act with authorisation to file returns, respond to notices and represent clients in GST proceedings.</p>
        </div>
        <div class="authority-card">
          <div class="authority-icon">T</div>
          <h4>Technology Innovator</h4>
          <p>Recognised by Tally for innovation in technology-enabled advisory, compliance automation and professional workflow solutions.</p>
        </div>
        <div class="authority-card">
          <div class="authority-icon">I</div>
          <h4>Integrated Expertise</h4>
          <p>Single-point advisory across tax, legal, compliance, labour, corporate and technology domains — eliminating fragmented counsel.</p>
        </div>
        <div class="authority-card">
          <div class="authority-icon">IN</div>
          <h4>PAN India Advisory</h4>
          <p>Service coverage across India with particular depth in Puducherry, Tamil Nadu, Karnataka and Andhra Pradesh.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="consult">
    <div class="container">
      <div class="section-header centered">
        <p class="section-label">Need Professional Guidance?</p>
        <h2 class="section-title">Schedule a direct consultation with K. Sivasankaran.</h2>
        <p class="section-intro">Matters involving notices, litigation, compliance risk or strategic decisions benefit from senior-level review before action is taken.</p>
      </div>
      <div class="final-cta-shell">
        <div>
          <h3>Schedule a Consultation</h3>
          <p class="cta-intro">K. Sivasankaran is available for consultations on:</p>
          <ul class="final-cta-topics">
            <li>GST Matters</li>
            <li>Income Tax Matters</li>
            <li>Litigation &amp; Representation</li>
            <li>Labour Law Compliance</li>
            <li>Trust &amp; Society Advisory</li>
            <li>Business Compliance</li>
          </ul>
          <div class="final-cta-actions">
            <a class="btn btn-primary btn-lg" href="/contact.php#consult">Book Consultation</a>
          </div>
        </div>
        <div class="cta-contact-links">
          <a href="tel:+919894626300">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Call +91 98946 26300
          </a>
          <a href="mailto:ks@etaxadv.com">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            ks@etaxadv.com
          </a>
          <a href="https://wa.me/919500601119" target="_blank" rel="noopener">
            <svg viewBox="0 0 32 32" fill="currentColor" width="20" height="20"><path d="M16 2C8.2 2 2 8.2 2 16c0 3.1.9 6 2.5 8.5L2 30l5.8-2.3C10.3 29.2 13 30 16 30c7.8 0 14-6.2 14-14S23.8 2 16 2zm6.5 19.8c-.4 1-1.5 1.8-2.5 2-1 .2-2 .2-3.2-.6-1.7-.8-3.2-2.4-4.4-3.8-1.2-1.4-2-3-2.3-4.5-.2-1.2.1-2.2.6-2.8.4-.6 1-.8 1.3-.8h.8c.3 0 .5 0 .8.6.3.6 1 2.2 1 2.4s0 .4-.2.6c-.2.2-.4.5-.6.7-.2.2-.4.4-.2.8.2.4 1 1.7 2 2.6 1.2 1.2 2.2 1.6 2.6 1.8.4.2.6.2.8 0 .2-.2.8-.8 1-1.2.2-.4.4-.4.6-.3.2.2 1.4.7 1.6.8.2.2.4.2.5.3 0 .4 0 1-.2 1.4z"/></svg>
            WhatsApp Consultation
          </a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
