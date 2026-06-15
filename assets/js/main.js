const siteHeader = document.getElementById('siteHeader');
const mobileNav = document.getElementById('mobileNav');
const hamburger = document.querySelector('.hamburger');
const navItemsWithPanels = Array.from(document.querySelectorAll('.nav-item-has-panel'));

function setHeaderScrolledState() {
  if (!siteHeader) return;
  siteHeader.classList.toggle('is-scrolled', window.scrollY > 14);
}

function closeAllNavPanels() {
  navItemsWithPanels.forEach((item) => {
    item.classList.remove('is-open');
    const trigger = item.querySelector('.nav-panel-toggle');
    if (trigger) trigger.setAttribute('aria-expanded', 'false');
  });
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
  closeAllNavPanels();
  setMobileMenuState(shouldOpen);
};

window.scrollTestimonialTrack = function(direction) {
  const track = document.getElementById('testimonialTrack');
  if (!track) return;
  const distance = Math.max(track.clientWidth * 0.85, 320);
  const delta = direction === 'prev' ? -distance : distance;
  track.scrollBy({ left: delta, behavior: 'smooth' });
};

navItemsWithPanels.forEach((item) => {
  const trigger = item.querySelector('.nav-panel-toggle');
  if (!trigger) return;
  trigger.addEventListener('click', () => {
    const shouldOpen = !item.classList.contains('is-open');
    closeAllNavPanels();
    if (shouldOpen) {
      item.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
    }
  });
});

if (hamburger) {
  hamburger.addEventListener('click', () => {
    const shouldOpen = !mobileNav?.classList.contains('show');
    setMobileMenuState(Boolean(shouldOpen));
  });
}

document.addEventListener('click', (event) => {
  const clickedInsidePanel = navItemsWithPanels.some((item) => item.contains(event.target));
  if (!clickedInsidePanel) closeAllNavPanels();
  if (mobileNav && hamburger && mobileNav.classList.contains('show') && !mobileNav.contains(event.target) && !hamburger.contains(event.target)) {
    setMobileMenuState(false);
  }
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    closeAllNavPanels();
    setMobileMenuState(false);
  }
});

window.addEventListener('resize', () => {
  if (window.innerWidth > 1120) setMobileMenuState(false);
});

window.addEventListener('scroll', setHeaderScrolledState, { passive: true });
setHeaderScrolledState();

// Conversion tracking
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

// Exit intent popup
(function() {
  let shown = false;
  const popup = document.getElementById('exitPopup');
  if (!popup) return;
  document.addEventListener('mouseleave', (e) => {
    if (shown || e.clientY > 0) return;
    shown = true;
    popup.classList.add('show');
  });
  popup.querySelector('.popup-close')?.addEventListener('click', () => {
    popup.classList.remove('show');
  });
  popup.addEventListener('click', (e) => {
    if (e.target === popup) popup.classList.remove('show');
  });
})();
