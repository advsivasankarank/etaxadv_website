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
  ['href' => '/services.php', 'label' => 'Services', 'match' => ['services.php', 'digital-signature.php', 'gst-consultant-puducherry.php', 'gst-consultant-chennai.php', 'income-tax-consultant-puducherry.php', 'income-tax-consultant-chennai.php', 'tds-return-filing.php', 'labour-law-compliance.php', 'labour-law-hr-compliance.php', 'roc-company-compliance.php', 'trust-society-registration.php', 'trust-ngo-advisory.php', 'manufacturing-compliance.php', 'litigation-representation.php', 'project-report-cma.php']],
  ['href' => '/fintech-tools.php', 'label' => 'Fintech Tools', 'match' => ['fintech-tools.php']],
  ['href' => '/about.php', 'label' => 'About', 'match' => ['about.php', 'team.php', 'why-choose-us.php', 'ks-sivasankaran.php', 'success-stories.php', 'faq.php']],
  ['href' => '/contact.php', 'label' => 'Contact', 'match' => ['contact.php']],
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

function render_nav_link(array $item, string $current_page, string $extra_classes = ''): string {
  $is_active = in_array($current_page, $item['match'], true);
  $classes = trim('nav-link ' . $extra_classes . ($is_active ? ' is-active' : ''));
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

require_once __DIR__ . '/security.php';
send_security_headers();

$og_image = $og_image ?? '/assets/img/og-image.jpg';
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
  <meta property="og:image" content="<?= htmlspecialchars(page_url($og_image)) ?>" />
  <meta property="og:site_name" content="E Tax Advisors Private Limited" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($page_description) ?>" />
  <meta name="twitter:image" content="<?= htmlspecialchars(page_url($og_image)) ?>" />
  <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars(site_href('/favicon.ico')) ?>" />
  <link rel="apple-touch-icon" href="<?= htmlspecialchars(site_href('/apple-touch-icon.png')) ?>" />
  <meta name="google-site-verification" content="REPLACE_WITH_GSC_CODE" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= htmlspecialchars(site_href('/assets/css/style.css')) ?>" />
<?php if (!empty($extra_css)): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($extra_css) ?>" />
<?php endif; ?>
  <script type="application/ld+json"><?= json_encode($organization_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

  <!-- Google Analytics 4 -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-XXXXXXXXXX');
  </script>
</head>
<body>

<a class="skip-link" href="#main-content">Skip to content</a>

<header class="site-header" id="siteHeader">
  <div class="container header-shell">
    <a class="brand" href="<?= htmlspecialchars(site_href('/index.php')) ?>" aria-label="E Tax Advisors home">
      <img class="brand-mark" src="<?= htmlspecialchars(site_href('/assets/img/logo.png')) ?>" alt="E Tax Advisors logo" width="44" height="44" />
      <div class="brand-copy">
        <span class="brand-name">E Tax Advisors Private Limited</span>
        <span class="brand-cin">CIN: U74120PY2015PTC003005</span>
        <span class="brand-tag">Advocates | Tax Consultants | Compliance Advisors</span>
      </div>
    </a>

    <nav class="nav" aria-label="Primary">
<?php foreach ($nav_items as $item): ?>
      <?= render_nav_link($item, $current_page) . PHP_EOL ?>
<?php endforeach; ?>
      <a class="btn btn-primary btn-sm nav-cta" href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Book Consultation</a>
    </nav>

    <button
      class="hamburger"
      type="button"
      aria-expanded="false"
      aria-controls="mobileNav"
      aria-label="Open navigation"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>

  <div class="mobile-nav-panel" id="mobileNav">
    <div class="container mobile-nav-shell">
      <nav class="mobileNav" aria-label="Mobile">
        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/index.php')) ?>">Home</a>
        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/services.php')) ?>">Services</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/income-tax-consultant-puducherry.php')) ?>">Income Tax</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/gst-consultant-puducherry.php')) ?>">GST</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/tds-return-filing.php')) ?>">TDS & Payroll</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/labour-law-hr-compliance.php')) ?>">Labour Law & HR</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/roc-company-compliance.php')) ?>">Corporate Compliance</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/trust-ngo-advisory.php')) ?>">Trust & NGO Advisory</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/litigation-representation.php')) ?>">Litigation Support</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/project-report-cma.php')) ?>">Project Reports</a>

        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/fintech-tools.php')) ?>">Fintech Tools</a>

        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/about.php')) ?>">About</a>
        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Contact</a>

        <div class="mobile-nav-actions">
          <a class="btn btn-primary" href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Book Consultation</a>
        </div>
      </nav>
    </div>
  </div>
</header>
