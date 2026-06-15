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

window.scrollTestimonialTrack = function(direction){
  const track = document.getElementById('testimonialTrack');

  if (!track) {
    return;
  }

  const distance = Math.max(track.clientWidth * 0.85, 320);
  const delta = direction === 'prev' ? -distance : distance;
  track.scrollBy({ left: delta, behavior: 'smooth' });
};

window.sendConsult = function(event){
  event.preventDefault();

  const form = event.target;
  const name = form.querySelector('[name="name"]')?.value.trim() ?? '';
  const mobile = form.querySelector('[name="mobile"]')?.value.trim() ?? '';
  const email = form.querySelector('[name="email"]')?.value.trim() ?? '';
  const organisation = form.querySelector('[name="organisation"]')?.value.trim() ?? '';
  const service = form.querySelector('[name="service"]')?.value.trim() ?? '';
  const preferredTime = form.querySelector('[name="preferred_time"]')?.value.trim() ?? '';
  const message = form.querySelector('[name="message"]')?.value.trim() ?? '';
  const subject = encodeURIComponent('Consultation Request - E Tax Advisors');
  const body = encodeURIComponent(
    'Name: ' + name + '\n' +
    'Mobile: ' + mobile + '\n' +
    'Email: ' + email + '\n' +
    'Organisation: ' + organisation + '\n' +
    'Service: ' + service + '\n' +
    'Preferred Consultation Time: ' + preferredTime + '\n\n' +
    'Requirement:\n' + message
  );

  window.location.href = 'mailto:support@etaxadv.com?subject=' + subject + '&body=' + body;
  return false;
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

document.addEventListener('submit', function(event){
  if (event.target.matches('.js-consult-form')) {
    sendConsult(event);
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
