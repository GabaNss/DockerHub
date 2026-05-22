<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function is_logs_authenticated(): bool
{
    return !empty($_SESSION[SESSION_KEY_LOGGED_IN]);
}

function login_logs_user(string $password): bool
{
    if (!hash_equals(LOGS_PASSWORD, $password)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION[SESSION_KEY_LOGGED_IN] = true;

    return true;
}

function logout_logs_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'] ?: '/',
            $params['domain'] ?? '',
            (bool) ($params['secure'] ?? false),
            (bool) ($params['httponly'] ?? false)
        );
    }

    session_destroy();
}
