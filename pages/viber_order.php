<?php
declare(strict_types=1);

if (!defined('SITE_PHONE_TEL')) {
    require_once __DIR__ . '/../config/contact.php';
}

$text = trim((string) ($_GET['text'] ?? ''));
if ($text === '') {
    http_response_code(400);
    echo 'Mesaj lipsă.';
    exit;
}

if (mb_strlen($text) > 2000) {
    $text = mb_substr($text, 0, 2000);
}

$deepLink = viberChatDeepLink($text);
$phoneLabel = defined('SITE_PHONE_DISPLAY') ? (string) SITE_PHONE_DISPLAY : (string) SITE_PHONE_TEL;
?>
<!doctype html>
<html lang="ro">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Comandă pe Viber — Alina Bradu</title>
  <link rel="icon" type="image/png" href="https://alinabradupozestorage.blob.core.windows.net/poze/favicon.png">
  <style>
    :root {
      --ink: #1c1917;
      --muted: #57534e;
      --gold: #c9a96e;
      --viber: #7360f2;
      --cream: #fffaf2;
      --border: #eadfc9;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
      background: var(--cream);
      color: var(--ink);
      line-height: 1.5;
      min-height: 100vh;
      padding: 1.25rem;
    }
    .viber-order {
      max-width: 28rem;
      margin: 0 auto;
    }
    .viber-order__title {
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.35rem;
      font-weight: 600;
      margin: 0 0 0.35rem;
    }
    .viber-order__sub {
      margin: 0 0 1rem;
      font-size: 0.9rem;
      color: var(--muted);
    }
    .viber-order__label {
      display: block;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--muted);
      margin-bottom: 0.4rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .viber-order__message {
      width: 100%;
      min-height: 9rem;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: 0.5rem;
      font: inherit;
      font-size: 0.9rem;
      resize: vertical;
      background: #fff;
      color: var(--ink);
    }
    .viber-order__actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.6rem;
      margin-top: 1rem;
    }
    .viber-order__btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.65rem 1.1rem;
      border-radius: 0.35rem;
      font-size: 0.9rem;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: 1px solid transparent;
      font-family: inherit;
    }
    .viber-order__btn--primary {
      background: var(--viber);
      color: #fff;
      border-color: var(--viber);
    }
    .viber-order__btn--primary:hover { filter: brightness(1.05); }
    .viber-order__btn--ghost {
      background: #fff;
      color: var(--ink);
      border-color: var(--border);
    }
    .viber-order__btn--ghost:hover { background: #fff6ea; }
    .viber-order__status {
      margin: 0.85rem 0 0;
      font-size: 0.85rem;
      color: var(--muted);
      min-height: 1.25rem;
    }
    .viber-order__status.is-ok { color: #166534; }
    .viber-order__hint {
      margin: 1.25rem 0 0;
      padding-top: 1rem;
      border-top: 1px solid var(--border);
      font-size: 0.8rem;
      color: var(--muted);
    }
    .viber-order__hint strong { color: var(--gold); }
  </style>
</head>
<body>
  <div class="viber-order">
    <h1 class="viber-order__title">Comandă pe Viber</h1>
    <p class="viber-order__sub">Trimite mesajul de mai jos către <strong><?= e($phoneLabel) ?></strong></p>

    <label class="viber-order__label" for="viber-order-message">Mesaj pregătit</label>
    <textarea id="viber-order-message" class="viber-order__message" readonly><?= e($text) ?></textarea>

    <div class="viber-order__actions">
      <a href="<?= e($deepLink) ?>" id="viber-order-open" class="viber-order__btn viber-order__btn--primary">Deschide Viber</a>
      <button type="button" id="viber-order-copy" class="viber-order__btn viber-order__btn--ghost">Copiază mesaj</button>
    </div>
    <p id="viber-order-status" class="viber-order__status" role="status" aria-live="polite"></p>

    <p class="viber-order__hint">
      Dacă Viber nu pornește automat, apasă <strong>Deschide Viber</strong> sau lipește mesajul în conversația cu Alina Bradu.
      Pe telefon, mesajul poate apărea deja în câmpul de scris.
    </p>
  </div>
  <script>
    (function () {
      var messageEl = document.getElementById('viber-order-message');
      var statusEl = document.getElementById('viber-order-status');
      var copyBtn = document.getElementById('viber-order-copy');
      var openBtn = document.getElementById('viber-order-open');
      var deepLink = <?= json_encode($deepLink, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
      var text = messageEl ? messageEl.value : '';

      function setStatus(msg, ok) {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.classList.toggle('is-ok', !!ok);
      }

      function copyMessage() {
        if (!text) return Promise.reject();
        if (navigator.clipboard && navigator.clipboard.writeText) {
          return navigator.clipboard.writeText(text);
        }
        messageEl.select();
        messageEl.setSelectionRange(0, text.length);
        return document.execCommand('copy') ? Promise.resolve() : Promise.reject();
      }

      if (copyBtn) {
        copyBtn.addEventListener('click', function () {
          copyMessage()
            .then(function () { setStatus('Mesaj copiat în clipboard.', true); })
            .catch(function () { setStatus('Selectează mesajul și copiază manual (Ctrl+C).', false); });
        });
      }

      if (openBtn) {
        openBtn.addEventListener('click', function () {
          copyMessage().catch(function () {});
        });
      }

      copyMessage()
        .then(function () { setStatus('Mesaj copiat — deschidem Viber…', true); })
        .catch(function () { setStatus('Se deschide Viber…', false); });

      setTimeout(function () {
        try {
          window.location.href = deepLink;
        } catch (e) {}
      }, 500);
    })();
  </script>
</body>
</html>
