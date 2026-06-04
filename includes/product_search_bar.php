<?php
declare(strict_types=1);

/**
 * Căutare produse după denumire → /magazin?q=
 *
 * @var string $productSearchQuery
 * @var string $productSearchInputId
 * @var string $productSearchLabel
 * @var string $productSearchPlaceholder
 * @var string $productSearchWrapperClass clase extra pe wrapper (form sau fieldset)
 * @var bool $productSearchStandalone true = form propriu; false = doar câmpuri (în #shopFilters)
 * @var string|null $productSearchLegend legendă fieldset (doar embedded)
 */
$productSearchQuery = $productSearchQuery ?? '';
$productSearchInputId = $productSearchInputId ?? 'shop-search';
$productSearchLabel = $productSearchLabel ?? 'Denumire produs';
$productSearchPlaceholder = $productSearchPlaceholder ?? 'Ex: Rochie Andreea';
$productSearchWrapperClass = $productSearchWrapperClass ?? '';
$productSearchStandalone = $productSearchStandalone ?? false;
$productSearchLegend = $productSearchLegend ?? null;

$openForm = $productSearchStandalone
    ? '<form method="get" action="' . e(url('/magazin')) . '" class="product-search ' . e($productSearchWrapperClass) . '" role="search">'
    : '';
$closeForm = $productSearchStandalone ? '</form>' : '';

$openFieldset = !$productSearchStandalone && $productSearchLegend !== null
    ? '<fieldset class="shop-filters__group shop-filters__group--search ' . e($productSearchWrapperClass) . '"><legend class="shop-filters__title">' . e($productSearchLegend) . '</legend>'
    : (!$productSearchStandalone && $productSearchWrapperClass !== ''
        ? '<div class="' . e($productSearchWrapperClass) . '">'
        : '');
$closeFieldset = !$productSearchStandalone && $productSearchLegend !== null
    ? '</fieldset>'
    : (!$productSearchStandalone && $productSearchWrapperClass !== '' ? '</div>' : '');

echo $openForm . $openFieldset;
?>
<label class="shop-filters__select-label product-search__label" for="<?= e($productSearchInputId) ?>"><?= e($productSearchLabel) ?></label>
<div class="shop-search">
  <input
    type="search"
    name="q"
    id="<?= e($productSearchInputId) ?>"
    class="shop-search__input"
    value="<?= e($productSearchQuery) ?>"
    placeholder="<?= e($productSearchPlaceholder) ?>"
    autocomplete="off"
    enterkeyhint="search"
  >
  <button type="submit" class="shop-search__btn">Caută</button>
</div>
<?= $closeFieldset . $closeForm ?>
