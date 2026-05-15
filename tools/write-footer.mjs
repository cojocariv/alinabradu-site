import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../includes/footer.php');
const d = 'd' + 'iv';

const content = `  </main>
  <?php
  if (!defined('SITE_EMAIL')) {
      require_once __DIR__ . '/../config/contact.php';
  }
  ?>
  <footer class="site-footer">
    <${d} class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-14 grid md:grid-cols-3 gap-10">
      <${d}>
        <a href="<?= e(url('/')) ?>" class="inline-block mb-4">
          <img src="https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png" alt="Alina Bradu" class="h-9 w-auto brightness-0 invert opacity-90" loading="lazy">
        </a>
        <p class="site-footer__tagline font-serif italic text-cream/80 text-lg leading-snug max-w-xs">Atelier boutique inspirat din portul tradițional moldovenesc.</p>
      </${d}>
      <${d}>
        <h3 class="site-footer__heading">Navigare</h3>
        <ul class="site-footer__links">
          <li><a href="<?= e(url('/magazin')) ?>">Magazin</a></li>
          <li><a href="<?= e(url('/despre-noi')) ?>">Despre noi</a></li>
          <li><a href="<?= e(url('/contact')) ?>">Contact</a></li>
          <li><a href="<?= e(url('/cos')) ?>">Coș</a></li>
        </ul>
      </${d}>
      <${d}>
        <h3 class="site-footer__heading">Contact</h3>
        <ul class="site-footer__links">
          <li><a href="tel:<?= e(SITE_PHONE_TEL) ?>"><?= e(SITE_PHONE_DISPLAY) ?></a></li>
          <li><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
        </ul>
      </${d}>
    </${d}>
    <${d} class="site-footer__bar">
      <p class="max-w-7xl mx-auto px-4 md:px-6 text-center text-xs text-cream/40 tracking-wide">
        © <?= date('Y') ?> Alina Bradu · Creație cu accent
      </p>
    </${d}>
  </footer>
  <script src="<?= e(url('/assets/js/app.js')) ?>"></script>
</body>
</html>
`;

fs.writeFileSync(p, content);
console.log('footer ok');
