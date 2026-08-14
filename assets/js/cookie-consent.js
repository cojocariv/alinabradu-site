(function () {
  'use strict';

  const STORAGE_KEY = 'ab_cookie_consent';
  const COOKIE_NAME = 'ab_cookie_consent';
  const MAX_AGE = 365 * 24 * 60 * 60;
  const FONTS_HREF =
    'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Jost:wght@300;400;500;600&display=swap';

  function readCookie() {
    const match = document.cookie.match(new RegExp('(?:^|; )' + COOKIE_NAME + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
  }

  function getConsent() {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored === 'all' || stored === 'essential') {
        return stored;
      }
    } catch (_) {
      /* ignore */
    }
    const fromCookie = readCookie();
    if (fromCookie === 'all' || fromCookie === 'essential') {
      return fromCookie;
    }
    return null;
  }

  function setConsent(level) {
    try {
      localStorage.setItem(STORAGE_KEY, level);
    } catch (_) {
      /* ignore */
    }
    document.cookie =
      COOKIE_NAME +
      '=' +
      encodeURIComponent(level) +
      '; path=/; max-age=' +
      MAX_AGE +
      '; SameSite=Lax';
    document.documentElement.dataset.cookieConsent = level;
    applyConsent(level);
    hideBanner();
    window.dispatchEvent(new CustomEvent('cookieconsent', { detail: { level: level } }));
  }

  function loadGoogleFonts() {
    if (document.getElementById('site-google-fonts')) {
      return;
    }
    const pre1 = document.createElement('link');
    pre1.rel = 'preconnect';
    pre1.href = 'https://fonts.googleapis.com';
    document.head.appendChild(pre1);
    const pre2 = document.createElement('link');
    pre2.rel = 'preconnect';
    pre2.href = 'https://fonts.gstatic.com';
    pre2.crossOrigin = 'anonymous';
    document.head.appendChild(pre2);
    const link = document.createElement('link');
    link.id = 'site-google-fonts';
    link.rel = 'stylesheet';
    link.href = FONTS_HREF;
    document.head.appendChild(link);
  }

  function applyConsent(level) {
    if (level === 'all') {
      loadGoogleFonts();
    }
  }

  window.hasFunctionalConsent = function () {
    return getConsent() === 'all';
  };

  function showBanner() {
    const el = document.getElementById('cookie-consent');
    if (!el) {
      return;
    }
    el.hidden = false;
    el.removeAttribute('aria-hidden');
    el.classList.add('cookie-consent--visible');
  }

  function hideBanner() {
    const el = document.getElementById('cookie-consent');
    if (!el) {
      return;
    }
    el.classList.remove('cookie-consent--visible');
    el.hidden = true;
    el.setAttribute('aria-hidden', 'true');
  }

  function bindControls() {
    document.querySelectorAll('[data-cookie-accept]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const value = btn.getAttribute('data-cookie-accept') === 'all' ? 'all' : 'essential';
        setConsent(value);
      });
    });

    document.addEventListener('click', function (e) {
      const settingsBtn = e.target.closest('[data-cookie-settings]');
      if (!settingsBtn) {
        return;
      }
      e.preventDefault();
      showBanner();
    });
  }

  function init() {
    bindControls();
    const existing = getConsent();
    if (existing) {
      document.documentElement.dataset.cookieConsent = existing;
      applyConsent(existing);
      return;
    }
    showBanner();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
