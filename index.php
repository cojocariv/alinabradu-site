<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/ProductModel.php';

$path = trim(currentUrlPath(), '/');
$routeParams = [];

if ($path === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    $host = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $staticUrls = ['/', '/magazin', '/despre-noi', '/contact'];
    $products = ProductModel::filter();
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($staticUrls as $url) {
        echo '<url><loc>' . e($host . $url) . '</loc><changefreq>weekly</changefreq></url>';
    }
    foreach ($products as $product) {
        echo '<url><loc>' . e($host . '/produs/' . $product['slug']) . '</loc><changefreq>weekly</changefreq></url>';
    }
    echo '</urlset>';
    exit;
}

$routes = [
    '' => __DIR__ . '/pages/home.php',
    'magazin' => __DIR__ . '/pages/shop.php',
    'despre-noi' => __DIR__ . '/pages/about.php',
    'contact' => __DIR__ . '/pages/contact.php',
    'cos' => __DIR__ . '/pages/cart.php',
    'checkout' => __DIR__ . '/pages/checkout.php',
];

if (isset($routes[$path])) {
    require $routes[$path];
    exit;
}

if (preg_match('#^produs/([a-z0-9-]+)$#', $path, $matches)) {
    $routeParams['slug'] = $matches[1];
    require __DIR__ . '/pages/product.php';
    exit;
}

if (preg_match('#^([a-z0-9-]+)/([a-z0-9-]+)$#', $path, $matches)) {
    $routeParams['category'] = $matches[1];
    $routeParams['subcategory'] = $matches[2];
    require __DIR__ . '/pages/categories.php';
    exit;
}

if (preg_match('#^([a-z0-9-]+)$#', $path, $matches)) {
    $allowed = ['bluze', 'fuste', 'home-decor', 'rochii'];
    if (in_array($matches[1], $allowed, true)) {
        $routeParams['category'] = $matches[1];
        require __DIR__ . '/pages/categories.php';
        exit;
    }
}

http_response_code(404);
require __DIR__ . '/pages/404.php';
