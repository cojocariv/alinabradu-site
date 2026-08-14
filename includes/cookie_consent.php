<?php
declare(strict_types=1);
?>
<div id="cookie-consent" class="cookie-consent" role="dialog" aria-labelledby="cookie-consent-title" aria-describedby="cookie-consent-desc" aria-hidden="true" hidden>
  <div class="cookie-consent__panel">
    <h2 id="cookie-consent-title" class="cookie-consent__title">Confidențialitate și cookie-uri</h2>
    <p id="cookie-consent-desc" class="cookie-consent__text">
      Folosim cookie-uri <strong>strict necesare</strong> pentru coș și memorarea preferinței tale.
      Cu acordul tău explicit (conform <?= e(PRIVACY_LAW_SHORT) ?>), activăm servicii funcționale:
      fonturi Google, hartă contact, înregistrare click WhatsApp/Viber.
      Poți accepta toate, refuza cele opționale sau citi
      <a href="<?= e(url('/politica-cookies')) ?>">Politica cookies</a> și
      <a href="<?= e(url('/politica-confidentialitate')) ?>">Politica de confidențialitate</a>.
    </p>
    <div class="cookie-consent__actions">
      <button type="button" class="cookie-consent__btn cookie-consent__btn--primary" data-cookie-accept="all">Accept toate</button>
      <button type="button" class="cookie-consent__btn cookie-consent__btn--ghost" data-cookie-accept="essential">Doar necesare</button>
    </div>
  </div>
</div>
