document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('[data-feedback-form]');
  if (!form) return;
  form.addEventListener('submit', () => {
    const button = form.querySelector('button[type="submit"]');
    if (button && form.checkValidity()) { button.disabled = true; button.textContent = 'Submitting...'; }
  });
});
