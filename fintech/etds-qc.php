<?php
$base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$base = $base === '/' ? '' : rtrim($base, '/');
header('Location: ' . $base . '/fintech/etds-qc/', true, 302);
exit;
