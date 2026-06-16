  </main>
  <footer class="site-footer">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-14 grid md:grid-cols-2 gap-10">
      <div>
        <a href="<?= e(url('/')) ?>" class="inline-block mb-4">
          <img src="https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png" alt="Alina Bradu" class="h-9 md:h-10 w-auto opacity-95" loading="lazy">
        </a>
        <p class="site-footer__tagline font-serif italic text-cream/80 text-lg leading-snug max-w-xs">Fabrica de broderie inspirat din portul tradițional moldovenesc.</p>
      </div>
      <div>
        <h3 class="site-footer__heading">Navigare</h3>
        <ul class="site-footer__links">
          <li><a href="<?= e(url('/magazin')) ?>">Catalog</a></li>
          <li><a href="<?= e(url('/despre-noi')) ?>">Despre noi</a></li>
          <li><a href="<?= e(url('/contact')) ?>">Contact</a></li>
          <li><a href="<?= e(url('/cos')) ?>">Coș</a></li>
        </ul>
      </div>
    </div>
    <div class="site-footer__bar">
      <div class="site-footer__bar-inner max-w-7xl mx-auto px-4 md:px-6">
        <p class="site-footer__copy text-xs text-cream/40 tracking-wide">
          © <?= date('Y') ?> Alina Bradu · Creație cu accent
        </p>
        <p class="site-footer__credit text-xs text-cream/35 tracking-wide">
          Created by <a href="https://smartsolutions.md" target="_blank" rel="noopener noreferrer" class="site-footer__credit-link">smartsolutions.md</a>
        </p>
      </div>
    </div>
  </footer>
  <?php
  if (!defined('SITE_PHONE_TEL')) {
      require_once __DIR__ . '/../config/contact.php';
  }
  $chatPhoneRaw = preg_replace('/\D+/', '', (string) SITE_PHONE_TEL) ?? '';
  $chatPhone = ltrim($chatPhoneRaw, '+');
  $chatMessagePlain = 'Bună! Aș dori mai multe detalii despre produse.';
  $whatsAppUrl = 'https://wa.me/' . $chatPhone . '?text=' . rawurlencode($chatMessagePlain);
  $viberUrl = productViberOrderUrl($chatMessagePlain);
  ?>
  <div class="chat-fab" aria-label="Contact rapid">
    <div class="chat-fab__trigger-wrap">
      <svg class="chat-fab__label-ring" viewBox="0 0 140 140" aria-hidden="true">
        <defs>
          <path id="chatFabTextPath" fill="none" d="M 70,70 m -56,0 a 56,56 0 1,1 112,0 a 56,56 0 1,1 -112,0"></path>
        </defs>
        <text class="chat-fab__label-ring-text">
          <textPath href="#chatFabTextPath" xlink:href="#chatFabTextPath" startOffset="50%" text-anchor="middle">· Discută cu noi ·</textPath>
        </text>
      </svg>
      <button
        type="button"
        class="chat-fab__trigger"
        aria-label="Discută cu noi — deschide opțiuni chat"
        title="Discută cu noi"
      >
        <span class="chat-fab__trigger-icon" aria-hidden="true">💬</span>
      </button>
    </div>
    <div class="chat-fab__menu" role="menu" aria-label="Alege aplicația de chat">
      <a
        href="<?= e($whatsAppUrl) ?>" data-whatsapp-popup="1" data-chat-source="footer_fab"
        class="chat-fab__btn chat-fab__btn--whatsapp"
        target="_blank"
        rel="noopener noreferrer"
        role="menuitem"
        aria-label="Chat WhatsApp"
        title="Scrie pe WhatsApp"
      >
        <span class="chat-fab__icon" aria-hidden="true">W</span>
        <span class="chat-fab__label">WhatsApp</span>
      </a>
      <a
        href="<?= e($viberUrl) ?>" data-viber-popup="1" data-chat-source="footer_fab"
        class="chat-fab__btn chat-fab__btn--viber"
        target="_blank"
        rel="noopener noreferrer"
        role="menuitem"
        aria-label="Chat Viber"
        title="Scrie pe Viber"
      >
        <span class="chat-fab__icon" aria-hidden="true">V</span>
        <span class="chat-fab__label">Viber</span>
      </a>
    </div>
  </div>
  <button
    type="button"
    class="scroll-top"
    id="scrollTopBtn"
    aria-label="Înapoi sus"
    title="Înapoi sus"
    hidden
  >
    <svg class="scroll-top__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M12 19V5M5 12l7-7 7 7"/>
    </svg>
  </button>
  <link rel="stylesheet" href="<?= e(url('/assets/css/product-names.css')) ?>?v=2">
  <script src="<?= e(url('/assets/js/app.js')) ?>?v=9"></script>
</body>
</html>
