<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/contact.php';
require_once __DIR__ . '/../includes/send_mail.php';
require_once __DIR__ . '/../models/ProductModel.php';

$errors = [];
$success = false;

$defaults = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'order_number' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $qsProd = trim((string) ($_GET['produs'] ?? ''));
    if ($qsProd !== '') {
        $pinf = ProductModel::bySlug($qsProd);
        if ($pinf) {
            $mar = trim((string) ($_GET['marime'] ?? ''));
            $cant = max(1, min(99, (int) ($_GET['cantitate'] ?? 1)));
            $defaults['message'] = "Bună ziua,\n\nDoresc să comand produsul «{$pinf['name']}»";
            if ($mar !== '') {
                $defaults['message'] .= ", mărimea: {$mar}";
            }
            $defaults['message'] .= ", cantitate: {$cant}.\n\n";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $defaults['name'] = trim((string) ($_POST['name'] ?? ''));
    $defaults['email'] = trim((string) ($_POST['email'] ?? ''));
    $defaults['phone'] = trim((string) ($_POST['phone'] ?? ''));
    $defaults['order_number'] = trim((string) ($_POST['order_number'] ?? ''));
    $defaults['message'] = trim((string) ($_POST['message'] ?? ''));

    if ($defaults['name'] === '' || mb_strlen($defaults['name']) < 2) {
        $errors[] = 'Introdu numele (minim 2 caractere).';
    }
    if ($defaults['email'] === '' || !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresa de email nu este validă.';
    }
    if ($defaults['phone'] === '' || !preg_match('/^[0-9+\s().-]{8,25}$/', $defaults['phone'])) {
        $errors[] = 'Numărul de telefon nu este valid.';
    }
    if (mb_strlen($defaults['order_number']) > 80) {
        $errors[] = 'Numărul comenzii este prea lung.';
    }
    if ($defaults['message'] === '' || mb_strlen($defaults['message']) < 10) {
        $errors[] = 'Mesajul trebuie să aibă cel puțin 10 caractere.';
    }
    if (mb_strlen($defaults['message']) > 8000) {
        $errors[] = 'Mesajul este prea lung.';
    }

    if (!$errors) {
        $subject = '[Contact site] ' . mb_substr($defaults['name'], 0, 80);
        $body = "Mesaj de pe formularul de contact\n\n";
        $body .= 'Nume: ' . $defaults['name'] . "\n";
        $body .= 'Email: ' . $defaults['email'] . "\n";
        $body .= 'Telefon: ' . $defaults['phone'] . "\n";
        $body .= 'Nr. comandă: ' . ($defaults['order_number'] !== '' ? $defaults['order_number'] : '—') . "\n\n";
        $body .= "Mesaj:\n" . $defaults['message'] . "\n";

        $sent = sendContactFormMail($subject, $body, $defaults['email']);
        if ($sent) {
            $success = true;
            $defaults = ['name' => '', 'email' => '', 'phone' => '', 'order_number' => '', 'message' => ''];
        } else {
            $errors[] = 'Trimiterea a eșuat momentan. Te rugăm să ne scrii direct la ' . CONTACT_FORM_TO_EMAIL . ' sau să ne suni la ' . SITE_PHONE_DISPLAY . '.';
        }
    }
}

$seo = [
    'title' => 'Contact - Alina Bradu',
    'description' => 'Contactează atelierul Alina Bradu: email, telefon, adresă în Chișinău, program. Trimite-ne un mesaj din formular.',
];
$stores = require __DIR__ . '/../config/stores.php';
require_once __DIR__ . '/../config/google_maps.php';
$storesJson = json_encode($stores, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
$contactPagePhoto = 'https://alinabradupozestorage.blob.core.windows.net/poze/photo_2026-05-12_17-15-59.jpg';
require __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script type="application/json" id="contact-stores-data"><?= $storesJson ?></script>
<section class="contact-map-section" aria-label="Hartă — locații magazine">
  <div
    id="contact-stores-map"
    class="contact-map"
    data-api-key="<?= e(GOOGLE_MAPS_API_KEY) ?>"
  >
    <div class="contact-map__panel" role="tablist" aria-label="Selectează magazinul pe hartă">
      <button type="button" class="contact-map__chip is-active" data-store-id="">
        Toate (Chișinău)
      </button>
      <?php foreach ($stores as $store): ?>
      <button
        type="button"
        class="contact-map__chip"
        data-store-id="<?= e($store['id']) ?>"
        role="tab"
      >
        <?= e($store['name']) ?>
      </button>
      <?php endforeach; ?>
    </div>
    <div class="contact-map__canvas" role="img" aria-label="Hartă cu locațiile magazinelor"></div>
  </div>
</section>

<section class="contact-page max-w-6xl mx-auto px-4 md:px-6 py-10 md:py-12">
  <h1 class="font-serif text-4xl mb-2">Contact</h1>
  <p class="text-zinc-600 mb-10 max-w-2xl">Suntem aici pentru comenzi, întrebări despre produse sau suport. Completează formularul sau folosește datele de mai jos.</p>

  <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">
    <div class="bg-white rounded-lg border border-zinc-200 p-6 md:p-8 shadow-sm">
      <?php if ($success): ?>
        <div class="bg-green-50 text-green-900 border border-green-200 p-4 rounded-lg mb-4">
          Mulțumim! Mesajul tău a fost trimis. Te vom contacta cât de curând.
        </div>
      <?php else: ?>
        <?php if ($errors): ?>
          <div class="bg-red-50 text-red-800 border border-red-200 p-4 rounded-lg mb-4">
            <ul class="list-disc pl-5 space-y-1 text-sm">
              <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <!-- formular contact v2025: contact@ → admin@alinabradu.com -->
        <form method="post" class="space-y-4" novalidate>
          <div>
            <label for="contact-name" class="block text-sm font-medium text-zinc-700 mb-1">Nume <span class="text-red-600">*</span></label>
            <input id="contact-name" name="name" type="text" required maxlength="200" value="<?= e($defaults['name']) ?>" autocomplete="name" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none">
          </div>
          <div>
            <label for="contact-email" class="block text-sm font-medium text-zinc-700 mb-1">Email <span class="text-red-600">*</span></label>
            <input id="contact-email" name="email" type="email" required maxlength="200" value="<?= e($defaults['email']) ?>" autocomplete="email" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none">
          </div>
          <div>
            <label for="contact-phone" class="block text-sm font-medium text-zinc-700 mb-1">Telefon <span class="text-red-600">*</span></label>
            <input id="contact-phone" name="phone" type="tel" required maxlength="30" value="<?= e($defaults['phone']) ?>" autocomplete="tel" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none" placeholder="+373 …">
          </div>
          <div>
            <label for="contact-order" class="block text-sm font-medium text-zinc-700 mb-1">Nr. comandă <span class="text-zinc-400 font-normal">(opțional)</span></label>
            <input id="contact-order" name="order_number" type="text" maxlength="80" value="<?= e($defaults['order_number']) ?>" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none" placeholder="Dacă mesajul se referă la o comandă">
          </div>
          <div>
            <label for="contact-message" class="block text-sm font-medium text-zinc-700 mb-1">Mesaj <span class="text-red-600">*</span></label>
            <textarea id="contact-message" name="message" required rows="6" maxlength="8000" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none resize-y"><?= e($defaults['message']) ?></textarea>
          </div>
          <button type="submit" class="w-full sm:w-auto bg-zinc-900 text-white px-8 py-3 rounded-lg hover:bg-zinc-800 transition-colors font-medium">Trimite mesajul</button>
        </form>
      <?php endif; ?>
    </div>

    <div class="space-y-8">
      <figure class="rounded-xl overflow-hidden border border-zinc-200 bg-zinc-100 shadow-sm">
        <img
          src="<?= e($contactPagePhoto) ?>"
          alt="Adrese magazine Alina Bradu: CC UNIC etaj 1, CC GEMENI etaj 2, CC ZORILE etaj 2; showroom str. Ștefan cel Mare 126; Brașov, str. Republicii 1."
          class="w-full h-auto object-cover max-h-[min(85vh,52rem)] object-center"
          width="900"
          height="1200"
          loading="lazy"
          decoding="async"
        >
      </figure>

      <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
        <h2 class="font-serif text-lg text-wine mb-3">Adrese magazine</h2>
        <ul class="space-y-2 text-sm text-zinc-700 list-disc pl-5 marker:text-gold">
          <li>CC UNIC, etajul 1, 3</li>
          <li>CC GEMENI, etajul 2</li>
          <li>CC ZORILE, etajul 2</li>
          <li>Showroom „Alina Bradu”, str. Ștefan cel Mare 126</li>
          <li>or. Brașov, str. Republicii 1</li>
        </ul>
      </div>

      <div>
        <h2 class="font-serif text-2xl text-zinc-900 mb-4">Informații contact</h2>
        <ul class="space-y-2 text-zinc-700">
          <li>
            <span class="text-zinc-500 text-sm block">Email</span>
            <a href="mailto:<?= e(SITE_EMAIL) ?>" class="text-gold font-medium hover:underline"><?= e(SITE_EMAIL) ?></a>
          </li>
          <li>
            <span class="text-zinc-500 text-sm block">Telefon</span>
            <a href="tel:<?= e(SITE_PHONE_TEL) ?>" class="text-zinc-900 font-medium hover:text-gold"><?= e(SITE_PHONE_DISPLAY) ?></a>
          </li>
        </ul>
      </div>

      <div class="border-t border-zinc-200 pt-8">
        <h2 class="font-serif text-2xl text-zinc-900 mb-3">Adresă</h2>
        <p class="text-zinc-700 leading-relaxed">
          Republica Moldova, Chișinău<br>
          str. Ștefan cel Mare și Sfânt 126
        </p>
      </div>

      <div class="border-t border-zinc-200 pt-8">
        <h2 class="font-serif text-xl text-zinc-900 mb-3">Orele de suport</h2>
        <p class="text-zinc-700">Luni – Vineri, 09:00 – 17:00</p>
        <p class="text-sm text-red-600 mt-2">*În afară de zilele de weekend</p>
      </div>

      <div class="border-t border-zinc-200 pt-8">
        <h2 class="font-serif text-3xl text-zinc-900 mb-4">Chișinău</h2>
        <p class="text-zinc-700 mb-6 leading-relaxed">
          Republica Moldova, Chișinău<br>
          str. Ștefan cel Mare și Sfânt 126
        </p>
        <div class="space-y-0 border-t border-zinc-200">
          <div class="flex flex-wrap justify-between gap-2 py-3 border-b border-zinc-200 text-sm sm:text-base">
            <span class="text-zinc-600">Zile lucrătoare</span>
            <span class="font-medium text-zinc-900 tabular-nums">09:00 – 19:00</span>
          </div>
          <div class="flex flex-wrap justify-between gap-2 py-3 text-sm sm:text-base">
            <span class="text-zinc-600">Sâmbătă – Duminică</span>
            <span class="font-medium text-zinc-900 tabular-nums">09:00 – 17:00</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="<?= e(url('/assets/js/contact-map.js')) ?>?v=3" defer></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
