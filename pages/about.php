<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/AboutGalleryModel.php';

$gallery = AboutGalleryModel::allActive();
if (count($gallery) < 2) {
    $gallery = [
        'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png',
        'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-11-1-400x500.png',
        'https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png',
    ];
}

$seo = [
    'title' => 'Despre noi - Alina Bradu',
    'description' => 'Povestea atelierului Alina Bradu: tradiție, feminitate și eleganță contemporană.',
];
$aboutPageBgUrl = 'https://alinabradupozestorage.blob.core.windows.net/poze/2024/2024/04/AI1I6383-1.jpg?sp=racwdl&st=2026-04-28T08:39:31Z&se=2030-04-28T16:54:31Z&sv=2025-11-05&sr=c&sig=d0AXmmbogXdEMlu2%2B0l9vYNYkUp2XoepL%2F3jBcl%2FYYk%3D';
require __DIR__ . '/../includes/header.php';
?>
<div
  class="about-page-bg min-h-[calc(100dvh-5rem)] bg-cover bg-center bg-no-repeat pb-16"
  style="background-image: linear-gradient(to bottom, rgba(255,248,238,0.94), rgba(255,248,238,0.88)), url('<?= e($aboutPageBgUrl) ?>');"
>
<section class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="font-serif text-4xl mb-6 drop-shadow-sm">Despre noi</h1>
  <div class="grid lg:grid-cols-2 gap-10 items-start">
    <p class="leading-8 text-zinc-700 drop-shadow-sm">
      Alina Bradu este un brand boutique care reinventează portul tradițional moldovenesc într-un limbaj modern, feminin și rafinat.
      Fiecare piesă este creată cu atenție la detalii, broderii autentice și croieli contemporane.
    </p>

    <div class="about-carousel scene mt-12 lg:mt-24">
      <div class="a3d" style="--n: <?= (int) count($gallery) ?>;">
        <?php foreach ($gallery as $i => $imgUrl): ?>
          <img
            class="card"
            src="<?= e($imgUrl) ?>"
            style="--i: <?= (int) $i ?>;"
            alt="Imagine atelier Alina Bradu"
            loading="lazy"
          >
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
