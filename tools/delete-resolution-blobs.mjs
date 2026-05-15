#!/usr/bin/env node
/**
 * Șterge din Azure Blob toate fișierele cu sufix de rezoluție în nume (ex. -570x728.jpg).
 *
 * Utilizare:
 *   set AZURE_STORAGE_SAS_URL=https://....blob.core.windows.net/poze?sp=...
 *   node tools/delete-resolution-blobs.mjs              # dry-run (implicit)
 *   node tools/delete-resolution-blobs.mjs --execute    # ștergere efectivă
 *
 * Opțional: --images-only  (doar extensii imagine)
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const RESOLUTION_SUFFIX_RE = /-\d{1,5}x\d{1,5}\.[a-z0-9]{2,5}$/i;
const IMAGE_RE = /\.(jpe?g|png|gif|webp|avif|bmp|svg)$/i;

const args = process.argv.slice(2);
const execute = args.includes('--execute');
const imagesOnly = args.includes('--images-only');

function hasResolutionInName(blobPath) {
  const base = blobPath.split('/').pop() || blobPath;
  return RESOLUTION_SUFFIX_RE.test(base);
}

function loadSasUrl() {
  const env = process.env.AZURE_STORAGE_SAS_URL;
  if (env) return env.trim();
  const cfg = path.join(path.dirname(fileURLToPath(import.meta.url)), '../config/azure_storage.php');
  const text = fs.readFileSync(cfg, 'utf8');
  const m = text.match(/define\s*\(\s*['"]AZURE_STORAGE_SAS_URL['"]\s*,\s*['"]([^'"]+)['"]/);
  if (m) return m[1];
  throw new Error('Setează AZURE_STORAGE_SAS_URL sau păstrează SAS în config/azure_storage.php');
}

function parseSas(sasUrl) {
  const q = sasUrl.indexOf('?');
  if (q < 0) throw new Error('SAS URL invalid');
  return { baseUrl: sasUrl.slice(0, q), sasToken: sasUrl.slice(q + 1) };
}

async function listAllBlobs(baseUrl, sasToken) {
  const blobs = [];
  let marker = '';
  for (;;) {
    let url = `${baseUrl}?${sasToken}&restype=container&comp=list&maxresults=5000`;
    if (marker) url += `&marker=${encodeURIComponent(marker)}`;
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`Listare: ${response.status} ${await response.text()}`);
    }
    const text = await response.text();
    const names = [...text.matchAll(/<Name>([^<]+)<\/Name>/g)].map((m) => m[1]).filter((n) => n && !n.endsWith('/'));
    blobs.push(...names);
    const nextM = text.match(/<NextMarker>([^<]*)<\/NextMarker>/);
    const next = nextM?.[1];
    if (!next) break;
    marker = next;
  }
  return blobs;
}

async function deleteBlob(baseUrl, sasToken, name) {
  const pathEnc = name.split('/').map(encodeURIComponent).join('/');
  const url = `${baseUrl}/${pathEnc}?${sasToken}`;
  const res = await fetch(url, { method: 'DELETE' });
  if (!res.ok && res.status !== 404) {
    throw new Error(`Ștergere ${name}: ${res.status} ${await res.text()}`);
  }
}

async function main() {
  const sasUrl = loadSasUrl();
  const { baseUrl, sasToken } = parseSas(sasUrl);

  console.log(execute ? 'MOD: ȘTERGERE' : 'MOD: dry-run (adaugă --execute pentru ștergere)');
  console.log('Container:', baseUrl.split('/').pop());

  const all = await listAllBlobs(baseUrl, sasToken);
  let targets = all.filter(hasResolutionInName);
  if (imagesOnly) targets = targets.filter((n) => IMAGE_RE.test(n));

  console.log(`Total bloburi: ${all.length}`);
  console.log(`Cu sufix rezoluție: ${targets.length}`);
  if (targets.length) {
    console.log('\nExemple:');
    targets.slice(0, 15).forEach((n) => console.log('  ', n));
    if (targets.length > 15) console.log(`  … și încă ${targets.length - 15}`);
  }

  if (!targets.length) {
    console.log('\nNimic de șters.');
    return;
  }

  if (!execute) {
    console.log('\nRulează cu --execute pentru a șterge.');
    return;
  }

  let ok = 0;
  let fail = 0;
  for (let i = 0; i < targets.length; i++) {
    const name = targets[i];
    process.stdout.write(`\r[${i + 1}/${targets.length}] ${name.slice(0, 60)}…`);
    try {
      await deleteBlob(baseUrl, sasToken, name);
      ok++;
    } catch (e) {
      fail++;
      console.error('\n', e.message);
    }
  }
  console.log(`\n\nGata. Șterse: ${ok}, eșuate: ${fail}.`);
}

main().catch((e) => {
  console.error(e.message || e);
  process.exit(1);
});
