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
  const dotsContainer = storyRoot.querySelector('.home-story__dots');
  if (!slides.length || !dotsContainer) {
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
    dotsContainer.querySelectorAll('.home-story__dot').forEach((btn, j) => {
      const active = j === n;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-selected', active ? 'true' : 'false');
    });
  }

  slides.forEach((_, i) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'home-story__dot';
    btn.setAttribute('role', 'tab');
    btn.setAttribute('aria-label', `Fragment ${i + 1} din ${slides.length}`);
    btn.setAttribute('tabindex', i === 0 ? '0' : '-1');
    btn.addEventListener('click', () => {
      show(i);
      restartTimer();
    });
    dotsContainer.appendChild(btn);
  });

  function restartTimer() {
    if (reducedMotion) {
      return;
    }
    if (timer !== null) {
      clearInterval(timer);
    }
    timer = window.setInterval(() => {
      show(idx + 1);
    }, intervalMs);
  }

  function pauseTimer() {
    if (timer !== null) {
      clearInterval(timer);
      timer = null;
    }
  }

  show(0);

  if (reducedMotion) {
    dotsContainer.style.display = 'none';
    slides.slice(1).forEach((el) => {
      el.classList.remove('is-active');
      el.style.display = 'none';
      el.setAttribute('aria-hidden', 'true');
    });
    slides[0].classList.add('is-active');
    slides[0].setAttribute('aria-hidden', 'false');
    return;
  }

  restartTimer();

  storyRoot.addEventListener('mouseenter', pauseTimer);
  storyRoot.addEventListener('mouseleave', restartTimer);
  storyRoot.addEventListener('focusin', pauseTimer);
  storyRoot.addEventListener('focusout', (e) => {
    if (!storyRoot.contains(e.relatedTarget)) {
      restartTimer();
    }
  });
});
