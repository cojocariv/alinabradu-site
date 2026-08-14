<?php
declare(strict_types=1);
?>
<div id="cookie-consent" class="cookie-consent" role="dialog" aria-labelledby="cookie-consent-title" aria-describedby="cookie-consent-desc" aria-hidden="true" hidden>
  <div class="cookie-consent__panel">
    <h2 id="cookie-consent-title" class="cookie-consent__title">Cookie-uri și confidențialitate</h2>
    <p id="cookie-consent-desc" class="cookie-consent__text">
      Folosim cookie-uri strict necesare pentru coș și preferințe. Cu acordul tău, activăm și servicii funcționale (fonturi, hartă, statistici click chat).
      Citește <a href="<?= e(url('/politica-cookies')) ?>">Politica cookies</a> și
      <a href="<?= e(url('/politica-confidentialitate')) ?>">Politica de confidențialitate</a>.
    </p>
    <div class="cookie-consent__actions">
      <button type="button" class="cookie-consent__btn cookie-consent__btn--primary" data-cookie-accept="all">Accept toate</button>
      <button type="button" class="cookie-consent__btn cookie-consent__btn--ghost" data-cookie-accept="essential">Doar necesare</button>
    </div>
  </div>
</div>
