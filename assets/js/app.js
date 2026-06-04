document.addEventListener('DOMContentLoaded', () => {
  const overlayHeader = document.querySelector('.site-header--overlay');
  if (overlayHeader && document.body.classList.contains('page-home')) {
    const scrollThreshold = 40;
    let headerScrollTicking = false;

    const updateHeaderScrollBlur = () => {
      overlayHeader.classList.toggle('site-header--scrolled', window.scrollY > scrollThreshold);
      headerScrollTicking = false;
    };

    window.addEventListener(
      'scroll',
      () => {
        if (headerScrollTicking) return;
        headerScrollTicking = true;
        requestAnimationFrame(updateHeaderScrollBlur);
      },
      { passive: true }
    );
    updateHeaderScrollBlur();
  }

  const mobileMenuButton = document.querySelector('[data-mobile-menu-btn]');
  const mobileMenu = document.querySelector('[data-mobile-menu]');
  if (mobileMenuButton && mobileMenu) {
    const isOpen = () => !mobileMenu.classList.contains('hidden');

    const setOpen = (open) => {
      mobileMenu.classList.toggle('hidden', !open);
      mobileMenuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.style.overflow = open ? 'hidden' : '';
    };

    mobileMenuButton.addEventListener('click', () => {
      setOpen(!isOpen());
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isOpen()) {
        setOpen(false);
      }
    });

    mobileMenu.addEventListener('click', (e) => {
      const a = e.target.closest('a');
      if (!a || !mobileMenu.contains(a)) return;
      const href = a.getAttribute('href') || '';
      if (href.startsWith('javascript:')) return;
      setOpen(false);
    });
  }

  const scrollTopBtn = document.getElementById('scrollTopBtn');
  if (scrollTopBtn) {
    const showAfter = 320;

    const updateScrollTop = () => {
      const tallPage = document.documentElement.scrollHeight > window.innerHeight + 400;
      const scrolled = window.scrollY > showAfter;
      if (tallPage && scrolled) {
        scrollTopBtn.removeAttribute('hidden');
        scrollTopBtn.classList.add('is-visible');
      } else {
        scrollTopBtn.setAttribute('hidden', '');
        scrollTopBtn.classList.remove('is-visible');
      }
    };

    let scrollTicking = false;
    window.addEventListener(
      'scroll',
      () => {
        if (scrollTicking) return;
        scrollTicking = true;
        requestAnimationFrame(() => {
          updateScrollTop();
          scrollTicking = false;
        });
      },
      { passive: true }
    );
    window.addEventListener('resize', updateScrollTop);
    updateScrollTop();

    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  const chatTrackUrl = document.body.dataset.chatTrackUrl || '';

  const messagePreviewFromHref = (href) => {
    try {
      const u = new URL(href, window.location.origin);
      const text = u.searchParams.get('text') || u.searchParams.get('q');
      if (text) {
        return text.length > 500 ? text.slice(0, 500) : text;
      }
    } catch (_) {
      /* ignore */
    }
    return '';
  };

  const trackChatContact = (link, channel) => {
    if (!chatTrackUrl || !link) return;

    const payload = {
      channel,
      source: link.dataset.chatSource || 'unknown',
      page_path: window.location.pathname + window.location.search,
      product_id: link.dataset.productId || '',
      product_slug: link.dataset.productSlug || '',
      product_name: link.dataset.productName || '',
      message_preview: messagePreviewFromHref(link.getAttribute('href') || ''),
    };

    const body = JSON.stringify(payload);
    if (navigator.sendBeacon) {
      navigator.sendBeacon(chatTrackUrl, new Blob([body], { type: 'application/json' }));
      return;
    }
    fetch(chatTrackUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body,
      keepalive: true,
    }).catch(() => {});
  };

  const openChatPopup = (href, windowName) => {
    const popupWidth = 520;
    const popupHeight = 760;
    const dualLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
    const dualTop = window.screenTop !== undefined ? window.screenTop : window.screenY;
    const winWidth = window.innerWidth || document.documentElement.clientWidth || screen.width;
    const winHeight = window.innerHeight || document.documentElement.clientHeight || screen.height;
    const left = Math.max(0, Math.floor(dualLeft + (winWidth - popupWidth) / 2));
    const top = Math.max(0, Math.floor(dualTop + (winHeight - popupHeight) / 2));

    const popup = window.open(
      href,
      windowName,
      `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
    );

    if (!popup) {
      window.open(href, '_blank', 'noopener,noreferrer');
      return;
    }
    popup.focus();
  };

  document.querySelectorAll('a[data-whatsapp-popup="1"]').forEach((link) => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href') || '';
      if (!href) return;
      e.preventDefault();
      trackChatContact(link, 'whatsapp');
      openChatPopup(href, 'whatsappPopup');
    });
  });

  document.querySelectorAll('a[data-viber-popup="1"]').forEach((link) => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href') || '';
      if (!href) return;
      e.preventDefault();
      trackChatContact(link, 'viber');
      openChatPopup(href, 'viberPopup');
    });
  });

});
