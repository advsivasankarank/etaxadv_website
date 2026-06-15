<?php
require __DIR__ . '/../config.php';
session_name(SESSION_NAME);
session_start();
session_unset();
session_destroy();
header('Location: ' . app_href('/testimonial/admin/login.php'));
exit;
