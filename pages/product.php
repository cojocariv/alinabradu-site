<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
$slug = $routeParams['slug'] ?? '';
$product = ProductModel::bySlug($slug);
$productImages = [];
if ($product) {
    $productImages = ProductModel::getImageUrls((int) $product['id']);
    if (empty($productImages)) {
        $productImages = [(string) $product['image']];
    }
}
if (!$product) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ((int) ($product['in_stock'] ?? 1) !== 1) {
        redirectTo('/contact?' . http_build_query([
            'produs' => $product['slug'],
            'marime' => trim((string) ($_POST['marime'] ?? $_POST['size'] ?? '')),
            'cantitate' => max(1, min(99, (int) ($_POST['cantitate'] ?? $_POST['quantity'] ?? 1))),
        ]));
    }
    $size = $_POST['size'] ?? '';
    $qtyAdd = max(1, min(99, (int) ($_POST['quantity'] ?? 1)));
    $validSizes = array_map('trim', explode(',', (string) $product['size']));
    if (in_array($size, $validSizes, true)) {
        $key = $product['id'] . ':' . $size;
        $prev = (int) ($_SESSION['cart'][$key]['qty'] ?? 0);
        $_SESSION['cart'][$key] = [
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'price' => (float) $product['price'],
            'image' => ProductModel::getPrimaryImageUrl($product),
            'selected_size' => $size,
            'qty' => $prev + $qtyAdd,
        ];
        redirectTo('/cos');
    }
}

$similar = ProductModel::similar((int) $product['id'], $product['category']);
$seo = [
    'title' => $product['name'] . ' - Alina Bradu',
    'description' => mb_substr(strip_tags((string) $product['description']), 0, 150),
    'type' => 'product',
    'image' => $productImages[0] ?? $product['image'],
];
require __DIR__ . '/../includes/header.php';
$inStockProduct = (int) ($product['in_stock'] ?? 1) === 1;
$offerAvailability = $inStockProduct ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder';
$productSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product['name'],
    'description' => $product['description'],
    'image' => $productImages,
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'MDL',
        'price' => (float) $product['price'],
        'availability' => $offerAvailability,
    ],
];
?>
<script type="application/ld+json"><?= json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<section class="max-w-7xl mx-auto px-4 py-10 grid md:grid-cols-2 gap-10">
  <div>
    <div class="space-y-3">
      <div class="rounded-lg bg-white/70 border border-zinc-200 p-3">
        <img id="product-main-img" src="<?= e($productImages[0]) ?>" alt="<?= e($product['name']) ?>" class="w-full rounded-lg object-contain max-h-[min(80vh,680px)]" loading="eager">
      </div>
      <?php if (count($productImages) > 1): ?>
        <div class="flex flex-wrap gap-2 justify-center">
          <?php foreach ($productImages as $i => $imgUrl): ?>
            <button type="button" class="product-thumb border-2 rounded overflow-hidden w-20 h-20 bg-white shrink-0 <?= $i === 0 ? 'border-gold' : 'border-transparent opacity-80 hover:opacity-100' ?>" data-src="<?= e($imgUrl) ?>" aria-label="Imagine <?= $i + 1 ?>">
              <img src="<?= e($imgUrl) ?>" alt="" class="w-full h-full object-contain p-1 bg-white">
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div>
    <h1 class="font-serif text-4xl mb-3"><?= e($product['name']) ?></h1>
    <p class="text-gold text-2xl font-semibold"><?= e(formatPrice((float) $product['price'])) ?></p>
    <?php if ($inStockProduct): ?>
      <p class="mt-1 mb-4 text-sm font-medium text-gold">În stoc</p>
    <?php else: ?>
      <p class="mt-1 mb-4 text-sm font-medium text-zinc-500">La comanda</p>
    <?php endif; ?>
    <p class="text-zinc-600 mb-6"><?= nl2br(e($product['description'])) ?></p>
    <?php if ($inStockProduct): ?>
    <form method="post" class="space-y-4">
      <label class="block font-medium">Mărime</label>
      <select name="size" required class="w-full border p-2 rounded">
        <option value="">Selectează mărimea</option>
        <?php foreach (explode(',', (string) $product['size']) as $size): ?>
          <option value="<?= e(trim($size)) ?>"><?= e(trim($size)) ?></option>
        <?php endforeach; ?>
      </select>
      <div>
        <label class="block font-medium mb-1" for="product-qty">Cantitate</label>
        <input
          id="product-qty"
          type="number"
          name="quantity"
          min="1"
          max="99"
          value="1"
          required
          class="w-full max-w-[8rem] border p-2 rounded"
        >
      </div>
      <button type="submit" class="bg-zinc-900 text-white px-6 py-3 rounded hover:bg-zinc-700">Adaugă în coș</button>
    </form>
    <?php else: ?>
    <form action="<?= e(url('/contact')) ?>" method="get" class="space-y-4">
      <input type="hidden" name="produs" value="<?= e($product['slug']) ?>">
      <label class="block font-medium" for="com-marime">Mărime dorită</label>
      <select id="com-marime" name="marime" required class="w-full border p-2 rounded">
        <option value="">Selectează mărimea</option>
        <?php foreach (explode(',', (string) $product['size']) as $size): ?>
          <?php $sz = trim($size); ?>
          <option value="<?= e($sz) ?>"><?= e($sz) ?></option>
        <?php endforeach; ?>
      </select>
      <div>
        <label class="block font-medium mb-1" for="com-qty">Cantitate</label>
        <input id="com-qty" type="number" name="cantitate" min="1" max="99" value="1" required class="w-full max-w-[8rem] border p-2 rounded">
      </div>
      <p class="text-sm text-zinc-500">Produs la comandă — vei fi redirecționat către formularul de contact pentru a finaliza cererea.</p>
      <button type="submit" class="bg-zinc-900 text-white px-6 py-3 rounded hover:bg-zinc-700">Adaugă în coș</button>
    </form>
    <?php endif; ?>
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
<?php if (count($productImages) > 1): ?>
<script>
(function () {
  var main = document.getElementById('product-main-img');
  if (!main) return;
  document.querySelectorAll('.product-thumb').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var src = btn.getAttribute('data-src');
      if (src) main.src = src;
      document.querySelectorAll('.product-thumb').forEach(function (b) {
        var active = b === btn;
        b.classList.toggle('border-gold', active);
        b.classList.toggle('border-transparent', !active);
        b.classList.toggle('opacity-80', !active);
      });
    });
  });
})();
</script>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
