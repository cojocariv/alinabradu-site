import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const root = path.join(path.dirname(fileURLToPath(import.meta.url)), '..');
const src = fs.readFileSync(path.join(root, 'produs.html'), 'utf8');

const logoutBar =
  '<p class="topbar" style="display:flex;justify-content:flex-end;margin:0 0 1rem">' +
  '<a href="<?= e(url(\'/produs.php?logout=1\')) ?>" class="btn btn--ghost btn--sm">Deconectare</a></p>\n';

let body = src.replace(
  /const sasUrl = "[^"]+";/,
  'const sasUrl = <?= json_encode($sasUrl, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;'
);
body = body.replace('<body>\n\n    <h1>', '<body>\n\n' + logoutBar + '    <h1>');
body = body.replace(
  'SAS-ul din acest fișier trebuie',
  'SAS-ul din <code>config/azure_storage.php</code> trebuie'
);

const out =
  '<?php\n' +
  'declare(strict_types=1);\n' +
  '/** @var string $sasUrl */\n' +
  '?>\n' +
  body;

fs.writeFileSync(path.join(root, 'pages/storage-manager.php'), out);
try {
  fs.unlinkSync(path.join(root, 'produs.html'));
} catch {
  /* already removed */
}
console.log('built storage-manager.php');
