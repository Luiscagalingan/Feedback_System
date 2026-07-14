(() => {
  const loginUrl = '../index.html';
  const currentFile = decodeURIComponent(location.pathname).split('/').pop();
  const requestedPage = ['dashboard', 'submit', 'profile'].includes(location.hash.slice(1))
    ? location.hash.slice(1)
    : 'dashboard';

  fetch('../get_student_dashboard_profile.php', { credentials: 'same-origin', cache: 'no-store' })
    .then(response => response.ok ? response.json() : Promise.reject(new Error('Session check failed')))
    .then(data => {
      if (!data.success || !data.student || !data.student.dashboard_path) {
        location.replace(loginUrl);
        return;
      }
      const assignedFile = data.student.dashboard_path.split('/').pop();
      if (currentFile !== assignedFile) {
        location.replace('../student_dashboard.php?page=' + encodeURIComponent(requestedPage));
      }
    })
    .catch(() => location.replace(loginUrl));
})();
