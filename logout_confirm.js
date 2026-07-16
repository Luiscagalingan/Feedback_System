function showLogoutConfirmation() {
  const root = decodeURIComponent(location.pathname).split('/Feedback_System/')[0] + '/Feedback_System/';
  return AppAlert.logout(root + 'auth/logout.php');
}
