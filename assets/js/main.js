const siteHeader = document.getElementById('siteHeader');
const mobileNav = document.getElementById('mobileNav');
const hamburger = document.querySelector('.hamburger');

function setHeaderScrolledState() {
  if (!siteHeader) return;
  siteHeader.classList.toggle('is-scrolled', window.scrollY > 14);
}

function setMobileMenuState(shouldOpen) {
  if (!mobileNav || !hamburger) return;
  mobileNav.classList.toggle('show', shouldOpen);
  hamburger.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
  document.body.classList.toggle('menu-open', shouldOpen);
}

window.toggleMenu = function(forceState) {
  const shouldOpen = typeof forceState === 'boolean'
    ? forceState
    : !(mobileNav && mobileNav.classList.contains('show'));
  setMobileMenuState(shouldOpen);
};

if (hamburger) {
  hamburger.addEventListener('click', () => {
    const shouldOpen = !mobileNav?.classList.contains('show');
    setMobileMenuState(Boolean(shouldOpen));
  });
}

document.addEventListener('click', (event) => {
  if (mobileNav && hamburger && mobileNav.classList.contains('show') && !mobileNav.contains(event.target) && !hamburger.contains(event.target)) {
    setMobileMenuState(false);
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    setMobileMenuState(false);
  }
});

window.addEventListener('resize', () => {
  if (window.innerWidth > 1120) setMobileMenuState(false);
});

window.addEventListener('scroll', setHeaderScrolledState, { passive: true });
setHeaderScrolledState();

document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]').forEach(el => {
  el.addEventListener('click', () => {
    if (typeof gtag === 'function') gtag('event', 'whatsapp_click', { event_category: 'engagement' });
  });
});

document.querySelectorAll('a[href^="tel:"]').forEach(el => {
  el.addEventListener('click', () => {
    if (typeof gtag === 'function') gtag('event', 'call_click', { event_category: 'engagement' });
  });
});
