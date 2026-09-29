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
            autoplay
            muted
            loop
            playsinline
            disablepictureinpicture
            disableremoteplayback
            preload="auto"
            poster="<?= e($homePromo['poster']) ?>"
            aria-label="Video promoțional — voucher cadou și reducere pentru profesori"
          ></video>
        </div>
        <script>
        (function () {
          var v = document.querySelector('.home-promo__video');
          var frame = v && v.closest('.home-promo__video-frame');
          if (!v || !frame) return;
          v.defaultMuted = true;
          v.muted = true;
          var ready = function () { frame.classList.add('is-ready'); };
          var play = function () {
            var p = v.play();
            if (p && p.catch) p.catch(function () {});
          };
          v.addEventListener('loadeddata', ready, { once: true });
          if (v.readyState >= 2) {
            ready();
            play();
          } else {
            v.addEventListener('loadedmetadata', play, { once: true });
            play();
          }
        })();
        </script>
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
