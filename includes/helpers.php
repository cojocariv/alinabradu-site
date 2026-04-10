<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('/[^a-z0-9\s-]/u', '', $text) ?? '';
    $text = preg_replace('/[\s-]+/', '-', $text) ?? '';
    return trim($text, '-');
}

function formatPrice(float $price): string
{
    return number_format($price, 2, ',', '.') . ' MDL';
}

function cartItemsCount(): int
{
    return array_sum(array_map(static fn(array $item): int => (int) $item['qty'], $_SESSION['cart'] ?? []));
}

function currentUrlPath(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return $path ?: '/';
}

function appBasePath(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $baseDir = rtrim(str_replace('/index.php', '', $scriptName), '/');
    return $baseDir === '' ? '' : $baseDir;
}

function routePath(): string
{
    $path = currentUrlPath();
    $base = appBasePath();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base)) ?: '/';
    }
    return trim($path, '/');
}

function url(string $path = '/'): string
{
    $base = appBasePath();
    $path = '/' . ltrim($path, '/');
    if ($path === '//') {
        $path = '/';
    }
    return ($base === '' ? '' : $base) . $path;
}

function redirectTo(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function seoDefaults(): array
{
    return [
        'title' => 'Alina Bradu - Magazin rochii tradiționale',
        'description' => 'Boutique premium cu rochii, bluze și fuste tradiționale moldovenești, lucrate cu atenție la detalii.',
        'keywords' => 'rochii populare moldovenești, rochie tradițională, bluză tradițională, fustă populară, costum popular, rochie mireasă tradițională',
        'image' => 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png',
        'type' => 'website',
    ];
}

function mergeSeo(array $seo): array
{
    return array_merge(seoDefaults(), $seo);
}
