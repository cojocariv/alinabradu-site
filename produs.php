<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/storage_manager_auth.php';

if (isset($_GET['logout'])) {
    storageManagerLogout();
    redirectTo('/produs.php');
}

$loginError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['storage_login'])) {
    $pass = (string) ($_POST['password'] ?? '');
    if ($pass !== '' && storageManagerTryLogin($pass)) {
        $_SESSION['storage_manager_logged'] = true;
        redirectTo('/produs.php');
    }
    $loginError = 'Parolă incorectă.';
}

if (!storageManagerIsLoggedIn()) {
    require __DIR__ . '/pages/storage-manager-login.php';
    exit;
}

require_once __DIR__ . '/config/azure_storage.php';
$sasUrl = AZURE_STORAGE_SAS_URL;
require __DIR__ . '/pages/storage-manager.php';
