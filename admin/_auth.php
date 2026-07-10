<?php
require_once __DIR__ . '/../support/config.php';

const ENQ_AUTH_SESSION = 'ENQUIRIES_ADMIN';
const ENQ_AUTH_USERS_FILE = __DIR__ . '/../storage/support-auth/users.json';
const ENQ_AUTH_RESET_TTL = 1800;

function enq_auth_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(ENQ_AUTH_SESSION);
    session_start();
}

function enq_auth_storage_dir(): string
{
    return dirname(ENQ_AUTH_USERS_FILE);
}

function enq_auth_default_users(): array
{
    $timestamp = date('c');

    return [
        [
            'id' => 'ENQ-ADMIN-001',
            'name' => 'Administrator',
            'email' => 'etaxpdy@gmail.com',
            'role' => 'admin',
            'status' => 'active',
            'must_set_password' => true,
            'password_hash' => null,
            'reset_token_hash' => null,
            'reset_expires_at' => null,
            'last_login_at' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
    ];
}

function enq_auth_bootstrap_users(): void
{
    if (file_exists(ENQ_AUTH_USERS_FILE)) {
        return;
    }

    $dir = enq_auth_storage_dir();
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    file_put_contents(
        ENQ_AUTH_USERS_FILE,
        json_encode(enq_auth_default_users(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );
}

function enq_auth_load_users(): array
{
    enq_auth_bootstrap_users();
    $json = @file_get_contents(ENQ_AUTH_USERS_FILE);
    $data = json_decode($json ?: '[]', true);

    return is_array($data) ? $data : [];
}

function enq_auth_save_users(array $users): void
{
    $dir = enq_auth_storage_dir();
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    file_put_contents(
        ENQ_AUTH_USERS_FILE,
        json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );
}

function enq_auth_normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function enq_auth_find_user_by_email(string $email): ?array
{
    $needle = enq_auth_normalize_email($email);

    foreach (enq_auth_load_users() as $user) {
        if (enq_auth_normalize_email((string) ($user['email'] ?? '')) === $needle) {
            return $user;
        }
    }

    return null;
}

function enq_auth_find_user_by_token(string $token): ?array
{
    if ($token === '') {
        return null;
    }

    $now = time();
    foreach (enq_auth_load_users() as $user) {
        $hash = (string) ($user['reset_token_hash'] ?? '');
        $expires = strtotime((string) ($user['reset_expires_at'] ?? ''));
        if ($hash === '' || !$expires || $expires < $now) {
            continue;
        }
        if (password_verify($token, $hash)) {
            return $user;
        }
    }

    return null;
}

function enq_auth_update_user(array $updatedUser): void
{
    $users = enq_auth_load_users();
    foreach ($users as $index => $user) {
        if (($user['id'] ?? '') === ($updatedUser['id'] ?? '')) {
            $users[$index] = $updatedUser;
            enq_auth_save_users($users);
            return;
        }
    }
}

function enq_auth_next_user_id(array $users): string
{
    $max = 0;
    foreach ($users as $user) {
        $id = (string) ($user['id'] ?? '');
        if (preg_match('/ENQ-USER-(\d+)/', $id, $matches)) {
            $max = max($max, (int) $matches[1]);
        }
    }

    return 'ENQ-USER-' . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
}

function enq_auth_create_user(string $name, string $email, string $role = 'bo'): ?string
{
    $email = enq_auth_normalize_email($email);
    $name = trim($name);
    $role = $role === 'admin' ? 'admin' : 'bo';

    if ($name === '') {
        return 'User name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'A valid email is required.';
    }

    $users = enq_auth_load_users();
    foreach ($users as $user) {
        if (enq_auth_normalize_email((string) ($user['email'] ?? '')) === $email) {
            return 'A user with this email already exists.';
        }
    }

    $timestamp = date('c');
    $users[] = [
        'id' => enq_auth_next_user_id($users),
        'name' => $name,
        'email' => $email,
        'role' => $role,
        'status' => 'active',
        'must_set_password' => true,
        'password_hash' => null,
        'reset_token_hash' => null,
        'reset_expires_at' => null,
        'last_login_at' => null,
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
    ];

    enq_auth_save_users($users);
    enq_auth_send_reset_link($email);

    return null;
}

function enq_auth_password_error(string $password): ?string
{
    if (strlen($password) < 10) {
        return 'Password must be at least 10 characters.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must contain at least one number.';
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return 'Password must contain at least one special character.';
    }

    return null;
}

function enq_auth_login(string $email, string $password): bool
{
    $users = enq_auth_load_users();
    $needle = enq_auth_normalize_email($email);

    foreach ($users as $index => $user) {
        if (enq_auth_normalize_email((string) ($user['email'] ?? '')) !== $needle) {
            continue;
        }
        if (($user['status'] ?? 'inactive') !== 'active') {
            return false;
        }
        if (!empty($user['must_set_password']) || empty($user['password_hash'])) {
            return false;
        }
        if (!password_verify($password, (string) $user['password_hash'])) {
            return false;
        }

        $users[$index]['last_login_at'] = date('c');
        $users[$index]['updated_at'] = date('c');
        enq_auth_save_users($users);

        enq_auth_session_start();
        session_regenerate_id(true);
        $_SESSION['enq_auth'] = true;
        $_SESSION['enq_user_id'] = $user['id'];
        $_SESSION['enq_role'] = $user['role'];
        $_SESSION['enq_email'] = $user['email'];
        $_SESSION['enq_name'] = $user['name'];
        $_SESSION['enq_time'] = time();

        return true;
    }

    return false;
}

function enq_auth_logout(): void
{
    enq_auth_session_start();
    session_unset();
    session_destroy();
}

function enq_auth_current_user(): ?array
{
    enq_auth_session_start();
    if (empty($_SESSION['enq_auth']) || empty($_SESSION['enq_user_id'])) {
        return null;
    }

    foreach (enq_auth_load_users() as $user) {
        if (($user['id'] ?? '') === $_SESSION['enq_user_id']) {
            return $user;
        }
    }

    return null;
}

function enq_auth_require_auth(): array
{
    $user = enq_auth_current_user();
    if (!$user) {
        header('Location: login.php');
        exit;
    }

    return $user;
}

function enq_auth_require_admin(): array
{
    $user = enq_auth_require_auth();
    if (($user['role'] ?? '') !== 'admin') {
        header('Location: enquiries.php');
        exit;
    }

    return $user;
}

function enq_auth_reset_url(string $token): string
{
    $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'www.etaxadv.com';

    return $scheme . '://' . $host . app_href('/admin/reset_password.php?token=' . urlencode($token));
}

function enq_auth_send_reset_link(string $email): void
{
    $users = enq_auth_load_users();
    $needle = enq_auth_normalize_email($email);

    foreach ($users as $index => $user) {
        if (enq_auth_normalize_email((string) ($user['email'] ?? '')) !== $needle) {
            continue;
        }
        if (($user['status'] ?? 'inactive') !== 'active') {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $users[$index]['reset_token_hash'] = password_hash($token, PASSWORD_DEFAULT);
        $users[$index]['reset_expires_at'] = date('c', time() + ENQ_AUTH_RESET_TTL);
        $users[$index]['updated_at'] = date('c');
        enq_auth_save_users($users);

        $firstTime = !empty($user['must_set_password']) || empty($user['password_hash']);
        $subject = $firstTime ? 'Set Your E Tax Advisors Enquiries Password' : 'Reset Your E Tax Advisors Enquiries Password';
        $url = enq_auth_reset_url($token);
        $body = "Hello {$user['name']},\n\n"
            . ($firstTime
                ? "Use the link below to set your password for the E Tax Advisors enquiries follow-up dashboard.\n\n"
                : "Use the link below to reset your password for the E Tax Advisors enquiries follow-up dashboard.\n\n")
            . $url . "\n\n"
            . "This link will expire in 30 minutes.\n\n"
            . "If you did not request this email, please ignore it.\n\n"
            . "Regards,\nE Tax Advisors";

        send_mail_safe((string) $user['email'], $subject, $body);
        return;
    }
}

function enq_auth_complete_password_setup(string $token, string $password): ?string
{
    $error = enq_auth_password_error($password);
    if ($error !== null) {
        return $error;
    }

    $users = enq_auth_load_users();
    $now = time();
    foreach ($users as $index => $user) {
        $hash = (string) ($user['reset_token_hash'] ?? '');
        $expires = strtotime((string) ($user['reset_expires_at'] ?? ''));
        if ($hash === '' || !$expires || $expires < $now) {
            continue;
        }
        if (!password_verify($token, $hash)) {
            continue;
        }

        $users[$index]['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        $users[$index]['must_set_password'] = false;
        $users[$index]['reset_token_hash'] = null;
        $users[$index]['reset_expires_at'] = null;
        $users[$index]['updated_at'] = date('c');
        enq_auth_save_users($users);

        return null;
    }

    return 'This password setup link is invalid or has expired.';
}

function enq_auth_change_password(string $userId, string $currentPassword, string $newPassword): ?string
{
    $users = enq_auth_load_users();
    foreach ($users as $index => $user) {
        if (($user['id'] ?? '') !== $userId) {
            continue;
        }
        if (empty($user['password_hash']) || !password_verify($currentPassword, (string) $user['password_hash'])) {
            return 'Invalid current password.';
        }
        $error = enq_auth_password_error($newPassword);
        if ($error !== null) {
            return $error;
        }

        $users[$index]['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        $users[$index]['must_set_password'] = false;
        $users[$index]['updated_at'] = date('c');
        enq_auth_save_users($users);
        return null;
    }

    return 'Unable to locate the current user.';
}
