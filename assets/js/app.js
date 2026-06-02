document.addEventListener('DOMContentLoaded', () => {
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

  document.querySelectorAll('a[data-whatsapp-popup="1"]').forEach((link) => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href') || '';
      if (!href) return;
      e.preventDefault();

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
        'whatsappPopup',
        `width=${popupWidth},height=${popupHeight},left=${left},top=${top},resizable=yes,scrollbars=yes`
      );

      if (!popup) {
        window.open(href, '_blank', 'noopener,noreferrer');
        return;
      }
      popup.focus();
    });
  });

  initHomeHeroVideo();
});

function initHomeHeroVideo() {
  const wrap = document.querySelector('[data-home-hero-video]');
  if (!wrap || wrap.dataset.heroVideoInit === '1') return;

  const videoId = wrap.getAttribute('data-video-id') || '';
  const mountId = 'homeHeroVideoPlayer';
  if (!videoId || !document.getElementById(mountId)) return;

  wrap.dataset.heroVideoInit = '1';

  const markPlaying = () => {
    wrap.classList.add('is-playing');
    wrap.classList.remove('is-loading');
  };

  const createPlayer = () => {
    if (typeof YT === 'undefined' || !YT.Player) return;

    new YT.Player(mountId, {
      videoId,
      host: 'https://www.youtube-nocookie.com',
      width: '100%',
      height: '100%',
      playerVars: {
        autoplay: 1,
        mute: 1,
        controls: 0,
        disablekb: 1,
        fs: 0,
        playsinline: 1,
        rel: 0,
        modestbranding: 1,
        iv_load_policy: 3,
        cc_load_policy: 0,
        enablejsapi: 1,
        origin: window.location.origin,
      },
      events: {
        onReady(event) {
          event.target.mute();
          event.target.playVideo();
        },
        onStateChange(event) {
          if (event.data === YT.PlayerState.PLAYING) {
            markPlaying();
          } else if (event.data === YT.PlayerState.ENDED) {
            event.target.seekTo(0, true);
            event.target.playVideo();
          } else if (event.data === YT.PlayerState.PAUSED) {
            event.target.playVideo();
          }
        },
      },
    });
  };

  const bootApi = () => {
    if (window.YT && window.YT.Player) {
      createPlayer();
      return;
    }

    const prevReady = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = () => {
      if (typeof prevReady === 'function') prevReady();
      createPlayer();
    };

    if (!document.querySelector('script[src*="youtube.com/iframe_api"]')) {
      const tag = document.createElement('script');
      tag.src = 'https://www.youtube.com/iframe_api';
      document.head.appendChild(tag);
    }
  };

  bootApi();

  window.setTimeout(() => {
    if (!wrap.classList.contains('is-playing')) {
      markPlaying();
    }
  }, 5000);
}
