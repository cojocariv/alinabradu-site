  </div>
  <p class="mt-10 pt-6 border-t border-gold/25 text-sm text-ink-muted">
    Ultima actualizare: <?= e(date('d.m.Y')) ?> · Conform <?= e(PRIVACY_LAW_SHORT) ?> (în vigoare din <?= e(PRIVACY_LAW_EFFECTIVE) ?>) ·
    <a href="<?= e(url('/politica-confidentialitate')) ?>" class="text-gold hover:underline">Politica de confidențialitate</a> ·
    <a href="<?= e(url('/politica-cookies')) ?>" class="text-gold hover:underline">Politica cookies</a> ·
    <a href="<?= e(url('/cerere-date-personale')) ?>" class="text-gold hover:underline">Cerere date personale</a>
  </p>
</section>
<?php require __DIR__ . '/footer.php'; ?>
