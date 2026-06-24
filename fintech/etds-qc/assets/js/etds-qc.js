document.addEventListener('click', function (event) {
  const target = event.target;
  if (!(target instanceof HTMLElement)) {
    return;
  }
  if (target.matches('[data-confirm]')) {
    const message = target.getAttribute('data-confirm') || 'Are you sure?';
    if (!window.confirm(message)) {
      event.preventDefault();
    }
  }
});

document.addEventListener('submit', async function (event) {
  const form = event.target;
  if (!(form instanceof HTMLFormElement)) {
    return;
  }
  if (form.getAttribute('data-ajax') !== 'reload') {
    return;
  }

  event.preventDefault();
  const submitButton = form.querySelector('button[type="submit"]');
  const previousText = submitButton ? submitButton.textContent : '';
  if (submitButton) {
    submitButton.disabled = true;
    submitButton.textContent = 'Working...';
  }

  try {
    const formData = new FormData(form);
    formData.set('ajax', '1');
    const response = await fetch(form.action, {
      method: form.method || 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const contentType = response.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
      HTMLFormElement.prototype.submit.call(form);
      return;
    }
    const payload = await response.json();
    if (!response.ok || !payload.ok) {
      throw new Error(payload.message || 'Request failed.');
    }
    window.location.href = payload.redirect || window.location.href;
  } catch (error) {
    HTMLFormElement.prototype.submit.call(form);
    return;
  } finally {
    if (submitButton) {
      submitButton.disabled = false;
      submitButton.textContent = previousText;
    }
  }
});
