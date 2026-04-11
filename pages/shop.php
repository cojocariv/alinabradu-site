<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';

$categories = ['Bluză', 'Fustă', 'Home decor', 'Rochie'];
$subcategories = ['Colecția Dor', 'Colecția Mireasă', 'Colecția Mistery', 'Colecția Soare', 'Colecția Spicul'];
$sizes = ['XS', 'S', 'M', 'L', 'XL'];

$filters = [
    'category' => $_GET['category'] ?? '',
    'subcategory' => $_GET['subcategory'] ?? '',
    'size' => $_GET['size'] ?? '',
];
$products = ProductModel::filter($filters);
$seo = [
    'title' => 'Magazin - Rochii și bluze tradiționale',
    'description' => 'Magazin online Alina Bradu: rochii tradiționale, bluze și fuste premium cu motive etnice.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 py-10">
  <h1 class="font-serif text-4xl mb-6">Magazin</h1>
  <form class="grid md:grid-cols-4 gap-3 bg-white p-4 rounded-lg mb-8">
    <select name="category" class="border rounded p-2">
      <option value="">Toate categoriile</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= e($cat) ?>" <?= $filters['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="subcategory" class="border rounded p-2">
      <option value="">Toate subcategoriile</option>
      <?php foreach ($subcategories as $sub): ?>
        <option value="<?= e($sub) ?>" <?= $filters['subcategory'] === $sub ? 'selected' : '' ?>><?= e($sub) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="size" class="border rounded p-2">
      <option value="">Toate mărimile</option>
      <?php foreach ($sizes as $size): ?>
        <option value="<?= e($size) ?>" <?= $filters['size'] === $size ? 'selected' : '' ?>><?= e($size) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="bg-zinc-900 text-white rounded px-4 py-2">Filtrează</button>
  </form>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $product): ?>
      <?php
      $imgUrl = ProductModel::getPrimaryImageUrl($product);
      $inStock = (int) ($product['in_stock'] ?? 1) === 1;
      $sizesList = array_filter(array_map('trim', explode(',', (string) $product['size'])));
      $firstSize = $sizesList[0] ?? '';
      ?>
      <article class="bg-white rounded-lg overflow-hidden shadow-sm card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>">
          <img src="<?= e($imgUrl) ?>" alt="<?= e($product['name']) ?>" class="w-full h-80 object-cover" loading="lazy">
        </a>
        <div class="p-4">
          <h2 class="font-serif text-xl"><?= e($product['name']) ?></h2>
          <p class="text-sm text-zinc-500"><?= e($product['category']) ?> <?= $product['subcategory'] ? ' - ' . e($product['subcategory']) : '' ?></p>
          <p class="mt-2 text-gold font-semibold"><?= e(formatPrice((float) $product['price'])) ?></p>
          <?php if ($inStock): ?>
            <p class="mt-1 text-sm font-medium text-gold">În stoc</p>
          <?php else: ?>
            <p class="mt-1 text-sm font-medium text-zinc-500">La comanda</p>
            <?php if ($firstSize !== ''): ?>
              <form method="post" action="<?= e(url('/produs/' . $product['slug'])) ?>" class="mt-3">
                <input type="hidden" name="size" value="<?= e($firstSize) ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full sm:w-auto bg-zinc-900 text-white text-sm px-4 py-2 rounded hover:bg-zinc-800">Adaugă în coș</button>
              </form>
            <?php endif; ?>
          <?php endif; ?>
          <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="inline-block mt-3 text-sm underline">Vezi produs</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
