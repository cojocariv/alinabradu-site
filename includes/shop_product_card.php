<?php
declare(strict_types=1);

/**
 * Card produs pentru grila magazinului.
 */
function renderShopProductCard(array $product): void
{
    if (!defined('SITE_PHONE_TEL')) {
        require_once __DIR__ . '/../config/contact.php';
    }
    $imgUrl = ProductModel::getPrimaryImageUrl($product);
    $galleryUrls = ProductModel::getImageUrls((int) $product['id']);
    $hoverImgUrl = null;
    if (isset($galleryUrls[1])) {
        $second = trim((string) $galleryUrls[1]);
        $first = trim((string) ($galleryUrls[0] ?? $imgUrl));
        if ($second !== '' && $second !== $first) {
            $hoverImgUrl = $second;
        }
    }
    $inStock = (int) ($product['in_stock'] ?? 1) === 1;
    $sizesList = array_filter(array_map('trim', explode(',', (string) $product['size'])));
    $firstSize = $sizesList[0] ?? '';
    $orderMessage = productOrderMessage((string) $product['name'], (string) $product['slug'], $imgUrl);
    $whatsAppUrl = productWhatsAppOrderUrl($orderMessage);
    $viberUrl = productViberOrderUrl($orderMessage);
    $compact = true;
    $chatSource = 'shop_card';
    $chatProductId = (int) $product['id'];
    $chatProductSlug = (string) $product['slug'];
    $chatProductName = (string) $product['name'];
    ?>
    <article class="bg-white overflow-hidden border border-gold/30 card-hover">
      <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="block bg-cream">
        <div class="product-card-media h-80 bg-cream p-3 flex items-center justify-center<?= $hoverImgUrl ? ' product-card-media--has-hover' : '' ?>">
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
        <div class="mt-3 flex items-center gap-3 flex-wrap">
          <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="inline-block text-sm underline">Vezi produs</a>
          <?php require __DIR__ . '/product_order_chat_buttons.php'; ?>
        </div>
      </div>
    </article>
    <?php
}
