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
});
