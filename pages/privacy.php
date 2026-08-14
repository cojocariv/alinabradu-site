<?php
declare(strict_types=1);

$legalPageTitle = 'Politica de confidențialitate';
$legalPageDescription = 'Cum prelucrăm datele personale pe site-ul Alina Bradu, conform Legii nr. 195/2024.';
require __DIR__ . '/../includes/legal_page_start.php';
?>

<p>Această politică descrie modul în care <strong><?= e(DATA_CONTROLLER_NAME) ?></strong> („noi”) prelucrează datele personale ale vizitatorilor și clienților site-ului <strong>alinabradu.com</strong>, în conformitate cu Legea nr. 195/2024 privind protecția datelor cu caracter personal.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">1. Operatorul de date</h2>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Denumire:</strong> <?= e(DATA_CONTROLLER_NAME) ?></li>
  <li><strong>Adresă:</strong> <?= e(DATA_CONTROLLER_ADDRESS) ?></li>
  <li><strong>Email contact:</strong> <a href="mailto:<?= e(SITE_EMAIL) ?>" class="text-gold hover:underline"><?= e(SITE_EMAIL) ?></a></li>
  <li><strong>Email date personale:</strong> <a href="mailto:<?= e(PRIVACY_EMAIL) ?>" class="text-gold hover:underline"><?= e(PRIVACY_EMAIL) ?></a></li>
  <li><strong>Telefon:</strong> <a href="tel:<?= e(SITE_PHONE_TEL) ?>" class="text-gold hover:underline"><?= e(SITE_PHONE_DISPLAY) ?></a></li>
</ul>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">2. Ce date colectăm</h2>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Formular contact:</strong> nume, email, telefon, număr comandă (opțional), mesaj.</li>
  <li><strong>Comenzi online (checkout):</strong> nume, telefon, adresă de livrare, produse comandate.</li>
  <li><strong>Coș de cumpărături:</strong> identificator de sesiune (cookie tehnic).</li>
  <li><strong>Butoane WhatsApp / Viber:</strong> la click, putem înregistra canalul, pagina vizitată, produsul (dacă e cazul), adresa IP, agentul browserului și locația aproximativă (oraș, țară) — doar dacă ai acceptat cookie-urile funcționale.</li>
  <li><strong>Hartă contact:</strong> servicii de cartografie terțe (Google Maps) — doar cu consimțământ.</li>
  <li><strong>Jurnale tehnice:</strong> adresa IP, data/ora accesului (generat de server la trimiterea formularelor).</li>
</ul>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">3. Scopuri și temeiuri legale</h2>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Răspuns la solicitări</strong> (formular contact) — interes legitim / măsuri precontractuale.</li>
  <li><strong>Procesarea comenzilor</strong> — executarea contractului.</li>
  <li><strong>Funcționarea site-ului</strong> (coș, securitate) — interes legitim.</li>
  <li><strong>Tracking chat, hartă, fonturi</strong> — consimțământul tău (cookie banner).</li>
  <li><strong>Marketing</strong> (dacă bifezi opțional) — consimțământ explicit.</li>
</ul>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">4. Destinatari / împuterniciți</h2>
<p>Datele pot fi accesate de furnizori care ne prestează servicii IT: hosting web, baza de date, email (SMTP), stocare fișiere (Microsoft Azure), dezvoltare/monitorizare site. La click pe WhatsApp sau Viber, datele tale sunt prelucrate de platformele respective (Meta / Rakuten Viber), conform politicilor lor.</p>
<p>Geolocalizarea IP (oraș, țară) pentru admin folosește serviciul ipwho.is.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">5. Perioada de păstrare</h2>
<ul class="list-disc pl-5 space-y-1">
  <li>Mesaje contact: până la 24 de luni de la rezolvare.</li>
  <li>Comenzi: conform obligațiilor legale contabile/fiscale (de regulă 5–10 ani).</li>
  <li>Înregistrări click chat: până la 12 luni.</li>
  <li>Sesiune coș: câteva zile (până la finalizarea comenzii sau expirarea sesiunii).</li>
  <li>Preferință cookies: 12 luni.</li>
</ul>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">6. Drepturile tale</h2>
<p>Conform Legii nr. 195/2024, ai dreptul la: informare, acces, rectificare, ștergere, restricționare, portabilitate, opoziție (inclusiv la marketing) și retragerea consimțământului (fără a afecta legalitatea prelucrării anterioare).</p>
<p>Solicitările se trimit la <a href="mailto:<?= e(PRIVACY_EMAIL) ?>" class="text-gold hover:underline"><?= e(PRIVACY_EMAIL) ?></a>. Răspundem în termen de maximum 30 de zile (cu posibilitate de prelungire justificată).</p>
<p>Poți depune plângere la <a href="<?= e(CNPDP_URL) ?>" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer"><?= e(CNPDP_NAME) ?></a>.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">7. Securitate</h2>
<p>Aplicăm măsuri tehnice și organizatorice rezonabile: conexiune HTTPS, acces restricționat la admin, parole securizate, backup-uri.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">8. Cookies</h2>
<p>Detalii în <a href="<?= e(url('/politica-cookies')) ?>" class="text-gold hover:underline">Politica cookies</a>.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">9. Modificări</h2>
<p>Putem actualiza această politică. Versiunea curentă este publicată pe această pagină, cu data actualizării.</p>

<?php require __DIR__ . '/../includes/legal_page_end.php'; ?>
