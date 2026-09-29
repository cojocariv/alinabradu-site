<?php
declare(strict_types=1);

if (!defined('SITE_PHONE_TEL')) {
    require_once __DIR__ . '/../config/contact.php';
}

$homePromo = homePromoVideoConfig();
?>
<section class="home-promo" aria-labelledby="home-promo-title">
  <div class="home-promo__inner max-w-7xl mx-auto px-4 md:px-6">
    <div class="home-promo__grid">
      <div class="home-promo__media">
        <div class="home-promo__video-frame">
          <span class="home-promo__badge" aria-hidden="true">−5%</span>
          <video
            class="home-promo__video"
            src="<?= e($homePromo['video']) ?>"
            controls
            playsinline
            preload="metadata"
            poster="<?= e($homePromo['poster']) ?>"
            aria-label="Video promoțional — voucher cadou și reducere pentru profesori"
          ></video>
        </div>
      </div>

      <div class="home-promo__copy">
        <p class="home-promo__eyebrow">Ofertă cadou · Profesori</p>
        <h2 id="home-promo-title" class="home-promo__title">
          Oferă în dar libertatea de a <em>alege cadoul</em>
        </h2>
        <p class="home-promo__lead">
          Voucher <strong>alinabradu.brand</strong> și <strong>reducere de 5%</strong>
          pentru dragii noștri <span class="home-promo__highlight">profesori</span>.
        </p>
        <p class="home-promo__text">
          Vă așteptăm cu drag în magazinele noastre:
        </p>
        <ul class="home-promo__stores">
          <li>CC Zorile, et. 2</li>
          <li>CC Gemenii, et. 2</li>
          <li>CC UNIC, et. 1 și 3</li>
          <li>Showroom Chișinău, str. Ștefan cel Mare 126</li>
        </ul>
        <div class="home-promo__actions">
          <a href="tel:<?= e(SITE_PHONE_TEL) ?>" class="btn btn--outline home-promo__btn">
            Sună <?= e(SITE_PHONE_DISPLAY) ?>
          </a>
          <a href="<?= e(url('/contact')) ?>" class="btn btn--primary home-promo__btn">Contactează-ne</a>
        </div>
      </div>
    </div>
  </div>
</section>
