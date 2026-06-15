<?php
if (!isset($page_title)) {
  $page_title = "E Tax Advisors Private Limited";
}

if (!isset($page_description)) {
  $page_description = "Premium tax, legal, compliance and bookkeeping advisory services for businesses, founders and professional entities.";
}

if (!isset($page_path)) {
  $page_path = $_SERVER['PHP_SELF'] ?? '/index.php';
}

$page_description = trim($page_description);
$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$site_root = '';
$project_root = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__));
$document_root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ($_SERVER['DOCUMENT_ROOT'] ?? ''));

if ($document_root !== '' && str_starts_with($project_root, $document_root)) {
  $computed_root = substr($project_root, strlen($document_root));
  $computed_root = str_replace('\\', '/', $computed_root);
  $site_root = $computed_root === '' ? '' : rtrim($computed_root, '/');
}

$nav_items = [
  ['href' => '/index.php', 'label' => 'Home', 'match' => ['index.php']],
  ['href' => '/about.php', 'label' => 'About', 'match' => ['about.php']],
  ['href' => '/services.php', 'label' => 'Services', 'match' => ['services.php', 'digital-signature.php', 'ekanakan.php']],
  ['href' => '/tools.php', 'label' => 'Tools', 'match' => ['tools.php', 'e-task.php']],
  ['href' => '/client-support.php', 'label' => 'Support', 'match' => ['client-support.php']],
  ['href' => '/contact.php', 'label' => 'Contact', 'match' => ['contact.php']],
];

$service_quick_links = [
  ['href' => '/services.php#income-tax', 'label' => 'Income Tax'],
  ['href' => '/services.php#gst', 'label' => 'GST'],
  ['href' => '/services.php#company', 'label' => 'Company / LLP'],
  ['href' => '/ekanakan.php', 'label' => 'e-Kanakan'],
];

if (!function_exists('app_href')) {
  function app_href(string $path): string {
    global $site_root;

    return ($site_root !== '' ? $site_root : '') . $path;
  }
}

if (!function_exists('site_href')) {
  function site_href(string $path): string {
    return app_href($path);
  }
}

function page_url(string $path): string {
  if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
    return $path;
  }

  return 'https://www.etaxadv.com' . $path;
}

function render_nav_link(array $item, string $current_page): string {
  $is_active = in_array($current_page, $item['match'], true);
  $classes = trim('nav-link' . ($is_active ? ' is-active' : ''));
  $class_attr = ' class="' . htmlspecialchars($classes) . '"';
  $aria_current = $is_active ? ' aria-current="page"' : '';

  return sprintf(
    '<a href="%s"%s%s>%s</a>',
    htmlspecialchars(site_href($item['href'])),
    $class_attr,
    $aria_current,
    htmlspecialchars($item['label'])
  );
}

$organization_schema = [
  '@context' => 'https://schema.org',
  '@type' => 'ProfessionalService',
  'name' => 'E Tax Advisors Private Limited',
  'url' => 'https://www.etaxadv.com/',
  'telephone' => '+91-98946-26300',
  'email' => 'support@etaxadv.com',
  'address' => [
    '@type' => 'PostalAddress',
    'streetAddress' => 'No. 234, I Floor, Lawspet Main Road, Pakkamudayanpet, Lawspet',
    'addressLocality' => 'Puducherry',
    'postalCode' => '605008',
    'addressCountry' => 'IN',
  ],
  'areaServed' => 'India',
  'serviceType' => [
    'Income Tax Advisory',
    'GST Compliance',
    'TDS and Payroll Compliance',
    'Company and LLP Compliance',
    'Bookkeeping and Accounting Advisory',
  ],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_description) ?>" />
  <meta name="robots" content="index,follow" />
  <link rel="canonical" href="<?= htmlspecialchars(page_url($page_path)) ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>" />
  <meta property="og:url" content="<?= htmlspecialchars(page_url($page_path)) ?>" />
  <meta property="og:site_name" content="E Tax Advisors Private Limited" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($page_description) ?>" />
  <link rel="stylesheet" href="<?= htmlspecialchars(site_href('/assets/css/style.css')) ?>" />
<?php if (!empty($extra_css)): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($extra_css) ?>" />
<?php endif; ?>
  <script type="application/ld+json"><?= json_encode($organization_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>

<a class="skip-link" href="#main-content">Skip to content</a>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-copy">
      <span>Tax, legal and compliance advisory for growth-focused businesses.</span>
    </div>
    <div class="topbar-links">
      <a href="tel:+919894626300">Call: +91 98946 26300</a>
      <a href="mailto:support@etaxadv.com">support@etaxadv.com</a>
      <span>Puducherry, India</span>
    </div>
  </div>
</div>

<header class="header">
  <div class="container header-shell">
    <a class="brand" href="<?= htmlspecialchars(site_href('/index.php')) ?>">
      <img class="brand-logo" src="<?= htmlspecialchars(site_href('/assets/img/logo.png')) ?>" alt="E Tax Advisors Logo" />
      <div class="brand-copy">
        <span class="brand-name">E Tax Advisors Private Limited</span>
        <span class="brand-tag">Tax &middot; Legal &middot; Compliance &middot; Advisory</span>
      </div>
    </a>

    <nav class="nav" aria-label="Primary">
<?php foreach ($nav_items as $item): ?>
      <?= render_nav_link($item, $current_page) . PHP_EOL ?>
<?php endforeach; ?>
      <div class="nav-group">
        <button
          class="nav-link nav-services-toggle"
          type="button"
          aria-expanded="false"
          aria-controls="serviceQuickLinks"
          onclick="toggleQuickLinks()"
        >
          Solutions
          <span class="caret"></span>
        </button>
        <div class="quick-links" id="serviceQuickLinks">
<?php foreach ($service_quick_links as $service_link): ?>
          <a href="<?= htmlspecialchars(site_href($service_link['href'])) ?>"><?= htmlspecialchars($service_link['label']) ?></a>
<?php endforeach; ?>
        </div>
      </div>
    </nav>

    <div class="header-actions">
      <a class="header-link" href="<?= htmlspecialchars(site_href('/tools.php')) ?>">Tools</a>
      <a class="btn btn-outline btn-sm" href="https://wa.me/919500601119" target="_blank" rel="noopener">WhatsApp</a>
      <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Free Consultation</a>
    </div>

    <button
      class="hamburger"
      type="button"
      aria-expanded="false"
      aria-controls="mobileNav"
      aria-label="Open navigation"
      onclick="toggleMenu()"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>

  <div class="container">
    <nav class="mobileNav" id="mobileNav" aria-label="Mobile">
<?php foreach ($nav_items as $item): ?>
      <?= render_nav_link($item, $current_page) . PHP_EOL ?>
<?php endforeach; ?>
      <div class="mobile-subtitle">Service Quick Links</div>
<?php foreach ($service_quick_links as $service_link): ?>
      <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href($service_link['href'])) ?>"><?= htmlspecialchars($service_link['label']) ?></a>
<?php endforeach; ?>
      <div class="mobile-nav-actions">
        <a class="btn btn-outline" href="tel:+919894626300">Call Now</a>
        <a class="btn btn-primary" href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Book Consultation</a>
      </div>
    </nav>
  </div>
</header>
