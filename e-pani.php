<?php
require_once __DIR__ . '/includes/contact-handler.php';
$page_title = "e-Pani – Office Management & Workflow Suite | E Tax Advisors";
$page_description = "e-Pani is an office management suite for workflow visibility, task routing, service delivery control and operational coordination.";
$page_path = '/e-pani.php';
$consult_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'product_consult') {
  $consult_result = contact_process_submission();
}
contact_register_form();
require_once __DIR__ . '/includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">e-Pani</div>
        <h1>An office management and workflow suite for teams that need task clarity, service visibility and operational control.</h1>
        <p>
          e-Pani is the office management suite from E Tax Advisors Private Limited. It is designed for organisations that
          want structured task routing, workflow tracking, service delivery monitoring and operational coordination across
          departments and locations.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#consult">Request Demo</a>
          <a class="btn btn-outline" href="contact.php#consult">Book Consultation</a>
        </div>
        <div class="proof-line">
          <span class="proof-chip">Workflow visibility</span>
          <span class="proof-chip">Task routing & tracking</span>
          <span class="proof-chip">Service delivery control</span>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-grid">
          <div class="hero-metric">
            <strong>Task Management</strong>
            <span>Assignment, prioritisation, deadline tracking and closure validation for every operational task.</span>
          </div>
          <div class="hero-metric">
            <strong>Workflow Routing</strong>
            <span>Structured movement of tasks across teams, departments and approval stages with clear ownership.</span>
          </div>
          <div class="hero-metric">
            <strong>Service Delivery Tracking</strong>
            <span>Dashboards showing active engagements, pending items, turnaround times and delivery status.</span>
          </div>
          <div class="hero-metric">
            <strong>Operational Coordination</strong>
            <span>Centralised communication around tasks, document handoffs and service milestones.</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Platform Features</p>
        <h2 class="section-title">e-Pani brings structure and visibility to office and service operations.</h2>
      </div>

      <div class="grid-3">
        <article class="card card-muted">
          <div class="feature-icon">TSK</div>
          <h3>Task & Workflow Engine</h3>
          <p>Create, assign, prioritise and track tasks with deadlines, dependencies, approvals and closure checklists built into each workflow.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">RT</div>
          <h3>Real-time Dashboards</h3>
          <p>Role-based dashboards showing workload, pending items, overdue tasks, service delivery metrics and team performance.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">DOC</div>
          <h3>Document & Data Handoff</h3>
          <p>Structured intake, version control and routing of documents between teams with audit trails for every handoff.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">CLN</div>
          <h3>Client Engagement View</h3>
          <p>Consolidated view of client engagements, open items, service delivery status and communication history in one place.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">NOT</div>
          <h3>Notifications & Escalation</h3>
          <p>Automated reminders for pending tasks, escalation triggers for overdue items and alerts for critical workflow events.</p>
        </article>
        <article class="card card-muted">
          <div class="feature-icon">RPT</div>
          <h3>Reporting & Analytics</h3>
          <p>Operational reports covering turnaround times, team productivity, service delivery trends and bottleneck identification.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Why e-Pani</p>
        <h2 class="section-title">Designed for organisations that need operational rigour without complexity.</h2>
      </div>

      <div class="grid-2">
        <article class="card">
          <h3>For management</h3>
          <ul class="list-clean">
            <li>Complete visibility into what each team is working on and where bottlenecks exist</li>
            <li>Data-driven decisions based on delivery metrics and workload distribution</li>
            <li>Standardised service delivery across multiple teams or branch locations</li>
            <li>Reduced dependency on verbal coordination and informal follow-ups</li>
            <li>Audit trail for every task, decision and service milestone</li>
          </ul>
        </article>
        <article class="card">
          <h3>For operations teams</h3>
          <ul class="list-clean">
            <li>Clear task ownership with defined deadlines and priority levels</li>
            <li>Structured document handoff reduces data loss and rework</li>
            <li>Automated escalations ensure no critical item falls through the cracks</li>
            <li>Role-based access controls information visibility by team and function</li>
            <li>Integrated communication keeps discussions attached to the relevant task or engagement</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">How It Works</p>
        <h2 class="section-title">Four steps to operational visibility and control.</h2>
      </div>

      <div class="process-timeline">
        <article class="timeline-step">
          <span class="timeline-number">1</span>
          <h3>Workspace setup and team onboarding</h3>
          <p>Teams, roles, service lines, task categories and workflow templates are configured based on your organisational structure.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">2</span>
          <h3>Task creation and assignment</h3>
          <p>Tasks are created manually or through workflow triggers, assigned to specific team members with clear deadlines and documentation.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">3</span>
          <h3>Execution and tracking</h3>
          <p>Team members update task status, attach deliverables and flag issues. Managers track progress through real-time dashboards.</p>
        </article>
        <article class="timeline-step">
          <span class="timeline-number">4</span>
          <h3>Review and closure</h3>
          <p>Completed tasks go through review and approval stages. Deliverables are handed off, tasks are closed and performance metrics are updated.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Screenshots</p>
        <h2 class="section-title">A preview of the e-Pani workflow suite interface.</h2>
      </div>

      <div class="grid-3">
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>e-Pani Dashboard View</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Task Workflow Board</span>
        </div>
        <div class="card card-muted" style="aspect-ratio:16/10;display:flex;align-items:center;justify-content:center;background:var(--surface-muted);color:var(--muted);font-weight:600;">
          <span>Service Delivery View</span>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Pricing</p>
        <h2 class="section-title">Predictable pricing for teams of every size.</h2>
      </div>

      <div class="card" style="max-width:480px;margin:0 auto;text-align:center;padding:48px 32px;">
        <h3 style="margin-bottom:12px;">Contact for Pricing</h3>
        <p style="color:var(--muted);margin-bottom:24px;">
          e-Pani pricing depends on team size, number of workflows and required modules. Contact us for a custom quote and demo.
        </p>
        <a class="btn btn-primary" href="#consult">Request Pricing</a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">FAQs</p>
        <h2 class="section-title">Common questions about e-Pani.</h2>
      </div>

      <div class="faq-list">
        <details class="faq-item">
          <summary>What types of organisations can use e-Pani?</summary>
          <p>e-Pani is suitable for professional service firms, compliance teams, back-office operations, legal departments, accounting firms and any organisation that manages multiple tasks, workflows and service deliveries across teams.</p>
        </details>
        <details class="faq-item">
          <summary>Can e-Pani be customised for our specific workflows?</summary>
          <p>Yes. e-Pani supports custom workflow templates, task categories, approval stages and role-based access. Our implementation team works with you to map your existing processes into the platform.</p>
        </details>
        <details class="faq-item">
          <summary>Does e-Pani include client access or portal features?</summary>
          <p>Yes. e-Pani includes a client engagement view where clients can track the status of their service requests, view deliverables and communicate with the service team through structured channels.</p>
        </details>
        <details class="faq-item">
          <summary>Is e-Pani cloud-based or on-premise?</summary>
          <p>e-Pani is cloud-based and accessible through standard web browsers. For organisations with specific data residency or security requirements, on-premise deployment options are available.</p>
        </details>
        <details class="faq-item">
          <summary>How long does it take to implement e-Pani?</summary>
          <p>Typical implementation takes 3–6 weeks depending on the complexity of workflows, number of teams and volume of existing data to migrate. A dedicated project manager is assigned for the deployment.</p>
        </details>
        <details class="faq-item">
          <summary>What kind of support is included with e-Pani?</summary>
          <p>e-Pani includes email and phone support during business hours, a knowledge base with documentation and video guides, and quarterly review calls to optimise workflow configurations.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="consult">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Get Started</p>
        <h2 class="section-title">Ready to bring operational visibility to your team?</h2>
      </div>

      <div class="contact-card consult-form-card" style="max-width:720px;margin:0 auto;">
        <p>Fill in your details and our team will reach out with a personalised demo and pricing for e-Pani.</p>

<?php if ($consult_result && $consult_result['success']): ?>
        <?= contact_render_success($consult_result['message']) ?>
<?php elseif ($consult_result && !$consult_result['success']): ?>
        <?= contact_render_error($consult_result['error']) ?>
<?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(site_href('/e-pani.php')) ?>#consult">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="product_consult">
          <input type="hidden" name="service" value="e-Pani - Office Management & Workflow Suite">
          <input type="hidden" name="source_page" value="/e-pani.php">
          <div class="form-grid">
            <div class="field">
              <label for="pani_name">Name</label>
              <input class="input" id="pani_name" name="name" required />
            </div>
            <div class="field">
              <label for="pani_mobile">Mobile</label>
              <input class="input" id="pani_mobile" name="mobile" required />
            </div>
            <div class="field">
              <label for="pani_email">Email</label>
              <input class="input" id="pani_email" name="email" type="email" required />
            </div>
            <div class="field">
              <label for="pani_org">Organisation</label>
              <input class="input" id="pani_org" name="organisation" />
            </div>
            <div class="field">
              <label for="pani_time">Preferred Contact Time</label>
              <input class="input" id="pani_time" name="preferred_time" placeholder="Today evening / Tomorrow morning / Specific date" />
            </div>
            <div class="field full-span">
              <label for="pani_msg">Message / Requirement</label>
              <textarea class="input" id="pani_msg" name="message" placeholder="Tell us about your office management and workflow requirements..." required></textarea>
            </div>
            <div class="field full-span">
              <button class="btn btn-primary" type="submit">Submit Enquiry</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
