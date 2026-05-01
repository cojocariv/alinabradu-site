<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../includes/admin_upload.php';
require_once __DIR__ . '/../../models/AboutGalleryModel.php';

adminRequireLogin();

$errors = [];
$saved = isset($_GET['saved']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $urlsRaw = (string) ($_POST['image_urls'] ?? '');
    $manualUrls = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $urlsRaw) ?: [])));
    $uploadedUrls = adminSaveUploads($_FILES['gallery'] ?? null, 'about');
    $allUrls = array_values(array_unique(array_merge($manualUrls, $uploadedUrls)));

    if (count($allUrls) < 2) {
        $errors[] = 'Adaugă cel puțin 2 imagini pentru animația de pe pagina Despre noi.';
    }

    if (!$errors) {
        AboutGalleryModel::replaceAll($allUrls);
        redirectTo('/admin/despre?saved=1');
    }
}

$images = AboutGalleryModel::allForAdmin();
$imageUrlsText = implode("\n", array_map(static fn(array $img): string => (string) $img['image_url'], $images));
$seo = ['title' => 'Galerie Despre noi - Admin'];
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
    <span class="font-serif text-lg">Admin — Alina Bradu</span>
    <nav class="flex gap-4 text-sm">
      <a href="<?= e(url('/admin/produse')) ?>" class="hover:underline">Produse</a>
      <a href="<?= e(url('/admin/despre')) ?>" class="text-gold">Despre noi</a>
      <a href="<?= e(url('/')) ?>" class="hover:underline">Site</a>
      <a href="<?= e(url('/admin/logout')) ?>" class="hover:underline">Ieșire</a>
    </nav>
  </header>

  <main class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    <h1 class="font-serif text-3xl">Galerie „Despre noi”</h1>
    <p class="text-sm text-zinc-600">Imaginile sunt folosite în animația 3D de pe pagina <code>/despre-noi</code>. Ordinea din listă definește și ordinea în animație.</p>

    <?php if ($saved): ?>
      <p class="text-sm text-green-700 bg-green-50 border border-green-200 rounded px-3 py-2">Galeria a fost salvată.</p>
    <?php endif; ?>
    <?php if ($errors): ?>
      <ul class="text-sm text-red-700 bg-red-50 border border-red-200 rounded px-4 py-3 list-disc pl-6">
        <?php foreach ($errors as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white border border-zinc-200 rounded-lg p-5 shadow-sm space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">URL-uri imagini (câte un rând)</label>
        <textarea name="image_urls" rows="8" class="w-full border rounded px-3 py-2 font-mono text-sm" placeholder="https://..."><?= e($imageUrlsText) ?></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Sau încarcă poze noi (JPEG, PNG, WebP, GIF)</label>
        <input type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="w-full text-sm">
      </div>
      <button type="submit" class="bg-zinc-900 text-white px-6 py-2 rounded hover:bg-zinc-800">Salvează galeria</button>
    </form>

    <?php if ($images): ?>
      <div class="bg-white border border-zinc-200 rounded-lg p-5 shadow-sm">
        <h2 class="font-semibold mb-3">Previzualizare imagini</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <?php foreach ($images as $img): ?>
            <div class="border border-zinc-200 rounded p-2 bg-zinc-50">
              <img src="<?= e((string) $img['image_url']) ?>" alt="" class="w-full h-40 object-cover rounded">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
