<?php
declare(strict_types=1);

$legalPageTitle = 'Politica de confidențialitate';
$legalPageDescription = 'Informare privind prelucrarea datelor cu caracter personal pe alinabradu.com, conform Legii nr. 195/2024.';
require __DIR__ . '/../includes/legal_page_start.php';
?>

<p>
  Prezenta politică informează persoanele vizate despre modul în care
  <strong><?= e(DATA_CONTROLLER_NAME) ?></strong> („operatorul”, „noi”) prelucrează datele cu caracter personal
  prin site-ul <strong>alinabradu.com</strong>, în conformitate cu
  <a href="<?= e(PRIVACY_LAW_URL) ?>" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer"><?= e(PRIVACY_LAW_FULL) ?></a>
  (publicată în Monitorul Oficial la 23 august 2024; aplicabilă din <?= e(PRIVACY_LAW_EFFECTIVE) ?>),
  care transpune principiile Regulamentului (UE) 2016/679 (GDPR).
</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">1. Operatorul de date și date de contact</h2>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Operator:</strong> <?= e(DATA_CONTROLLER_NAME) ?></li>
  <li><strong>Adresă:</strong> <?= e(DATA_CONTROLLER_ADDRESS) ?></li>
  <li><strong>Email general:</strong> <a href="mailto:<?= e(SITE_EMAIL) ?>" class="text-gold hover:underline"><?= e(SITE_EMAIL) ?></a></li>
  <li><strong>Email pentru date personale / drepturi:</strong> <a href="mailto:<?= e(PRIVACY_EMAIL) ?>" class="text-gold hover:underline"><?= e(PRIVACY_EMAIL) ?></a></li>
  <li><strong>Telefon:</strong> <a href="tel:<?= e(SITE_PHONE_TEL) ?>" class="text-gold hover:underline"><?= e(SITE_PHONE_DISPLAY) ?></a></li>
  <li><strong>Responsabil cu protecția datelor (DPO):</strong> ne desemnăm la cererea <?= e(CNPDCP_NAME) ?>; până atunci solicitările privind datele se trimit la adresa de email pentru date personale de mai sus.</li>
</ul>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">2. Principiile prelucrării</h2>
<p>Prelucrăm datele cu caracter personal în mod legal, echitabil și transparent, limitat la scopuri determinate, exacte și actualizate, păstrate doar cât este necesar, securizate și doar în măsura strict necesară (minimizarea datelor), cu respectarea responsabilității operatorului — conform art. 5 din <?= e(PRIVACY_LAW_SHORT) ?>.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">3. Categorii de date prelucrate</h2>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Identificare și contact:</strong> nume, email, telefon, adresă de livrare.</li>
  <li><strong>Comunicări:</strong> conținutul mesajelor, număr comandă (opțional).</li>
  <li><strong>Comenzi:</strong> produse, cantități, valoare.</li>
  <li><strong>Tehnice:</strong> adresă IP, agent browser (User-Agent), dată/oră acces, cookie sesiune (<code>PHPSESSID</code>), preferință cookies (<code>ab_cookie_consent</code>).</li>
  <li><strong>Interacțiuni chat (WhatsApp/Viber):</strong> canal, pagină, produs (dacă e cazul), IP, locație aproximativă (oraș, țară) — doar cu consimțământ pentru cookie-uri funcționale.</li>
</ul>
<p>Nu colectăm intenționat date despre copii sub 16 ani. Dacă afli că un minor ne-a furnizat date fără acordul părintelui/tutorelui, contactează-ne pentru ștergere.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">4. Scopuri, temeiuri legale și interese legitime</h2>
<div class="overflow-x-auto">
  <table class="w-full text-sm border border-gold/20 mt-2">
    <thead>
      <tr class="bg-gold/10 text-left">
        <th class="p-2 border-b border-gold/20">Scop</th>
        <th class="p-2 border-b border-gold/20">Date</th>
        <th class="p-2 border-b border-gold/20">Temei legal (art. 6)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="p-2 border-b border-gold/10 align-top">Răspuns la solicitări (formular contact)</td>
        <td class="p-2 border-b border-gold/10 align-top">Nume, email, telefon, mesaj</td>
        <td class="p-2 border-b border-gold/10 align-top">Măsuri precontractuale / interes legitim (comunicarea cu clienții)</td>
      </tr>
      <tr>
        <td class="p-2 border-b border-gold/10 align-top">Procesarea și livrarea comenzilor</td>
        <td class="p-2 border-b border-gold/10 align-top">Nume, telefon, adresă, produse</td>
        <td class="p-2 border-b border-gold/10 align-top">Executarea contractului</td>
      </tr>
      <tr>
        <td class="p-2 border-b border-gold/10 align-top">Coș de cumpărături, securitate site</td>
        <td class="p-2 border-b border-gold/10 align-top">Cookie sesiune, IP</td>
        <td class="p-2 border-b border-gold/10 align-top">Interes legitim (funcționarea site-ului)</td>
      </tr>
      <tr>
        <td class="p-2 border-b border-gold/10 align-top">Suport prin WhatsApp/Viber, hartă, fonturi</td>
        <td class="p-2 border-b border-gold/10 align-top">Date tehnice, locație IP</td>
        <td class="p-2 border-b border-gold/10 align-top">Consimțământ (banner cookies / bifă formular)</td>
      </tr>
      <tr>
        <td class="p-2 align-top">Oferte și noutăți (marketing)</td>
        <td class="p-2 align-top">Email, telefon</td>
        <td class="p-2 align-top">Consimțământ explicit (opțional, separat)</td>
      </tr>
    </tbody>
  </table>
</div>
<p class="mt-3">Poți retrage consimțământul oricând, fără a afecta legalitatea prelucrării efectuate anterior. Pentru prelucrările bazate pe interes legitim, poți formula opoziție — vezi secțiunea 8.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">5. Destinatari și persoane împuternicite</h2>
<p>Datele pot fi comunicate, strict necesar, următorilor categorii de destinatari:</p>
<ul class="list-disc pl-5 space-y-1">
  <li>Furnizori hosting, bază de date, email (SMTP), administrare tehnică;</li>
  <li>Microsoft Azure — stocare imagini/fișiere;</li>
  <li>Meta Platforms (WhatsApp), Rakuten Viber — la deschiderea chat-ului;</li>
  <li>Google LLC — Maps, Fonts (cu consimțământ);</li>
  <li>ipwho.is — geolocalizare aproximativă IP (admin);</li>
  <li>Autorități publice — când legea o impune.</li>
</ul>
<p>Încheiem acorduri cu persoanele împuternicite care impun obligații de confidențialitate și securitate conform <?= e(PRIVACY_LAW_SHORT) ?>.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">6. Transferuri internaționale</h2>
<p>Unele servicii (Google, Meta, Microsoft Azure, ipwho.is) pot prelucra date în state terțe (inclusiv SUA). Transferul are loc numai cu garanții adecvate prevăzute de lege: clauze contractuale standard, politici de confidențialitate ale furnizorilor și măsuri tehnice suplimentare. Detalii suplimentare le poți solicita la <a href="mailto:<?= e(PRIVACY_EMAIL) ?>" class="text-gold hover:underline"><?= e(PRIVACY_EMAIL) ?></a>.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">7. Termene de păstrare</h2>
<ul class="list-disc pl-5 space-y-1">
  <li>Mesaje contact: până la 24 luni de la soluționare;</li>
  <li>Comenzi: conform obligațiilor contabile și fiscale (de regulă 5–10 ani);</li>
  <li>Click-uri chat: până la 12 luni;</li>
  <li>Sesiune coș: până la finalizarea comenzii sau expirarea sesiunii;</li>
  <li>Preferință cookies: 12 luni;</li>
  <li>Marketing: până la retragerea consimțământului.</li>
</ul>
<p>După expirare, datele sunt șterse sau anonimizate.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">8. Drepturile persoanei vizate</h2>
<p>Conform <?= e(PRIVACY_LAW_SHORT) ?>, ai următoarele drepturi:</p>
<ul class="list-disc pl-5 space-y-1">
  <li><strong>Informare și acces</strong> — să știi ce date prelucrăm;</li>
  <li><strong>Rectificare</strong> — corectarea datelor inexacte;</li>
  <li><strong>Ștergere</strong> („dreptul de a fi uitat”) — în condițiile legii;</li>
  <li><strong>Restricționare</strong> — limitarea prelucrării în anumite situații;</li>
  <li><strong>Portabilitate</strong> — primirea datelor într-un format structurat;</li>
  <li><strong>Opoziție</strong> — inclusiv la marketing direct;</li>
  <li><strong>Retragerea consimțământului</strong> — oricând, pentru prelucrările bazate pe consimțământ;</li>
  <li><strong>Plângere</strong> — la autoritatea de supraveghere.</li>
</ul>
<p>Nu efectuăm decizii automatizate sau profilare cu efect juridic similar asupra ta.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">9. Exercitarea drepturilor</h2>
<p>Trimite o solicitare la <a href="mailto:<?= e(PRIVACY_EMAIL) ?>" class="text-gold hover:underline"><?= e(PRIVACY_EMAIL) ?></a> sau folosește <a href="<?= e(url('/cerere-date-personale')) ?>" class="text-gold hover:underline">formularul de cereri privind datele personale</a>. Include: numele, datele de contact, dreptul pe care dorești să îl exerciți și informații care ne permit identificarea ta.</p>
<p>Răspundem gratuit în termen de <strong>maximum 30 de zile</strong> de la primire (prelungibil cu 60 de zile în cazuri complexe, cu informare prealabilă). Putem solicita informații suplimentare pentru confirmarea identității.</p>
<p>Poți depune plângere la <a href="<?= e(CNPDCP_URL) ?>" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer"><?= e(CNPDCP_NAME) ?></a> (CNPDCP).</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">10. Obligația de a furniza datele</h2>
<p>Pentru comenzi și contact, furnizarea numelui, telefonului și (după caz) adresei/emailului este <strong>necesară pentru executarea solicitării</strong>. Fără aceste date nu putem procesa comanda sau răspunde mesajului. Marketingul este opțional.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">11. Securitate și încălcări</h2>
<p>Aplicăm măsuri tehnice și organizatorice adecvate: HTTPS, control acces admin, parole securizate, backup-uri, limitarea accesului la date. În cazul unei încălcări a securității datelor cu risc ridicat pentru drepturile tale, te vom informa conform obligațiilor legale.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">12. Cookies și tehnologii similare</h2>
<p>Detalii în <a href="<?= e(url('/politica-cookies')) ?>" class="text-gold hover:underline">Politica cookies</a>. Consimțământul pentru cookie-uri non-esențiale se obține prin bannerul site-ului.</p>

<h2 class="font-serif text-xl text-ink mt-8 mb-2">13. Modificări</h2>
<p>Putem actualiza această politică pentru a reflecta schimbări legislative sau operaționale. Versiunea curentă este publicată pe această pagină.</p>

<?php require __DIR__ . '/../includes/legal_page_end.php'; ?>
