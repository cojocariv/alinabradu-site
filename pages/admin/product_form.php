<?php
declare(strict_types=1);
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/admin_upload.php';
require_once __DIR__ . '/../../includes/shop_filter_config.php';

adminRequireLogin();

$shopFilterCfg = shopFilterConfig();
$rochieSlug = $shopFilterCfg['rochie_slug'];
$rochieSubcategories = $shopFilterCfg['subcategories'];

$editId = isset($routeParams['id']) ? (int) $routeParams['id'] : null;
$isNew = $editId === null || $editId < 1;

$CATEGORIES_LEGACY = [
    'bluze' => ['name' => 'Bluză', 'slug' => 'bluze'],
    'fuste' => ['name' => 'Fustă', 'slug' => 'fuste'],
    'home-decor' => ['name' => 'Home decor', 'slug' => 'home-decor'],
    'rochii' => ['name' => 'Rochie', 'slug' => 'rochii'],
];

$CATEGORIES = [];
foreach (CategoryModel::all() as $row) {
    $CATEGORIES[$row['slug']] = ['name' => $row['name'], 'slug' => $row['slug']];
}
if ($CATEGORIES === []) {
    $CATEGORIES = $CATEGORIES_LEGACY;
}

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
    $priceRaw = str_replace(',', '.', trim((string) ($_POST['price'] ?? '')));
    $price = $priceRaw === '' ? 0.0 : (float) $priceRaw;
    $catKey = (string) ($_POST['category_key'] ?? '');
    $newCategoryName = trim((string) ($_POST['new_category_name'] ?? ''));
    $subSlugPost = trim((string) ($_POST['subcategory_slug'] ?? ''));
    $subName = '';
    $subSlug = null;
    $sizes = preg_replace('/\s+/', '', trim((string) ($_POST['sizes'] ?? '')));
    $urlsRaw = (string) ($_POST['image_urls'] ?? '');
    $featured = isset($_POST['featured_on_home']) ? 1 : 0;
    $homeSort = ProductModel::normalizeHomeSort((int) ($_POST['home_sort'] ?? 0), (bool) $featured);
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
    if ($price < 0) {
        $errors[] = 'Prețul nu poate fi negativ.';
    }
    $catForProduct = null;
    if ($newCategoryName !== '') {
        if (mb_strlen($newCategoryName) < 2) {
            $errors[] = 'Numele categoriei noi este prea scurt.';
        }
        $newCatSlug = slugify($newCategoryName);
        if (!$errors && $newCatSlug === '') {
            $errors[] = 'Nu s-a putut genera URL-ul categoriei din nume.';
        }
        if (!$errors && CategoryModel::findBySlug($newCatSlug)) {
            $errors[] = 'Există deja o categorie cu același URL (slug).';
        }
        if (!$errors) {
            $catForProduct = ['name' => $newCategoryName, 'slug' => $newCatSlug];
        }
    } else {
        if (!isset($CATEGORIES[$catKey])) {
            $errors[] = 'Selectează categoria sau completează o categorie nouă.';
        } else {
            $catForProduct = $CATEGORIES[$catKey];
        }
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

    if (!$errors && $catForProduct) {
        if ($newCategoryName !== '') {
            CategoryModel::create($catForProduct['name'], $catForProduct['slug']);
            $CATEGORIES[$catForProduct['slug']] = $catForProduct;
        }
        $cat = $catForProduct;
        if ($cat['slug'] === $rochieSlug && $subSlugPost !== '') {
            foreach ($rochieSubcategories as $subOpt) {
                if ($subOpt['slug'] === $subSlugPost) {
                    $subName = $subOpt['value'];
                    $subSlug = $subOpt['slug'];
                    break;
                }
            }
            if ($subName === '') {
                $errors[] = 'Subcategoria selectată nu este validă.';
            }
        }

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
            'category' => $catForProduct !== null ? $catForProduct['name'] : ($CATEGORIES[$catKey]['name'] ?? ''),
            'category_slug' => $catForProduct !== null ? $catForProduct['slug'] : ($CATEGORIES[$catKey]['slug'] ?? ''),
            'subcategory' => $subName !== '' ? $subName : null,
            'subcategory_slug' => $subSlug,
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

$newCategoryInput = '';
if ($errors && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $newCategoryInput = trim((string) ($_POST['new_category_name'] ?? ''));
}

$rochieCatKey = 'rochii';
foreach ($CATEGORIES as $key => $c) {
    if (($c['slug'] ?? '') === $rochieSlug) {
        $rochieCatKey = (string) $key;
        break;
    }
}

$currentSubSlug = '';
if ($product) {
    $currentSubSlug = trim((string) ($product['subcategory_slug'] ?? ''));
    if ($currentSubSlug === '' && !empty($product['subcategory'])) {
        foreach ($rochieSubcategories as $subOpt) {
            if ($subOpt['value'] === $product['subcategory']) {
                $currentSubSlug = $subOpt['slug'];
                break;
            }
        }
    }
}
if ($errors && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $currentSubSlug = trim((string) ($_POST['subcategory_slug'] ?? ''));
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
      <a href="<?= e(url('/admin/despre')) ?>" class="hover:underline">Despre noi</a>
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
        <label class="block text-sm font-medium mb-1">Preț (MDL)</label>
        <p class="text-xs text-zinc-500 mb-1">Opțional — lasă gol dacă prețul nu e afișat (pe site: „Preț la cerere”).</p>
        <?php
        $priceFormValue = '';
        if (isset($product['price']) && (float) $product['price'] > 0) {
            $priceFormValue = (string) $product['price'];
        }
        ?>
        <input type="text" name="price" value="<?= e($priceFormValue) ?>" class="w-full max-w-xs border rounded px-3 py-2" inputmode="decimal" placeholder="ex. 1200">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Categorie *</label>
        <p class="text-xs text-zinc-500 mb-2">Alege din listă sau lasă lista și creează o categorie nouă mai jos.</p>
        <select name="category_key" id="category_key" class="w-full border rounded px-3 py-2">
          <?php foreach ($CATEGORIES as $key => $c): ?>
            <option value="<?= e($key) ?>" <?= ($currentCatKey === $key) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Categorie nouă</label>
        <input type="text" name="new_category_name" value="<?= e($newCategoryInput) ?>" placeholder="Ex: Accesorii — lasă gol dacă ai ales categoria de mai sus" class="w-full border rounded px-3 py-2" autocomplete="off">
        <p class="text-xs text-zinc-500 mt-1">Dacă completezi, se salvează categoria în magazin și produsul este legat de ea (nu mai este folosită selecția de deasupra).</p>
      </div>
      <div id="subcategory-field" class="<?= ($currentCatKey === $rochieCatKey && $newCategoryInput === '') ? '' : 'hidden' ?>">
        <label class="block text-sm font-medium mb-1" for="subcategory_slug">Subcategorie (colecție Rochie)</label>
        <select name="subcategory_slug" id="subcategory_slug" class="w-full border rounded px-3 py-2">
          <option value="">— fără subcategorie —</option>
          <?php foreach ($rochieSubcategories as $subOpt): ?>
            <option value="<?= e($subOpt['slug']) ?>" <?= $currentSubSlug === $subOpt['slug'] ? 'selected' : '' ?>><?= e($subOpt['label']) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="text-xs text-zinc-500 mt-1">Disponibil când categoria este „Rochie”.</p>
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
          <span class="text-sm">Ordine homepage</span>
          <input type="number" name="home_sort" value="<?= ProductModel::displayHomeSort((int) ($product['home_sort'] ?? 0), !empty($product['featured_on_home'])) ?>" min="1" step="1" class="w-24 border rounded px-2 py-1" title="1 = piesa evidențiată, 2–3… = restul în grilă">
          <span class="text-xs text-zinc-500">1 = evidențiată · 2, 3… = ordine în grilă</span>
        </div>
      </div>

      <div class="flex gap-3 pt-4">
        <button type="submit" class="bg-zinc-900 text-white px-6 py-2 rounded hover:bg-zinc-800"><?= $isNew ? 'Creează produsul' : 'Salvează modificările' ?></button>
        <a href="<?= e(url('/admin/produse')) ?>" class="px-6 py-2 border rounded hover:bg-zinc-50">Anulează</a>
      </div>
    </form>
  </main>
  <script>
    (function () {
      const rochieKey = <?= json_encode($rochieCatKey, JSON_THROW_ON_ERROR) ?>;
      const categorySelect = document.getElementById("category_key");
      const newCategoryInput = document.querySelector('input[name="new_category_name"]');
      const subField = document.getElementById("subcategory-field");

      function toggleSubcategory() {
        if (!subField || !categorySelect) return;
        const isNewCat = newCategoryInput && newCategoryInput.value.trim() !== "";
        const isRochie = !isNewCat && categorySelect.value === rochieKey;
        subField.classList.toggle("hidden", !isRochie);
        if (!isRochie) {
          const subSelect = document.getElementById("subcategory_slug");
          if (subSelect) subSelect.value = "";
        }
      }

      if (categorySelect) categorySelect.addEventListener("change", toggleSubcategory);
      if (newCategoryInput) newCategoryInput.addEventListener("input", toggleSubcategory);
      toggleSubcategory();
    })();
  </script>
</body>
</html>
