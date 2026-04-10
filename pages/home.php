<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
$products = ProductModel::featured(8);
$seo = [
    'title' => 'Acasa - Alina Bradu Boutique Traditional',
    'description' => 'Descopera colectii autentice de inspiratie moldoveneasca: rochii, bluze, fuste si home decor premium.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="relative">
  <img src="https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png" alt="Alina Bradu Collection" class="w-full h-[60vh] object-cover" fetchpriority="high">
  <div class="absolute inset-0 bg-black/35"></div>
  <div class="absolute inset-0 max-w-7xl mx-auto px-4 flex items-center">
    <div class="text-white max-w-xl">
      <h1 class="font-serif text-4xl md:text-6xl leading-tight mb-4">Eleganta traditionala reinterpretata modern</h1>
      <a href="<?= e(url('/magazin')) ?>" class="inline-block bg-gold text-white px-6 py-3 rounded hover:opacity-90">Descopera colectiile</a>
    </div>
  </div>
</section>
<section class="max-w-7xl mx-auto px-4 py-12">
  <h2 class="font-serif text-3xl mb-6">Produse noi</h2>
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($products as $product): ?>
      <article class="bg-white rounded-lg overflow-hidden shadow-sm card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>">
          <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="w-full h-72 object-cover" loading="lazy">
        </a>
        <div class="p-4">
          <h3 class="font-serif text-lg"><?= e($product['name']) ?></h3>
          <p class="text-sm text-zinc-500"><?= e($product['category']) ?></p>
          <p class="mt-2 font-semibold text-gold"><?= e(formatPrice((float) $product['price'])) ?></p>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
