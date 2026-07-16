function setupAsyncForm(options) {
  var form = document.getElementById(options.formId);
  var button = document.getElementById('submitBtn');
  var text = button.querySelector('.button-text');
  var message = document.getElementById('formMessage');
  var originalText = text.textContent;
  document.body.classList.add('fade-in');
  form.addEventListener('submit', async function (event) {
    event.preventDefault(); message.style.display = 'none';
    if (!form.checkValidity()) { AppAlert.warning('Complete the form','Please complete all required fields correctly.'); return; }
    var password = form.querySelector('[name=password]');
    var confirm = form.querySelector('[name=confirm_password]');
    if (password && confirm && password.value !== confirm.value) { message.textContent = 'Passwords do not match.'; message.style.display = 'block'; return; }
    button.disabled = true; button.classList.add('is-loading'); text.textContent = options.loadingText;
    try {
      var response = await fetch(options.endpoint, {method:'POST', body:new FormData(form)});
      var data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.message || 'Request failed.');
      options.onSuccess(data.message);
    } catch (error) { message.textContent = error.message || 'Could not connect to the server.'; message.style.display = 'block'; }
    finally { button.disabled = false; button.classList.remove('is-loading'); text.textContent = originalText; }
  });
}
