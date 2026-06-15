<?php
require_once __DIR__ . '/config.php';

// Support pages header -> reuse main site header
if (!isset($page_title) || trim($page_title) === '') {
  $page_title = "Support | E Tax Advisors Private Limited";
}

// Optional: set to "" if you don't have this file
$extra_css = "";
// $extra_css = "/support/assets/support.css";

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Support Page Wrapper (optional, keeps spacing nice) -->
<section class="section">
  <div class="container">
