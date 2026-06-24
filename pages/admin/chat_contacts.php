<?php
declare(strict_types=1);
require_once __DIR__ . '/../../models/ChatContactModel.php';
require_once __DIR__ . '/../../includes/ip_geo.php';
require_once __DIR__ . '/../../includes/admin_auth.php';

adminRequireLogin();

$loadError = null;
$leads = [];
$totalLeads = 0;

try {
    $totalLeads = ChatContactModel::countAll();
    $leads = ChatContactModel::listRecent(250);
    $leads = ChatContactModel::hydrateGeoForList($leads);
} catch (Throwable $e) {
    $loadError = $e->getMessage();
}

$sourceLabels = [
    'header' => 'Header',
    'footer_fab' => 'Buton „Discută cu noi”',
    'product' => 'Pagină produs',
    'shop_card' => 'Card magazin',
    'category' => 'Pagină categorie',
    'unknown' => 'Necunoscut',
];

$seo = ['title' => 'Contacte WhatsApp / Viber - Admin'];
?><!doctype html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($seo['title']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-100 min-h-screen text-zinc-800">
  <header class="bg-zinc-900 text-white px-4 py-3 flex flex-wrap items-center justify-between gap-2">
    <span class="font-serif text-lg">Admin — Alina Bradu</span>
    <nav class="flex flex-wrap gap-4 text-sm">
      <a href="<?= e(url('/admin/produse')) ?>" class="hover:underline">Produse</a>
      <a href="<?= e(url('/admin/contacte-chat')) ?>" class="text-gold">Contacte chat</a>
      <a href="<?= e(url('/admin/despre')) ?>" class="hover:underline">Despre noi</a>
      <a href="<?= e(url('/')) ?>" class="hover:underline">Site</a>
      <a href="<?= e(url('/admin/logout')) ?>" class="hover:underline">Ieșire</a>
    </nav>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="font-serif text-3xl mb-2">Contacte WhatsApp / Viber</h1>
    <p class="text-sm text-zinc-600 mb-6">Înregistrăm vizitatorii care apasă pe un link WhatsApp sau Viber (intenție de contact). Locația este aproximativă (oraș, țară) pe baza IP-ului. Pentru detalii WHOIS suplimentare poți folosi <a href="https://whois.domaintools.com/" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer">DomainTools</a>.</p>

    <?php if ($loadError !== null): ?>
      <p class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded px-3 py-2">
        Nu s-au putut încărca datele. Rulează migrările <code class="text-xs">sql/migration_chat_contacts.sql</code> și <code class="text-xs">sql/migration_chat_contacts_geo.sql</code> în phpMyAdmin.
        <span class="block mt-1 text-red-600"><?= e($loadError) ?></span>
      </p>
    <?php else: ?>
      <p class="text-sm text-zinc-600 mb-4">Total înregistrări: <strong><?= (int) $totalLeads ?></strong></p>

      <div class="bg-white rounded-lg border border-zinc-200 overflow-x-auto shadow-sm">
        <table class="w-full text-sm min-w-[720px]">
          <thead class="bg-zinc-50 border-b text-left text-zinc-600">
            <tr>
              <th class="p-3 whitespace-nowrap">Data</th>
              <th class="p-3">Canal</th>
              <th class="p-3">Sursă</th>
              <th class="p-3">Produs</th>
              <th class="p-3">Pagină</th>
              <th class="p-3">IP</th>
              <th class="p-3 whitespace-nowrap">Locație</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($leads === []): ?>
              <tr>
                <td colspan="7" class="p-6 text-center text-zinc-500">Nicio înregistrare încă.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($leads as $lead): ?>
                <?php
                $channel = (string) ($lead['channel'] ?? '');
                $source = (string) ($lead['source'] ?? 'unknown');
                $sourceLabel = $sourceLabels[$source] ?? $source;
                $productLabel = trim((string) ($lead['product_name'] ?? ''));
                if ($productLabel === '' && !empty($lead['product_slug'])) {
                    $productLabel = (string) $lead['product_slug'];
                }
                $ipAddress = trim((string) ($lead['ip_address'] ?? ''));
                $geoLabel = formatIpGeoLabel(
                    isset($lead['geo_city']) ? (string) $lead['geo_city'] : null,
                    isset($lead['geo_country']) ? (string) $lead['geo_country'] : null,
                    isset($lead['geo_region']) ? (string) $lead['geo_region'] : null
                );
                ?>
                <tr class="border-b border-zinc-100 hover:bg-zinc-50 align-top">
                  <td class="p-3 whitespace-nowrap text-zinc-600"><?= e((string) ($lead['created_at'] ?? '')) ?></td>
                  <td class="p-3">
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium <?= $channel === 'whatsapp' ? 'bg-green-50 text-green-800' : 'bg-violet-50 text-violet-800' ?>">
                      <?= $channel === 'whatsapp' ? 'WhatsApp' : 'Viber' ?>
                    </span>
                  </td>
                  <td class="p-3"><?= e($sourceLabel) ?></td>
                  <td class="p-3">
                    <?php if ($productLabel !== '' && !empty($lead['product_slug'])): ?>
                      <a href="<?= e(url('/produs/' . $lead['product_slug'])) ?>" class="text-gold hover:underline" target="_blank" rel="noopener noreferrer"><?= e($productLabel) ?></a>
                    <?php elseif ($productLabel !== ''): ?>
                      <?= e($productLabel) ?>
                    <?php else: ?>
                      <span class="text-zinc-400">—</span>
                    <?php endif; ?>
                  </td>
                  <td class="p-3 text-zinc-600 max-w-[12rem] truncate" title="<?= e((string) ($lead['page_path'] ?? '')) ?>"><?= e((string) ($lead['page_path'] ?? '')) ?></td>
                  <td class="p-3 text-zinc-500 whitespace-nowrap"><?= $ipAddress !== '' ? e($ipAddress) : '—' ?></td>
                  <td class="p-3 text-zinc-600 whitespace-nowrap">
                    <?php if ($geoLabel !== '—'): ?>
                      <?= e($geoLabel) ?>
                      <?php if ($ipAddress !== ''): ?>
                        <a href="<?= e(domainToolsIpLookupUrl($ipAddress)) ?>" class="ml-1 text-xs text-zinc-400 hover:text-gold" target="_blank" rel="noopener noreferrer" title="WHOIS pe DomainTools">↗</a>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-zinc-400">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
