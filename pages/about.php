<?php
declare(strict_types=1);

$headerOverlay = true;
$seo = [
    'title' => 'Despre noi - Alina Bradu',
    'description' => 'Povestea fabricii de broderie Alina Bradu: tradiție, feminitate și eleganță contemporană.',
];
$aboutPageVideoUrl = 'https://alinabradupozestorage.blob.core.windows.net/poze/bunici-web.mp4';
$headPreloadVideo = $aboutPageVideoUrl;
require __DIR__ . '/../includes/header.php';
?>
<div class="about-page">
  <div class="about-page__bg" aria-hidden="true">
    <video
      class="about-page__video"
      src="<?= e($aboutPageVideoUrl) ?>"
      autoplay
      muted
      loop
      playsinline
      disablepictureinpicture
      disableremoteplayback
      preload="auto"
      aria-hidden="true"
    ></video>
    <div class="about-page__scrim"></div>
  </div>
  <section class="about-page__content" aria-labelledby="about-page-title">
    <div class="about-page__inner">
      <p class="about-page__eyebrow">Cea mai mare fabrică de broderie din Moldova</p>
      <h1 id="about-page-title" class="about-page__title">Despre <em>noi</em></h1>
      <p class="about-page__lead">
        Alina Bradu este un brand boutique care reinventează portul tradițional moldovenesc într-un limbaj modern, feminin și rafinat.
        Fiecare piesă este creată cu atenție la detalii, broderii autentice și croieli contemporane.
      </p>
    </div>
  </section>
</div>
<script>
(function () {
  var v = document.querySelector('.about-page__video');
  if (!v) return;
  v.muted = true;
  var play = function () {
    var p = v.play();
    if (p && p.catch) p.catch(function () {});
  };
  v.addEventListener('loadedmetadata', play, { once: true });
  play();
})();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
