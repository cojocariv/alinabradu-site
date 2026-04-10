<?php
declare(strict_types=1);
$seo = mergeSeo($seo ?? []);
$orgSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Alina Bradu',
    'url' => (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
    'logo' => 'https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png',
];
?>
<!doctype html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($seo['title']) ?></title>
  <meta name="description" content="<?= e($seo['description']) ?>">
  <meta name="keywords" content="<?= e($seo['keywords']) ?>">
  <meta property="og:title" content="<?= e($seo['title']) ?>">
  <meta property="og:description" content="<?= e($seo['description']) ?>">
  <meta property="og:image" content="<?= e($seo['image']) ?>">
  <meta property="og:type" content="<?= e($seo['type']) ?>">
  <meta property="og:url" content="<?= e((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url('/' . routePath())) ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            cream: '#FFF8EE',
            gold: '#B9965A',
            wine: '#7A1E2C'
          },
          fontFamily: {
            serif: ['"Playfair Display"', 'serif'],
            sans: ['Inter', 'sans-serif']
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('/assets/css/custom.css')) ?>">
  <script type="application/ld+json"><?= json_encode($orgSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body class="bg-cream text-zinc-800 font-sans">
  <header class="bg-white/90 backdrop-blur border-b border-gold/20 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="<?= e(url('/')) ?>" class="flex items-center gap-3">
        <img src="https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png" alt="Alina Bradu" class="h-10 w-auto" loading="lazy">
      </a>
      <nav class="hidden md:flex items-center gap-6 text-sm uppercase tracking-wide">
        <a href="<?= e(url('/')) ?>" class="hover:text-gold">Acasă</a>
        <a href="<?= e(url('/magazin')) ?>" class="hover:text-gold">Magazin</a>
        <a href="<?= e(url('/despre-noi')) ?>" class="hover:text-gold">Despre noi</a>
        <a href="<?= e(url('/contact')) ?>" class="hover:text-gold">Contact</a>
        <a href="<?= e(url('/cos')) ?>" class="hover:text-gold">Coș (<?= cartItemsCount() ?>)</a>
      </nav>
    </div>
  </header>
  <main class="min-h-[70vh]">
