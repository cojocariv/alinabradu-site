<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/ProductModel.php';
$headerOverlay = true;
$products = ProductModel::featured(8);
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
<section class="home-story relative py-16 md:py-24 overflow-hidden bg-cream/80" aria-label="Despre colecții și brand">
  <div class="home-story__leaf-wrap pointer-events-none select-none" aria-hidden="true">
    <img src="<?= e($storyLeaf) ?>" alt="" class="home-story__leaf" width="520" height="520" loading="lazy" decoding="async">
  </div>
  <div class="relative z-10 max-w-3xl mx-auto px-4 space-y-14 md:space-y-20">
    <p class="home-story__observe font-serif text-lg md:text-xl text-zinc-800 leading-relaxed text-center">
      Colecțiile noastre prezintă o gamă largă de ținute etno, de la bluze și fuste la cămăși, rochii și paltoane, toate create cu dragoste și atenție la detalii. Fiecare piesă de la Alina Bradu nu este doar o piesă de îmbrăcăminte, ci o întreagă poveste, care transformă fiecare moment într-o experiență de neuitat.
    </p>
    <p class="home-story__observe font-serif text-lg md:text-xl text-zinc-800 leading-relaxed text-center">
      Fiecare din colecțiile noastre este un amalgam dintre trecut și prezent, întruchipat în modele unice care reflectă rădăcinile și tradițiile noastre. Ne străduim să combinăm eleganța cu confortul, creând articole inspirate pentru orice sezon.
    </p>
    <p class="home-story__observe font-serif text-lg md:text-xl text-zinc-800 leading-relaxed text-center">
      <span class="font-medium text-zinc-900">„Alina Bradu”</span> — acolo unde tradiția se împletește cu contemporanul, iar arta devine modă! Suntem un brand național cu dragoste aparte pentru frumusețea și autenticitatea broderiei tradiționale, pe care o îmbinăm cu creativitatea în designul actual.
    </p>
    <blockquote class="home-story__observe home-story__quote font-serif text-xl md:text-2xl text-zinc-900 leading-snug text-center not-italic border-0 m-0 p-0">
      Nu creăm doar haine, ci și povești care ne inspiră pe noi<br class="hidden sm:inline">
      <span class="block sm:inline sm:mt-0 mt-2">cât și pe clienții noștri.</span>
    </blockquote>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-12">
  <h2 class="font-serif text-3xl mb-6">Produse noi</h2>
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($products as $product): ?>
      <article class="bg-white rounded-lg overflow-hidden shadow-sm card-hover">
        <a href="<?= e(url('/produs/' . $product['slug'])) ?>">
          <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="w-full h-72 object-cover" loading="lazy">
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
