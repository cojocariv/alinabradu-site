<?php
declare(strict_types=1);
/** @var string $sasUrl */
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Gestionare poze — Azure Storage</title>
    <style>
        :root {
            --cream: #fbf6ee;
            --gold: #c9a96e;
            --ink: #1a1410;
            --ink-soft: #3d342c;
            --danger: #9b2c2c;
        }
        * { box-sizing: border-box; }
        body {
            font-family: "Segoe UI", system-ui, sans-serif;
            margin: 0;
            padding: 24px;
            line-height: 1.5;
            background: var(--cream);
            color: var(--ink);
        }
        h1 { font-size: 1.5rem; font-weight: 600; margin: 0 0 0.25rem; }
        .subtitle { color: var(--ink-soft); font-size: 0.9rem; margin-bottom: 1.5rem; }
        .panel {
            background: #fff;
            border: 1px solid rgba(201, 169, 110, 0.45);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: flex-end;
            margin-bottom: 1rem;
        }
        label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ink-soft); margin-bottom: 0.35rem; }
        input[type="text"] {
            width: 100%;
            min-width: 200px;
            padding: 0.5rem 0.65rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .field { flex: 1; min-width: 180px; }
        .btn {
            appearance: none;
            border: 1px solid var(--ink);
            background: var(--ink);
            color: var(--cream);
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.2s, border-color 0.2s;
        }
        .btn:hover:not(:disabled) { background: var(--ink-soft); }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn--gold { background: var(--gold); border-color: var(--gold); color: var(--ink); }
        .btn--gold:hover:not(:disabled) { filter: brightness(0.95); }
        .btn--ghost { background: transparent; color: var(--ink); border-color: var(--ink); }
        .btn--danger { background: var(--danger); border-color: var(--danger); color: #fff; }
        .btn--sm { padding: 0.35rem 0.65rem; font-size: 0.7rem; }
        .dropzone {
            border: 2px dashed rgba(201, 169, 110, 0.8);
            border-radius: 8px;
            padding: 2rem 1rem;
            text-align: center;
            background: rgba(251, 246, 238, 0.6);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }
        .dropzone.is-dragover {
            border-color: var(--gold);
            background: rgba(201, 169, 110, 0.12);
        }
        .dropzone input { display: none; }
        .dropzone p { margin: 0.5rem 0 0; font-size: 0.85rem; color: var(--ink-soft); }
        #status {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            min-height: 1.25rem;
        }
        #status.is-error { color: var(--danger); font-weight: 600; }
        #status.is-ok { color: #2d6a4f; }
        .file-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        .file-card {
            border: 1px solid rgba(201, 169, 110, 0.35);
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            display: flex;
            flex-direction: column;
        }
        .file-card__thumb-wrap {
            aspect-ratio: 1;
            background: #f5f0e8;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .file-card__thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .file-card__placeholder {
            font-size: 0.75rem;
            color: var(--ink-soft);
            padding: 1rem;
            text-align: center;
        }
        .file-card__body { padding: 0.75rem; flex: 1; display: flex; flex-direction: column; gap: 0.5rem; }
        .file-card__name {
            font-size: 0.8rem;
            word-break: break-all;
            color: var(--ink);
            font-family: ui-monospace, monospace;
        }
        .file-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-top: auto;
        }
        .file-card.is-selected {
            border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(201, 169, 110, 0.45);
        }
        .file-card__thumb-wrap { position: relative; }
        .file-card__select-wrap {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 4px;
            border: 1px solid rgba(201, 169, 110, 0.5);
            cursor: pointer;
        }
        .file-card__select-wrap input {
            width: 1rem;
            height: 1rem;
            margin: 0;
            cursor: pointer;
            accent-color: var(--gold);
        }
        .bulk-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem 1rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            background: #fff;
            border: 1px solid rgba(201, 169, 110, 0.45);
            border-radius: 8px;
        }
        .bulk-bar__count {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--ink-soft);
            min-width: 6rem;
        }
        .bulk-bar__count.has-selection { color: var(--ink); }
        .filters { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.85rem; }
        .filters input { width: auto; margin: 0; }
        .hint {
            font-size: 0.8rem;
            color: var(--ink-soft);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        code { font-size: 0.85em; background: #f5f0e8; padding: 0.1em 0.35em; border-radius: 3px; }
    </style>
</head>
<body>

    <p class="topbar" style="display:flex;justify-content:flex-end;margin:0 0 1rem">
        <a href="<?= e(url('/produs.php?logout=1')) ?>" class="btn btn--ghost btn--sm">Deconectare</a>
    </p>
    <h1>Container: <span id="containerName">poze</span></h1>
    <p class="subtitle">Încarcă și șterge imagini direct în Azure Blob Storage (cont <strong>alinabradupozestorage</strong>).</p>

    <div class="panel">
        <div class="toolbar">
            <div class="field">
                <label for="uploadPrefix">Subfolder la încărcare (opțional)</label>
                <input type="text" id="uploadPrefix" placeholder="ex: 2026/produse/" autocomplete="off">
                <p style="margin:0.35rem 0 0;font-size:0.75rem;color:var(--ink-soft)">Fără slash la început; se adaugă înaintea numelui fișierului.</p>
            </div>
            <button type="button" class="btn btn--ghost" id="btnRefresh">Reîncarcă lista</button>
        </div>

        <label class="dropzone" id="dropzone" for="fileInput">
            <strong>Adaugă poze</strong> — trage aici sau click pentru a selecta
            <p>JPG, PNG, WebP, GIF, AVIF (max. recomandat 15 MB / fișier)</p>
            <input type="file" id="fileInput" accept="image/jpeg,image/png,image/webp,image/gif,image/avif,image/bmp" multiple>
        </label>
    </div>

    <div id="status">Se încarcă fișierele…</div>

    <div class="filters">
        <label><input type="checkbox" id="imagesOnly" checked> Doar imagini</label>
        <label><input type="checkbox" id="sortNewest"> Sortare descrescătoare (nume)</label>
    </div>


    <div class="bulk-bar" id="bulkBar">
        <label class="bulk-bar__select-all">
            <input type="checkbox" id="selectAllVisible" title="Selectează toate fișierele afișate">
            Selectează toate afișate
        </label>
        <span class="bulk-bar__count" id="selectionCount">0 selectate</span>
        <button type="button" class="btn btn--danger btn--sm" id="btnDeleteSelected" disabled>Șterge selectate</button>
        <button type="button" class="btn btn--ghost btn--sm" id="btnClearSelection" disabled>Anulează selecția</button>
    </div>
    <div class="file-list" id="fileList" aria-live="polite"></div>

    <p class="hint">
        Dacă încărcarea sau ștergerea eșuează, verifică în Azure Portal → Storage → CORS: originea
        <code>https://new.alinabradu.com</code> trebuie să aibă permisiuni GET, PUT, DELETE, HEAD, OPTIONS.
        SAS-ul din <code>config/azure_storage.php</code> trebuie să includă permisiunile <code>racwdl</code> (read, add, create, write, delete, list).
    </p>

    <script>
        // SAS la nivel de container (permisiuni: racwdl)
        const sasUrl = <?= json_encode($sasUrl, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

        const [baseUrl, sasToken] = (() => {
            const q = sasUrl.indexOf("?");
            return [sasUrl.slice(0, q), sasUrl.slice(q + 1)];
        })();

        const IMAGE_RE = /\.(jpe?g|png|gif|webp|avif|bmp|svg)$/i;
        const MAX_FILE_BYTES = 15 * 1024 * 1024;

        let allBlobs = [];
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

        function blobPublicUrl(name) {
            const path = name.split("/").map(encodeURIComponent).join("/");
            return baseUrl + "/" + path + "?" + sasToken;
        }

        function normalizePrefix(raw) {
            let p = (raw || "").trim().replace(/^\/+/, "");
            if (p && !p.endsWith("/")) p += "/";
            return p;
        }

        function buildBlobName(file, prefix) {
            const base = file.name.replace(/\\/g, "/").split("/").pop();
            const safe = base.replace(/[^\w.\- ()ăâîșțĂÂÎȘȚ]/gi, "-").replace(/-+/g, "-");
            return prefix + (safe || "imagine-" + Date.now() + ".jpg");
        }

        function setStatus(msg, type) {
            const el = document.getElementById("status");
            el.textContent = msg;
            el.className = type === "error" ? "is-error" : type === "ok" ? "is-ok" : "";
        }

        function escapeHtml(s) {
            return String(s)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;");
        }

        async function listAllBlobs() {
            const blobs = [];
            let marker = "";

            for (;;) {
                let url = baseUrl + "?" + sasToken + "&restype=container&comp=list&maxresults=5000";
                if (marker) url += "&marker=" + encodeURIComponent(marker);

                const response = await fetch(url);
                if (!response.ok) {
                    const t = await response.text();
                    throw new Error("Listare: " + response.status + " " + (t || response.statusText));
                }

                const text = await response.text();
                const xml = new DOMParser().parseFromString(text, "application/xml");
                const err = xml.querySelector("Error Code");
                if (err) throw new Error(xml.querySelector("Message")?.textContent || err.textContent);

                xml.querySelectorAll("Blob > Name").forEach((n) => {
                    const name = n.textContent;
                    if (name && !name.endsWith("/")) blobs.push(name);
                });

                const next = xml.querySelector("NextMarker")?.textContent;
                if (!next) break;
                marker = next;
            }

            return blobs;
        }

        function getFilteredBlobs() {
            let list = [...allBlobs];
            if (document.getElementById("imagesOnly").checked) {
                list = list.filter((n) => IMAGE_RE.test(n));
            }
            if (document.getElementById("sortNewest").checked) {
                list.sort((a, b) => b.localeCompare(a));
            } else {
                list.sort((a, b) => a.localeCompare(b));
            }
            return list;
        }

        function renderList() {
            const list = getFilteredBlobs();
            const root = document.getElementById("fileList");
            root.innerHTML = "";

            if (!list.length) {
                root.innerHTML = '<p style="grid-column:1/-1;color:var(--ink-soft)">Niciun fișier de afișat.</p>';
                updateBulkBar();
                return;
            }

            list.forEach((name) => {
                const url = blobPublicUrl(name);
                const isImg = IMAGE_RE.test(name);
                const card = document.createElement("article");
                card.className = "file-card";
                card.dataset.blob = name;

                const isSelected = selectedBlobs.has(name);
                if (isSelected) card.classList.add("is-selected");

                card.innerHTML =
                    '<div class="file-card__thumb-wrap">' +
                    '<label class="file-card__select-wrap" title="Selectează pentru ștergere">' +
                    '<input type="checkbox" class="file-card__select" data-action="select"' +
                    (isSelected ? " checked" : "") +
                    ">" +
                    "</label>" +
                    (isImg
                        ? '<img class="file-card__thumb" src="' + escapeHtml(url) + '" alt="" loading="lazy">'
                        : '<span class="file-card__placeholder">Fișier non-imagine</span>') +
                    "</div>" +
                    '<div class="file-card__body">' +
                    '<div class="file-card__name">' + escapeHtml(name) + "</div>" +
                    '<div class="file-card__actions">' +
                    '<button type="button" class="btn btn--sm btn--ghost" data-action="copy">Copiază URL</button>' +
                    '<a class="btn btn--sm btn--gold" href="' + escapeHtml(url) + '" target="_blank" rel="noopener">Vezi</a>' +
                    '<button type="button" class="btn btn--sm btn--danger" data-action="delete">Șterge</button>' +
                    "</div>" +
                    "</div>";

                root.appendChild(card);
            });
        }

        async function refreshList() {
            if (busy) return;
            busy = true;
            setStatus("Se încarcă lista…");
            document.getElementById("btnRefresh").disabled = true;

            try {
                allBlobs = await listAllBlobs();
                pruneSelection();
                renderList();
                updateBulkBar();
                const shown = getFilteredBlobs().length;
                setStatus("Total în container: " + allBlobs.length + " · Afișate: " + shown, "ok");
            } catch (e) {
                setStatus(e.message, "error");
            } finally {
                busy = false;
                document.getElementById("btnRefresh").disabled = false;
            }
        }

        async function uploadBlob(file, blobName) {
            if (file.size > MAX_FILE_BYTES) {
                throw new Error(file.name + ": fișier prea mare (max " + (MAX_FILE_BYTES / 1024 / 1024) + " MB)");
            }

            const url = blobPublicUrl(blobName);
            const res = await fetch(url, {
                method: "PUT",
                headers: {
                    "x-ms-blob-type": "BlockBlob",
                    "Content-Type": file.type || "application/octet-stream",
                },
                body: file,
            });

            if (!res.ok) {
                const t = await res.text();
                throw new Error("Încărcare " + blobName + ": " + res.status + " " + (t || res.statusText));
            }
        }

        async function deleteBlob(name) {
            const res = await fetch(blobPublicUrl(name), { method: "DELETE" });
            if (!res.ok && res.status !== 404) {
                const t = await res.text();
                throw new Error("Ștergere: " + res.status + " " + (t || res.statusText));
            }
        }

        async function uploadFiles(fileList) {
            if (!fileList.length) return;
            if (busy) return;

            const prefix = normalizePrefix(document.getElementById("uploadPrefix").value);
            busy = true;
            document.getElementById("fileInput").disabled = true;

            let ok = 0;
            let fail = 0;

            for (const file of fileList) {
                const blobName = buildBlobName(file, prefix);
                setStatus("Încarc " + blobName + "…");
                try {
                    await uploadBlob(file, blobName);
                    ok++;
                } catch (e) {
                    fail++;
                    console.error(e);
                    setStatus(e.message, "error");
                }
            }

            await refreshList();
            if (fail === 0) {
                setStatus("Încărcat cu succes: " + ok + " fișier(e).", "ok");
            } else {
                setStatus("Încărcate: " + ok + ", eșuate: " + fail + ".", fail > 0 && ok === 0 ? "error" : "ok");
            }

            busy = false;
            document.getElementById("fileInput").disabled = false;
            document.getElementById("fileInput").value = "";
        }

        async function copyUrl(url, btn) {
            try {
                await navigator.clipboard.writeText(url);
                const prev = btn.textContent;
                btn.textContent = "Copiat!";
                setTimeout(() => { btn.textContent = prev; }, 2000);
            } catch (e) {
                alert("Nu s-a putut copia: " + e.message);
            }
        }


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
            if (!confirm("Ștergi definitiv " + names.length + " fișiere din container?\n\nAcțiunea nu poate fi anulată.")) {
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

        document.getElementById("btnRefresh").addEventListener("click", refreshList);
        document.getElementById("imagesOnly").addEventListener("change", () => {
            renderList();
            updateBulkBar();
            const shown = getFilteredBlobs().length;
            setStatus("Afișate: " + shown + " din " + allBlobs.length, "ok");
        });
        document.getElementById("sortNewest").addEventListener("change", () => {
            renderList();
            updateBulkBar();
        });

        document.getElementById("fileInput").addEventListener("change", (e) => {
            uploadFiles([...e.target.files]);
        });

        const dropzone = document.getElementById("dropzone");
        ["dragenter", "dragover"].forEach((ev) => {
            dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.add("is-dragover");
            });
        });
        ["dragleave", "drop"].forEach((ev) => {
            dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.remove("is-dragover");
            });
        });
        dropzone.addEventListener("drop", (e) => {
            const files = [...e.dataTransfer.files].filter((f) => f.type.startsWith("image/") || IMAGE_RE.test(f.name));
            uploadFiles(files);
        });

        document.getElementById("fileList").addEventListener("click", async (e) => {
            if (e.target.closest(".file-card__select-wrap")) return;
            const btn = e.target.closest("button[data-action]");
            if (!btn || busy) return;

            const card = btn.closest(".file-card");
            const name = card?.dataset.blob;
            if (!name) return;

            if (btn.dataset.action === "copy") {
                copyUrl(blobPublicUrl(name), btn);
                return;
            }

            if (btn.dataset.action === "delete") {
                if (!confirm('Ștergi definitiv din container?\n\n' + name)) return;
                busy = true;
                btn.disabled = true;
                setStatus("Șterg " + name + "…");
                try {
                    await deleteBlob(name);
                    selectedBlobs.delete(name);
                    allBlobs = allBlobs.filter((b) => b !== name);
                    renderList();
                    updateBulkBar();
                    setStatus("Șters: " + name, "ok");
                } catch (err) {
                    setStatus(err.message, "error");
                } finally {
                    busy = false;
                }
            }
        });

        updateBulkBar();
        refreshList();
    </script>
</body>
</html>
