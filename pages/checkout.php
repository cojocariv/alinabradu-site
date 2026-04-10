<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/OrderModel.php';

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    header('Location: /cos');
    exit;
}

$errors = [];
$success = null;
$total = 0.0;
foreach ($cart as $item) {
    $total += ((float) $item['price'] * (int) $item['qty']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));

    if ($name === '' || mb_strlen($name) < 3) {
        $errors[] = 'Numele este obligatoriu (minim 3 caractere).';
    }
    if (!preg_match('/^[0-9+\s-]{9,20}$/', $phone)) {
        $errors[] = 'Numar de telefon invalid.';
    }
    if ($address === '' || mb_strlen($address) < 10) {
        $errors[] = 'Adresa trebuie sa aiba minim 10 caractere.';
    }

    if (!$errors) {
        $orderId = OrderModel::create(
            ['name' => $name, 'phone' => $phone, 'address' => $address],
            $cart,
            $total
        );
        unset($_SESSION['cart']);
        $success = "Comanda #{$orderId} a fost inregistrata cu succes.";
    }
}

$seo = ['title' => 'Checkout - Alina Bradu'];
require __DIR__ . '/../includes/header.php';
?>
<section class="max-w-3xl mx-auto px-4 py-10">
  <h1 class="font-serif text-4xl mb-6">Checkout</h1>
  <?php if ($success): ?>
    <div class="bg-green-100 text-green-800 p-4 rounded"><?= e($success) ?></div>
  <?php else: ?>
    <?php if ($errors): ?>
      <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
        <ul class="list-disc pl-5">
          <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" class="space-y-4 bg-white p-6 rounded-lg">
      <input type="text" name="name" placeholder="Nume complet" required class="w-full border rounded p-3">
      <input type="text" name="phone" placeholder="Telefon" required class="w-full border rounded p-3">
      <textarea name="address" placeholder="Adresa livrare" required class="w-full border rounded p-3 min-h-28"></textarea>
      <p class="font-semibold">Total plata: <?= e(formatPrice($total)) ?></p>
      <button class="bg-zinc-900 text-white px-6 py-3 rounded">Trimite comanda</button>
    </form>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
