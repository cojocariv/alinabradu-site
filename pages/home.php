<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
$headerOverlay = true;
$bodyClass = 'page-home';
$products = ProductModel::featuredHome(12);
if (count($products) === 0) {
    $products = ProductModel::featured(8);
}
$featuredProduct = $products[0] ?? null;
$gridProducts = $featuredProduct ? array_slice($products, 1, 5) : [];
require_once __DIR__ . '/../includes/home_hero_config.php';
$heroVideo = homeHeroVideoConfig();
$headPreloadVideo = $heroVideo['mp4'];
$seo = [
    'title' => 'Alina Bradu — Creație cu accent',
    'description' => 'Modă moldovenească de autor: broderie artizanală, colecții boutique și piese care îmbină tradiția cu eleganța contemporană.',
];
require __DIR__ . '/../includes/header.php';
?>

<section class="home-hero" aria-label="Colecția Alina Bradu">
  <div class="home-hero__bg" aria-hidden="true">
    <div class="home-hero__video-wrap">
      <video
        class="home-hero__video-native"
        src="<?= e($heroVideo['mp4']) ?>"
        autoplay
        muted
        loop
        playsinline
        disablepictureinpicture
        disableremoteplayback
        preload="auto"
        aria-hidden="true"
      ></video>
    </div>
    <script>
    (function () {
      var v = document.querySelector('.home-hero__video-native');
      var wrap = v && v.closest('.home-hero__video-wrap');
      if (!v || !wrap) return;
      v.defaultMuted = true;
      v.muted = true;
      var ready = function () { wrap.classList.add('is-ready'); };
      var play = function () {
        var p = v.play();
        if (p && p.catch) p.catch(function () {});
      };
      v.addEventListener('loadedmetadata', play, { once: true });
      v.addEventListener('loadeddata', ready, { once: true });
      if (v.readyState >= 2) {
        ready();
        play();
      } else if (v.readyState >= 1) {
        play();
      } else {
        play();
      }
    })();
    </script>
    <div class="home-hero__scrim"></div>
  </div>
  <div class="home-hero__inner max-w-7xl mx-auto">
    <div class="home-hero__content">
      <p class="home-hero__eyebrow">Cea mai mare fabrică de broderie din Moldova</p>
      <h1 class="home-hero__title">Eleganță tradițională, <em>reinterpretată</em> cu rafinament</h1>
      <p class="home-hero__lead">Piese create manual, cu broderie care poartă amprenta meșteșugului moldovenesc — pentru femeia care alege autenticitatea, nu trendul.</p>
      <div class="home-hero__actions">
        <a href="<?= e(url('/magazin')) ?>" class="btn btn--primary">Descoperă colecțiile</a>
        <a href="<?= e(url('/despre-noi')) ?>" class="btn btn--ghost-light">Povestea brandului</a>
      </div>
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
      <h2 id="home-products-title" class="home-section-head__title">Top <em>produse</em></h2>
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

<?php require __DIR__ . '/../includes/footer.php'; ?>
