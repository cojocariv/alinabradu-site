<?php
declare(strict_types=1);

function envOrDefault(string $key, string $default): string
{
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

define('DB_HOST', envOrDefault('DB_HOST', '127.0.0.1'));
define('DB_NAME', envOrDefault('DB_NAME', 'ab_db_'));
define('DB_USER', envOrDefault('DB_USER', 'admin'));
define('DB_PASS', envOrDefault('DB_PASS', ''));
define('BASE_URL', '/');

function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        throw new RuntimeException(
            'Conexiunea la baza de date a esuat. Verifica DB_HOST, DB_NAME, DB_USER, DB_PASS.',
            0,
            $e
        );
    }

    return $pdo;
}
