<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/admin_auth.php';

if (adminIsLoggedIn()) {
    redirectTo('/admin/produse');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = (string) ($_POST['password'] ?? '');
    if ($pass !== '' && adminTryLogin($pass)) {
        $_SESSION['admin_logged'] = true;
        redirectTo('/admin/produse');
    }
    $error = 'Parolă incorectă.';
}

$seo = ['title' => 'Admin - Alina Bradu'];
?><!doctype html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($seo['title']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-100 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-sm border border-zinc-200">
    <h1 class="font-serif text-2xl mb-6 text-center">Administrare magazin</h1>
    <?php if ($error): ?>
      <p class="text-red-600 text-sm mb-4"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="post" class="space-y-4">
      <label class="block text-sm font-medium text-zinc-700">Parolă</label>
      <input type="password" name="password" required class="w-full border rounded px-3 py-2" autocomplete="current-password">
      <button type="submit" class="w-full bg-zinc-900 text-white py-2 rounded hover:bg-zinc-800">Intră</button>
    </form>
    <p class="text-xs text-zinc-500 mt-4 text-center"><a href="<?= e(url('/')) ?>" class="underline">← Înapoi la site</a></p>
  </div>
</body>
</html>
