<?php

function eta_config_value(string|array $keys, ?string $default = null): ?string {
  $keys = is_array($keys) ? $keys : [$keys];
  foreach ($keys as $key) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if (!is_string($value)) {
      continue;
    }
    $value = trim($value);
    if ($value !== '') {
      return $value;
    }
  }
  return $default;
}

function eta_is_local_environment(): bool {
  $appEnv = strtolower((string) eta_config_value(['ETA_APP_ENV', 'APP_ENV'], ''));
  if (in_array($appEnv, ['local', 'development', 'dev', 'testing', 'test'], true)) {
    return true;
  }

  $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
  return $host === ''
    || $host === 'localhost'
    || str_starts_with($host, '127.')
    || str_starts_with($host, '192.168.')
    || str_ends_with($host, '.local');
}

function eta_app_env(): string {
  $appEnv = strtolower((string) eta_config_value(['ETA_APP_ENV', 'APP_ENV'], ''));
  if ($appEnv !== '') {
    return $appEnv;
  }
  return eta_is_local_environment() ? 'development' : 'production';
}

function eta_is_production(): bool {
  return eta_app_env() === 'production';
}

function eta_is_placeholder_value(?string $value, array $placeholders = []): bool {
  if ($value === null) {
    return true;
  }
  $normalized = trim($value);
  if ($normalized === '') {
    return true;
  }

  $defaults = [
    'REPLACE_WITH_GSC_CODE',
    'G-XXXXXXXXXX',
    'UA-XXXXXXXX-X',
    'your_database_name',
    'your_database_user',
    'your_database_password',
  ];

  foreach (array_merge($defaults, $placeholders) as $placeholder) {
    if (strcasecmp($normalized, (string) $placeholder) === 0) {
      return true;
    }
  }

  return false;
}

function eta_google_site_verification(): ?string {
  $value = eta_config_value(['ETA_GOOGLE_SITE_VERIFICATION', 'GOOGLE_SITE_VERIFICATION']);
  return eta_is_placeholder_value($value) ? null : $value;
}

function eta_google_analytics_id(): ?string {
  $value = eta_config_value(['ETA_GOOGLE_ANALYTICS_ID', 'GOOGLE_ANALYTICS_ID', 'GA_MEASUREMENT_ID']);
  return eta_is_placeholder_value($value) ? null : $value;
}

function eta_support_db_config(): array {
  return [
    'host' => eta_config_value(['ETA_SUPPORT_DB_HOST', 'SUPPORT_DB_HOST', 'DB_HOST'], 'localhost') ?? 'localhost',
    'name' => eta_config_value(['ETA_SUPPORT_DB_NAME', 'SUPPORT_DB_NAME', 'DB_NAME'], 'your_database_name') ?? 'your_database_name',
    'user' => eta_config_value(['ETA_SUPPORT_DB_USER', 'SUPPORT_DB_USER', 'DB_USER'], 'your_database_user') ?? 'your_database_user',
    'pass' => eta_config_value(['ETA_SUPPORT_DB_PASS', 'SUPPORT_DB_PASS', 'DB_PASS'], 'your_database_password') ?? 'your_database_password',
  ];
}

function eta_support_db_is_configured(): bool {
  $config = eta_support_db_config();
  return !eta_is_placeholder_value($config['name'])
    && !eta_is_placeholder_value($config['user'])
    && !eta_is_placeholder_value($config['pass']);
}

function eta_support_db_error_message(): string {
  return 'Support database is not configured. Set ETA_SUPPORT_DB_NAME, ETA_SUPPORT_DB_USER, and ETA_SUPPORT_DB_PASS before enabling support database features in production.';
}
