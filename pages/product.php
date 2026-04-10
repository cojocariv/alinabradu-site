<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
$slug = $routeParams['slug'] ?? '';
$product = ProductModel::bySlug($slug);
if (!$product) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $size = $_POST['size'] ?? '';
    $validSizes = array_map('trim', explode(',', (string) $product['size']));
    if (in_array($size, $validSizes, true)) {
        $key = $product['id'] . ':' . $size;
        $_SESSION['cart'][$key] = [
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'price' => (float) $product['price'],
            'image' => $product['image'],
            'selected_size' => $size,
            'qty' => (($_SESSION['cart'][$key]['qty'] ?? 0) + 1),
        ];
        redirectTo('/cos');
    }
}

$similar = ProductModel::similar((int) $product['id'], $product['category']);
$seo = [
    'title' => $product['name'] . ' - Alina Bradu',
    'description' => mb_substr(strip_tags((string) $product['description']), 0, 150),
    'type' => 'product',
    'image' => $product['image'],
];
require __DIR__ . '/../includes/header.php';
$productSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product['name'],
    'description' => $product['description'],
    'image' => [$product['image']],
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'RON',
        'price' => (float) $product['price'],
        'availability' => 'https://schema.org/InStock',
    ],
];
?>
<script type="application/ld+json"><?= json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<section class="max-w-7xl mx-auto px-4 py-10 grid md:grid-cols-2 gap-10">
  <div>
    <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="w-full rounded-lg object-cover" loading="eager">
  </div>
  <div>
    <h1 class="font-serif text-4xl mb-3"><?= e($product['name']) ?></h1>
    <p class="text-gold text-2xl font-semibold mb-4"><?= e(formatPrice((float) $product['price'])) ?></p>
    <p class="text-zinc-600 mb-6"><?= nl2br(e($product['description'])) ?></p>
    <form method="post" class="space-y-4">
      <label class="block font-medium">Marime</label>
      <select name="size" required class="w-full border p-2 rounded">
        <option value="">Selecteaza marimea</option>
        <?php foreach (explode(',', (string) $product['size']) as $size): ?>
          <option value="<?= e(trim($size)) ?>"><?= e(trim($size)) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="bg-zinc-900 text-white px-6 py-3 rounded hover:bg-zinc-700">Adauga in cos</button>
    </form>
  </div>
</section>
<section class="max-w-7xl mx-auto px-4 pb-12">
  <h2 class="font-serif text-2xl mb-4">Produse similare</h2>
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <?php foreach ($similar as $item): ?>
      <a href="<?= e(url('/produs/' . $item['slug'])) ?>" class="bg-white rounded shadow-sm overflow-hidden card-hover">
        <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>" class="h-56 w-full object-cover" loading="lazy">
        <div class="p-3">
          <p class="font-serif"><?= e($item['name']) ?></p>
          <p class="text-gold font-semibold"><?= e(formatPrice((float) $item['price'])) ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
