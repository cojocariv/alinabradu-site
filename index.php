<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/models/ProductModel.php';
require_once __DIR__ . '/includes/admin_auth.php';

set_exception_handler(static function (Throwable $e): void {
    http_response_code(500);
    $message = $e->getMessage();
    $isDbError = str_contains($message, 'Conexiunea la bază de date a eșuat');
    ?>
    <!doctype html>
    <html lang="ro">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="icon" type="image/png" href="https://alinabradupozestorage.blob.core.windows.net/poze/favicon.png">
      <title>Eroare de server</title>
      <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-zinc-50 text-zinc-800">
      <main class="max-w-3xl mx-auto p-6 md:p-10">
        <div class="bg-white border border-zinc-200 rounded-lg p-6">
          <h1 class="text-2xl font-semibold mb-3">Eroare de configurare</h1>
          <?php if ($isDbError): ?>
            <p class="mb-3">Aplicația nu se poate conecta la baza de date.</p>
            <p class="text-sm text-zinc-600">Setează în hosting credențialele corecte: <code>DB_HOST</code>, <code>DB_NAME</code>, <code>DB_USER</code>, <code>DB_PASS</code>.</p>
          <?php else: ?>
            <p class="text-sm text-zinc-600">A apărut o eroare internă. Verifică jurnalele serverului.</p>
          <?php endif; ?>
        </div>
      </main>
    </body>
    </html>
    <?php
});

$path = routePath();
$routeParams = [];

if ($path === 'admin' || $path === 'admin/') {
    require __DIR__ . '/pages/admin/index.php';
    exit;
}
if ($path === 'admin/logout') {
    require __DIR__ . '/pages/admin/logout.php';
    exit;
}
if ($path === 'admin/produse') {
    adminRequireLogin();
    require __DIR__ . '/pages/admin/products.php';
    exit;
}
if ($path === 'admin/produse/nou') {
    adminRequireLogin();
    $routeParams['id'] = null;
    require __DIR__ . '/pages/admin/product_form.php';
    exit;
}
if (preg_match('#^admin/produse/(\d+)$#', $path, $adm)) {
    adminRequireLogin();
    $routeParams['id'] = (int) $adm[1];
    require __DIR__ . '/pages/admin/product_form.php';
    exit;
}

if ($path === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    $host = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $staticUrls = [url('/'), url('/magazin'), url('/despre-noi'), url('/contact')];
    $products = ProductModel::filter();
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($staticUrls as $staticUrl) {
        echo '<url><loc>' . e($host . $staticUrl) . '</loc><changefreq>weekly</changefreq></url>';
    }
    foreach ($products as $product) {
        echo '<url><loc>' . e($host . url('/produs/' . $product['slug'])) . '</loc><changefreq>weekly</changefreq></url>';
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
