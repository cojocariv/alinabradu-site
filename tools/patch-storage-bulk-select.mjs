import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/storage-manager.php');
let s = fs.readFileSync(p, 'utf8');
const el = 'd' + 'iv';

const bulkBar =
  '\n    <' + el + ' class="bulk-bar" id="bulkBar">\n' +
  '        <label class="bulk-bar__select-all">\n' +
  '            <input type="checkbox" id="selectAllVisible" title="Selectează toate fișierele afișate">\n' +
  '            Selectează toate afișate\n' +
  '        </label>\n' +
  '        <span class="bulk-bar__count" id="selectionCount">0 selectate</span>\n' +
  '        <button type="button" class="btn btn--danger btn--sm" id="btnDeleteSelected" disabled>Șterge selectate</button>\n' +
  '        <button type="button" class="btn btn--ghost btn--sm" id="btnClearSelection" disabled>Anulează selecția</button>\n' +
  '    </' + el + '>\n';

if (!s.includes('id="bulkBar"')) {
  s = s.replace(
    '    <' + el + ' class="file-list" id="fileList" aria-live="polite"></' + el + '>',
    bulkBar + '    <' + el + ' class="file-list" id="fileList" aria-live="polite"></' + el + '>'
  );
}

if (!s.includes('selectedBlobs')) {
  s = s.replace(
    '        let allBlobs = [];\n        let busy = false;',
    `        let allBlobs = [];
        let busy = false;
        const selectedBlobs = new Set();

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
        }`
  );
}

if (!s.includes('file-card__select')) {
  s = s.replace(
    `                card.dataset.blob = name;

                card.innerHTML =
                    '<motion></motion>' +
                    (isImg`,
    `                card.dataset.blob = name;

                const isSelected = selectedBlobs.has(name);
                if (isSelected) card.classList.add("is-selected");

                card.innerHTML =
                    '<${EL} class="file-card__thumb-wrap">' +
                    '<label class="file-card__select-wrap" title="Selectează pentru ștergere">' +
                    '<input type="checkbox" class="file-card__select" data-action="select"' +
                    (isSelected ? " checked" : "") + ">" +
                    "</label>" +
                    (isImg`.replace('${EL}', el)
  );
  // fix wrong replacement - the old string had motion by mistake. Use exact file content:
  const needle = `                card.dataset.blob = name;

                card.innerHTML =
                    '<${EL} class="file-card__thumb-wrap">' +
                    (isImg`.replace('${EL}', el);
  if (s.includes(needle)) {
    // already patched
  } else {
    s = s.replace(
      `                card.dataset.blob = name;

                card.innerHTML =
                    '<${EL} class="file-card__thumb-wrap">' +
                    (isImg`.replace('${EL}', el),
      `                card.dataset.blob = name;

                const isSelected = selectedBlobs.has(name);
                if (isSelected) card.classList.add("is-selected");

                card.innerHTML =
                    '<${EL} class="file-card__thumb-wrap">' +
                    '<label class="file-card__select-wrap" title="Selectează pentru ștergere">' +
                    '<input type="checkbox" class="file-card__select" data-action="select"' +
                    (isSelected ? " checked" : "") + ">" +
                    "</label>" +
                    (isImg`.replace(/\$\{EL\}/g, el)
    );
  }
}

// Direct replace for actual file content
if (!s.includes('file-card__select')) {
  s = s.replace(
    "                card.dataset.blob = name;\n\n                card.innerHTML =\n                    '<motion></motion>' +",
    "                card.dataset.blob = name;\n\n                const isSelected = selectedBlobs.has(name);\n                if (isSelected) card.classList.add(\"is-selected\");\n\n                card.innerHTML =\n                    '<" +
      el +
      " class=\"file-card__thumb-wrap\">' +\n                    '<label class=\"file-card__select-wrap\" title=\"Selectează pentru ștergere\">' +\n                    '<input type=\"checkbox\" class=\"file-card__select\" data-action=\"select\"' +\n                    (isSelected ? \" checked\" : \"\") +\n                    \">\" +\n                    \"</label>\" +"
  );
}

if (!s.includes('file-card__select')) {
  s = s.replace(
    "                card.innerHTML =\n                    '<motion></motion>' +",
    "                const isSelected = selectedBlobs.has(name);\n                if (isSelected) card.classList.add(\"is-selected\");\n\n                card.innerHTML =\n                    '<" +
      el +
      " class=\"file-card__thumb-wrap\">' +\n                    '<label class=\"file-card__select-wrap\" title=\"Selectează pentru ștergere\">' +\n                    '<input type=\"checkbox\" class=\"file-card__select\" data-action=\"select\"' +\n                    (isSelected ? \" checked\" : \"\") +\n                    \">\" +\n                    \"</label>\" +"
  );
}

// Still might fail - read file and do exact replace
if (!s.includes('file-card__select')) {
  const a =
    "                card.dataset.blob = name;\n\n                card.innerHTML =\n                    '<" +
    el +
    " class=\"file-card__thumb-wrap\">' +\n                    (isImg";
  const b =
    "                card.dataset.blob = name;\n\n                const isSelected = selectedBlobs.has(name);\n                if (isSelected) card.classList.add(\"is-selected\");\n\n                card.innerHTML =\n                    '<" +
    el +
    " class=\"file-card__thumb-wrap\">' +\n                    '<label class=\"file-card__select-wrap\" title=\"Selectează pentru ștergere\">' +\n                    '<input type=\"checkbox\" class=\"file-card__select\" data-action=\"select\"' +\n                    (isSelected ? \" checked\" : \"\") +\n                    \">\" +\n                    \"</label>\" +\n                    (isImg";
  if (s.includes(a)) s = s.replace(a, b);
}

if (!s.includes('pruneSelection')) {
  s = s.replace(
    '                allBlobs = await listAllBlobs();\n                renderList();',
    '                allBlobs = await listAllBlobs();\n                pruneSelection();\n                renderList();\n                updateBulkBar();'
  );
}

if (!s.includes('deleteSelected')) {
  const block = `
        document.getElementById("selectAllVisible").addEventListener("change", (e) => {
            if (busy) {
                e.target.checked = false;
                return;
            }
            const visible = getFilteredBlobs();
            if (e.target.checked) {
                visible.forEach((name) => selectedBlobs.add(name));
            } else {
                visible.forEach((name) => selectedBlobs.delete(name));
            }
            renderList();
            updateBulkBar();
        });

        document.getElementById("btnClearSelection").addEventListener("click", () => {
            selectedBlobs.clear();
            renderList();
            updateBulkBar();
        });

        document.getElementById("btnDeleteSelected").addEventListener("click", () => deleteSelected());

        document.getElementById("fileList").addEventListener("change", (e) => {
            if (!e.target.classList.contains("file-card__select") || busy) return;
            const card = e.target.closest(".file-card");
            if (card) setCardSelected(card, e.target.checked);
        });

        async function deleteSelected() {
            const names = [...selectedBlobs];
            if (!names.length || busy) return;
            if (!confirm("Ștergi definitiv " + names.length + " fișiere din container?\\n\\nAcțiunea nu poate fi anulată.")) {
                return;
            }
            busy = true;
            updateBulkBar();
            let ok = 0;
            let fail = 0;
            const failed = [];
            for (let i = 0; i < names.length; i++) {
                const name = names[i];
                setStatus("Șterg " + (i + 1) + "/" + names.length + ": " + name + "…");
                try {
                    await deleteBlob(name);
                    selectedBlobs.delete(name);
                    ok++;
                } catch (err) {
                    fail++;
                    failed.push(name);
                    console.error(err);
                }
            }
            allBlobs = allBlobs.filter((b) => !names.includes(b) || failed.includes(b));
            pruneSelection();
            renderList();
            updateBulkBar();
            if (fail === 0) {
                setStatus("Șterse cu succes: " + ok + " fișier(e).", "ok");
            } else {
                setStatus("Șterse: " + ok + ", eșuate: " + fail + ".", ok > 0 ? "ok" : "error");
            }
            busy = false;
            updateBulkBar();
        }

`;
  s = s.replace(
    '        document.getElementById("btnRefresh").addEventListener("click", refreshList);',
    block + '        document.getElementById("btnRefresh").addEventListener("click", refreshList);'
  );
}

s = s.replace(
  'document.getElementById("imagesOnly").addEventListener("change", () => {\n            renderList();\n            const shown = getFilteredBlobs().length;\n            setStatus("Afișate: " + shown + " din " + allBlobs.length, "ok");\n        });',
  'document.getElementById("imagesOnly").addEventListener("change", () => {\n            renderList();\n            updateBulkBar();\n            const shown = getFilteredBlobs().length;\n            setStatus("Afișate: " + shown + " din " + allBlobs.length, "ok");\n        });'
);

s = s.replace(
  'document.getElementById("sortNewest").addEventListener("change", renderList);',
  'document.getElementById("sortNewest").addEventListener("change", () => {\n            renderList();\n            updateBulkBar();\n        });'
);

s = s.replace(
  'await deleteBlob(name);\n                    allBlobs = allBlobs.filter((b) => b !== name);\n                    renderList();\n                    setStatus("Șters: " + name, "ok");',
  'await deleteBlob(name);\n                    selectedBlobs.delete(name);\n                    allBlobs = allBlobs.filter((b) => b !== name);\n                    renderList();\n                    updateBulkBar();\n                    setStatus("Șters: " + name, "ok");'
);

if (!s.includes('updateBulkBar();\n        refreshList();')) {
  s = s.replace('        refreshList();', '        updateBulkBar();\n        refreshList();');
}

// Prevent click on checkbox from bubbling to card
if (!s.includes('data-action === "select"')) {
  s = s.replace(
    'document.getElementById("fileList").addEventListener("click", async (e) => {\n            const btn = e.target.closest("button[data-action]");',
    'document.getElementById("fileList").addEventListener("click", async (e) => {\n            if (e.target.closest(".file-card__select")) return;\n            const btn = e.target.closest("button[data-action]");'
  );
}

fs.writeFileSync(p, s);
console.log('ok', {
  bulkBar: s.includes('bulkBar'),
  select: s.includes('file-card__select'),
  deleteSelected: s.includes('deleteSelected'),
});
