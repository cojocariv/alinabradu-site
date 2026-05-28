<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
if (!defined('SITE_EMAIL')) {
    require_once __DIR__ . '/../config/contact.php';
}
$headerOverlay = true;
$products = ProductModel::featuredHome(12);
if (count($products) === 0) {
    $products = ProductModel::featured(8);
}
$featuredProduct = $products[0] ?? null;
$gridProducts = $featuredProduct ? array_slice($products, 1, 5) : [];
$heroImage = 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png';
$seo = [
    'title' => 'Alina Bradu — Creație cu accent',
    'description' => 'Modă moldovenească de autor: broderie artizanală, colecții boutique și piese care îmbină tradiția cu eleganța contemporană.',
];
require __DIR__ . '/../includes/header.php';
?>

<section class="home-hero" aria-label="Colecția Alina Bradu">
  <div class="home-hero__inner max-w-7xl mx-auto">
    <div class="home-hero__content">
      <p class="home-hero__eyebrow">Cea mai mare fabrică de broderie din Moldova</p>
      <h1 class="home-hero__title">Eleganță tradițională, <em>reinterpretată</em> cu rafinament</h1>
      <p class="home-hero__lead">Piese create manual, cu broderie care poartă amprenta meșteșugului moldovenesc — pentru femeia care alege autenticitatea, nu trendul.</p>
      <div class="home-hero__actions">
        <a href="<?= e(url('/magazin')) ?>" class="btn btn--primary">Descoperă colecțiile</a>
        <a href="<?= e(url('/despre-noi')) ?>" class="btn btn--ghost">Povestea brandului</a>
      </div>
    </div>
    <div class="home-hero__separator" aria-hidden="true"></div>
    <div class="home-hero__visual">
      <img src="<?= e($heroImage) ?>" alt="Colecție Alina Bradu" class="home-hero__image" fetchpriority="high" width="900" height="1100">
    </div>
  </div>
</section>

<div class="home-marquee" aria-hidden="true">
  <div class="home-marquee__track">
    <span class="home-marquee__text">Broderie artizanală</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Colecții sezoniere</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Piese unicat</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Tradiție &amp; contemporan</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Modă moldovenească de autor</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Broderie artizanală</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Colecții sezoniere</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Piese unicat</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Tradiție &amp; contemporan</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
    <span class="home-marquee__text">Modă moldovenească de autor</span>
    <span class="home-marquee__dot" aria-hidden="true">◆</span>
  </div>
</div>

<section class="home-products" aria-labelledby="home-products-title">
  <div class="max-w-7xl mx-auto px-4 md:px-6">
    <header class="home-section-head">
      <p class="home-section-head__eyebrow">Noutăți din fabrică</p>
      <h2 id="home-products-title" class="home-section-head__title">Selecție <em>curată</em></h2>
      <a href="<?= e(url('/magazin')) ?>" class="home-section-head__link">Vezi magazinul →</a>
    </header>
    <?php if ($featuredProduct || count($gridProducts) > 0): ?>
    <div class="home-products__grid">
      <?php if ($featuredProduct): ?>
      <article class="home-products__card home-products__card--featured card-hover">
        <a href="<?= e(url('/produs/' . $featuredProduct['slug'])) ?>" class="home-products__media">
          <img src="<?= e(ProductModel::getPrimaryImageUrl($featuredProduct)) ?>" alt="<?= e($featuredProduct['name']) ?>" class="home-products__img" fetchpriority="high">
        </a>
        <div class="home-products__body">
          <span class="home-products__badge">Piesă evidențiată</span>
          <p class="home-products__name product-name"><?= e($featuredProduct['name']) ?></p>
          <p class="home-products__cat"><?= e($featuredProduct['category']) ?></p>
          <p class="home-products__price"><?= e(formatPrice((float) $featuredProduct['price'])) ?></p>
        </div>
      </article>
      <?php endif; ?>
      <?php foreach ($gridProducts as $product): ?>
      <article class="home-products__card card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="home-products__media">
          <img src="<?= e(ProductModel::getPrimaryImageUrl($product)) ?>" alt="<?= e($product['name']) ?>" class="home-products__img" loading="lazy">
        </a>
        <div class="home-products__body">
          <p class="home-products__name product-name"><?= e($product['name']) ?></p>
          <p class="home-products__cat"><?= e($product['category']) ?></p>
          <p class="home-products__price"><?= e(formatPrice((float) $product['price'])) ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-ink-soft text-center py-12">Momentan nu sunt produse evidențiate. Vizitează <a href="<?= e(url('/magazin')) ?>" class="text-gold underline">magazinul</a>.</p>
    <?php endif; ?>
  </div>
</section>

<section class="home-about" aria-labelledby="home-about-title">
  <div class="max-w-7xl mx-auto px-4 md:px-6">
    <div class="home-about__grid">
      <div class="home-about__copy">
        <p class="home-section-head__eyebrow">Despre noi</p>
        <h2 id="home-about-title" class="home-section-head__title home-section-head__title--left">Unde <em>tradiția</em> devine semnătură</h2>
        <p class="home-about__text">„Alina Bradu” este un brand național cu dragoste pentru broderia tradițională moldovenească, transformată în design contemporan. Fiecare piesă este o poveste — nu doar îmbrăcăminte.</p>
        <a href="<?= e(url('/despre-noi')) ?>" class="btn btn--outline">Citește povestea completă</a>
      </div>
      <ul class="home-about__stats">
        <li class="home-about__stat">
          <span class="home-about__stat-value">15+</span>
          <span class="home-about__stat-label">ani de meșteșug</span>
        </li>
        <li class="home-about__stat">
          <span class="home-about__stat-value">98</span>
          <span class="home-about__stat-label">colecții în 15 ani</span>
        </li>
        <li class="home-about__stat">
          <span class="home-about__stat-value">100%</span>
          <span class="home-about__stat-label">calitate</span>
        </li>
        <li class="home-about__stat">
          <span class="home-about__stat-value">din Moldova</span>
          <span class="home-about__stat-label">cea mai mare fabrică de broderie</span>
        </li>
      </ul>
    </div>
  </div>
</section>

<section class="home-contact" aria-labelledby="home-contact-title">
  <div class="max-w-7xl mx-auto px-4 md:px-6">
    <div class="home-contact__inner">
      <header class="home-contact__header">
        <p class="home-contact__eyebrow">Contact</p>
        <h2 id="home-contact-title" class="home-contact__title">Suntem aici pentru <em>consultații</em> și comenzi</h2>
        <p class="home-contact__lead">Programează o vizită la fabrică, întreabă despre mărimi sau comenzi personalizate — răspundem cu grijă, ca într-o casă de modă.</p>
      </header>
      <div class="home-contact__channels">
        <a href="tel:<?= e(SITE_PHONE_TEL) ?>" class="home-contact__channel">
          <span class="home-contact__channel-label">Telefon</span>
          <span class="home-contact__channel-value"><?= e(SITE_PHONE_DISPLAY) ?></span>
        </a>
        <a href="mailto:<?= e(SITE_EMAIL) ?>" class="home-contact__channel">
          <span class="home-contact__channel-label">Email</span>
          <span class="home-contact__channel-value"><?= e(SITE_EMAIL) ?></span>
        </a>
        <a href="https://api.whatsapp.com/send?phone=37368693056" target="_blank" rel="noopener noreferrer" class="home-contact__channel">
          <span class="home-contact__channel-label">WhatsApp</span>
          <span class="home-contact__channel-value">Scrie-ne acum</span>
        </a>
      </div>
      <div class="home-contact__actions">
        <a href="<?= e(url('/contact')) ?>" class="btn btn--light">Formular de contact</a>
        <a href="<?= e(url('/magazin')) ?>" class="btn btn--ghost-light">Explorează magazinul</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
