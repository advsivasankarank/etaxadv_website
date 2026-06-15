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

document.addEventListener('click', function(event){
  const menu = document.getElementById('mobileNav');
  const trigger = document.querySelector('.hamburger');

  if (!menu || !trigger || window.innerWidth > 1080) {
    return;
  }

  if (!menu.contains(event.target) && !trigger.contains(event.target)) {
    toggleMenu(false);
  }
});

document.addEventListener('keydown', function(event){
  if (event.key === 'Escape') {
    toggleMenu(false);
  }
});

window.addEventListener('resize', function(){
  if (window.innerWidth > 1080) {
    toggleMenu(false);
  }
});
