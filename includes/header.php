<?php
declare(strict_types=1);
$headerOverlay = $headerOverlay ?? false;
$bodyClass = $bodyClass ?? '';
$headPreloadVideo = $headPreloadVideo ?? '';
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
  <?php if ($headPreloadVideo !== ''): ?>
  <link rel="preconnect" href="https://alinabradupozestorage.blob.core.windows.net" crossorigin>
  <link rel="dns-prefetch" href="//alinabradupozestorage.blob.core.windows.net">
  <link rel="preload" href="<?= e($headPreloadVideo) ?>" as="video" type="video/mp4" fetchpriority="high">
  <?php endif; ?>
  <link rel="icon" type="image/png" href="https://alinabradupozestorage.blob.core.windows.net/poze/favicon.png">
  <link rel="apple-touch-icon" href="https://alinabradupozestorage.blob.core.windows.net/poze/favicon.png">
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
            cream: '#FBF6EE',
            gold: '#C9A96E',
            ink: '#1A1410',
            'ink-soft': '#3D342C',
            'ink-muted': '#6B5F54',
            'gold-light': '#E8DCC4'
          },
          fontFamily: {
            serif: ['"Cormorant Garamond"', 'Georgia', 'serif'],
            sans: ['Jost', 'system-ui', 'sans-serif']
          },
          letterSpacing: {
            boutique: '0.18em'
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('/assets/css/custom.css')) ?>?v=12">
  <script>document.documentElement.classList.add('js');</script>
  <script type="application/ld+json"><?= json_encode($orgSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body class="bg-cream text-ink font-sans antialiased<?= $bodyClass !== '' ? ' ' . e($bodyClass) : '' ?>">
  <header class="site-header <?= $headerOverlay ? 'site-header--overlay' : 'site-header--solid' ?>">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-3.5 flex items-center justify-between gap-3">
      <a href="<?= e(url('/')) ?>" class="site-header__logo shrink-0">
        <img src="https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png" alt="Alina Bradu" class="h-9 md:h-10 w-auto" loading="lazy">
      </a>
      <div class="flex items-center gap-2 md:gap-5">
        <nav class="header-nav hidden md:flex items-center gap-7 uppercase tracking-boutique font-medium" aria-label="Meniu principal">
          <a href="<?= e(url('/')) ?>" class="header-nav__link">Acasă</a>
          <a href="<?= e(url('/magazin')) ?>" class="header-nav__link">Catalog</a>
          <a href="<?= e(url('/despre-noi')) ?>" class="header-nav__link">Despre noi</a>
          <a href="<?= e(url('/contact')) ?>" class="header-nav__link">Contact</a>
          <a href="<?= e(url('/cos')) ?>" class="header-nav__link">Coș <span class="text-gold">(<?= cartItemsCount() ?>)</span></a>
        </nav>
        <div class="header-social hidden sm:flex items-center gap-0.5 md:border-l md:pl-4 md:ml-1" role="group" aria-label="Rețele sociale">
          <a href="https://www.facebook.com/AlinaBraduEmbroidery" target="_blank" rel="noopener noreferrer" class="header-social__btn" aria-label="Facebook">
            <svg class="w-[1.15rem] h-[1.15rem]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </a>
          <a href="https://www.instagram.com/alinabradu.brand" target="_blank" rel="noopener noreferrer" class="header-social__btn" aria-label="Instagram">
            <svg class="w-[1.15rem] h-[1.15rem]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
          </a>
          <a href="https://api.whatsapp.com/send?phone=37368693056" data-whatsapp-popup="1" target="_blank" rel="noopener noreferrer" class="header-social__btn" aria-label="WhatsApp">
            <svg class="w-[1.15rem] h-[1.15rem]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          </a>
        </div>
        <button type="button" class="site-header__menu-btn md:hidden" data-mobile-menu-btn aria-expanded="false" aria-controls="site-mobile-nav" id="site-mobile-nav-btn">
          <span class="sr-only">Deschide meniul</span>
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>
        </button>
      </div>
    </div>
    <div id="site-mobile-nav" data-mobile-menu class="site-header__drawer hidden md:hidden">
      <nav class="site-header__drawer-nav max-w-7xl mx-auto px-5 py-5 flex flex-col gap-0.5 uppercase tracking-boutique font-medium" aria-label="Meniu mobil">
        <a href="<?= e(url('/')) ?>" class="site-header__drawer-link">Acasă</a>
        <a href="<?= e(url('/magazin')) ?>" class="site-header__drawer-link">Magazin</a>
        <a href="<?= e(url('/despre-noi')) ?>" class="site-header__drawer-link">Despre noi</a>
        <a href="<?= e(url('/contact')) ?>" class="site-header__drawer-link">Contact</a>
        <a href="<?= e(url('/cos')) ?>" class="site-header__drawer-link text-gold">Coș (<?= cartItemsCount() ?>)</a>
      </nav>
      <div class="max-w-7xl mx-auto px-5 pb-5 flex items-center gap-2 border-t border-white/10 pt-4" role="group" aria-label="Rețele sociale">
        <a href="https://www.facebook.com/AlinaBraduEmbroidery" target="_blank" rel="noopener noreferrer" class="header-social__btn header-social__btn--drawer" aria-label="Facebook"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
        <a href="https://www.instagram.com/alinabradu.brand" target="_blank" rel="noopener noreferrer" class="header-social__btn header-social__btn--drawer" aria-label="Instagram"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
        <a href="https://api.whatsapp.com/send?phone=37368693056" data-whatsapp-popup="1" target="_blank" rel="noopener noreferrer" class="header-social__btn header-social__btn--drawer" aria-label="WhatsApp"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
      </div>
    </div>
  </header>
  <main class="site-main <?= $headerOverlay ? '' : 'site-main--padded' ?>">
