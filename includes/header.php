<?php
if (!isset($page_title)) {
  $page_title = "E Tax Advisors Private Limited";
}

$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$site_root = $script_dir === '/' || $script_dir === '.' ? '' : rtrim($script_dir, '/');

$nav_items = [
  ['href' => '/index.php', 'label' => 'Home', 'match' => ['index.php']],
  ['href' => '/about.php', 'label' => 'About', 'match' => ['about.php']],
  ['href' => '/services.php', 'label' => 'Services', 'match' => ['services.php']],
  ['href' => '/ebal/ebal.php', 'label' => 'e-BAL', 'match' => ['ebal.php']],
  ['href' => '/ekanakan.php', 'label' => 'e-Kanakan', 'match' => ['ekanakan.php'], 'class' => 'nav-ekanakan-text'],
  ['href' => '/digital-signature.php', 'label' => 'DSC', 'match' => ['digital-signature.php']],
  ['href' => '/tools.php', 'label' => 'Tools', 'match' => ['tools.php']],
  ['href' => '/client-support.php', 'label' => 'Support', 'match' => ['client-support.php']],
];

function site_href(string $path): string {
  global $site_root;

  return ($site_root !== '' ? $site_root : '') . $path;
}

function render_nav_link(array $item, string $current_page): string {
  $is_active = in_array($current_page, $item['match'], true);
  $classes = trim(($item['class'] ?? '') . ($is_active ? ' is-active' : ''));
  $class_attr = $classes !== '' ? ' class="' . htmlspecialchars($classes) . '"' : '';
  $aria_current = $is_active ? ' aria-current="page"' : '';

  return sprintf(
    '<a href="%s"%s%s>%s</a>',
    htmlspecialchars(site_href($item['href'])),
    $class_attr,
    $aria_current,
    htmlspecialchars($item['label'])
  );
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="<?= htmlspecialchars(site_href('/assets/css/style.css')) ?>" />
<?php if (!empty($extra_css)): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($extra_css) ?>" />
<?php endif; ?>
</head>
<body>

<a class="skip-link" href="#main-content">Skip to content</a>

<div class="topbar">
  <div class="container">
    <div class="row">
      <div class="left">
        <span>CEO: <a href="tel:+919894626300">98946 26300</a></span>
        <span>Email: <a href="mailto:support@etaxadv.com">support@etaxadv.com</a></span>
      </div>
      <div class="right">
        <span>Puducherry</span>
        <span>Professional Services</span>
      </div>
    </div>
  </div>
</div>

<header class="header">
  <div class="container nav-shell">
    <a class="brand" href="<?= htmlspecialchars(site_href('/index.php')) ?>">
      <img class="brand-logo" src="<?= htmlspecialchars(site_href('/assets/img/logo.png')) ?>" alt="E Tax Advisors Logo" />
      <div class="brand-text">
        <div class="company">E Tax Advisors Private Limited</div>
        <div class="tagline">Tax &middot; Compliance &middot; Finance &middot; Litigation</div>
      </div>
    </a>

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

    <nav class="nav" aria-label="Primary">
<?php foreach ($nav_items as $item): ?>
      <?= render_nav_link($item, $current_page) . PHP_EOL ?>
<?php endforeach; ?>
      <a href="<?= htmlspecialchars(site_href('/contact.php')) ?>" class="nav-outline<?= $current_page === 'contact.php' ? ' is-active' : '' ?>"<?= $current_page === 'contact.php' ? ' aria-current="page"' : '' ?>>Contact</a>
      <a href="<?= htmlspecialchars(site_href('/e-task.php')) ?>" class="nav-primary<?= $current_page === 'e-task.php' ? ' is-active' : '' ?>"<?= $current_page === 'e-task.php' ? ' aria-current="page"' : '' ?>>e-Task</a>
    </nav>
  </div>

  <div class="container">
    <nav class="mobileNav" id="mobileNav" aria-label="Mobile">
<?php foreach ($nav_items as $item): ?>
      <?= render_nav_link($item, $current_page) . PHP_EOL ?>
<?php endforeach; ?>
      <a href="<?= htmlspecialchars(site_href('/contact.php')) ?>" class="nav-outline<?= $current_page === 'contact.php' ? ' is-active' : '' ?>"<?= $current_page === 'contact.php' ? ' aria-current="page"' : '' ?>>Contact</a>
      <a href="<?= htmlspecialchars(site_href('/e-task.php')) ?>" class="nav-primary<?= $current_page === 'e-task.php' ? ' is-active' : '' ?>"<?= $current_page === 'e-task.php' ? ' aria-current="page"' : '' ?>>e-Task</a>
    </nav>
  </div>
</header>
