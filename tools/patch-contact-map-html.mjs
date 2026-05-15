import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/contact.php');
let php = fs.readFileSync(p, 'utf8');

const leafletCss =
  '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">';
const leafletJs =
  '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>';

if (!php.includes('leaflet@1.9.4')) {
  php = php.replace(
    '<section class="contact-map-section"',
    leafletCss + '\n' + leafletJs + '\n<section class="contact-map-section"'
  );
}

const oldBlock = /<section class="contact-map-section"[\s\S]*?<\/section>\n\n/;
const d = 'd' + 'iv';
const newBlock = `<section class="contact-map-section" aria-label="Hartă — locații magazine">
  <script type="application/json" id="contact-stores-data"><?= $storesJson ?></script>
  <${d}
    id="contact-stores-map"
    class="contact-map"
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
    <${d} class="contact-map__canvas" role="img" aria-label="Hartă interactivă cu locațiile magazinelor"></${d}>
  </${d}>
</section>

`;

php = php.replace(oldBlock, newBlock);

if (!php.includes('contact-map.js?v=2')) {
  php = php.replace(
    "contact-map.js')) ?>",
    "contact-map.js?v=2')) ?>"
  );
}

fs.writeFileSync(p, php);
console.log('contact.php map html updated');
