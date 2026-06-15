window.toggleMenu = function(forceState){
  const menu = document.getElementById('mobileNav');
  const trigger = document.querySelector('.hamburger');

  if (!menu || !trigger) {
    return;
  }

  const shouldOpen = typeof forceState === 'boolean' ? forceState : !menu.classList.contains('show');
  menu.classList.toggle('show', shouldOpen);
  trigger.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
};

window.toggleQuickLinks = function(forceState){
  const links = document.getElementById('serviceQuickLinks');
  const trigger = document.querySelector('.nav-services-toggle');

  if (!links || !trigger) {
    return;
  }

  const shouldOpen = typeof forceState === 'boolean' ? forceState : !links.classList.contains('show');
  links.classList.toggle('show', shouldOpen);
  trigger.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
};

document.addEventListener('click', function(event){
  const menu = document.getElementById('mobileNav');
  const trigger = document.querySelector('.hamburger');
  const quickLinks = document.getElementById('serviceQuickLinks');
  const quickTrigger = document.querySelector('.nav-services-toggle');

  if (menu && trigger && window.innerWidth <= 1120 && !menu.contains(event.target) && !trigger.contains(event.target)) {
    toggleMenu(false);
  }

  if (quickLinks && quickTrigger && !quickLinks.contains(event.target) && !quickTrigger.contains(event.target)) {
    toggleQuickLinks(false);
  }
});

document.addEventListener('keydown', function(event){
  if (event.key === 'Escape') {
    toggleMenu(false);
    toggleQuickLinks(false);
  }
});

window.addEventListener('resize', function(){
  if (window.innerWidth > 1120) {
    toggleMenu(false);
  }
});
