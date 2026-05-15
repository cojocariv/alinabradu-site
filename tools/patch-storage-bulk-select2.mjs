import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/storage-manager.php');
let s = fs.readFileSync(p, 'utf8');
const d = 'd' + 'iv';

const helpers = `        const selectedBlobs = new Set();

        function pruneSelection() {
            const existing = new Set(allBlobs);
            for (const name of selectedBlobs) {
                if (!existing.has(name)) selectedBlobs.delete(name);
            }
        }

        function updateBulkBar() {
            const n = selectedBlobs.size;
            const countEl = document.getElementById("selectionCount");
            const delBtn = document.getElementById("btnDeleteSelected");
            const clearBtn = document.getElementById("btnClearSelection");
            const selectAll = document.getElementById("selectAllVisible");
            if (!countEl) return;
            countEl.textContent = n + (n === 1 ? " selectat" : " selectate");
            countEl.classList.toggle("has-selection", n > 0);
            delBtn.disabled = n === 0 || busy;
            clearBtn.disabled = n === 0 || busy;
            const visible = getFilteredBlobs();
            const selectedVisible = visible.filter((name) => selectedBlobs.has(name)).length;
            selectAll.checked = visible.length > 0 && selectedVisible === visible.length;
            selectAll.indeterminate = selectedVisible > 0 && selectedVisible < visible.length;
        }

        function setCardSelected(card, on) {
            const name = card.dataset.blob;
            if (!name) return;
            if (on) {
                selectedBlobs.add(name);
                card.classList.add("is-selected");
            } else {
                selectedBlobs.delete(name);
                card.classList.remove("is-selected");
            }
            const cb = card.querySelector(".file-card__select");
            if (cb) cb.checked = on;
            updateBulkBar();
        }

`;

if (!s.includes('const selectedBlobs')) {
  s = s.replace('        let busy = false;\n\n        function blobPublicUrl', helpers + '        function blobPublicUrl');
}

const renderOld =
  '                card.dataset.blob = name;\n\n                card.innerHTML =\n                    \'<' +
  d +
  ' class="file-card__thumb-wrap">\' +\n                    (isImg\n                        ? \'<img class="file-card__thumb" src="\' + escapeHtml(url) + \'" alt="" loading="lazy">\'\n                        : \'<span class="file-card__placeholder">Fișier non-imagine</span>\') +\n                    "</' +
  d +
  '>" +\n                    \'<' +
  d +
  ' class="file-card__body">\' +\n                    \'<' +
  d +
  ' class="file-card__name">\' + escapeHtml(name) + "</' +
  d +
  '>" +\n                    \'<' +
  d +
  ' class="file-card__actions">\' +\n                    \'<button type="button" class="btn btn--sm btn--ghost" data-action="copy">Copiază URL</button>\' +\n                    \'<a class="btn btn--sm btn--gold" href="\' + escapeHtml(url) + \'" target="_blank" rel="noopener">Vezi</a>\' +\n                    \'<button type="button" class="btn btn--sm btn--danger" data-action="delete">Șterge</button>\' +\n                    "</' +
  d +
  '>" +\n                    "</' +
  d +
  '>";\n\n                root.appendChild(card);';

const renderNew =
  '                card.dataset.blob = name;\n\n                const isSelected = selectedBlobs.has(name);\n                if (isSelected) card.classList.add("is-selected");\n\n                card.innerHTML =\n                    \'<' +
  d +
  ' class="file-card__thumb-wrap">\' +\n                    \'<label class="file-card__select-wrap" title="Selectează pentru ștergere">\' +\n                    \'<input type="checkbox" class="file-card__select" data-action="select"\' +\n                    (isSelected ? " checked" : "") +\n                    ">" +\n                    "</label>" +\n                    (isImg\n                        ? \'<img class="file-card__thumb" src="\' + escapeHtml(url) + \'" alt="" loading="lazy">\'\n                        : \'<span class="file-card__placeholder">Fișier non-imagine</span>\') +\n                    "</' +
  d +
  '>" +\n                    \'<' +
  d +
  ' class="file-card__body">\' +\n                    \'<' +
  d +
  ' class="file-card__name">\' + escapeHtml(name) + "</' +
  d +
  '>" +\n                    \'<' +
  d +
  ' class="file-card__actions">\' +\n                    \'<button type="button" class="btn btn--sm btn--ghost" data-action="copy">Copiază URL</button>\' +\n                    \'<a class="btn btn--sm btn--gold" href="\' + escapeHtml(url) + \'" target="_blank" rel="noopener">Vezi</a>\' +\n                    \'<button type="button" class="btn btn--sm btn--danger" data-action="delete">Șterge</button>\' +\n                    "</' +
  d +
  '>" +\n                    "</' +
  d +
  '>";\n\n                root.appendChild(card);';

if (!s.includes('file-card__select-wrap')) {
  if (!s.includes(renderOld.slice(0, 80))) {
    console.error('renderList block not found');
    process.exit(1);
  }
  s = s.replace(renderOld, renderNew);
}

if (!s.includes('pruneSelection();\n                renderList();')) {
  s = s.replace(
    '                allBlobs = await listAllBlobs();\n                renderList();',
    '                allBlobs = await listAllBlobs();\n                pruneSelection();\n                renderList();\n                updateBulkBar();'
  );
}

if (!s.includes('if (!list.length) {\n                root.innerHTML')) {
  // empty list should update bulk bar
}
s = s.replace(
  `            if (!list.length) {
                root.innerHTML = '<p style="grid-column:1/-1;color:var(--ink-soft)">Niciun fișier de afișat.</p>';
                return;
            }`,
  `            if (!list.length) {
                root.innerHTML = '<p style="grid-column:1/-1;color:var(--ink-soft)">Niciun fișier de afișat.</p>';
                updateBulkBar();
                return;
            }`
);

if (!s.includes('selectedBlobs.delete(name);\n                    allBlobs = allBlobs.filter')) {
  s = s.replace(
    '                    await deleteBlob(name);\n                    allBlobs = allBlobs.filter((b) => b !== name);\n                    renderList();\n                    setStatus("Șters: " + name, "ok");',
    '                    await deleteBlob(name);\n                    selectedBlobs.delete(name);\n                    allBlobs = allBlobs.filter((b) => b !== name);\n                    renderList();\n                    updateBulkBar();\n                    setStatus("Șters: " + name, "ok");'
  );
}

if (!s.includes('file-card__select")) return;')) {
  s = s.replace(
    'document.getElementById("fileList").addEventListener("click", async (e) => {\n            const btn = e.target.closest("button[data-action]");',
    'document.getElementById("fileList").addEventListener("click", async (e) => {\n            if (e.target.closest(".file-card__select-wrap")) return;\n            const btn = e.target.closest("button[data-action]");'
  );
}

s = s.replace(
  `        document.getElementById("imagesOnly").addEventListener("change", () => {
            renderList();
            const shown = getFilteredBlobs().length;
            setStatus("Afișate: " + shown + " din " + allBlobs.length, "ok");
        });`,
  `        document.getElementById("imagesOnly").addEventListener("change", () => {
            renderList();
            updateBulkBar();
            const shown = getFilteredBlobs().length;
            setStatus("Afișate: " + shown + " din " + allBlobs.length, "ok");
        });`
);

fs.writeFileSync(p, s);
console.log('ok2', {
  selectedBlobs: s.includes('const selectedBlobs'),
  checkbox: s.includes('file-card__select-wrap'),
  updateBulkBar: s.includes('function updateBulkBar'),
});
