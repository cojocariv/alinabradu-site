  </main>
  <?php
  if (!defined('SITE_EMAIL')) {
      require_once __DIR__ . '/../config/contact.php';
  }
  ?>
  <footer class="site-footer">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-14 grid md:grid-cols-3 gap-10">
      <div>
        <a href="<?= e(url('/')) ?>" class="inline-block mb-4">
          <img src="https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png" alt="Alina Bradu" class="h-9 w-auto brightness-0 invert opacity-90" loading="lazy">
        </a>
        <p class="site-footer__tagline font-serif italic text-cream/80 text-lg leading-snug max-w-xs">Atelier boutique inspirat din portul tradițional moldovenesc.</p>
      </div>
      <div>
        <h3 class="site-footer__heading">Navigare</h3>
        <ul class="site-footer__links">
          <li><a href="<?= e(url('/magazin')) ?>">Magazin</a></li>
          <li><a href="<?= e(url('/despre-noi')) ?>">Despre noi</a></li>
          <li><a href="<?= e(url('/contact')) ?>">Contact</a></li>
          <li><a href="<?= e(url('/cos')) ?>">Coș</a></li>
        </ul>
      </div>
      <div>
        <h3 class="site-footer__heading">Contact</h3>
        <ul class="site-footer__links">
          <li><a href="tel:<?= e(SITE_PHONE_TEL) ?>"><?= e(SITE_PHONE_DISPLAY) ?></a></li>
          <li><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
        </ul>
      </div>
    </div>
    <div class="site-footer__bar">
      <p class="max-w-7xl mx-auto px-4 md:px-6 text-center text-xs text-cream/40 tracking-wide">
        © <?= date('Y') ?> Alina Bradu · Creație cu accent
      </p>
    </div>
  </footer>
  <link rel="stylesheet" href="<?= e(url('/assets/css/product-names.css')) ?>?v=1">
  <script src="<?= e(url('/assets/js/app.js')) ?>"></script>
</body>
</html>
