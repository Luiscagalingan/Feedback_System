(() => {
  const inAcademicDashboards = decodeURIComponent(location.pathname).includes('/Acad_Dashboards/');
  const base = inAcademicDashboards ? '../' : '';
  const current = decodeURIComponent(location.pathname).replace(/^.*\/Feedback_System\//, '');
  const expectedRole = current === 'admin_dashboard.html' ? 'admin' : (current === 'Non_Acad_Dashboard.html' ? 'nonacademic' : 'dean');
  sessionStorage.setItem('plpExpectedRole', expectedRole);

  fetch(base + 'auth/session_route.php', { credentials: 'same-origin', cache: 'no-store' })
    .then(response => response.ok ? response.json() : Promise.reject())
    .then(data => {
      if (!data.success || !data.dashboard_path) throw new Error('No active session');
      if (data.role !== expectedRole || current !== data.dashboard_path) {
        location.replace(base + 'index.html?session_conflict=1');
      }
    })
    .catch(() => location.replace(base + 'index.html?session_conflict=1'));
})();
