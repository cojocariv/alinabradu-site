<?php
declare(strict_types=1);

$legalPageTitle = 'Politica cookies';
$legalPageDescription = 'Ce cookie-uri și tehnologii similare folosim pe alinabradu.com.';
require __DIR__ . '/../includes/legal_page_start.php';
?>

<p>Site-ul <strong>alinabradu.com</strong> folosește cookie-uri și tehnologii similare. Poți gestiona preferințele din bannerul afișat la prima vizită sau oricând din linkul „Setări cookies” din footer.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">1. Ce sunt cookie-urile?</h2>
<p>Fișiere mici stocate în browser care permit funcționarea site-ului sau memorarea preferințelor.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">2. Categorii folosite</h2>

<h3 class="font-semibold text-ink mt-4 mb-1">Strict necesare (fără consimțământ)</h3>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>PHPSESSID</strong> — sesiune coș de cumpărături și funcții site.</li>
  <li><strong>ab_cookie_consent</strong> — memorează alegerea ta privind cookie-urile (12 luni).</li>
</ul>

<h3 class="font-semibold text-ink mt-4 mb-1">Funcționale / preferințe (cu consimțământ „Accept toate”)</h3>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Google Fonts</strong> — fonturi tipografice (Google LLC).</li>
  <li><strong>Google Maps</strong> — hartă pe pagina Contact.</li>
  <li><strong>Tracking chat</strong> — înregistrare click WhatsApp/Viber (IP, pagină, produs) pentru suport clienți.</li>
  <li><strong>ipwho.is</strong> — locație aproximativă (oraș, țară) pe baza IP, vizibilă în admin.</li>
</ul>

<h3 class="font-semibold text-ink mt-4 mb-1">Resurse încărcate pentru afișarea site-ului</h3>
<p>Tailwind CSS (CDN), imagini și video de pe Microsoft Azure, rețele sociale (Facebook, Instagram) — accesate la navigare; detalii în <a href="<?= e(url('/politica-confidentialitate')) ?>" class="text-gold hover:underline">Politica de confidențialitate</a>.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">3. Cum îți exercită drepturile</h2>
<p>Poți retrage consimțământul oricând apăsând „Setări cookies” în footer și alegând „Doar necesare”.</p>
<p>Pentru alte solicitări: <a href="mailto:<?= e(PRIVACY_EMAIL) ?>" class="text-gold hover:underline"><?= e(PRIVACY_EMAIL) ?></a>.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">4. Browser</h2>
<p>Poți șterge sau bloca cookie-urile din setările browserului. Unele funcții (coș, preferințe) pot fi afectate.</p>

<?php require __DIR__ . '/../includes/legal_page_end.php'; ?>
