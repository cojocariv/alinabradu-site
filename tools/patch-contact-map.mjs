import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const contactPath = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/contact.php');
let php = fs.readFileSync(contactPath, 'utf8');
const d = 'd' + 'iv';
const sec = 's' + 'ection';

const insertPhp = `$stores = require __DIR__ . '/../config/stores.php';
require_once __DIR__ . '/../config/google_maps.php';
$storesJson = json_encode($stores, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
`;

if (!php.includes('config/stores.php')) {
  php = php.replace('$contactPagePhoto = ', insertPhp + '$contactPagePhoto = ');
}

const mapBlock = `
<${sec} class="contact-map-section" aria-label="Hartă — locații magazine">
  <${d}
    id="contact-stores-map"
    class="contact-map"
    data-stores='<?= $storesJson ?>'
    data-api-key="<?= e(GOOGLE_MAPS_API_KEY) ?>"
  >
    <${d} class="contact-map__panel" role="tablist" aria-label="Selectează magazinul pe hartă">
      <button type="button" class="contact-map__chip is-active" data-store-id="">
        Toate (Chișinău)
      </button>
      <?php foreach ($stores as $store): ?>
      <button
        type="button"
        class="contact-map__chip"
        data-store-id="<?= e($store['id']) ?>"
        role="tab"
      >
        <?= e($store['name']) ?>
      </button>
      <?php endforeach; ?>
    </${d}>
    <iframe
      class="contact-map__embed"
      title="Hartă Google — magazine Alina Bradu"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      allowfullscreen
      src="https://maps.google.com/maps?q=47.028,28.86&amp;hl=ro&amp;z=12&amp;output=embed"
    ></iframe>
  </${d}>
</${sec}>
`;

if (!php.includes('contact-map-section')) {
  php = php.replace(
    /require __DIR__ \. '\/\.\.\/includes\/header\.php';\s*\?>\s*/,
    "require __DIR__ . '/../includes/header.php';\n?>\n" + mapBlock + '\n'
  );
}

if (!php.includes('contact-map.js')) {
  php = php.replace(
    "require __DIR__ . '/../includes/footer.php';",
    '<script src="<?= e(url(\'/assets/js/contact-map.js\')) ?>" defer></script>\n<?php require __DIR__ . \'/../includes/footer.php\'; ?>'
  );
}

php = php.replace(
  '<section class="max-w-6xl mx-auto px-4 py-12">',
  '<section class="contact-page max-w-6xl mx-auto px-4 md:px-6 py-10 md:py-12">'
);

fs.writeFileSync(contactPath, php);
console.log('ok');
