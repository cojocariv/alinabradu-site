import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../produs.html');
let s = fs.readFileSync(p, 'utf8');
const d = 'd' + 'iv';

const badBlock = /<motion><\/motion>\s*<div class="toolbar">[\s\S]*?<\/div>\s*\n\s*<label class="dropzone"/;
const good =
  '<div class="toolbar">\n' +
  '            <' +
  d +
  ' class="field">\n' +
  '                <label for="uploadPrefix">Subfolder la încărcare (opțional)</label>\n' +
  '                <input type="text" id="uploadPrefix" placeholder="ex: 2026/produse/" autocomplete="off">\n' +
  '                <p style="margin:0.35rem 0 0;font-size:0.75rem;color:var(--ink-soft)">Fără slash la început; se adaugă înaintea numelui fișierului.</p>\n' +
  '            </' +
  d +
  '>\n' +
  '            <button type="button" class="btn btn--ghost" id="btnRefresh">Reîncarcă lista</button>\n' +
  '        </' +
  d +
  '>\n\n        <label class="dropzone"';

if (badBlock.test(s)) {
  s = s.replace(badBlock, good);
} else {
  s = s.replace(/<motion><\/motion>/g, '');
  s = s.replace(
    /<div class="toolbar">\s*<div class="field">/,
    '<motion></motion>'
  );
  s = s.split('<motion></motion>').join('<' + d + ' class="toolbar">\n            <' + d + ' class="field">');
}

s = s.replace(/<motion>/g, '<' + d + '>');
s = s.replace(/<\/motion>/g, '</' + d + '>');

fs.writeFileSync(p, s);
console.log('ok', s.includes('class="toolbar"'));
