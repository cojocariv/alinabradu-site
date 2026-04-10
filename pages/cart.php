<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$_POST['remove']]);
    } elseif (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $key => $qty) {
            $qty = max(1, (int) $qty);
            if (isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key]['qty'] = $qty;
            }
        }
    }
    redirectTo('/cos');
}

$cart = $_SESSION['cart'] ?? [];
$total = 0.0;
foreach ($cart as $item) {
    $total += ((float) $item['price'] * (int) $item['qty']);
}

$seo = ['title' => 'Cos - Alina Bradu'];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-5xl mx-auto px-4 py-10">
  <h1 class="font-serif text-4xl mb-6">Cosul tau</h1>
  <?php if (!$cart): ?>
    <p>Cosul este gol. <a href="<?= e(url('/magazin')) ?>" class="underline">Continua cumparaturile</a></p>
  <?php else: ?>
    <form method="post">
      <div class="space-y-4">
        <?php foreach ($cart as $key => $item): ?>
          <div class="bg-white rounded p-4 flex gap-4 items-center">
            <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>" class="w-24 h-24 object-cover rounded" loading="lazy">
            <div class="flex-1">
              <p class="font-serif text-lg"><?= e($item['name']) ?></p>
              <p class="text-sm text-zinc-500">Marime: <?= e($item['selected_size']) ?></p>
              <p class="text-gold font-semibold"><?= e(formatPrice((float) $item['price'])) ?></p>
            </div>
            <input type="number" min="1" name="qty[<?= e($key) ?>]" value="<?= (int) $item['qty'] ?>" class="w-20 border rounded p-1">
            <button name="remove" value="<?= e($key) ?>" class="text-red-600">Sterge</button>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mt-6 flex justify-between items-center">
        <p class="text-xl font-semibold">Total: <?= e(formatPrice($total)) ?></p>
        <div class="flex gap-3">
          <button class="px-4 py-2 border rounded">Actualizeaza</button>
          <a href="<?= e(url('/checkout')) ?>" class="px-6 py-2 bg-zinc-900 text-white rounded">Checkout</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
