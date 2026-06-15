<?php
/*
 * Trust & Authority Content Sections for E Tax Advisors
 * Include this file on any page where these sections should appear.
 * Each section is a function that outputs HTML using the existing design system.
 */

function render_membership_section(): void {
?>
<section class="section section-alt">
  <div class="container">
    <div class="section-header">
      <p class="section-label">Professional Memberships</p>
      <h2 class="section-title">Our practice is conducted under recognised professional bodies with binding ethical and conduct standards.</h2>
    </div>
    <div class="grid-2" style="gap:32px;">
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">Bar Council of Tamil Nadu &amp; Puducherry</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.7;margin:0 0 12px;">
          K. Sivasankaran is an enrolled Advocate with the Bar Council of Tamil Nadu &amp; Puducherry. All litigation and representation work is conducted under the Advocates Act, 1961, with the ethical obligations and professional standards that membership requires.
        </p>
        <ul class="list-clean">
          <li>Enrolled Advocate — Bar Council registration number available on request</li>
          <li>Authorised to represent clients before tax authorities, tribunals and courts</li>
          <li>Bound by Bar Council of India ethics rules and professional conduct standards</li>
          <li>Professional indemnity and client confidentiality obligations</li>
        </ul>
      </div>
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">Registered GST Practitioner</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.7;margin:0 0 12px;">
          The firm is registered as a GST Practitioner under the CGST Act, 2017. All GST-related work — registration, return filing, notice response and representation — is conducted through authorised GST Practitioner credentials.
        </p>
        <ul class="list-clean">
          <li>Authorised to file GST returns on behalf of clients</li>
          <li>Empowered to represent clients in GST proceedings</li>
          <li>Subject to GST Practitioner regulations and departmental oversight</li>
          <li>Annual renewal and continuing compliance obligations maintained</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<?php
}

function render_client_industries_section(): void {
?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <p class="section-label">Industries Served</p>
      <h2 class="section-title">We advise across sectors where compliance gaps carry measurable business, regulatory and reputational risk.</h2>
    </div>
    <div class="grid-4" style="gap:20px;">
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">MF</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Manufacturing</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">Factory license, GST input credit, labour law, pollution board, ESI/PF, contract labour, fire NOC and inspection readiness.</p>
      </div>
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">TR</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Trading &amp; Distribution</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">GST return filing, e-way bill compliance, ITC reconciliation, notice response and annual return preparation.</p>
      </div>
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">IT</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">IT &amp; Professional Services</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">Company compliance, payroll, TDS, ESOP advisory, ROC filings, founder tax planning and compliance systems.</p>
      </div>
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">ED</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Education</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">Trust/society registration, 12A/80G, FCRA, property compliance, payroll, ESI/PF for staff and scholarship documentation.</p>
      </div>
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">NG</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">NGOs &amp; Trusts</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">Registration, tax exemption approvals, FCRA compliance, board governance, donor documentation and annual returns.</p>
      </div>
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">HC</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Healthcare</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">Entity compliance, ESI/PF for staff, contract labour for outsourced services, shop &amp; establishment and clinical establishment registration.</p>
      </div>
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">HO</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Hospitality</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">GST compliance, liquor licence coordination, labour law, ESI/PF for hotel staff, contract labour and shop &amp; establishment.</p>
      </div>
      <div class="card" style="padding:24px;text-align:center;">
        <div class="reason-icon" style="margin:0 auto 16px;">CO</div>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Construction &amp; Real Estate</h3>
        <p style="color:var(--gray-600);font-size:13px;margin:0;">GST composition, TDS on property, contract labour, ESI/PF for site workers, project compliance and registration.</p>
      </div>
    </div>
  </div>
</section>
<?php
}

function render_why_switch_section(): void {
?>
<section class="section section-alt">
  <div class="container">
    <div class="section-header centered">
      <p class="section-label">Why Businesses Switch to E Tax Advisors</p>
      <h2 class="section-title">The most common reason clients give: they were spending the same money but getting fragmented, reactive support. They wanted one accountable advisor.</h2>
    </div>
    <div class="reasons-grid">
      <div class="reason-card">
        <div class="reason-icon">FR</div>
        <h3>Fragmented Advisory Model</h3>
        <p>Most businesses use separate CA for tax, lawyer for legal, consultant for GST, and auditor for books. Coordination is left to the business owner.</p>
      </div>
      <div class="reason-card">
        <div class="reason-icon">WA</div>
        <h3>We Replace Fragmentation With One Desk</h3>
        <p>Tax, compliance, legal, bookkeeping and representation under one accountable team with a single point of contact, documented workflows and leadership oversight.</p>
      </div>
      <div class="reason-card">
        <div class="reason-icon">PR</div>
        <h3>Proactive vs. Reactive</h3>
        <p>Traditional firms respond when you call with a problem. Our model includes calendar-driven compliance tracking, proactive deadline alerts and quarterly health reviews.</p>
      </div>
      <div class="reason-card">
        <div class="reason-icon">LI</div>
        <h3>Litigation &amp; Representation Readiness</h3>
        <p>Most CAs file returns but do not handle litigation. When a notice arrives, clients must find a separate lawyer. We handle both — from filing to appeal — so nothing falls through.</p>
      </div>
    </div>
  </div>
</section>
<?php
}

function render_consultation_process_section(): void {
?>
<section class="section">
  <div class="container">
    <div class="section-header centered">
      <p class="section-label">Our Consultation Process</p>
      <h2 class="section-title">From first contact to engagement — a structured process designed for decision-makers.</h2>
    </div>
    <div class="grid-4" style="gap:20px;">
      <div class="card" style="text-align:center;padding:32px 20px;">
        <strong style="display:block;font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--navy);margin-bottom:12px;">01</strong>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Initial Discussion</h3>
        <p style="color:var(--gray-600);font-size:13px;line-height:1.6;margin:0;">Free 15-minute call to understand your situation and determine if we can help. No commitment.</p>
      </div>
      <div class="card" style="text-align:center;padding:32px 20px;">
        <strong style="display:block;font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--navy);margin-bottom:12px;">02</strong>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Briefing &amp; Scope</h3>
        <p style="color:var(--gray-600);font-size:13px;line-height:1.6;margin:0;">Detailed discussion with the relevant lead professional. We define the scope, deliverables and timeline.</p>
      </div>
      <div class="card" style="text-align:center;padding:32px 20px;">
        <strong style="display:block;font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--navy);margin-bottom:12px;">03</strong>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Engagement Proposal</h3>
        <p style="color:var(--gray-600);font-size:13px;line-height:1.6;margin:0;">Written proposal with scope, fee structure, timelines and contact terms. No hidden charges.</p>
      </div>
      <div class="card" style="text-align:center;padding:32px 20px;">
        <strong style="display:block;font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--navy);margin-bottom:12px;">04</strong>
        <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;">Onboarding &amp; Execution</h3>
        <p style="color:var(--gray-600);font-size:13px;line-height:1.6;margin:0;">Document collection, team assignment, kick-off meeting and structured execution with progress reporting.</p>
      </div>
    </div>
    <div style="text-align:center;margin-top:32px;">
      <a class="btn btn-primary btn-lg" href="/contact.php#consult">Start Your Initial Discussion</a>
    </div>
  </div>
</section>
<?php
}

function render_service_guarantee_section(): void {
?>
<section class="section section-alt">
  <div class="container">
    <div class="section-header centered">
      <p class="section-label">Our Commitments to You</p>
      <h2 class="section-title">Every engagement at E Tax Advisors is backed by these service commitments.</h2>
    </div>
    <div class="grid-3" style="gap:20px;">
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">Qualified Professional Review</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.6;margin:0;">Every return, filing, notice response or legal document we submit is reviewed and approved by a qualified professional before it leaves our office. No junior-only handling.</p>
      </div>
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">Deadline Compliance</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.6;margin:0;">We maintain a compliance calendar for every client and provide proactive deadline alerts. If we miss a filing deadline due to our error, we bear the late fee.</p>
      </div>
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">Urgent Notice Response</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.6;margin:0;">For existing clients, we acknowledge notice-related communications within 2 hours during business hours and provide a preliminary response strategy within 24 hours.</p>
      </div>
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">Transparent Communication</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.6;margin:0;">You will always know the status of your matter. Every engagement includes periodic status updates and a written closure summary with forward-action checklist.</p>
      </div>
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">Confidentiality</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.6;margin:0;">All client information, documents and matter details are treated as confidential. We do not disclose client identity, matter details or engagement specifics without explicit written permission.</p>
      </div>
      <div class="card" style="padding:28px;">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin:0 0 12px;color:var(--navy);">No Hidden Fees</h3>
        <p style="color:var(--gray-600);font-size:14px;line-height:1.6;margin:0;">Our engagement proposals clearly state the scope, deliverables, timelines and fee structure. Any change in scope is communicated and agreed upon before additional work begins.</p>
      </div>
    </div>
  </div>
</section>
<?php
}

function render_confidentiality_section(): void {
?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <p class="section-label">Confidentiality &amp; Data Security</p>
      <h2 class="section-title">Our clients entrust us with their most sensitive financial, legal and business information. We treat that trust as our highest obligation.</h2>
    </div>
    <div class="grid-2" style="gap:40px;">
      <div>
        <h3 style="font-family:var(--font-display);font-size:20px;font-weight:700;margin:0 0 16px;color:var(--navy);">Confidentiality Principles</h3>
        <ul class="list-clean">
          <li>All client information is treated as confidential and is not disclosed to third parties without written authorisation</li>
          <li>Client identities are never used in marketing or case studies without explicit written permission</li>
          <li>All case studies and success stories are anonymised before publication</li>
          <li>Matter-specific information is shared only with the engagement team and the client</li>
          <li>Professional ethics under the Advocates Act and Bar Council rules require strict client confidentiality</li>
          <li>Employee access to client data is restricted to engagement-specific requirements</li>
        </ul>
      </div>
      <div>
        <h3 style="font-family:var(--font-display);font-size:20px;font-weight:700;margin:0 0 16px;color:var(--navy);">Data Security Practices</h3>
        <ul class="list-clean">
          <li>Client documents are stored in access-controlled systems with role-based permissions</li>
          <li>Physical documents are maintained in secured cabinets within locked office premises</li>
          <li>Digital communication with clients uses encrypted channels wherever possible</li>
          <li>Document disposal follows secure destruction protocols</li>
          <li>Access to client data is reviewed and revoked promptly upon engagement closure</li>
          <li>Our office premises and systems are secured with standard physical and digital security measures</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<?php
}
