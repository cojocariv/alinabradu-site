<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';

$categories = ['Bluza', 'Fusta', 'Home decor', 'Rochie'];
$subcategories = ['Colectia Dor', 'Colectia Mireasa', 'Colectia Mistery', 'Colectia Soare', 'Colectia Spicul'];
$sizes = ['XS', 'S', 'M', 'L', 'XL'];

$filters = [
    'category' => $_GET['category'] ?? '',
    'subcategory' => $_GET['subcategory'] ?? '',
    'size' => $_GET['size'] ?? '',
];
$products = ProductModel::filter($filters);
$seo = [
    'title' => 'Magazin - Rochii si Bluze Traditionale',
    'description' => 'Magazin online Alina Bradu: rochii traditionale, bluze si fuste premium cu motive etnice.',
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
      <option value="">Toate marimile</option>
      <?php foreach ($sizes as $size): ?>
        <option value="<?= e($size) ?>" <?= $filters['size'] === $size ? 'selected' : '' ?>><?= e($size) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="bg-zinc-900 text-white rounded px-4 py-2">Filtreaza</button>
  </form>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $product): ?>
      <article class="bg-white rounded-lg overflow-hidden shadow-sm card-hover">
        <a href="/produs/<?= e($product['slug']) ?>">
          <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="w-full h-80 object-cover" loading="lazy">
        </a>
        <div class="p-4">
          <h2 class="font-serif text-xl"><?= e($product['name']) ?></h2>
          <p class="text-sm text-zinc-500"><?= e($product['category']) ?> <?= $product['subcategory'] ? ' - ' . e($product['subcategory']) : '' ?></p>
          <p class="mt-2 text-gold font-semibold"><?= e(formatPrice((float) $product['price'])) ?></p>
          <a href="/produs/<?= e($product['slug']) ?>" class="inline-block mt-3 text-sm underline">Vezi produs</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
