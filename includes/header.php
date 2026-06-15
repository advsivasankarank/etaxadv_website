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
  ['href' => '/services.php', 'label' => 'Services', 'match' => ['services.php', 'digital-signature.php', 'gst-consultant-puducherry.php', 'gst-consultant-chennai.php', 'income-tax-consultant-puducherry.php', 'income-tax-consultant-chennai.php', 'tds-return-filing.php', 'labour-law-compliance.php', 'labour-law-hr-compliance.php', 'roc-company-compliance.php', 'trust-society-registration.php', 'trust-ngo-advisory.php', 'manufacturing-compliance.php', 'litigation-representation.php', 'project-report-cma.php']],
  ['href' => '/index.php#technology-driven-advisory', 'label' => 'Products', 'match' => ['ekanakan.php', 'e-task.php', 'tools.php', 'e-hr.php', 'e-pani.php', 'salpro.php', 'etax-academy.php']],
  ['href' => '/index.php#who-we-advise', 'label' => 'Industries', 'match' => []],
  ['href' => '/index.php#insights-updates', 'label' => 'Resources', 'match' => []],
  ['href' => '/about.php', 'label' => 'About', 'match' => ['about.php', 'team.php', 'why-choose-us.php', 'ks-sivasankaran.php', 'success-stories.php', 'faq.php']],
  ['href' => '/contact.php', 'label' => 'Contact', 'match' => ['contact.php']],
];

$service_mega_menu = [
  [
    'heading' => 'Tax Services',
    'items' => [
      ['href' => '/income-tax-consultant-puducherry.php', 'title' => 'Income Tax', 'description' => 'Advisory, planning and representation for businesses and promoters.'],
      ['href' => '/gst-consultant-puducherry.php', 'title' => 'GST', 'description' => 'Return, review and notice support for evolving GST obligations.'],
      ['href' => '/tds-return-filing.php', 'title' => 'TDS & Payroll', 'description' => 'Structured withholding, reconciliations and payroll-linked compliance.'],
      ['href' => '/litigation-representation.php', 'title' => 'Tax Litigation', 'description' => 'Replies, appeals and coordinated tax matter handling.'],
    ],
  ],
  [
    'heading' => 'Business Services',
    'items' => [
      ['href' => '/ekanakan.php', 'title' => 'Accounting', 'description' => 'Bookkeeping visibility through disciplined reporting and controls.'],
      ['href' => '/salpro.php', 'title' => 'Payroll & Salary', 'description' => 'Payroll execution linked to tax and labour compliance.'],
      ['href' => '/roc-company-compliance.php', 'title' => 'Registrations', 'description' => 'Business, tax and regulatory registrations under one advisory desk.'],
      ['href' => '/roc-company-compliance.php', 'title' => 'Compliance', 'description' => 'Company, LLP and recurring statutory compliance support.'],
    ],
  ],
  [
    'heading' => 'Legal Services',
    'items' => [
      ['href' => '/labour-law-hr-compliance.php', 'title' => 'Labour Law & HR', 'description' => 'ESI, PF, contract labour, factory act and employer compliance advisory.'],
      ['href' => '/roc-company-compliance.php', 'title' => 'Corporate Law', 'description' => 'Entity governance, records and statutory decision support.'],
      ['href' => '/trust-ngo-advisory.php', 'title' => 'Trust & NGO', 'description' => 'Registration, FCRA, 12A/80G and governance for charitable institutions.'],
      ['href' => '/litigation-representation.php', 'title' => 'Litigation', 'description' => 'Matter preparation, responses and professional representation support.'],
    ],
  ],
  [
    'heading' => 'Industry Solutions',
    'items' => [
      ['href' => '/manufacturing-compliance.php', 'title' => 'Manufacturing', 'description' => 'Factory compliance, GST input credit, labour law and inspection readiness.'],
      ['href' => '/project-report-cma.php', 'title' => 'Project Reports', 'description' => 'CMA data, DPR and bank loan documentation support.'],
      ['href' => '/digital-signature.php', 'title' => 'Digital Signature', 'description' => 'DSC issuance and renewal assistance.'],
      ['href' => '/team/ks-sivasankaran.php', 'title' => 'Our Team', 'description' => 'Meet our experienced professionals and practice leads.'],
    ],
  ],
];

$product_mega_menu = [
  ['href' => '/e-pani.php', 'title' => 'e-Pani', 'subtitle' => 'Office Management Suite', 'description' => 'Workflow visibility, task routing and service delivery control.'],
  ['href' => '/e-hr.php', 'title' => 'e-HR', 'subtitle' => 'HR & Compliance Platform', 'description' => 'Labour law support, payroll coordination and HR compliance discipline.'],
  ['href' => '/ekanakan.php', 'title' => 'e-Kanakan', 'subtitle' => 'Bookkeeping Automation', 'description' => 'Structured accounting execution with management-ready outputs.'],
  ['href' => '/salpro.php', 'title' => 'SalPro', 'subtitle' => 'Salary Tax Planning', 'description' => 'Salary tax planning support aligned to payroll and TDS handling.'],
  ['href' => '/etax-academy.php', 'title' => 'E Tax Academy', 'subtitle' => 'Professional Training', 'description' => 'Knowledge-led training and process awareness for client teams.'],
];

$industry_links = [
  ['href' => '/index.php#who-we-advise', 'label' => 'MSMEs'],
  ['href' => '/index.php#who-we-advise', 'label' => 'Manufacturers'],
  ['href' => '/index.php#who-we-advise', 'label' => 'Traders'],
  ['href' => '/index.php#who-we-advise', 'label' => 'Startups'],
];

$resource_links = [
  ['href' => '/index.php#insights-updates', 'label' => 'Insights & Updates'],
  ['href' => '/testimonial/', 'label' => 'Client Testimonials'],
  ['href' => '/tools.php', 'label' => 'Client Tools'],
  ['href' => '/client-support.php', 'label' => 'Client Support'],
  ['href' => '/team.php', 'label' => 'Our Team'],
  ['href' => '/why-choose-us.php', 'label' => 'Why Choose Us'],
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

$services_active = in_array($current_page, ['services.php', 'digital-signature.php', 'gst-consultant-puducherry.php', 'gst-consultant-chennai.php', 'income-tax-consultant-puducherry.php', 'income-tax-consultant-chennai.php', 'tds-return-filing.php', 'labour-law-compliance.php', 'labour-law-hr-compliance.php', 'roc-company-compliance.php', 'trust-society-registration.php', 'trust-ngo-advisory.php', 'manufacturing-compliance.php', 'litigation-representation.php', 'project-report-cma.php'], true);
$products_active = in_array($current_page, ['ekanakan.php', 'e-task.php', 'tools.php', 'e-hr.php', 'e-pani.php', 'salpro.php', 'etax-academy.php'], true);

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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet" />
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
        <span class="brand-name"><span class="brand-name-full">E Tax Advisors Private Limited</span><span class="brand-name-short">E Tax Advisors</span></span>
        <span class="brand-tag">Advocates | Tax Consultants | Compliance Advisors</span>
      </div>
    </a>

    <nav class="nav" aria-label="Primary">
      <div class="nav-item nav-item-has-panel">
        <button
          class="nav-link nav-panel-toggle<?= $services_active ? ' is-active' : '' ?>"
          type="button"
          aria-expanded="false"
          aria-controls="servicesMegaMenu"
          data-nav-panel="servicesMegaMenu"
        >
          <span>Services</span>
          <span class="nav-caret" aria-hidden="true">
            <svg viewBox="0 0 16 16" focusable="false"><path d="M4 6.25 8 10l4-3.75" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/></svg>
          </span>
        </button>
        <div class="mega-menu" id="servicesMegaMenu">
          <div class="mega-menu-grid">
<?php foreach ($service_mega_menu as $column): ?>
            <section class="mega-menu-column">
              <h3><?= htmlspecialchars($column['heading']) ?></h3>
<?php foreach ($column['items'] as $service): ?>
              <a class="mega-menu-link" href="<?= htmlspecialchars(site_href($service['href'])) ?>">
                <strong><?= htmlspecialchars($service['title']) ?></strong>
                <span><?= htmlspecialchars($service['description']) ?></span>
              </a>
<?php endforeach; ?>
            </section>
<?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="nav-item nav-item-has-panel">
        <button
          class="nav-link nav-panel-toggle<?= $products_active ? ' is-active' : '' ?>"
          type="button"
          aria-expanded="false"
          aria-controls="productsMegaMenu"
          data-nav-panel="productsMegaMenu"
        >
          <span>Products</span>
          <span class="nav-caret" aria-hidden="true">
            <svg viewBox="0 0 16 16" focusable="false"><path d="M4 6.25 8 10l4-3.75" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/></svg>
          </span>
        </button>
        <div class="mega-menu mega-menu-products" id="productsMegaMenu">
          <div class="product-menu-grid">
<?php foreach ($product_mega_menu as $product): ?>
            <a class="product-card" href="<?= htmlspecialchars(site_href($product['href'])) ?>">
              <span class="product-card-title"><?= htmlspecialchars($product['title']) ?></span>
              <span class="product-card-subtitle"><?= htmlspecialchars($product['subtitle']) ?></span>
              <span class="product-card-description"><?= htmlspecialchars($product['description']) ?></span>
              <span class="product-card-link">Learn more</span>
            </a>
<?php endforeach; ?>
          </div>
        </div>
      </div>

<?php foreach ($nav_items as $item): ?>
      <?= render_nav_link($item, $current_page) . PHP_EOL ?>
<?php endforeach; ?>
    </nav>

    <div class="header-actions">
      <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Book Consultation</a>
    </div>

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
        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/services.php')) ?>">Services</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/income-tax-consultant-puducherry.php')) ?>">Income Tax</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/gst-consultant-puducherry.php')) ?>">GST</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/tds-return-filing.php')) ?>">TDS & Payroll</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/litigation-representation.php')) ?>">Tax Litigation</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/roc-company-compliance.php')) ?>">Company / LLP Compliance</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/labour-law-hr-compliance.php')) ?>">Labour Law & HR</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/trust-ngo-advisory.php')) ?>">Trust & NGO Advisory</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/manufacturing-compliance.php')) ?>">Manufacturing Compliance</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/project-report-cma.php')) ?>">Project Reports & CMA</a>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href('/digital-signature.php')) ?>">Digital Signature</a>

        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/index.php#technology-driven-advisory')) ?>">Products</a>
<?php foreach ($product_mega_menu as $product): ?>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href($product['href'])) ?>"><?= htmlspecialchars($product['title']) ?></a>
<?php endforeach; ?>

        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/index.php#who-we-advise')) ?>">Industries</a>
<?php foreach ($industry_links as $industry_link): ?>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href($industry_link['href'])) ?>"><?= htmlspecialchars($industry_link['label']) ?></a>
<?php endforeach; ?>

        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/index.php#insights-updates')) ?>">Resources</a>
<?php foreach ($resource_links as $resource_link): ?>
        <a class="mobile-secondary-link" href="<?= htmlspecialchars(site_href($resource_link['href'])) ?>"><?= htmlspecialchars($resource_link['label']) ?></a>
<?php endforeach; ?>

        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/about.php')) ?>">About</a>
        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/contact.php')) ?>">Contact</a>
        <a class="mobile-primary-link" href="<?= htmlspecialchars(site_href('/client-support.php')) ?>">Client Login</a>

        <div class="mobile-nav-actions">
          <a class="btn btn-outline" href="tel:+919894626300">Call Now</a>
          <a class="btn btn-primary" href="<?= htmlspecialchars(site_href('/contact.php#consult')) ?>">Book Consultation</a>
        </div>
      </nav>
    </div>
  </div>
</header>
