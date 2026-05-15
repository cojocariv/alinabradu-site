import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/storage-manager.php');
const raw = fs.readFileSync(p, 'utf8');
const crlf = raw.includes('\r\n');
let s = raw.replace(/\r\n/g, '\n');
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

if (!s.includes('const selectedBlobs = new Set()')) {
  s = s.replace(
    '        let busy = false;\n\n        function blobPublicUrl',
    '        let busy = false;\n\n' + helpers + '        function blobPublicUrl'
  );
}

const renderOld =
  `                card.dataset.blob = name;

                card.innerHTML =
                    '<${d} class="file-card__thumb-wrap">' +
                    (isImg
                        ? '<img class="file-card__thumb" src="' + escapeHtml(url) + '" alt="" loading="lazy">'
                        : '<span class="file-card__placeholder">Fișier non-imagine</span>') +
                    "</${d}>" +
                    '<${d} class="file-card__body">' +
                    '<${d} class="file-card__name">' + escapeHtml(name) + "</${d}>" +
                    '<${d} class="file-card__actions">' +
                    '<button type="button" class="btn btn--sm btn--ghost" data-action="copy">Copiază URL</button>' +
                    '<a class="btn btn--sm btn--gold" href="' + escapeHtml(url) + '" target="_blank" rel="noopener">Vezi</a>' +
                    '<button type="button" class="btn btn--sm btn--danger" data-action="delete">Șterge</button>' +
                    "</${d}>" +
                    "</${d}>";

                root.appendChild(card);`;

const renderNew =
  `                card.dataset.blob = name;

                const isSelected = selectedBlobs.has(name);
                if (isSelected) card.classList.add("is-selected");

                card.innerHTML =
                    '<${d} class="file-card__thumb-wrap">' +
                    '<label class="file-card__select-wrap" title="Selectează pentru ștergere">' +
                    '<input type="checkbox" class="file-card__select" data-action="select"' +
                    (isSelected ? " checked" : "") +
                    ">" +
                    "</label>" +
                    (isImg
                        ? '<img class="file-card__thumb" src="' + escapeHtml(url) + '" alt="" loading="lazy">'
                        : '<span class="file-card__placeholder">Fișier non-imagine</span>') +
                    "</${d}>" +
                    '<${d} class="file-card__body">' +
                    '<${d} class="file-card__name">' + escapeHtml(name) + "</${d}>" +
                    '<${d} class="file-card__actions">' +
                    '<button type="button" class="btn btn--sm btn--ghost" data-action="copy">Copiază URL</button>' +
                    '<a class="btn btn--sm btn--gold" href="' + escapeHtml(url) + '" target="_blank" rel="noopener">Vezi</a>' +
                    '<button type="button" class="btn btn--sm btn--danger" data-action="delete">Șterge</button>' +
                    "</${d}>" +
                    "</${d}>";

                root.appendChild(card);`;

if (!s.includes('file-card__select-wrap" title')) {
  if (!s.includes(renderOld)) {
    console.error('renderList block not found');
    process.exit(1);
  }
  s = s.replace(renderOld, renderNew);
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

if (!s.includes('pruneSelection();\n                renderList();')) {
  s = s.replace(
    '                allBlobs = await listAllBlobs();\n                renderList();',
    '                allBlobs = await listAllBlobs();\n                pruneSelection();\n                renderList();\n                updateBulkBar();'
  );
}

if (!s.includes('selectedBlobs.delete(name);\n                    allBlobs')) {
  s = s.replace(
    '                    await deleteBlob(name);\n                    allBlobs = allBlobs.filter((b) => b !== name);\n                    renderList();\n                    setStatus("Șters: " + name, "ok");',
    '                    await deleteBlob(name);\n                    selectedBlobs.delete(name);\n                    allBlobs = allBlobs.filter((b) => b !== name);\n                    renderList();\n                    updateBulkBar();\n                    setStatus("Șters: " + name, "ok");'
  );
}

if (!s.includes('file-card__select-wrap")) return;')) {
  s = s.replace(
    'document.getElementById("fileList").addEventListener("click", async (e) => {\n            const btn = e.target.closest("button[data-action]");',
    'document.getElementById("fileList").addEventListener("click", async (e) => {\n            if (e.target.closest(".file-card__select-wrap")) return;\n            const btn = e.target.closest("button[data-action]");'
  );
}

if (!s.includes('imagesOnly").addEventListener("change", () => {\n            renderList();\n            updateBulkBar();')) {
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
}

fs.writeFileSync(p, crlf ? s.replace(/\n/g, '\r\n') : s);
console.log('ok3', {
  selectedBlobs: s.includes('const selectedBlobs = new Set()'),
  checkbox: s.includes('file-card__select-wrap" title'),
  updateBulkBar: s.includes('function updateBulkBar'),
  bulkBar: s.includes('id="bulkBar"'),
});
