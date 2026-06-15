<?php
require_once __DIR__ . '/includes/testimonials.php';
require_once __DIR__ . '/includes/contact-handler.php';

$page_title = "Our Team – Tax, Legal & Compliance Professionals | E Tax Advisors";
$page_description = "Meet the leadership and professional team at E Tax Advisors Private Limited – experienced advocates, tax consultants and compliance experts.";
$page_path = '/team.php';
$page_og_image = '/assets/img/og-image.jpg';

$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'team_consult') {
  $consult_result = contact_process_submission();
}

contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Our Team</div>
        <h1>The professionals behind every engagement.</h1>
        <p>
          E Tax Advisors brings together advocates, tax consultants and compliance specialists who work
          across tax, legal, regulatory and bookkeeping domains. Each engagement is supported by experienced
          hands who understand both the technical depth and the follow-through discipline required.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="contact.php#consult">Speak With Our Team</a>
          <a class="btn btn-outline" href="services.php">Explore Services</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Advocate-led advisory</span>
          <span class="proof-chip">15+ years average experience</span>
          <span class="proof-chip">Multi-domain expertise</span>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-stack">
          <div class="hero-panel">
            <h2>Advisory depth across tax, legal and compliance domains.</h2>
            <p>
              Every professional at E Tax Advisors contributes to a structure where matters are reviewed,
              documented and followed through rather than handed off without continuity.
            </p>
          </div>
          <div class="hero-grid">
            <div class="hero-metric">
              <strong>50+</strong>
              <span>professionals across advisory and compliance teams</span>
            </div>
            <div class="hero-metric">
              <strong>8</strong>
              <span>integrated service domains under one roof</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Leadership Team</p>
        <h2 class="section-title">Experienced professionals who lead client relationships and advisory outcomes.</h2>
        <p class="section-intro">
          Leadership visibility matters when technical complexity, compliance risk and representation decisions
          depend on who is reviewing the matter and how accountability is structured.
        </p>
      </div>

      <div class="leadership-grid">
        <article class="leader-card">
          <a href="/team/ks-sivasankaran.php" class="leader-portrait" aria-label="View full profile of K. Sivasankaran" style="display:grid;place-items:center;text-decoration:none;">KS</a>
          <div class="leader-body">
            <p class="leader-role">Advocate | Tax Consultant</p>
            <h3><a href="/team/ks-sivasankaran.php" style="color:inherit;text-decoration:none;">K. Sivasankaran</a></h3>
            <p class="leader-credentials">B.Com., LL.B. &mdash; Nearly 30 years of professional experience</p>
            <ul class="leader-expertise">
              <li>GST Advisory &amp; Litigation &ndash; notice response, assessment representation and appeal support</li>
              <li>Income Tax Advisory &ndash; return review, scrutiny handling and tax planning</li>
              <li>Corporate Compliance &ndash; company and LLP governance, filings and documentation</li>
              <li>Labour Law Advisory &ndash; employer compliance, inspections and regulatory alignment</li>
              <li>Commercial Documentation &ndash; agreements, contracts and business documentation review</li>
            </ul>
            <p style="margin: 0; color: var(--muted); font-size: 0.92rem;">
              K. Sivasankaran leads the firm's tax litigation and corporate compliance practice.
              His experience spans GST and income tax representation before departmental authorities,
              corporate compliance management for operating entities, and structured commercial documentation
              advisory for businesses and promoters.
            </p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <a class="btn btn-outline btn-sm" href="/team/ks-sivasankaran.php">View Full Profile</a>
              <a class="btn btn-primary btn-sm" href="contact.php#consult">Schedule a Consultation</a>
            </div>
          </div>
        </article>

        <article class="leader-card">
          <div class="leader-portrait" aria-label="Portrait of S. Muthulakshmi">SM</div>
          <div class="leader-body">
            <p class="leader-role">Managing Director</p>
            <h3>S. Muthulakshmi</h3>
            <p class="leader-credentials">Operations and compliance delivery leadership</p>
            <ul class="leader-expertise">
              <li>Client Relations &amp; Account Management</li>
              <li>Operations &amp; Delivery Governance</li>
              <li>Compliance Process Design &amp; Oversight</li>
              <li>Business Development &amp; Strategic Planning</li>
              <li>Team Coordination &amp; Quality Assurance</li>
            </ul>
            <p style="margin: 0; color: var(--muted); font-size: 0.92rem;">
              S. Muthulakshmi oversees the firm's operational framework, ensuring that client engagements
              move through structured intake, execution and closure workflows. She leads compliance delivery
              governance, client communication standards and internal quality processes that underpin the
              firm's service model.
            </p>
            <a class="btn btn-primary" href="contact.php#consult">Schedule a Consultation</a>
          </div>
        </article>

        <article class="leader-card">
          <div class="leader-portrait" aria-label="Portrait of R. Veeraraghavan">RV</div>
          <div class="leader-body">
            <p class="leader-role">Senior Tax Consultant</p>
            <h3>R. Veeraraghavan</h3>
            <p class="leader-credentials">B.Com., FCMA &mdash; 18+ years in taxation and cost management</p>
            <ul class="leader-expertise">
              <li>Direct &amp; Indirect Tax Planning &amp; Compliance</li>
              <li>GST Return Review &amp; Reconciliation Advisory</li>
              <li>Costing, CMA Data &amp; Project Report Support</li>
              <li>Management Information &amp; Financial Analysis</li>
              <li>Audit Preparation &amp; Internal Control Review</li>
            </ul>
            <p style="margin: 0; color: var(--muted); font-size: 0.92rem;">
              R. Veeraraghavan brings deep experience in direct and indirect tax compliance, management
              reporting and financial analysis. He advises businesses on tax-efficient structures, GST
              reconciliation discipline and management-ready financial reporting.
            </p>
            <a class="btn btn-primary" href="contact.php#consult">Schedule a Consultation</a>
          </div>
        </article>

        <article class="leader-card">
          <div class="leader-portrait" aria-label="Portrait of P. Rajalakshmi">PR</div>
          <div class="leader-body">
            <p class="leader-role">Company Secretary &amp; Compliance Officer</p>
            <h3>P. Rajalakshmi</h3>
            <p class="leader-credentials">ACS, B.Com. &mdash; Specialising in corporate law and governance</p>
            <ul class="leader-expertise">
              <li>Company &amp; LLP Incorporation &amp; Compliance</li>
              <li>ROC Filings, Board Meetings &amp; Secretarial Records</li>
              <li>Event-based Compliance &amp; Due Diligence Support</li>
              <li>Trust &amp; Society Registration &amp; Governance</li>
              <li>FEMA &amp; RBI Compliance Advisory</li>
            </ul>
            <p style="margin: 0; color: var(--muted); font-size: 0.92rem;">
              P. Rajalakshmi manages the firm's corporate compliance practice, supporting entities across
              incorporation, annual compliance, board governance and event-based statutory filings.
              She also advises trusts and societies on registration pathways and ongoing governance requirements.
            </p>
            <a class="btn btn-primary" href="contact.php#consult">Schedule a Consultation</a>
          </div>
        </article>

        <article class="leader-card">
          <div class="leader-portrait" aria-label="Portrait of M. Venkatesan">MV</div>
          <div class="leader-body">
            <p class="leader-role">Labour Law &amp; HR Compliance Advisor</p>
            <h3>M. Venkatesan</h3>
            <p class="leader-credentials">M.A., LL.B. &mdash; 12+ years in labour law and industrial relations</p>
            <ul class="leader-expertise">
              <li>Labour Law Compliance Audits &amp; Advisory</li>
              <li>PF, ESI, Gratuity &amp; Bonus Act Compliance</li>
              <li>Factory &amp; Establishment Act Registrations</li>
              <li>Employment Agreements &amp; HR Policy Drafting</li>
              <li>Inspections, Notices &amp; Departmental Representation</li>
            </ul>
            <p style="margin: 0; color: var(--muted); font-size: 0.92rem;">
              M. Venkatesan leads labour law and HR compliance support for the firm. He advises employers
              on statutory compliance under labour codes, conducts compliance audits, supports inspection
              readiness and represents clients before labour authorities on notice and compliance matters.
            </p>
            <a class="btn btn-primary" href="contact.php#consult">Schedule a Consultation</a>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why Our Team Matters</p>
        <h2 class="section-title">Advisory outcomes depend on who leads the work, not just what is filed.</h2>
        <p class="section-intro">
          The quality of tax and compliance support is determined by the experience of the professionals
          handling the matter, the review structure behind the output and the continuity of follow-through.
        </p>
      </div>

      <div class="grid-3">
        <article class="card card-muted">
          <div class="feature-icon">E</div>
          <h3>Experience at every level</h3>
          <p>Matters are led by professionals who have handled notices, assessments, compliance programmes and documentation-sensitive assignments across diverse client contexts.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">R</div>
          <h3>Review-backed delivery</h3>
          <p>No output moves to the client or to an authority without internal verification, maker-checker review and documentation completeness checks.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">C</div>
          <h3>Continuity and accountability</h3>
          <p>Clients interact with the same leadership and team members across the engagement lifecycle rather than being handed off between unfamiliar handlers.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container consult-shell">
      <div class="section-header">
        <p class="section-label">Work With Our Team</p>
        <h2 class="section-title">Discuss your matter with an experienced professional.</h2>
        <p class="section-intro">
          Use this route to schedule a consultation with the relevant practice lead for your tax,
          compliance, labour, company or bookkeeping requirement.
        </p>
      </div>

      <div class="contact-grid">
        <div class="contact-card consult-benefits">
          <h3>What happens after you reach out</h3>
          <ul class="list-clean">
            <li>Your requirement is reviewed and routed to the relevant practice lead.</li>
            <li>A preliminary discussion is scheduled to understand scope, records and urgency.</li>
            <li>You receive a structured engagement note with next steps and expectations.</li>
          </ul>
          <div class="consult-note">
            <strong>Direct contact</strong>
            <span>Call: +91 98946 26300 &nbsp;|&nbsp; Email: support@etaxadv.com</span>
          </div>
        </div>

        <div class="contact-card">
<?php if ($consult_result && $consult_result['success']): ?>
          <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
          <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

          <form method="post" action="<?= htmlspecialchars(site_href('/team.php')) ?>#consult">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="team_consult">
            <input type="hidden" name="source_page" value="/team.php">
            <div class="form-grid">
              <div class="field">
                <label for="team_name">Name</label>
                <input class="input" id="team_name" name="name" required />
              </div>
              <div class="field">
                <label for="team_mobile">Mobile</label>
                <input class="input" id="team_mobile" name="mobile" required />
              </div>
              <div class="field">
                <label for="team_email">Email</label>
                <input class="input" id="team_email" name="email" type="email" required />
              </div>
              <div class="field">
                <label for="team_org">Organisation</label>
                <input class="input" id="team_org" name="organisation" />
              </div>
              <div class="field">
                <label for="team_service">Service Required</label>
                <input class="input" id="team_service" name="service" placeholder="GST, income tax, labour, company, bookkeeping, etc." required />
              </div>
              <div class="field">
                <label for="team_time">Preferred Consultation Time</label>
                <input class="input" id="team_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date & time" />
              </div>
              <div class="field full-span">
                <label for="team_msg">Brief Requirement</label>
                <textarea class="input" id="team_msg" name="message" required></textarea>
              </div>
              <div class="field full-span">
                <button class="btn btn-primary" type="submit">Submit Enquiry</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <?php require_once __DIR__ . '/includes/trust-badges.php'; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
