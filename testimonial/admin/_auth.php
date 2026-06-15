<?php
require __DIR__ . '/../config.php';
session_name(SESSION_NAME);
session_start();

function require_testimonial_admin(): void {
  if (empty($_SESSION['admin_id'])) {
    header('Location: /testimonial/admin/login.php');
    exit;
  }
}
