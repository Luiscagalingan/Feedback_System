/* BSA-style page switching for dashboards that retain their original student-management handlers. */
document.addEventListener('DOMContentLoaded', () => {
  const page = document.getElementById('dashboardPage');
  const studentNav = document.querySelector('.student-management-nav');
  const dashboardNav = document.querySelector('.nav-link[data-page="dashboard"]');
  const chartAction = document.getElementById('viewChartBtn');
  const chartCard = document.querySelector('.bsa-chart-card:last-child');

  if (!page || !studentNav) return;

  // Keep the existing chart action and its handler, but place it with the dashboard widgets.
  if (chartAction && chartCard) {
    chartAction.classList.add('dashboard-chart-action');
    chartCard.insertBefore(chartAction, chartCard.querySelector('.bsa-overview-grid'));
  }

  const showStudentManagement = (event) => {
    event.preventDefault();
    page.dataset.page = 'student-management';
    studentNav.classList.add('active');
    dashboardNav?.classList.remove('active');
    document.getElementById('studentSearch')?.focus();
  };

  studentNav.addEventListener('click', showStudentManagement);
  dashboardNav?.addEventListener('click', (event) => {
    event.preventDefault();
    delete page.dataset.page;
    dashboardNav.classList.add('active');
    studentNav.classList.remove('active');
  });
});
