<?php
declare(strict_types=1);

/**
 * Butoane comandă WhatsApp + Viber.
 *
 * @var string $whatsAppUrl
 * @var string $viberUrl
 * @var string $whatsAppLabel
 * @var string $viberLabel
 * @var bool $compact Icon-only (card magazin)
 */
$whatsAppLabel = $whatsAppLabel ?? 'Comandă pe WhatsApp';
$viberLabel = $viberLabel ?? 'Comandă pe Viber';
$compact = $compact ?? false;
?>
<?php if ($compact): ?>
  <a
    href="<?= e($whatsAppUrl) ?>" data-whatsapp-popup="1"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="<?= e($whatsAppLabel) ?>"
    title="<?= e($whatsAppLabel) ?>"
    class="product-chat-btn product-chat-btn--wa product-chat-btn--icon"
  ><span aria-hidden="true">W</span></a>
  <a
    href="<?= e($viberUrl) ?>"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="<?= e($viberLabel) ?>"
    title="<?= e($viberLabel) ?>"
    class="product-chat-btn product-chat-btn--viber product-chat-btn--icon"
  ><span aria-hidden="true">V</span></a>
<?php else: ?>
  <a
    href="<?= e($whatsAppUrl) ?>" data-whatsapp-popup="1"
    target="_blank"
    rel="noopener noreferrer"
    class="product-chat-btn product-chat-btn--wa"
  >
    <svg class="product-chat-btn__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    <span><?= e($whatsAppLabel) ?></span>
  </a>
  <a
    href="<?= e($viberUrl) ?>"
    target="_blank"
    rel="noopener noreferrer"
    class="product-chat-btn product-chat-btn--viber"
  >
    <svg class="product-chat-btn__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95l-1.37 4.98 5.12-1.34c1.45.79 3.08 1.21 4.84 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2m4.84 12.92c-.25.71-1.47 1.33-2 1.42-.51.08-1.17.12-1.88-.12-.71-.25-3.05-1.19-3.58-1.32-.53-.12-1.17-.25-1.65-.88-.48-.63-.48-1.47-.33-1.67.15-.2.55-.33 1.15-.63.6-.3 1-.5 1.12-.8.12-.3.08-.55-.05-.8-.12-.25-.88-2.12-1.2-2.9-.31-.75-.63-.65-.88-.66-.23-.01-.5-.01-.77-.01-.27 0-.7.1-1.07.5-.37.4-1.4 1.37-1.4 3.34s1.43 3.88 1.63 4.15z"/></svg>
    <span><?= e($viberLabel) ?></span>
  </a>
<?php endif; ?>
