<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/storage_manager.php';

function storageManagerIsLoggedIn(): bool
{
    return !empty($_SESSION['storage_manager_logged']);
}

function storageManagerTryLogin(string $password): bool
{
    if ($password === '') {
        return false;
    }
    return password_verify($password, STORAGE_MANAGER_PASSWORD_HASH);
}

function storageManagerLogout(): void
{
    unset($_SESSION['storage_manager_logged']);
}

function storageManagerRequireLogin(): void
{
    if (storageManagerIsLoggedIn()) {
        return;
    }
    header('Location: ' . url('/produs.php'));
    exit;
}
