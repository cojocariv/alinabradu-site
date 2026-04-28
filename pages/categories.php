<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';

$category = $routeParams['category'] ?? '';
$subcategory = $routeParams['subcategory'] ?? null;
$products = ProductModel::byCategorySlug($category, $subcategory);

$seo = [
    'title' => 'Categorie - Alina Bradu',
    'description' => 'Explorează colecții pe categorii și subcategorii.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="font-serif text-4xl mb-6">Categorie</h1>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $product): ?>
      <?php
      $imgUrl = ProductModel::getPrimaryImageUrl($product);
      $inStock = (int) ($product['in_stock'] ?? 1) === 1;
      $sizesList = array_filter(array_map('trim', explode(',', (string) $product['size'])));
      $firstSize = $sizesList[0] ?? '';
      ?>
      <article class="bg-white rounded-lg overflow-hidden shadow-sm card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="block bg-white">
          <div class="h-72 bg-white p-3 flex items-center justify-center">
            <img src="<?= e($imgUrl) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-contain" loading="lazy">
          </div>
        </a>
        <div class="p-4">
          <h2 class="font-serif text-xl"><?= e($product['name']) ?></h2>
          <p class="text-sm text-zinc-500"><?= e($product['category']) ?><?= $product['subcategory'] ? ' — ' . e($product['subcategory']) : '' ?></p>
          <p class="mt-2 text-gold font-semibold"><?= e(formatPrice((float) $product['price'])) ?></p>
          <?php if ($inStock): ?>
            <p class="mt-1 text-sm font-medium text-gold">În stoc</p>
            <?php if ($firstSize !== ''): ?>
              <form method="post" action="<?= e(url('/produs/' . $product['slug'])) ?>" class="mt-3">
                <input type="hidden" name="size" value="<?= e($firstSize) ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full sm:w-auto bg-zinc-900 text-white text-sm px-4 py-2 rounded hover:bg-zinc-800">Adaugă în coș</button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <p class="mt-1 text-sm font-medium text-zinc-500">La comanda</p>
            <a href="<?= e(url('/contact?' . http_build_query(['produs' => $product['slug']]))) ?>" class="mt-3 inline-block w-full sm:w-auto bg-zinc-900 text-white text-sm px-4 py-2 rounded hover:bg-zinc-800 text-center no-underline">Adaugă în coș</a>
          <?php endif; ?>
          <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="inline-block mt-3 text-sm underline">Vezi produs</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
