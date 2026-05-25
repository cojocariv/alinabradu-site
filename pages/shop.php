<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../includes/shop_filter_config.php';

$shopFilterConfig = shopFilterConfig();
$rochieSlug = $shopFilterConfig['rochie_slug'];
$rochieSubcategories = $shopFilterConfig['subcategories'];
$shopSizes = $shopFilterConfig['sizes'];

$shopParseList = static function (string $key): array {
    $raw = $_GET[$key] ?? [];
    if (!is_array($raw)) {
        return trim((string) $raw) !== '' ? [trim((string) $raw)] : [];
    }
    return array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $raw), static fn ($v) => $v !== ''));
};

$selectedCategories = $shopParseList('category');
$selectedSubSlugs = $shopParseList('subcategory');
$selectedSizes = $shopParseList('size');

$selectedSubFilters = [];
foreach ($rochieSubcategories as $sub) {
    if (in_array($sub['slug'], $selectedSubSlugs, true) || in_array($sub['value'], $selectedSubSlugs, true)) {
        $selectedSubFilters[] = ['value' => $sub['value'], 'slug' => $sub['slug']];
    }
}

$shopCategoryOptions = [];
$totalCatalogProducts = 0;
$shopLoadError = null;

try {
    $shopCategoryOptions = ProductModel::shopCategoryOptions();
    $totalCatalogProducts = ProductModel::countAll();
} catch (Throwable $e) {
    $shopLoadError = $e->getMessage();
    $shopCategoryOptions = [
        ['name' => 'Bluză', 'slug' => 'bluze', 'count' => 0],
        ['name' => 'Fustă', 'slug' => 'fuste', 'count' => 0],
        ['name' => 'Home decor', 'slug' => 'home-decor', 'count' => 0],
        ['name' => 'Rochie', 'slug' => 'rochii', 'count' => 0],
    ];
}

$filters = [
    'categories' => $selectedCategories,
    'subcategories' => $selectedSubFilters,
    'sizes' => $selectedSizes,
];
$shopPerPage = 9;
$shopPage = max(1, (int) ($_GET['page'] ?? 1));

$allProducts = [];
try {
    $allProducts = ProductModel::filter($filters);
} catch (Throwable $e) {
    $shopLoadError = $shopLoadError ?? $e->getMessage();
}

$totalProducts = count($allProducts);
$visibleCount = min($totalProducts, $shopPage * $shopPerPage);
$products = array_slice($allProducts, 0, $visibleCount);
$hasMoreProducts = $visibleCount < $totalProducts;
$shopNextPage = $shopPage + 1;

$shopPageUrl = static function (int $page) use ($selectedCategories, $selectedSubSlugs, $selectedSizes): string {
    $params = [];
    foreach ($selectedCategories as $cat) {
        $params['category'][] = $cat;
    }
    foreach ($selectedSubSlugs as $sub) {
        $params['subcategory'][] = $sub;
    }
    foreach ($selectedSizes as $size) {
        $params['size'][] = $size;
    }
    if ($page > 1) {
        $params['page'] = (string) $page;
    }
    $query = $params !== [] ? '?' . http_build_query($params) : '';
    return url('/magazin' . $query);
};

$subcategoryCounts = [];
foreach ($rochieSubcategories as $sub) {
    try {
        $subcategoryCounts[$sub['slug']] = ProductModel::countBySubcategory($sub['value'], $sub['slug']);
    } catch (Throwable $e) {
        $subcategoryCounts[$sub['slug']] = 0;
    }
}

$sizeCounts = [];
foreach ($shopSizes as $size) {
    try {
        $sizeCounts[$size] = ProductModel::countBySize($size);
    } catch (Throwable $e) {
        $sizeCounts[$size] = 0;
    }
}

$seo = [
    'title' => 'Magazin - Rochii și bluze tradiționale',
    'description' => 'Magazin online Alina Bradu: rochii tradiționale, bluze și fuste premium cu motive etnice.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-12">
  <p class="text-[0.65rem] uppercase tracking-boutique text-gold font-medium mb-2">Colecții</p>
  <h1 class="font-serif text-4xl md:text-5xl text-ink mb-8 font-medium tracking-tight">Magazin</h1>

  <?php if ($shopLoadError !== null): ?>
    <p class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">Listă temporar indisponibilă (<?= e($shopLoadError) ?>). Încearcă reîncărcarea paginii.</p>
  <?php endif; ?>

  <div class="lg:grid lg:grid-cols-[minmax(220px,260px)_1fr] gap-8 lg:gap-10 items-start">
    <aside class="shop-filters" aria-label="Filtre magazin">
      <form id="shopFilters" method="get" action="<?= e(url('/magazin')) ?>">
        <fieldset class="shop-filters__group">
          <legend class="shop-filters__title">Tip produs</legend>
          <ul class="shop-filters__list">
            <?php foreach ($shopCategoryOptions as $cat): ?>
              <?php
              $catName = (string) $cat['name'];
              $catSlug = (string) $cat['slug'];
              $catCount = (int) $cat['count'];
              $catChecked = in_array($catSlug, $selectedCategories, true) || in_array($catName, $selectedCategories, true);
              $isRochie = $catSlug === $rochieSlug;
              ?>
              <li class="shop-filters__item<?= $isRochie ? ' shop-filters__item--has-children' : '' ?>">
                <label class="shop-filters__label">
                  <input
                    type="checkbox"
                    name="category[]"
                    value="<?= e($catSlug) ?>"
                    class="shop-filters__checkbox"
                    <?= $catChecked ? 'checked' : '' ?>
                  >
                  <span class="shop-filters__text"><?= e($catName) ?></span>
                  <span class="shop-filters__count">(<?= $catCount ?>)</span>
                </label>
                <?php if ($isRochie): ?>
                  <ul class="shop-filters__sublist">
                    <?php foreach ($rochieSubcategories as $sub): ?>
                      <?php
                      $subChecked = in_array($sub['slug'], $selectedSubSlugs, true);
                      $subCount = $subcategoryCounts[$sub['slug']] ?? 0;
                      ?>
                      <li class="shop-filters__subitem">
                        <label class="shop-filters__label shop-filters__label--sub">
                          <input
                            type="checkbox"
                            name="subcategory[]"
                            value="<?= e($sub['slug']) ?>"
                            class="shop-filters__checkbox"
                            data-rochie-sub="1"
                            <?= $subChecked ? 'checked' : '' ?>
                          >
                          <span class="shop-filters__text"><?= e($sub['label']) ?></span>
                          <span class="shop-filters__count">(<?= $subCount ?>)</span>
                        </label>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </fieldset>

        <fieldset class="shop-filters__group shop-filters__group--sizes">
          <legend class="shop-filters__title">Mărime</legend>
          <ul class="shop-filters__list shop-filters__list--inline">
            <?php foreach ($shopSizes as $size): ?>
              <?php $sizeChecked = in_array($size, $selectedSizes, true); ?>
              <li class="shop-filters__item">
                <label class="shop-filters__label">
                  <input
                    type="checkbox"
                    name="size[]"
                    value="<?= e($size) ?>"
                    class="shop-filters__checkbox"
                    <?= $sizeChecked ? 'checked' : '' ?>
                  >
                  <span class="shop-filters__text"><?= e($size) ?></span>
                  <span class="shop-filters__count">(<?= (int) ($sizeCounts[$size] ?? 0) ?>)</span>
                </label>
              </li>
            <?php endforeach; ?>
          </ul>
        </fieldset>

        <?php if ($selectedCategories !== [] || $selectedSubSlugs !== [] || $selectedSizes !== []): ?>
          <p class="shop-filters__actions">
            <a href="<?= e(url('/magazin')) ?>" class="shop-filters__clear">Resetează filtrele</a>
          </p>
        <?php endif; ?>
      </form>
    </aside>

    <div class="shop-catalog min-w-0">
      <?php if ($totalProducts > 0): ?>
        <p class="text-sm text-ink-muted mb-6">Afișate <?= (int) $visibleCount ?> din <?= (int) $totalProducts ?> produse<?= $totalCatalogProducts > 0 && $selectedCategories === [] && $selectedSubSlugs === [] && $selectedSizes === [] ? ' · catalog: ' . (int) $totalCatalogProducts : '' ?></p>
      <?php endif; ?>

      <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
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
              <p class="product-name"><?= e($product['name']) ?></p>
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
    </div>
  </div>
</section>
<script>
  (function () {
    const form = document.getElementById("shopFilters");
    if (!form) return;

    const rochieSlug = <?= json_encode($rochieSlug, JSON_THROW_ON_ERROR) ?>;
    const rochieCategoryInput = form.querySelector('input[name="category[]"][value="' + rochieSlug + '"]');

    function submitFilters() {
      if (typeof form.requestSubmit === "function") {
        form.requestSubmit();
      } else {
        form.submit();
      }
    }

    form.querySelectorAll(".shop-filters__checkbox").forEach(function (checkbox) {
      checkbox.addEventListener("change", function () {
        if (checkbox.dataset.rochieSub === "1" && checkbox.checked && rochieCategoryInput && !rochieCategoryInput.checked) {
          rochieCategoryInput.checked = true;
        }
        submitFilters();
      });
    });
  })();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
