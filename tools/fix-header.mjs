import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../includes/header.php');
let s = fs.readFileSync(p, 'utf8');
const bad = '<' + 'motion></' + 'motion>';
const el = 'd' + 'iv';
const wrapOpen = `<${el} class="max-w-7xl mx-auto px-4 md:px-6 py-3.5 flex items-center justify-between gap-3">`;

s = s.replace(
  /(<header class="site-header[^"]*">)\s*(?:<motion><\/motion>\s*)?/,
  `$1\n    ${wrapOpen}\n      `
);
s = s.split(bad).join('');
fs.writeFileSync(p, s);
console.log('ok');
