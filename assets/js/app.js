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

  const storyRoot = document.querySelector('.home-story');
  if (!storyRoot) {
    return;
  }

  const slides = Array.from(storyRoot.querySelectorAll('.home-story__slide'));
  if (!slides.length) {
    return;
  }

  const intervalMs = Math.max(3000, parseInt(storyRoot.dataset.storyInterval || '5000', 10) || 5000);
  let idx = 0;
  let timer = null;

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function show(i) {
    const n = ((i % slides.length) + slides.length) % slides.length;
    idx = n;
    slides.forEach((el, j) => {
      const active = j === n;
      el.classList.toggle('is-active', active);
      el.setAttribute('aria-hidden', active ? 'false' : 'true');
    });
  }

  show(0);

  if (reducedMotion) {
    slides.slice(1).forEach((el) => {
      el.classList.remove('is-active');
      el.style.display = 'none';
      el.setAttribute('aria-hidden', 'true');
    });
    slides[0].classList.add('is-active');
    slides[0].setAttribute('aria-hidden', 'false');
    return;
  }

  function restartTimer() {
    if (timer !== null) {
      clearInterval(timer);
    }
    timer = window.setInterval(() => {
      show(idx + 1);
    }, intervalMs);
  }

  restartTimer();
});
