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

$allowedSorts = ['name_asc', 'name_desc', 'price_asc', 'price_desc'];
$selectedSort = trim((string) ($_GET['sort'] ?? ''));
if (!in_array($selectedSort, $allowedSorts, true)) {
    $selectedSort = '';
}

$searchQuery = trim((string) ($_GET['q'] ?? ''));
if (mb_strlen($searchQuery) > 120) {
    $searchQuery = mb_substr($searchQuery, 0, 120);
}

$shopSortOptions = [
    '' => 'Aleatoriu',
    'name_asc' => 'Nume A → Z',
    'name_desc' => 'Nume Z → A',
    'price_asc' => 'Preț crescător',
    'price_desc' => 'Preț descrescător',
];

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
    'sort' => $selectedSort,
    'q' => $searchQuery,
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

$shopPageUrl = static function (int $page) use ($selectedCategories, $selectedSubSlugs, $selectedSizes, $selectedSort, $searchQuery): string {
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
    if ($selectedSort !== '') {
        $params['sort'] = $selectedSort;
    }
    if ($searchQuery !== '') {
        $params['q'] = $searchQuery;
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

require_once __DIR__ . '/../includes/shop_product_card.php';

if (isset($_GET['partial']) && $_GET['partial'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    $offset = ($shopPage - 1) * $shopPerPage;
    $batchProducts = array_slice($allProducts, $offset, $shopPerPage);
    ob_start();
    foreach ($batchProducts as $product) {
        renderShopProductCard($product);
    }
    $html = ob_get_clean();
    $visibleAfter = min($totalProducts, $shopPage * $shopPerPage);
    echo json_encode([
        'html' => $html,
        'visibleCount' => $visibleAfter,
        'totalProducts' => $totalProducts,
        'hasMore' => $visibleAfter < $totalProducts,
        'nextPage' => $shopPage + 1,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$seo = [
    'title' => 'Magazin - Rochii și bluze tradiționale',
    'description' => 'Magazin online Alina Bradu: rochii tradiționale, bluze și fuste premium cu motive etnice.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-12">
  <p class="text-[0.65rem] uppercase tracking-boutique text-gold font-medium mb-2">Colecții</p>
  <h1 class="font-serif text-4xl md:text-5xl text-ink mb-8 font-medium tracking-tight">Shop</h1>

  <?php if ($shopLoadError !== null): ?>
    <p class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">Listă temporar indisponibilă (<?= e($shopLoadError) ?>). Încearcă reîncărcarea paginii.</p>
  <?php endif; ?>

  <div class="lg:grid lg:grid-cols-[minmax(220px,260px)_1fr] gap-8 lg:gap-10 items-start">
    <aside class="shop-filters" aria-label="Filtre magazin">
      <form id="shopFilters" method="get" action="<?= e(url('/magazin')) ?>">
        <?php
        $productSearchQuery = $searchQuery;
        $productSearchInputId = 'shop-search';
        $productSearchLegend = 'Căutare';
        $productSearchStandalone = false;
        require __DIR__ . '/../includes/product_search_bar.php';
        ?>

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

        <fieldset class="shop-filters__group shop-filters__group--sort">
          <legend class="shop-filters__title">Sortare</legend>
          <label class="shop-filters__select-label" for="shop-sort">Ordonează după</label>
          <select name="sort" id="shop-sort" class="shop-filters__select">
            <?php foreach ($shopSortOptions as $sortValue => $sortLabel): ?>
              <option value="<?= e($sortValue) ?>" <?= $selectedSort === $sortValue ? 'selected' : '' ?>><?= e($sortLabel) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if ($selectedSort === ''): ?>
            <p class="shop-filters__hint">Ordine aleatoare (implicit).</p>
          <?php endif; ?>
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

        <?php if ($selectedCategories !== [] || $selectedSubSlugs !== [] || $selectedSizes !== [] || $selectedSort !== '' || $searchQuery !== ''): ?>
          <p class="shop-filters__actions">
            <a href="<?= e(url('/magazin')) ?>" class="shop-filters__clear">Resetează filtrele</a>
          </p>
        <?php endif; ?>
      </form>
    </aside>

    <div class="shop-catalog min-w-0">
      <?php if ($searchQuery !== ''): ?>
        <p class="text-sm text-ink-muted mb-4">Rezultate pentru: <strong class="text-ink font-medium"><?= e($searchQuery) ?></strong></p>
      <?php endif; ?>
      <?php if ($totalProducts > 0): ?>
        <p id="shopCatalogStatus" class="text-sm text-ink-muted mb-6">Afișate <?= (int) $visibleCount ?> din <?= (int) $totalProducts ?> produse<?= $totalCatalogProducts > 0 && $selectedCategories === [] && $selectedSubSlugs === [] && $selectedSizes === [] && $searchQuery === '' ? ' · catalog: ' . (int) $totalCatalogProducts : '' ?></p>
      <?php endif; ?>

      <div id="shopProductGrid" class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($products as $product): ?>
          <?php renderShopProductCard($product); ?>
        <?php endforeach; ?>
      </div>

      <?php if ($hasMoreProducts): ?>
        <div id="shopLoadMoreWrap" class="mt-12 text-center">
          <button
            type="button"
            id="shopLoadMore"
            class="btn btn--outline"
            data-next-page="<?= (int) $shopNextPage ?>"
            data-fallback-url="<?= e($shopPageUrl($shopNextPage)) ?>"
          >Arată mai multe</button>
        </div>
      <?php elseif ($totalProducts === 0): ?>
        <p class="mt-10 text-center text-ink-muted">Niciun produs nu corespunde căutării sau filtrelor selectate.</p>
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

    const sortSelect = document.getElementById("shop-sort");
    if (sortSelect) {
      sortSelect.addEventListener("change", submitFilters);
    }

    const searchInput = document.getElementById("shop-search");
    if (searchInput) {
      let searchDebounce = null;
      searchInput.addEventListener("input", function () {
        window.clearTimeout(searchDebounce);
        searchDebounce = window.setTimeout(submitFilters, 450);
      });
      searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
          window.clearTimeout(searchDebounce);
          submitFilters();
        }
      });
    }

    const loadMoreBtn = document.getElementById("shopLoadMore");
    const productGrid = document.getElementById("shopProductGrid");
    const statusEl = document.getElementById("shopCatalogStatus");
    const loadMoreWrap = document.getElementById("shopLoadMoreWrap");
    const shopBaseUrl = <?= json_encode(url('/magazin'), JSON_THROW_ON_ERROR) ?>;

    if (loadMoreBtn && productGrid) {
      loadMoreBtn.addEventListener("click", async function () {
        const nextPage = parseInt(loadMoreBtn.dataset.nextPage || "2", 10);
        if (!nextPage || loadMoreBtn.disabled) return;

        const params = new URLSearchParams(window.location.search);
        params.set("page", String(nextPage));
        params.set("partial", "1");

        const label = loadMoreBtn.textContent;
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = "Se încarcă…";

        try {
          const res = await fetch(shopBaseUrl + "?" + params.toString(), {
            headers: { Accept: "application/json" },
          });
          if (!res.ok) throw new Error("HTTP " + res.status);
          const data = await res.json();
          if (data.html) {
            productGrid.insertAdjacentHTML("beforeend", data.html);
          }
          if (statusEl && data.visibleCount != null && data.totalProducts != null) {
            statusEl.textContent = "Afișate " + data.visibleCount + " din " + data.totalProducts + " produse";
          }
          params.delete("partial");
          params.set("page", String(nextPage));
          history.replaceState(null, "", shopBaseUrl + (params.toString() ? "?" + params.toString() : ""));

          if (data.hasMore && data.nextPage) {
            loadMoreBtn.dataset.nextPage = String(data.nextPage);
            const fallback = new URL(shopBaseUrl, window.location.origin);
            const fbParams = new URLSearchParams(params);
            fbParams.set("page", String(data.nextPage));
            fallback.search = fbParams.toString();
            loadMoreBtn.dataset.fallbackUrl = fallback.pathname + fallback.search;
          } else if (loadMoreWrap) {
            loadMoreWrap.remove();
          }
        } catch (err) {
          window.location.href = loadMoreBtn.dataset.fallbackUrl || shopBaseUrl;
          return;
        } finally {
          if (loadMoreBtn.isConnected) {
            loadMoreBtn.disabled = false;
            loadMoreBtn.textContent = label;
          }
        }
      });
    }
  })();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
