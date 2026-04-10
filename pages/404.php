<?php
declare(strict_types=1);
$seo = ['title' => '404 - Pagina nu exista'];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-3xl mx-auto px-4 py-24 text-center">
  <h1 class="font-serif text-6xl mb-4">404</h1>
  <p class="text-zinc-600 mb-6">Pagina cautata nu exista sau a fost mutata.</p>
  <a href="/" class="bg-zinc-900 text-white px-6 py-3 rounded">Inapoi la Home</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
