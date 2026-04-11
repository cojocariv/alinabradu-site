document.addEventListener('DOMContentLoaded', () => {
  const mobileMenuButton = document.querySelector('[data-mobile-menu-btn]');
  const mobileMenu = document.querySelector('[data-mobile-menu]');
  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
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
