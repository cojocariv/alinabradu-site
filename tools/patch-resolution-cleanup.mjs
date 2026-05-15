import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const p = path.join(path.dirname(fileURLToPath(import.meta.url)), '../pages/storage-manager.php');
const raw = fs.readFileSync(p, 'utf8');
const crlf = raw.includes('\r\n');
let s = raw.replace(/\r\n/g, '\n');
const el = 'd' + 'iv';

const panel =
  '\n    <' + el + ' class="panel resolution-panel">\n' +
  '        <h2 class="resolution-panel__title">Curățare variante redimensionate</h2>\n' +
  '        <p class="resolution-panel__desc">Șterge fișierele al căror nume conține sufixul de rezoluție WordPress (ex. <code>AB-284-570x728.jpg</code>).</p>\n' +
  '        <p class="resolution-panel__count" id="resolutionCount">Se calculează…</p>\n' +
  '        <' + el + ' class="resolution-panel__actions">\n' +
  '            <button type="button" class="btn btn--ghost btn--sm" id="btnSelectResolution" disabled>Selectează toate cu rezoluție</button>\n' +
  '            <button type="button" class="btn btn--danger btn--sm" id="btnDeleteAllResolution" disabled>Șterge toate cu rezoluție în nume</button>\n' +
  '        </' + el + '>\n' +
  '    </' + el + '>\n';

if (!s.includes('resolution-panel')) {
  s = s.replace(
    '    <' + el + ' class="filters">',
    panel + '    <' + el + ' class="filters">'
  );
}

if (!s.includes('resolution-panel__title')) {
  s = s.replace(
    '        .bulk-bar__count.has-selection { color: var(--ink); }',
    `        .bulk-bar__count.has-selection { color: var(--ink); }
        .resolution-panel { margin-bottom: 1.25rem; }
        .resolution-panel__title { font-size: 1rem; margin: 0 0 0.35rem; font-weight: 600; }
        .resolution-panel__desc { margin: 0 0 0.5rem; font-size: 0.85rem; color: var(--ink-soft); }
        .resolution-panel__count { margin: 0 0 0.75rem; font-size: 0.9rem; font-weight: 600; }
        .resolution-panel__actions { display: flex; flex-wrap: wrap; gap: 0.5rem; }`
  );
}

if (!s.includes('RESOLUTION_SUFFIX_RE')) {
  s = s.replace(
    '        const IMAGE_RE = /\\.(jpe?g|png|gif|webp|avif|bmp|svg)$/i;',
    `        const IMAGE_RE = /\\.(jpe?g|png|gif|webp|avif|bmp|svg)$/i;
        /** Sufix -{lățime}x{înălțime} înainte de extensie (ex. AB-284-570x728.jpg) */
        const RESOLUTION_SUFFIX_RE = /-\\d{1,5}x\\d{1,5}\\.[a-z0-9]{2,5}$/i;

        function hasResolutionInName(blobPath) {
            const base = blobPath.split("/").pop() || blobPath;
            return RESOLUTION_SUFFIX_RE.test(base);
        }

        function getAllResolutionBlobs() {
            return allBlobs.filter(hasResolutionInName);
        }

        function updateResolutionPanel() {
            const elCount = document.getElementById("resolutionCount");
            const btnSelect = document.getElementById("btnSelectResolution");
            const btnDelete = document.getElementById("btnDeleteAllResolution");
            if (!elCount) return;
            const matches = getAllResolutionBlobs();
            const n = matches.length;
            elCount.textContent = n === 0
                ? "Niciun fișier cu sufix de rezoluție în nume."
                : n + (n === 1 ? " fișier găsit" : " fișiere găsite") + " cu sufix -LxH (ex. -570x728.jpg).";
            const disabled = n === 0 || busy;
            if (btnSelect) btnSelect.disabled = disabled;
            if (btnDelete) btnDelete.disabled = disabled;
        }`
  );
}

if (!s.includes('resolutionOnly')) {
  s = s.replace(
    '        <label><input type="checkbox" id="sortNewest"> Sortare descrescătoare (nume)</label>\n    </' + el + '>',
    '        <label><input type="checkbox" id="sortNewest"> Sortare descrescătoare (nume)</label>\n' +
      '        <label><input type="checkbox" id="resolutionOnly"> Doar cu rezoluție în nume</label>\n    </' + el + '>'
  );

  s = s.replace(
    `        function getFilteredBlobs() {
            let list = [...allBlobs];
            if (document.getElementById("imagesOnly").checked) {
                list = list.filter((n) => IMAGE_RE.test(n));
            }
            if (document.getElementById("sortNewest").checked) {`,
    `        function getFilteredBlobs() {
            let list = [...allBlobs];
            if (document.getElementById("imagesOnly").checked) {
                list = list.filter((n) => IMAGE_RE.test(n));
            }
            if (document.getElementById("resolutionOnly")?.checked) {
                list = list.filter(hasResolutionInName);
            }
            if (document.getElementById("sortNewest").checked) {`
  );
}

if (!s.includes('async function deleteBlobsByNames')) {
  s = s.replace(
    `        async function deleteSelected() {
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
        }`,
    `        async function deleteBlobsByNames(names) {
            if (!names.length || busy) return { ok: 0, fail: 0 };
            busy = true;
            updateBulkBar();
            updateResolutionPanel();
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
            updateResolutionPanel();
            if (fail === 0) {
                setStatus("Șterse cu succes: " + ok + " fișier(e).", "ok");
            } else {
                setStatus("Șterse: " + ok + ", eșuate: " + fail + ".", ok > 0 ? "ok" : "error");
            }
            busy = false;
            updateBulkBar();
            updateResolutionPanel();
            return { ok, fail };
        }

        async function deleteSelected() {
            const names = [...selectedBlobs];
            if (!names.length) return;
            if (!confirm("Ștergi definitiv " + names.length + " fișiere din container?\\n\\nAcțiunea nu poate fi anulată.")) {
                return;
            }
            await deleteBlobsByNames(names);
        }

        async function deleteAllResolutionVariants() {
            const names = getAllResolutionBlobs();
            if (!names.length || busy) return;
            const preview = names.slice(0, 5).join("\\n") + (names.length > 5 ? "\\n… și încă " + (names.length - 5) + "" : "");
            if (!confirm(
                "Ștergi definitiv " + names.length + " fișiere cu sufix de rezoluție în nume?\\n\\n" +
                "Exemple:\\n" + preview + "\\n\\nAcțiunea nu poate fi anulată."
            )) {
                return;
            }
            await deleteBlobsByNames(names);
        }`
  );
}

if (!s.includes('btnDeleteAllResolution')) {
  s = s.replace(
    '        document.getElementById("btnDeleteSelected").addEventListener("click", () => deleteSelected());',
    `        document.getElementById("btnSelectResolution").addEventListener("click", () => {
            if (busy) return;
            getAllResolutionBlobs().forEach((name) => selectedBlobs.add(name));
            renderList();
            updateBulkBar();
            setStatus("Selectate " + selectedBlobs.size + " fișiere cu rezoluție în nume.", "ok");
        });

        document.getElementById("btnDeleteAllResolution").addEventListener("click", () => deleteAllResolutionVariants());

        document.getElementById("btnDeleteSelected").addEventListener("click", () => deleteSelected());`
  );
}

if (!s.includes('resolutionOnly").addEventListener')) {
  s = s.replace(
    `        document.getElementById("sortNewest").addEventListener("change", () => {
            renderList();
            updateBulkBar();
        });`,
    `        document.getElementById("sortNewest").addEventListener("change", () => {
            renderList();
            updateBulkBar();
        });
        document.getElementById("resolutionOnly").addEventListener("change", () => {
            renderList();
            updateBulkBar();
            const shown = getFilteredBlobs().length;
            setStatus("Afișate: " + shown + " din " + allBlobs.length, "ok");
        });`
  );
}

if (!s.includes('updateResolutionPanel();')) {
  s = s.replace(
    '                updateBulkBar();\n                const shown = getFilteredBlobs().length;\n                setStatus("Total în container:',
    '                updateBulkBar();\n                updateResolutionPanel();\n                const shown = getFilteredBlobs().length;\n                setStatus("Total în container:'
  );
  s = s.replace(
    '        updateBulkBar();\n        refreshList();',
    '        updateBulkBar();\n        updateResolutionPanel();\n        refreshList();'
  );
}

fs.writeFileSync(p, crlf ? s.replace(/\n/g, '\r\n') : s);
console.log('patched', {
  panel: s.includes('resolution-panel'),
  re: s.includes('RESOLUTION_SUFFIX_RE'),
  deleteAll: s.includes('deleteAllResolutionVariants'),
});
