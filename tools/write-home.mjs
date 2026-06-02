import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/home.php');
const d = 'd' + 'iv';
const sec = 's' + 'ection';
const art = 'a' + 'rticle';
const span = 's' + 'pan';

const productCard = (product, featured = false) => `      <${art} class="home-products__card${featured ? ' home-products__card--featured card-hover' : ' card-hover'}">
        <a href="<?= e(url('/produs/${product['slug']}')) ?>" class="home-products__media">
          <img src="<?= e(ProductModel::getPrimaryImageUrl($product)) ?>" alt="<?= e($product['name']) ?>" class="home-products__img" ${featured ? 'fetchpriority="high"' : 'loading="lazy"'}>
        </a>
        <${d} class="home-products__body">
          ${featured ? `<${span} class="home-products__badge">Piesă evidențiată</${span}>` : ''}
          <h3 class="home-products__name"><?= e($product['name']) ?></h3>
          <p class="home-products__cat"><?= e($product['category']) ?></p>
          <p class="home-products__price"><?= e(formatPrice((float) $product['price'])) ?></p>
        </${d}>
      </${art}>`;

// Use PHP in template - the above won't work with slug interpolation in JS. Write raw PHP template instead.

const content = `<?php
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
$heroVideoId = 'sRy1nbNMFrQ';
$seo = [
    'title' => 'Alina Bradu — Creație cu accent',
    'description' => 'Modă moldovenească de autor: broderie artizanală, colecții boutique și piese care îmbină tradiția cu eleganța contemporană.',
];
require __DIR__ . '/../includes/header.php';
?>

<${sec} class="home-hero" aria-label="Colecția Alina Bradu">
  <${d} class="home-hero__bg" aria-hidden="true">
    <${d} class="home-hero__video-wrap">
      <iframe
        class="home-hero__video"
        src="https://www.youtube-nocookie.com/embed/<?= e($heroVideoId) ?>?autoplay=1&amp;mute=1&amp;loop=1&amp;playlist=<?= e($heroVideoId) ?>&amp;controls=0&amp;disablekb=1&amp;fs=0&amp;playsinline=1&amp;rel=0&amp;modestbranding=1&amp;iv_load_policy=3&amp;cc_load_policy=0"
        title=""
        tabindex="-1"
        allow="autoplay; encrypted-media"
        referrerpolicy="strict-origin-when-cross-origin"
      ></iframe>
      <${d} class="home-hero__video-shield" aria-hidden="true"></${d}>
    </${d}>
    <${d} class="home-hero__scrim"></${d}>
  </${d}>
  <${d} class="home-hero__inner max-w-7xl mx-auto">
    <${d} class="home-hero__content">
      <p class="home-hero__eyebrow">Atelier boutique · Moldova</p>
      <h1 class="home-hero__title">Eleganță tradițională, <em>reinterpretată</em> cu rafinament</h1>
      <p class="home-hero__lead">Piese create manual, cu broderie care poartă amprenta meșteșugului moldovenesc — pentru femeia care alege autenticitatea, nu trendul.</p>
      <${d} class="home-hero__actions">
        <a href="<?= e(url('/magazin')) ?>" class="btn btn--primary">Descoperă colecțiile</a>
        <a href="<?= e(url('/despre-noi')) ?>" class="btn btn--ghost-light">Povestea brandului</a>
      </${d}>
    </${d}>
  </${d}>
</${sec}>

<${d} class="home-marquee" aria-hidden="true">
  <${d} class="home-marquee__track">
    <${span} class="home-marquee__text">Broderie artizanală</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Colecții sezoniere</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Piese unicat</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Tradiție &amp; contemporan</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Atelier Chișinău</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Modă moldovenească de autor</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Broderie artizanală</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Colecții sezoniere</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Piese unicat</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Tradiție &amp; contemporan</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Atelier Chișinău</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
    <${span} class="home-marquee__text">Modă moldovenească de autor</${span}>
    <${span} class="home-marquee__dot" aria-hidden="true">◆</${span}>
  </${d}>
</${d}>

<${sec} class="home-products" aria-labelledby="home-products-title">
  <${d} class="max-w-7xl mx-auto px-4 md:px-6">
    <header class="home-section-head">
      <p class="home-section-head__eyebrow">Noutăți din atelier</p>
      <h2 id="home-products-title" class="home-section-head__title">Top <em>produse</em></h2>
      <a href="<?= e(url('/magazin')) ?>" class="home-section-head__link">Vezi magazinul →</a>
    </header>
    <?php if ($featuredProduct || count($gridProducts) > 0): ?>
    <${d} class="home-products__grid">
      <?php if ($featuredProduct): ?>
      <${art} class="home-products__card home-products__card--featured card-hover">
        <a href="<?= e(url('/produs/' . $featuredProduct['slug'])) ?>" class="home-products__media">
          <img src="<?= e(ProductModel::getPrimaryImageUrl($featuredProduct)) ?>" alt="<?= e($featuredProduct['name']) ?>" class="home-products__img" fetchpriority="high">
        </a>
        <${d} class="home-products__body">
          <${span} class="home-products__badge">Piesă evidențiată</${span}>
          <h3 class="home-products__name"><?= e($featuredProduct['name']) ?></h3>
          <p class="home-products__cat"><?= e($featuredProduct['category']) ?></p>
          <p class="home-products__price"><?= e(formatPrice((float) $featuredProduct['price'])) ?></p>
        </${d}>
      </${art}>
      <?php endif; ?>
      <?php foreach ($gridProducts as $product): ?>
      <${art} class="home-products__card card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>" class="home-products__media">
          <img src="<?= e(ProductModel::getPrimaryImageUrl($product)) ?>" alt="<?= e($product['name']) ?>" class="home-products__img" loading="lazy">
        </a>
        <${d} class="home-products__body">
          <h3 class="home-products__name"><?= e($product['name']) ?></h3>
          <p class="home-products__cat"><?= e($product['category']) ?></p>
          <p class="home-products__price"><?= e(formatPrice((float) $product['price'])) ?></p>
        </${d}>
      </${art}>
      <?php endforeach; ?>
    </${d}>
    <?php else: ?>
    <p class="text-ink-soft text-center py-12">Momentan nu sunt produse evidențiate. Vizitează <a href="<?= e(url('/magazin')) ?>" class="text-gold underline">magazinul</a>.</p>
    <?php endif; ?>
  </${d}>
</${sec}>

<${sec} class="home-about" aria-labelledby="home-about-title">
  <${d} class="max-w-7xl mx-auto px-4 md:px-6">
    <${d} class="home-about__grid">
      <${d} class="home-about__copy">
        <p class="home-section-head__eyebrow">Despre noi</p>
        <h2 id="home-about-title" class="home-section-head__title home-section-head__title--left">Unde <em>tradiția</em> devine semnătură</h2>
        <p class="home-about__text">„Alina Bradu” este un brand național cu dragoste pentru broderia tradițională moldovenească, transformată în design contemporan. Fiecare piesă este o poveste — nu doar îmbrăcăminte.</p>
        <a href="<?= e(url('/despre-noi')) ?>" class="btn btn--outline">Citește povestea completă</a>
      </${d}>
      <ul class="home-about__stats">
        <li class="home-about__stat">
          <${span} class="home-about__stat-value">15+</${span}>
          <${span} class="home-about__stat-label">ani de meșteșug</${span}>
        </li>
        <li class="home-about__stat">
          <${span} class="home-about__stat-value">8</${span}>
          <${span} class="home-about__stat-label">colecții active</${span}>
        </li>
        <li class="home-about__stat">
          <${span} class="home-about__stat-value">100%</${span}>
          <${span} class="home-about__stat-label">lucrat manual</${span}>
        </li>
        <li class="home-about__stat">
          <${span} class="home-about__stat-value">1</${span}>
          <${span} class="home-about__stat-label">atelier boutique</${span}>
        </li>
      </ul>
    </${d}>
  </${d}>
</${sec}>

<${sec} class="home-contact" aria-labelledby="home-contact-title">
  <${d} class="max-w-7xl mx-auto px-4 md:px-6">
    <${d} class="home-contact__inner">
      <header class="home-contact__header">
        <p class="home-contact__eyebrow">Contact</p>
        <h2 id="home-contact-title" class="home-contact__title">Suntem aici pentru <em>consultații</em> și comenzi</h2>
        <p class="home-contact__lead">Programează o vizită la atelier, întreabă despre mărimi sau comenzi personalizate — răspundem cu grijă, ca într-o casă de modă.</p>
      </header>
      <${d} class="home-contact__channels">
        <a href="tel:<?= e(SITE_PHONE_TEL) ?>" class="home-contact__channel">
          <${span} class="home-contact__channel-label">Telefon</${span}>
          <${span} class="home-contact__channel-value"><?= e(SITE_PHONE_DISPLAY) ?></${span}>
        </a>
        <a href="mailto:<?= e(SITE_EMAIL) ?>" class="home-contact__channel">
          <${span} class="home-contact__channel-label">Email</${span}>
          <${span} class="home-contact__channel-value"><?= e(SITE_EMAIL) ?></${span}>
        </a>
        <a href="https://api.whatsapp.com/send?phone=37368693056" target="_blank" rel="noopener noreferrer" class="home-contact__channel">
          <${span} class="home-contact__channel-label">WhatsApp</${span}>
          <${span} class="home-contact__channel-value">Scrie-ne acum</${span}>
        </a>
      </${d}>
      <${d} class="home-contact__actions">
        <a href="<?= e(url('/contact')) ?>" class="btn btn--light">Formular de contact</a>
        <a href="<?= e(url('/magazin')) ?>" class="btn btn--ghost-light">Explorează magazinul</a>
      </${d}>
    </${d}>
  </${d}>
</${sec}>

<?php require __DIR__ . '/../includes/footer.php'; ?>
`;

fs.writeFileSync(p, content);
console.log('home written');
