<?php
if (!isset($tb_page_path)) {
  $tb_page_path = $_SERVER['PHP_SELF'] ?? '/index.php';
}
$tb_badges = [
  ['number' => '3000+', 'label' => 'Clients Served'],
  ['number' => '15+', 'label' => 'Years Experience'],
  ['number' => '50+', 'label' => 'Professionals'],
  ['number' => '8', 'label' => 'Service Domains'],
  ['number' => 'Dedicated', 'label' => 'Compliance Team'],
  ['number' => 'Confidential', 'label' => '&amp; Secure Handling'],
];
$tb_rating = 0;
$tb_total = 0;
if (function_exists('testimonial_get_summary')) {
  $tb_summary = testimonial_get_summary();
  $tb_rating = (float)$tb_summary['average_rating'];
  $tb_total = (int)$tb_summary['total_reviews'];
}
$tb_featured = [];
if (function_exists('testimonial_get_featured')) {
  $tb_featured = testimonial_get_featured(3);
}
?>
<div class="section">
  <div class="container">
    <div class="section-header">
      <p class="section-label">Trust &amp; Credentials</p>
      <h2 class="section-title">Backed by numbers that reflect consistent professional delivery.</h2>
    </div>

    <div class="authority-ribbon">
<?php foreach ($tb_badges as $badge): ?>
      <div class="authority-pill">
        <strong><?= $badge['number'] ?></strong>
        <span><?= $badge['label'] ?></span>
      </div>
<?php endforeach; ?>
    </div>

<?php if ($tb_featured): ?>
    <div style="margin-top: 36px;">
      <div class="section-header">
        <p class="section-label">What Clients Say</p>
        <?php if ($tb_rating > 0): ?>
        <p class="section-intro">Average rating: <strong><?= number_format($tb_rating, 1) ?>/5</strong> across <strong><?= $tb_total ?></strong> published reviews.</p>
        <?php endif; ?>
      </div>
      <div class="testimonial-grid">
<?php foreach ($tb_featured as $tb_item): ?>
        <article class="testimonial-card premium">
          <div class="testimonial-meta">
            <span class="verified-badge">Verified Client</span>
            <span class="star-rating"><?= testimonial_issue_stars((int)$tb_item['rating']) ?></span>
          </div>
          <span class="testimonial-type"><?= htmlspecialchars($tb_item['service_availed']) ?></span>
          <p><?= htmlspecialchars($tb_item['testimonial_text']) ?></p>
          <div class="testimonial-footer">
            <strong><?= htmlspecialchars($tb_item['client_name']) ?></strong>
            <span><?= htmlspecialchars($tb_item['company_name']) ?><?php if ($tb_item['city']): ?>, <?= htmlspecialchars($tb_item['city']) ?><?php endif; ?></span>
          </div>
        </article>
<?php endforeach; ?>
      </div>
      <div class="section-actions">
        <a class="btn btn-outline" href="<?= htmlspecialchars(site_href('/testimonial/')) ?>">View All Testimonials</a>
        <a class="btn btn-primary" href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Share Your Experience</a>
      </div>
    </div>
<?php endif; ?>
  </div>
</div>
