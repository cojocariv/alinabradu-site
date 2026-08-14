<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/contact.php';
require_once __DIR__ . '/../includes/send_mail.php';

$errors = [];
$success = false;
$rightsOptions = [
    'access' => 'Dreptul de acces',
    'rectify' => 'Rectificarea datelor',
    'erase' => 'Ștergerea datelor',
    'restrict' => 'Restricționarea prelucrării',
    'portability' => 'Portabilitatea datelor',
    'object' => 'Opoziția la prelucrare',
    'withdraw' => 'Retragerea consimțământului',
    'other' => 'Altă solicitare',
];

$defaults = [
    'name' => '',
    'email' => '',
    'right' => 'access',
    'details' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $defaults['name'] = trim((string) ($_POST['name'] ?? ''));
    $defaults['email'] = trim((string) ($_POST['email'] ?? ''));
    $defaults['right'] = (string) ($_POST['right'] ?? 'access');
    $defaults['details'] = trim((string) ($_POST['details'] ?? ''));

    if ($defaults['name'] === '' || mb_strlen($defaults['name']) < 2) {
        $errors[] = 'Introdu numele (minim 2 caractere).';
    }
    if ($defaults['email'] === '' || !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresa de email nu este validă.';
    }
    if (!isset($rightsOptions[$defaults['right']])) {
        $errors[] = 'Selectează tipul solicitării.';
    }
    if ($defaults['details'] === '' || mb_strlen($defaults['details']) < 10) {
        $errors[] = 'Descrie solicitarea (minim 10 caractere).';
    }
    if (empty($_POST['privacy_consent'])) {
        $errors[] = 'Trebuie să confirmi că înțelegi modul de prelucrare a datelor din această cerere.';
    }

    if (!$errors) {
        $rightLabel = $rightsOptions[$defaults['right']];
        $subject = '[Cerere date personale] ' . $rightLabel;
        $body = "Cerere privind drepturile persoanei vizate ({$rightLabel})\n\n";
        $body .= 'Nume: ' . $defaults['name'] . "\n";
        $body .= 'Email: ' . $defaults['email'] . "\n";
        $body .= "Drept solicitat: {$rightLabel}\n\n";
        $body .= "Detalii:\n" . $defaults['details'] . "\n\n";
        $body .= 'Referință legală: ' . PRIVACY_LAW_FULL . "\n";

        if (sendContactFormMail($subject, $body, $defaults['email'])) {
            $success = true;
            $defaults = ['name' => '', 'email' => '', 'right' => 'access', 'details' => ''];
        } else {
            $errors[] = 'Trimiterea a eșuat. Scrie direct la ' . PRIVACY_EMAIL . '.';
        }
    }
}

$legalPageTitle = 'Cerere privind datele personale';
$legalPageDescription = 'Exercită drepturile prevăzute de Legea nr. 195/2024 privind protecția datelor cu caracter personal.';
require __DIR__ . '/../includes/legal_page_start.php';
?>

<p>
  Conform <?= e(PRIVACY_LAW_SHORT) ?>, poți exercita drepturile descrise în
  <a href="<?= e(url('/politica-confidentialitate')) ?>" class="text-gold hover:underline">Politica de confidențialitate</a>.
  Răspundem în termen de maximum 30 de zile. Putem solicita informații suplimentare pentru confirmarea identității.
</p>

<?php if ($success): ?>
  <div class="bg-green-50 text-green-900 border border-green-200 p-4 rounded-lg mt-6">
    Cererea ta a fost înregistrată. Vei primi răspuns la adresa indicată.
  </div>
<?php else: ?>
  <?php if ($errors): ?>
    <div class="bg-red-50 text-red-800 border border-red-200 p-4 rounded-lg mt-6">
      <ul class="list-disc pl-5 space-y-1 text-sm">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" class="mt-8 space-y-4 bg-white border border-gold/20 rounded-lg p-6 shadow-sm">
    <div>
      <label for="dsr-name" class="block text-sm font-medium text-ink mb-1">Nume complet <span class="text-red-600">*</span></label>
      <input id="dsr-name" name="name" type="text" required maxlength="200" value="<?= e($defaults['name']) ?>" autocomplete="name" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none">
    </div>
    <div>
      <label for="dsr-email" class="block text-sm font-medium text-ink mb-1">Email <span class="text-red-600">*</span></label>
      <input id="dsr-email" name="email" type="email" required maxlength="200" value="<?= e($defaults['email']) ?>" autocomplete="email" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none">
    </div>
    <div>
      <label for="dsr-right" class="block text-sm font-medium text-ink mb-1">Dreptul pe care dorești să îl exerciți <span class="text-red-600">*</span></label>
      <select id="dsr-right" name="right" required class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none bg-white">
        <?php foreach ($rightsOptions as $value => $label): ?>
          <option value="<?= e($value) ?>"<?= $defaults['right'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label for="dsr-details" class="block text-sm font-medium text-ink mb-1">Detalii solicitare <span class="text-red-600">*</span></label>
      <textarea id="dsr-details" name="details" required rows="5" maxlength="8000" class="w-full border border-zinc-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none resize-y" placeholder="Ex.: doresc acces la datele furnizate prin formularul de contact din data de…"><?= e($defaults['details']) ?></textarea>
    </div>
    <label class="flex gap-3 items-start text-sm text-ink-soft cursor-pointer">
      <input type="checkbox" name="privacy_consent" value="1" required class="mt-0.5 shrink-0 rounded border-zinc-300 text-gold focus:ring-gold/40"<?= !empty($_POST['privacy_consent']) ? ' checked' : '' ?>>
      <span>Confirm că datele din acest formular sunt prelucrate exclusiv pentru soluționarea cererii mea, conform <?= e(PRIVACY_LAW_SHORT) ?> și <a href="<?= e(url('/politica-confidentialitate')) ?>" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer">Politicii de confidențialitate</a>. <span class="text-red-600">*</span></span>
    </label>
    <button type="submit" class="bg-ink text-cream px-8 py-3 rounded-lg hover:bg-ink-soft transition-colors font-medium">Trimite cererea</button>
  </form>
<?php endif; ?>

<?php require __DIR__ . '/../includes/legal_page_end.php'; ?>
