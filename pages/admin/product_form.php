<?php
declare(strict_types=1);
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/admin_upload.php';

adminRequireLogin();

$editId = isset($routeParams['id']) ? (int) $routeParams['id'] : null;
$isNew = $editId === null || $editId < 1;

$CATEGORIES = [
    'bluze' => ['name' => 'Bluză', 'slug' => 'bluze'],
    'fuste' => ['name' => 'Fustă', 'slug' => 'fuste'],
    'home-decor' => ['name' => 'Home decor', 'slug' => 'home-decor'],
    'rochii' => ['name' => 'Rochie', 'slug' => 'rochii'],
];

$errors = [];
$product = null;
$imageUrlsText = '';

if (!$isNew) {
    $product = ProductModel::findById($editId);
    if (!$product) {
        http_response_code(404);
        echo 'Produs inexistent.';
        exit;
    }
    $imageUrlsText = implode("\n", ProductModel::getImageUrls($editId));
    if ($imageUrlsText === '') {
        $imageUrlsText = (string) $product['image'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $slugInput = trim((string) ($_POST['slug'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $priceRaw = str_replace(',', '.', trim((string) ($_POST['price'] ?? '0')));
    $price = (float) $priceRaw;
    $catKey = (string) ($_POST['category_key'] ?? '');
    $subName = trim((string) ($_POST['subcategory'] ?? ''));
    $sizes = preg_replace('/\s+/', '', trim((string) ($_POST['sizes'] ?? '')));
    $urlsRaw = (string) ($_POST['image_urls'] ?? '');
    $featured = isset($_POST['featured_on_home']) ? 1 : 0;
    $homeSort = (int) ($_POST['home_sort'] ?? 0);
    $inStock = isset($_POST['in_stock']) ? 1 : 0;

    $slug = $slugInput !== '' ? slugify($slugInput) : slugify($name);
    if ($slug === '') {
        $errors[] = 'Slug invalid.';
    }
    if ($name === '' || mb_strlen($name) < 2) {
        $errors[] = 'Numele produsului este obligatoriu.';
    }
    if ($description === '') {
        $errors[] = 'Descrierea este obligatorie.';
    }
    if ($price <= 0) {
        $errors[] = 'Prețul trebuie să fie pozitiv.';
    }
    if (!isset($CATEGORIES[$catKey])) {
        $errors[] = 'Selectează categoria.';
    }
    if ($sizes === '') {
        $errors[] = 'Completează mărimile (ex: XS,S,M,L,XL).';
    }

    $exceptId = $isNew ? null : $editId;
    if (!$errors && ProductModel::slugExists($slug, $exceptId)) {
        $errors[] = 'Acest slug este deja folosit de alt produs.';
    }

    $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $urlsRaw)));
    $uploaded = adminSaveProductUploads(isset($_FILES['gallery']) ? $_FILES['gallery'] : null);
    $allImages = array_values(array_unique(array_merge($lines, $uploaded)));

    if (!$errors && empty($allImages)) {
        $errors[] = 'Adaugă cel puțin o imagine (URL sau fișier).';
    }

    if (!$errors) {
        $cat = $CATEGORIES[$catKey];
        $subSlug = $subName !== '' ? slugify($subName) : null;

        $row = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => $price,
            'category' => $cat['name'],
            'category_slug' => $cat['slug'],
            'subcategory' => $subName !== '' ? $subName : null,
            'subcategory_slug' => $subSlug,
            'size' => $sizes,
            'image' => $allImages[0],
            'featured_on_home' => $featured,
            'home_sort' => $homeSort,
            'in_stock' => $inStock,
        ];

        if ($isNew) {
            $newId = ProductModel::createProduct($row);
            ProductModel::replaceImages($newId, $allImages);
            redirectTo('/admin/produse?saved=1');
        }
        ProductModel::updateProduct($editId, $row);
        ProductModel::replaceImages($editId, $allImages);
        redirectTo('/admin/produse?saved=1');
    }

    if ($errors) {
        $product = $product ?? [];
        $product = array_merge($product, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => $price,
            'category' => $CATEGORIES[$catKey]['name'] ?? '',
            'category_slug' => $CATEGORIES[$catKey]['slug'] ?? '',
            'subcategory' => $subName,
            'size' => $sizes,
            'featured_on_home' => $featured,
            'home_sort' => $homeSort,
            'in_stock' => $inStock,
        ]);
        $imageUrlsText = $urlsRaw;
    }
}

$seo = ['title' => $isNew ? 'Produs nou - Admin' : 'Modifică produs - Admin'];
$catSlugToKey = array_combine(
    array_column($CATEGORIES, 'slug'),
    array_keys($CATEGORIES)
);
$currentCatKey = 'rochii';
if ($product) {
    $currentCatKey = $catSlugToKey[$product['category_slug']] ?? 'rochii';
}
if ($errors && isset($_POST['category_key']) && isset($CATEGORIES[$_POST['category_key']])) {
    $currentCatKey = (string) $_POST['category_key'];
}
?><!doctype html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($seo['title']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-100 min-h-screen text-zinc-800">
  <header class="bg-zinc-900 text-white px-4 py-3 flex flex-wrap items-center justify-between gap-2">
    <span class="font-serif text-lg"><?= $isNew ? 'Produs nou' : 'Modifică produs' ?></span>
    <nav class="flex gap-4 text-sm">
      <a href="<?= e(url('/admin/produse')) ?>" class="hover:underline">← Produse</a>
      <a href="<?= e(url('/admin/logout')) ?>" class="hover:underline">Ieșire</a>
    </nav>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-8">
    <?php if ($errors): ?>
      <ul class="mb-4 text-red-700 text-sm bg-red-50 border border-red-200 rounded px-3 py-2 list-disc pl-5">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white rounded-lg border border-zinc-200 p-6 shadow-sm space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Nume produs *</label>
        <input type="text" name="name" required value="<?= e($product['name'] ?? '') ?>" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Slug (URL) *</label>
        <input type="text" name="slug" placeholder="lasa gol pentru generare automata" value="<?= e($product['slug'] ?? '') ?>" class="w-full border rounded px-3 py-2 font-mono text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Descriere *</label>
        <textarea name="description" rows="6" required class="w-full border rounded px-3 py-2"><?= e($product['description'] ?? '') ?></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Preț (MDL) *</label>
        <input type="text" name="price" required value="<?= e(isset($product['price']) ? (string) $product['price'] : '') ?>" class="w-full max-w-xs border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Categorie *</label>
        <select name="category_key" required class="w-full border rounded px-3 py-2">
          <?php foreach ($CATEGORIES as $key => $c): ?>
            <option value="<?= e($key) ?>" <?= ($currentCatKey === $key) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Subcategorie (opțional, ex. Colecția Dor)</label>
        <input type="text" name="subcategory" value="<?= e($product['subcategory'] ?? '') ?>" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Mărimi * (separate prin virgulă)</label>
        <input type="text" name="sizes" required placeholder="XS,S,M,L,XL" value="<?= e($product['size'] ?? '') ?>" class="w-full border rounded px-3 py-2">
      </div>

      <div class="border-t pt-4">
        <label class="block text-sm font-medium mb-1">Imagini — URL-uri (câte un rând)</label>
        <textarea name="image_urls" rows="5" placeholder="https://..." class="w-full border rounded px-3 py-2 font-mono text-sm"><?= e($imageUrlsText) ?></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Sau încarcă fișiere (JPEG, PNG, WebP, GIF)</label>
        <input type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="w-full text-sm">
      </div>

      <div class="border-t pt-4 flex flex-wrap gap-4 items-center">
        <label class="inline-flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="in_stock" value="1" <?= ($isNew || (int) ($product['in_stock'] ?? 1) === 1) ? 'checked' : '' ?>>
          <span>În stoc</span>
        </label>
        <label class="inline-flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="featured_on_home" value="1" <?= !empty($product['featured_on_home']) ? 'checked' : '' ?>>
          <span>Afișează în „Produse noi” pe pagina principală</span>
        </label>
        <div class="flex items-center gap-2">
          <span class="text-sm">Ordine pe homepage</span>
          <input type="number" name="home_sort" value="<?= (int) ($product['home_sort'] ?? 0) ?>" class="w-20 border rounded px-2 py-1">
        </div>
      </div>

      <div class="flex gap-3 pt-4">
        <button type="submit" class="bg-zinc-900 text-white px-6 py-2 rounded hover:bg-zinc-800"><?= $isNew ? 'Creează produsul' : 'Salvează modificările' ?></button>
        <a href="<?= e(url('/admin/produse')) ?>" class="px-6 py-2 border rounded hover:bg-zinc-50">Anulează</a>
      </div>
    </form>
  </main>
</body>
</html>
