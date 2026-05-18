<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

$categories = array_column(CategoryModel::all(), 'name');
if ($categories === []) {
    $categories = ['Bluză', 'Fustă', 'Home decor', 'Rochie'];
}
$subcategories = ['Colecția Dor', 'Colecția Mireasă', 'Colecția Mistery', 'Colecția Soare', 'Colecția Spicul'];
$sizes = ['XS', 'S', 'M', 'L', 'XL'];

$filters = [
    'category' => $_GET['category'] ?? '',
    'subcategory' => $_GET['subcategory'] ?? '',
    'size' => $_GET['size'] ?? '',
];
$shopPerPage = 9;
$shopPage = max(1, (int) ($_GET['page'] ?? 1));
$allProducts = ProductModel::filter($filters);
$totalProducts = count($allProducts);
$visibleCount = min($totalProducts, $shopPage * $shopPerPage);
$products = array_slice($allProducts, 0, $visibleCount);
$hasMoreProducts = $visibleCount < $totalProducts;
$shopNextPage = $shopPage + 1;

$shopPageUrl = static function (int $page) use ($filters): string {
    $params = array_filter([
        'category' => $filters['category'],
        'subcategory' => $filters['subcategory'],
        'size' => $filters['size'],
        'page' => $page > 1 ? (string) $page : '',
    ], static fn ($v) => $v !== '');
    $query = $params !== [] ? '?' . http_build_query($params) : '';
    return url('/magazin' . $query);
};
$seo = [
    'title' => 'Magazin - Rochii și bluze tradiționale',
    'description' => 'Magazin online Alina Bradu: rochii tradiționale, bluze și fuste premium cu motive etnice.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-12">
  <p class="text-[0.65rem] uppercase tracking-boutique text-gold font-medium mb-2">Colecții</p>
  <h1 class="font-serif text-4xl md:text-5xl text-ink mb-8 font-medium tracking-tight">Magazin</h1>
  <form id="shopFilters" method="get" action="<?= e(url('/magazin')) ?>" class="grid md:grid-cols-3 gap-3 bg-white/80 border border-gold/30 p-4 md:p-5 mb-8">
    <select name="category" class="border rounded p-2" aria-label="Categorie">
      <option value="">Toate categoriile</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= e($cat) ?>" <?= $filters['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="subcategory" class="border rounded p-2" aria-label="Subcategorie">
      <option value="">Toate subcategoriile</option>
      <?php foreach ($subcategories as $sub): ?>
        <option value="<?= e($sub) ?>" <?= $filters['subcategory'] === $sub ? 'selected' : '' ?>><?= e($sub) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="size" class="border rounded p-2" aria-label="Mărime">
      <option value="">Toate mărimile</option>
      <?php foreach ($sizes as $size): ?>
        <option value="<?= e($size) ?>" <?= $filters['size'] === $size ? 'selected' : '' ?>><?= e($size) ?></option>
      <?php endforeach; ?>
    </select>
  </form>

  <?php if ($totalProducts > 0): ?>
    <p class="text-sm text-ink-muted mb-6">Afișate <?= (int) $visibleCount ?> din <?= (int) $totalProducts ?> produse</p>
  <?php endif; ?>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $product): ?>
      <?php
      $imgUrl = ProductModel::getPrimaryImageUrl($product);
      $galleryUrls = ProductModel::getImageUrls((int) $product['id']);
      $hoverImgUrl = $galleryUrls[1] ?? null;
      $inStock = (int) ($product['in_stock'] ?? 1) === 1;
      $sizesList = array_filter(array_map('trim', explode(',', (string) $product['size'])));
      $firstSize = $sizesList[0] ?? '';
      ?>
      <article class="bg-white overflow-hidden border border-gold/30 card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="block bg-cream">
          <div class="product-card-media h-80 bg-cream p-3 flex items-center justify-center">
            <img src="<?= e($imgUrl) ?>" alt="<?= e($product['name']) ?>" class="product-card-media__img product-card-media__img--primary w-full h-full object-contain" loading="lazy">
            <?php if ($hoverImgUrl): ?>
              <img src="<?= e($hoverImgUrl) ?>" alt="" class="product-card-media__img product-card-media__img--hover w-full h-full object-contain" loading="lazy" aria-hidden="true">
            <?php endif; ?>
          </div>
        </a>
        <div class="p-4">
          <h2 class="product-title font-sans font-normal"><?= e($product['name']) ?></h2>
          <p class="text-sm text-ink-muted"><?= e($product['category']) ?> <?= $product['subcategory'] ? ' - ' . e($product['subcategory']) : '' ?></p>
          <p class="mt-2 text-gold font-semibold"><?= e(formatPrice((float) $product['price'])) ?></p>
          <?php if ($inStock): ?>
            <p class="mt-1 text-sm font-medium text-gold">În stoc</p>
            <?php if ($firstSize !== ''): ?>
              <form method="post" action="<?= e(url('/produs/' . $product['slug'])) ?>" class="mt-3">
                <input type="hidden" name="size" value="<?= e($firstSize) ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full sm:w-auto bg-ink text-cream text-xs uppercase tracking-wide font-medium px-4 py-2.5 hover:bg-ink-soft transition-colors">Adaugă în coș</button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <p class="mt-1 text-sm font-medium text-ink-muted">La comandă</p>
            <a href="<?= e(url('/contact?' . http_build_query(['produs' => $product['slug']]))) ?>" class="mt-3 inline-block w-full sm:w-auto bg-ink text-cream text-xs uppercase tracking-wide font-medium px-4 py-2.5 hover:bg-ink-soft text-center no-underline transition-colors">Adaugă în coș</a>
          <?php endif; ?>
          <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="inline-block mt-3 text-sm underline">Vezi produs</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <?php if ($hasMoreProducts): ?>
    <div class="mt-12 text-center">
      <a href="<?= e($shopPageUrl($shopNextPage)) ?>" class="btn btn--outline">Arată mai multe</a>
    </div>
  <?php elseif ($totalProducts === 0): ?>
    <p class="mt-10 text-center text-ink-muted">Niciun produs nu corespunde filtrelor selectate.</p>
  <?php endif; ?>
</section>
<script>
  (function () {
    const form = document.getElementById("shopFilters");
    if (!form) return;
    form.querySelectorAll("select").forEach(function (select) {
      select.addEventListener("change", function () {
        if (typeof form.requestSubmit === "function") {
          form.requestSubmit();
        } else {
          form.submit();
        }
      });
    });
  })();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
