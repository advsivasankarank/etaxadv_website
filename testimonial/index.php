<?php
require __DIR__ . '/config.php';

$page_title = "Client Testimonials | E Tax Advisors";
$page_description = "Read approved client testimonials and share your service experience with E Tax Advisors Private Limited.";
$page_path = '/testimonial/';

$summary = testimonial_get_summary();
$testimonials = testimonial_get_featured(24);
$flashOk = $_GET['submitted'] ?? '';
$flashErr = $_GET['error'] ?? '';

testimonial_register_form_render();
$csrf = testimonial_csrf_token();

include __DIR__ . '/../includes/header.php';
?>

<main id="main-content">
  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="eyebrow">Client Testimonials</div>
        <h1>Client feedback reviewed before publication, not auto-posted.</h1>
        <p>
          We publish testimonials only after internal review and only where the client has granted permission to publish.
          This keeps the review section professional, trustworthy and aligned with the standards expected of an advisory firm.
        </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#share-review">Share Your Experience</a>
          <a class="btn btn-outline" href="/contact.php#consult">Book Consultation</a>
        </div>
      </div>

      <div class="hero-visual">
        <div class="hero-grid">
          <div class="hero-metric">
            <strong><?= h(number_format($summary['average_rating'], 1)) ?>/5</strong>
            <span>Average approved rating</span>
          </div>
          <div class="hero-metric">
            <strong><?= h((string)$summary['total_reviews']) ?></strong>
            <span>Approved public reviews</span>
          </div>
          <div class="hero-metric">
            <strong>Verified</strong>
            <span>Published only after moderation and publish permission</span>
          </div>
          <div class="hero-metric">
            <strong>Private First</strong>
            <span>No testimonial is auto-published without review</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Approved Reviews</p>
        <h2 class="section-title">Testimonials from clients who permitted publication after review.</h2>
      </div>

<?php if ($testimonials): ?>
      <div class="testimonial-grid">
<?php foreach ($testimonials as $item): ?>
        <article class="testimonial-card premium">
          <div class="testimonial-meta">
            <span class="verified-badge">Verified Client</span>
            <span class="star-rating"><?= h(testimonial_issue_stars((int)$item['rating'])) ?></span>
          </div>
          <p><?= h($item['testimonial_text']) ?></p>
          <div class="testimonial-footer">
            <strong><?= h($item['client_name']) ?></strong>
            <span><?= h($item['company_name']) ?><?php if ($item['city']): ?>, <?= h($item['city']) ?><?php endif; ?></span>
            <span><?= h($item['service_availed']) ?></span>
          </div>
        </article>
<?php endforeach; ?>
      </div>
<?php else: ?>
      <div class="card">
        <h3>Testimonials will appear here after approval.</h3>
        <p>No published testimonials are available yet. Client submissions remain private until reviewed and approved.</p>
      </div>
<?php endif; ?>
    </div>
  </section>

  <section class="section section-muted" id="share-review">
    <div class="container contact-grid">
      <div class="contact-card">
        <h3>Share your experience</h3>
        <p>Please use this form only if you are an actual client or authorised representative of a client entity.</p>
        <ul class="list-clean">
          <li>All submissions go through moderation before publication.</li>
          <li>Only approved reviews with publish permission are shown publicly.</li>
          <li>Contact details are collected to verify the submission privately.</li>
        </ul>
      </div>

      <div class="contact-card consult-form-card">
        <h3>Client Testimonial Submission</h3>

<?php if ($flashOk === '1'): ?>
        <div class="alert ok">Your testimonial has been submitted for review. It will not be published until approved.</div>
<?php endif; ?>
<?php if ($flashErr !== ''): ?>
        <div class="alert err"><?= h($flashErr) ?></div>
<?php endif; ?>

        <form method="post" action="/testimonial/submit.php">
          <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>" />
          <input type="text" name="website" class="sr-only" tabindex="-1" autocomplete="off" />

          <div class="form-grid">
            <div class="field">
              <label for="t_name">Name</label>
              <input class="input" id="t_name" name="name" required />
            </div>
            <div class="field">
              <label for="t_company">Company Name</label>
              <input class="input" id="t_company" name="company_name" required />
            </div>
            <div class="field">
              <label for="t_city">City</label>
              <input class="input" id="t_city" name="city" required />
            </div>
            <div class="field">
              <label for="t_mobile">Mobile</label>
              <input class="input" id="t_mobile" name="mobile" required />
            </div>
            <div class="field">
              <label for="t_email">Email</label>
              <input class="input" id="t_email" name="email" type="email" required />
            </div>
            <div class="field">
              <label for="t_service">Service Availed</label>
              <input class="input" id="t_service" name="service_availed" required />
            </div>
            <div class="field">
              <label for="t_rating">Rating</label>
              <select class="input" id="t_rating" name="rating" required>
                <option value="">Select Rating</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
              </select>
            </div>
            <div class="field">
              <label for="t_publish">Publish Permission</label>
              <select class="input" id="t_publish" name="publish_permission" required>
                <option value="">Select Permission</option>
                <option value="1">Yes, you may publish it</option>
                <option value="0">No, keep it private</option>
              </select>
            </div>
            <div class="field full-span">
              <label for="t_message">Testimonial</label>
              <textarea class="input" id="t_message" name="testimonial" required></textarea>
            </div>
            <div class="field full-span">
              <button class="btn btn-primary" type="submit">Submit for Review</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
