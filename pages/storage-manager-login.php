<?php
declare(strict_types=1);
/** @var string|null $loginError */
$loginError = $loginError ?? null;
?>
<!doctype html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Autentificare — Gestionare poze</title>
  <style>
    :root { --cream: #fbf6ee; --gold: #c9a96e; --ink: #1a1410; --danger: #9b2c2c; }
    body {
      font-family: "Segoe UI", system-ui, sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      background: var(--cream);
      color: var(--ink);
    }
    .card {
      width: 100%;
      max-width: 22rem;
      background: #fff;
      border: 1px solid rgba(201, 169, 110, 0.45);
      border-radius: 8px;
      padding: 1.75rem;
    }
    h1 { font-size: 1.25rem; margin: 0 0 0.35rem; font-weight: 600; }
    p { margin: 0 0 1.25rem; font-size: 0.9rem; color: #3d342c; }
    label { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.35rem; }
    input[type="password"] {
      width: 100%;
      padding: 0.55rem 0.65rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      margin-bottom: 1rem;
    }
    .btn {
      width: 100%;
      border: none;
      background: var(--ink);
      color: var(--cream);
      padding: 0.65rem 1rem;
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      cursor: pointer;
      border-radius: 4px;
    }
    .btn:hover { background: #3d342c; }
    .error { color: var(--danger); font-size: 0.875rem; margin-bottom: 1rem; }
    .back { display: block; margin-top: 1.25rem; text-align: center; font-size: 0.85rem; color: #3d342c; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Gestionare poze Azure</h1>
    <p>Acces restricționat</code>.</p>
    <?php if ($loginError): ?>
      <p class="error"><?= e($loginError) ?></p>
    <?php endif; ?>
    <form method="post" action="<?= e(url('/produs.php')) ?>">
      <input type="hidden" name="storage_login" value="1">
      <label for="storage-password">Parolă</label>
      <input type="password" id="storage-password" name="password" required autocomplete="current-password" autofocus>
      <button type="submit" class="btn">Intră</button>
    </form>
    <a class="back" href="<?= e(url('/')) ?>">← Înapoi la site</a>
  </div>
</body>
</html>
