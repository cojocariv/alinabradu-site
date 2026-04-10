<?php
declare(strict_types=1);
$seo = ['title' => '404 - Pagina nu există'];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-3xl mx-auto px-4 py-24 text-center">
  <h1 class="font-serif text-6xl mb-4">404</h1>
  <p class="text-zinc-600 mb-6">Pagina căutată nu există sau a fost mutată.</p>
  <a href="<?= e(url('/')) ?>" class="bg-zinc-900 text-white px-6 py-3 rounded">Înapoi la Acasă</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
