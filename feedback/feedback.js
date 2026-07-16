document.addEventListener('DOMContentLoaded', () => {
  const parameters = new URLSearchParams(window.location.search);
  if (parameters.get('success') === '1') {
    const title = 'Feedback Submitted!';
    const message = 'Thank you. Your feedback was submitted successfully.';
    const submitFeedbackUrl = '../../student_dashboard.php?page=submit';

    // Remove the success flag so refreshing the page does not show the alert again.
    const cleanUrl = new URL(window.location.href);
    cleanUrl.searchParams.delete('success');
    window.history.replaceState({}, document.title, cleanUrl.pathname + cleanUrl.search + cleanUrl.hash);

    AppAlert.success(title, message).then(() => { window.location.href = submitFeedbackUrl; });
  }

  const form = document.querySelector('[data-feedback-form]');
  if (!form) return;
  form.noValidate = true;
  form.addEventListener('submit', event => {
    const button = form.querySelector('button[type="submit"]');
    if (!form.checkValidity()) { event.preventDefault(); AppAlert.warning('Complete the form','Please answer every required feedback question.'); return; }
    if (button) { button.disabled = true; button.textContent = 'Submitting...'; }
  });
});
