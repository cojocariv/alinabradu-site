<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';

$category = $routeParams['category'] ?? '';
$subcategory = $routeParams['subcategory'] ?? null;
$products = ProductModel::byCategorySlug($category, $subcategory);

$seo = [
    'title' => 'Categorie - Alina Bradu',
    'description' => 'Exploreaza colectii pe categorii si subcategorii.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="font-serif text-4xl mb-6">Categorie</h1>
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $product): ?>
      <article class="bg-white rounded-lg overflow-hidden shadow-sm card-hover">
        <a href="/produs/<?= e($product['slug']) ?>">
          <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="h-72 w-full object-cover" loading="lazy">
        </a>
        <div class="p-4">
          <h2 class="font-serif text-xl"><?= e($product['name']) ?></h2>
          <p class="text-gold font-semibold"><?= e(formatPrice((float) $product['price'])) ?></p>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
