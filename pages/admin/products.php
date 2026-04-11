<?php
declare(strict_types=1);
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../includes/admin_auth.php';

adminRequireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $id = (int) $_POST['delete_id'];
        if ($id > 0) {
            ProductModel::deleteProduct($id);
        }
        redirectTo('/admin/produse');
    }
    if (isset($_POST['save_homepage'])) {
        $featured = $_POST['featured'] ?? [];
        if (!is_array($featured)) {
            $featured = [];
        }
        $featured = array_map('intval', $featured);
        $sort = $_POST['home_sort'] ?? [];
        if (!is_array($sort)) {
            $sort = [];
        }
        $sortMap = [];
        foreach ($sort as $k => $v) {
            $sortMap[(int) $k] = (int) $v;
        }
        ProductModel::saveHomepageSelection($featured, $sortMap);
        redirectTo('/admin/produse?saved=1');
    }
}

$products = ProductModel::allForAdmin();
$saved = isset($_GET['saved']);
$seo = ['title' => 'Produse - Admin'];
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
      <a href="<?= e(url('/admin/produse')) ?>" class="text-gold">Produse</a>
      <a href="<?= e(url('/admin/produse/nou')) ?>" class="hover:underline">Produs nou</a>
      <a href="<?= e(url('/')) ?>" class="hover:underline">Site</a>
      <a href="<?= e(url('/admin/logout')) ?>" class="hover:underline">Ieșire</a>
    </nav>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-8">
    <?php if ($saved): ?>
      <p class="mb-4 text-green-700 text-sm bg-green-50 border border-green-200 rounded px-3 py-2">Setările pentru pagina principală au fost salvate.</p>
    <?php endif; ?>

    <h1 class="font-serif text-3xl mb-2">Produse</h1>
    <p class="text-sm text-zinc-600 mb-6">Bifează produsele care apar în blocul „Produse noi” de pe pagina principală și setează ordinea (număr mic = primul).</p>

    <form method="post" class="mb-10 bg-white rounded-lg border border-zinc-200 p-4 shadow-sm">
      <input type="hidden" name="save_homepage" value="1">
      <h2 class="font-semibold mb-3">Pagina principală — „Produse noi”</h2>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b text-left text-zinc-500">
              <th class="py-2 pr-2">Pe homepage</th>
              <th class="py-2 pr-2">Ordine</th>
              <th class="py-2 pr-2">Nume</th>
              <th class="py-2">Preț</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr class="border-b border-zinc-100">
                <td class="py-2 pr-2">
                  <input type="checkbox" name="featured[]" value="<?= (int) $p['id'] ?>" <?= !empty($p['featured_on_home']) ? 'checked' : '' ?>>
                </td>
                <td class="py-2 pr-2">
                  <input type="number" name="home_sort[<?= (int) $p['id'] ?>]" value="<?= (int) ($p['home_sort'] ?? 0) ?>" class="w-16 border rounded px-1 py-0.5">
                </td>
                <td class="py-2 pr-2"><?= e($p['name']) ?></td>
                <td class="py-2"><?= e(formatPrice((float) $p['price'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <button type="submit" class="mt-4 bg-gold text-white px-4 py-2 rounded hover:opacity-90">Salvează selecția homepage</button>
    </form>

    <div class="flex justify-between items-center mb-4">
      <h2 class="font-serif text-xl">Toate produsele</h2>
      <a href="<?= e(url('/admin/produse/nou')) ?>" class="bg-zinc-900 text-white px-4 py-2 rounded text-sm hover:bg-zinc-800">+ Produs nou</a>
    </div>

    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden shadow-sm">
      <table class="w-full text-sm">
        <thead class="bg-zinc-50 border-b">
          <tr class="text-left text-zinc-600">
            <th class="p-3">ID</th>
            <th class="p-3">Nume</th>
            <th class="p-3">Categorie</th>
            <th class="p-3">Preț</th>
            <th class="p-3">Homepage</th>
            <th class="p-3"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr class="border-b border-zinc-100 hover:bg-zinc-50">
              <td class="p-3"><?= (int) $p['id'] ?></td>
              <td class="p-3 font-medium"><?= e($p['name']) ?></td>
              <td class="p-3"><?= e($p['category']) ?></td>
              <td class="p-3"><?= e(formatPrice((float) $p['price'])) ?></td>
              <td class="p-3"><?= !empty($p['featured_on_home']) ? 'Da' : '—' ?></td>
              <td class="p-3 text-right space-x-2">
                <a href="<?= e(url('/admin/produse/' . (int) $p['id'])) ?>" class="text-gold hover:underline">Modifică</a>
                <form method="post" class="inline" onsubmit="return confirm('Ștergi acest produs?');">
                  <input type="hidden" name="delete_id" value="<?= (int) $p['id'] ?>">
                  <button type="submit" class="text-red-600 hover:underline">Șterge</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if (!$products): ?>
        <p class="p-6 text-zinc-500">Nu există produse. <a href="<?= e(url('/admin/produse/nou')) ?>" class="underline text-gold">Adaugă primul produs</a>.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
