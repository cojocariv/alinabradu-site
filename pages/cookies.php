<?php
declare(strict_types=1);

$legalPageTitle = 'Politica cookies';
$legalPageDescription = 'Informare privind cookie-urile și tehnologiile similare pe alinabradu.com, conform Legii nr. 195/2024.';
require __DIR__ . '/../includes/legal_page_start.php';
?>

<p>
  Site-ul <strong>alinabradu.com</strong> utilizează cookie-uri și tehnologii similare (localStorage)
  în conformitate cu <a href="<?= e(PRIVACY_LAW_URL) ?>" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer"><?= e(PRIVACY_LAW_FULL) ?></a>
  (în vigoare din <?= e(PRIVACY_LAW_EFFECTIVE) ?>).
  Consimțământul pentru cookie-urile non-esențiale este liber exprimat, specific, informat și neechivoc;
  poate fi retras oricând, la același nivel de simplitate cu acordarea lui.
</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">1. Ce sunt cookie-urile?</h2>
<p>Fișiere mici stocate în dispozitivul tău care permit funcționarea site-ului, memorarea preferințelor sau analiza utilizării. „Tehnologii similare” include și stocarea locală în browser (localStorage) pentru preferința cookies.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">2. Cine le gestionează?</h2>
<p><strong>Cookie-uri proprii (first-party):</strong> setate de alinabradu.com.<br>
<strong>Cookie-uri / servicii terțe (third-party):</strong> Google, OpenStreetMap/Leaflet, Meta (WhatsApp), Rakuten (Viber) — la interacțiune sau cu consimțământul tău.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">3. Categorii și temei legal</h2>

<h3 class="font-semibold text-ink mt-4 mb-1">Strict necesare — fără consimțământ</h3>
<p>Necesare pentru funcționarea site-ului (art. 6 alin. (1) lit. f — interes legitim).</p>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>PHPSESSID</strong> — sesiune PHP, coș de cumpărături; durată: sesiune.</li>
  <li><strong>ab_cookie_consent</strong> — memorează alegerea privind cookie-urile; durată: 12 luni.</li>
  <li><strong>ab_cookie_consent</strong> (localStorage) — aceeași preferință; durată: 12 luni.</li>
</ul>

<h3 class="font-semibold text-ink mt-4 mb-1">Funcționale / preferințe — cu consimțământ</h3>
<p>Activate doar dacă apeși „Accept toate” în banner (art. 6 alin. (1) lit. a — consimțământ).</p>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Google Fonts</strong> — fonturi tipografice (Google LLC, posibil SUA).</li>
  <li><strong>Google Maps API</strong> — hartă interactivă pe pagina Contact.</li>
  <li><strong>Leaflet / OpenStreetMap</strong> — hartă alternativă (dacă Maps nu e disponibil).</li>
  <li><strong>Tracking chat</strong> — înregistrare click WhatsApp/Viber (canal, pagină, produs, IP).</li>
  <li><strong>ipwho.is</strong> — locație aproximativă IP (oraș, țară) pentru admin.</li>
</ul>

<h3 class="font-semibold text-ink mt-4 mb-1">Resurse la navigare</h3>
<p>Tailwind CSS (CDN), imagini/video Azure, link-uri rețele sociale — necesare afișării site-ului; unele pot genera date tehnice la furnizor. Detalii în <a href="<?= e(url('/politica-confidentialitate')) ?>" class="text-gold hover:underline">Politica de confidențialitate</a>, secțiunea transferuri internaționale.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">4. Gestionarea preferințelor</h2>
<ul class="list-disc pl-5 space-y-1">
  <li>La prima vizită: banner cu <strong>Accept toate</strong> sau <strong>Doar necesare</strong> (refuzul este la fel de ușor ca acceptul).</li>
  <li>Oricând: linkul <strong>Setări cookies</strong> din footer.</li>
  <li>Retragerea consimțământului oprește tracking-ul chat și încărcarea hărții/fonturilor la vizitele viitoare; cookie-urile existente pot fi șterse din browser.</li>
</ul>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">5. Browser</h2>
<p>Poți șterge sau bloca cookie-urile din setările browserului. Funcții precum coșul de cumpărături pot fi afectate dacă blochezi cookie-urile strict necesare.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">6. Contact și plângeri</h2>
<p>Întrebări: <a href="mailto:<?= e(PRIVACY_EMAIL) ?>" class="text-gold hover:underline"><?= e(PRIVACY_EMAIL) ?></a>.<br>
Plângere: <a href="<?= e(CNPDCP_URL) ?>" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer"><?= e(CNPDCP_NAME) ?></a>.</p>

<?php require __DIR__ . '/../includes/legal_page_end.php'; ?>
