import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/contact.php');
let s = fs.readFileSync(p, 'utf8');
const d = 'd' + 'iv';
const bad = '<' + 'motion></' + 'motion>';
const canvas =
  '<' +
  d +
  ' class="contact-map__canvas" role="img" aria-label="Hartă cu locațiile magazinelor"></' +
  d +
  '>';

s = s.split(bad).join(canvas);

if (!s.includes('contact-stores-data')) {
  s = s.replace(
    '<section class="contact-map-section"',
    '<script type="application/json" id="contact-stores-data"><?= $storesJson ?></script>\n<section class="contact-map-section"'
  );
}

s = s.replace(/\s*data-stores='<\?= \$storesJson \?>'/, '');

if (!s.includes('contact-map.js?v=3')) {
  s = s.replace(/contact-map\.js(\?v=\d+)?/, 'contact-map.js?v=3');
}

fs.writeFileSync(p, s);
console.log('fixed', s.includes('contact-map__canvas'), s.includes('contact-stores-data'));
