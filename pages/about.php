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
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="font-serif text-4xl mb-6">Despre noi</h1>
  <div class="grid lg:grid-cols-2 gap-10 items-start">
    <p class="leading-8 text-zinc-700">
      Alina Bradu este un brand boutique care reinventează portul tradițional moldovenesc într-un limbaj modern, feminin și rafinat.
      Fiecare piesă este creată cu atenție la detalii, broderii autentice și croieli contemporane.
    </p>

    <div class="about-carousel scene">
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
<?php require __DIR__ . '/../includes/footer.php'; ?>
