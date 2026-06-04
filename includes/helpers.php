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
    if ($price <= 0) {
        return 'Preț la cerere';
    }
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

/** Mesaj precompletat pentru comandă WhatsApp / Viber. */
function productOrderMessage(string $productName, string $productSlug, string $imageUrl): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $productPath = url('/produs/' . $productSlug);
    $productUrl = $host !== '' ? ($scheme . '://' . $host . $productPath) : $productPath;

    return "Bună! Doresc să comand produsul \"{$productName}\".\n"
        . "Link produs: {$productUrl}\n"
        . "Poză produs: {$imageUrl}";
}

function productWhatsAppOrderUrl(string $plainMessage): string
{
    if (!defined('SITE_PHONE_TEL')) {
        require_once __DIR__ . '/../config/contact.php';
    }
    $phone = preg_replace('/\D+/', '', (string) SITE_PHONE_TEL) ?? '';

    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($plainMessage);
}

/** Link deep Viber (chat + mesaj draft) — folosit din pagina popup. */
function viberChatDeepLink(string $plainMessage): string
{
    if (!defined('SITE_PHONE_TEL')) {
        require_once __DIR__ . '/../config/contact.php';
    }
    $phone = ltrim(preg_replace('/\D+/', '', (string) SITE_PHONE_TEL) ?? '', '+');

    return 'viber://chat?number=%2B' . $phone . '&draft=' . rawurlencode($plainMessage);
}

/** URL pagină popup (mesaj vizibil pe desktop + deschidere Viber). */
function productViberOrderUrl(string $plainMessage): string
{
    return url('/viber-comanda?text=' . rawurlencode($plainMessage));
}

function clientIpAddress(): string
{
    $candidates = [
        (string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? ''),
        (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''),
        (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
    ];

    foreach ($candidates as $raw) {
        if ($raw === '') {
            continue;
        }
        $first = trim(explode(',', $raw)[0]);
        if (filter_var($first, FILTER_VALIDATE_IP)) {
            return $first;
        }
    }

    return '';
}
