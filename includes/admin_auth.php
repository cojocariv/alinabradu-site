<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/admin.php';

function adminIsLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged']);
}

function adminRequireLogin(): void
{
    if (!adminIsLoggedIn()) {
        header('Location: ' . url('/admin'));
        exit;
    }
}

function adminTryLogin(string $password): bool
{
    return password_verify($password, ADMIN_PASSWORD_HASH);
}

function adminLogout(): void
{
    unset($_SESSION['admin_logged']);
}
