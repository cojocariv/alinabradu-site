<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
$headerOverlay = true;
$products = ProductModel::featuredHome(12);
if (count($products) === 0) {
    $products = ProductModel::featured(8);
}
$seo = [
    'title' => 'Alina Bradu - Creație cu accent',
    'description' => 'Descoperă colecții autentice de inspirație moldovenească: rochii, bluze, fuste și home decor premium.',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="relative overflow-hidden">
  <img src="https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png" alt="Alina Bradu Collection" class="w-full min-h-[65vh] h-[65vh] md:min-h-[72vh] md:h-[72vh] object-cover object-top" fetchpriority="high">
  <div class="absolute inset-0 bg-black/35"></div>
  <div class="absolute inset-0 max-w-7xl mx-auto px-4 flex items-end justify-start pb-12 md:pb-16 lg:pb-24 pt-24">
    <div class="text-white max-w-xl">
      <h1 class="font-serif text-4xl md:text-6xl leading-tight mb-4">Eleganță tradițională reinterpretată modern</h1>
      <a href="<?= e(url('/magazin')) ?>" class="inline-block bg-gold text-white px-6 py-3 rounded hover:opacity-90">Descoperă colecțiile</a>
    </div>
  </div>
</section>

<?php
$storyLeaf = 'https://alinabradupozestorage.blob.core.windows.net/poze/leaf-shape-2-qjd2e8q1mruaag9ndk6bv7cqz5r9x69167979v98qg.png';
?>
<section
  class="home-story relative flex flex-col items-center justify-center py-3 md:py-5 overflow-x-hidden bg-cream/80 select-none"
  aria-label="Despre colecții și brand"
  data-story-interval="5000"
>
  <div class="home-story__box home-story__box--fixed relative w-full max-w-3xl mx-auto px-5 md:px-8 flex flex-col min-h-0 py-1">
    <div class="home-story__leaf-center pointer-events-none select-none" aria-hidden="true">
      <img src="<?= e($storyLeaf) ?>" alt="" class="home-story__leaf" width="520" height="520" loading="lazy" decoding="async">
    </div>
    <div class="home-story__slides relative z-10 w-full flex-1 min-h-0 text-center" aria-live="off">
      <div class="home-story__slide text-zinc-800 leading-snug is-active" data-slide="0" aria-hidden="false">
        Colecțiile noastre prezintă o gamă largă de ținute etno, de la bluze și fuste la cămăși, rochii și paltoane, toate create cu dragoste și atenție la detalii. Fiecare piesă de la Alina Bradu nu este doar o piesă de îmbrăcăminte, ci o întreagă poveste, care transformă fiecare moment într-o experiență de neuitat.
      </div>
      <div class="home-story__slide text-zinc-800 leading-snug" data-slide="1" aria-hidden="true">
        Fiecare din colecțiile noastre este un amalgam dintre trecut și prezent, întruchipat în modele unice care reflectă rădăcinile și tradițiile noastre. Ne străduim să combinăm eleganța cu confortul, creând articole inspirate pentru orice sezon.
      </div>
      <div class="home-story__slide text-zinc-800 leading-snug" data-slide="2" aria-hidden="true">
        <span class="font-medium text-zinc-900">„Alina Bradu”</span> — acolo unde tradiția se împletește cu contemporanul, iar arta devine modă! Suntem un brand național cu dragoste aparte pentru frumusețea și autenticitatea broderiei tradiționale, pe care o îmbinăm cu creativitatea în designul actual.
      </div>
      <div class="home-story__slide home-story__slide--quote text-zinc-900 leading-snug" data-slide="3" aria-hidden="true">
        Nu creăm doar haine, ci și povești care ne inspiră pe noi<br class="hidden sm:inline">
        <span class="block sm:inline sm:mt-0 mt-2">cât și pe clienții noștri.</span>
      </div>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-12">
  <h2 class="font-serif text-3xl mb-6">Produse noi</h2>
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($products as $product): ?>
      <article class="bg-white rounded-lg overflow-hidden shadow-sm card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>">
          <img src="<?= e(ProductModel::getPrimaryImageUrl($product)) ?>" alt="<?= e($product['name']) ?>" class="w-full h-72 object-cover" loading="lazy">
        </a>
        <div class="p-4">
          <h3 class="font-serif text-lg"><?= e($product['name']) ?></h3>
          <p class="text-sm text-zinc-500"><?= e($product['category']) ?></p>
          <p class="mt-2 font-semibold text-gold"><?= e(formatPrice((float) $product['price'])) ?></p>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
