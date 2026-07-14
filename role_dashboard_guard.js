(() => {
  const inAcademicDashboards = decodeURIComponent(location.pathname).includes('/Acad_Dashboards/');
  const base = inAcademicDashboards ? '../' : '';
  const current = decodeURIComponent(location.pathname).replace(/^.*\/Feedback_System\//, '');

  fetch(base + 'auth/session_route.php', { credentials: 'same-origin', cache: 'no-store' })
    .then(response => response.ok ? response.json() : Promise.reject())
    .then(data => {
      if (!data.success || !data.dashboard_path) throw new Error('No active session');
      if (current !== data.dashboard_path) location.replace(base + data.dashboard_path);
    })
    .catch(() => location.replace(base + 'index.html'));
})();
