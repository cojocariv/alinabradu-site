<?php
declare(strict_types=1);

$expectedKey = getenv('MAIL_DIAG_KEY');
if (!is_string($expectedKey) || $expectedKey === '') {
    http_response_code(404);
    echo 'Diagnostic dezactivat. Setează MAIL_DIAG_KEY în Plesk (PHP).';
    exit;
}

$provided = (string) ($_GET['key'] ?? '');
if (!hash_equals($expectedKey, $provided)) {
    http_response_code(403);
    echo 'Cheie invalidă.';
    exit;
}

require_once __DIR__ . '/../includes/mail_diagnose.php';

$trySend = isset($_GET['send']) && $_GET['send'] === '1';
$rows = runMailDiagnosis($trySend);

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Diagnostic email — contact</title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 2rem; background: #fbf6ee; color: #1a1410; }
    h1 { font-size: 1.25rem; }
    table { width: 100%; max-width: 52rem; border-collapse: collapse; background: #fff; }
    th, td { border: 1px solid #ddd; padding: 0.6rem 0.75rem; text-align: left; vertical-align: top; }
    th { background: #f5f0e8; font-size: 0.75rem; text-transform: uppercase; }
    .ok { color: #166534; font-weight: 600; }
    .fail { color: #9b2c2c; font-weight: 600; }
    .warn { color: #92400e; font-weight: 600; }
    pre { margin: 0; white-space: pre-wrap; font-size: 0.85rem; }
    p.note { max-width: 52rem; font-size: 0.9rem; color: #3d342c; }
    a { color: #9b7b3d; }
  </style>
</head>
<body>
  <h1>Diagnostic trimitere email (formular contact)</h1>
  <p class="note">
    Dacă mesajul de eroare de pe site încă arată <code>alinabradu.office@gmail.com</code>,
    <strong>codul vechi este încă pe server</strong> — urcă fișierele noi din Git/FTP.
    <a href="?key=<?= htmlspecialchars($provided, ENT_QUOTES, 'UTF-8') ?>&send=1">Rulează și test de trimitere</a>
  </p>
  <table>
    <thead>
      <tr><th>Verificare</th><th>Status</th><th>Detaliu</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['label'], ENT_QUOTES, 'UTF-8') ?></td>
          <td class="<?= htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><pre><?= htmlspecialchars($r['detail'], ENT_QUOTES, 'UTF-8') ?></pre></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="note">Șterge sau dezactivează această pagină după debug (MAIL_DIAG_KEY).</p>
</body>
</html>
