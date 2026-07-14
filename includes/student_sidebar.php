<?php
declare(strict_types=1);

/**
 * Shared student navigation. Keep this markup and student_sidebar.css aligned
 * with the finalized sidebar in Student Dashboard/dashboard_*.html.
 */
function render_student_sidebar(array $student, string $activePage = 'submit'): void
{
    $college = (string) ($student['college'] ?? '');
    $dashboard = feedback_dashboard_url($college, 'dashboard');
    $submit = feedback_dashboard_url($college, 'submit');
    $profile = feedback_dashboard_url($college, 'profile');
    ?>
    <aside class="student-sidebar" aria-label="Student navigation">
      <div class="student-sidebar-brand">
        <img src="../../logoooo.jfif" alt="PLP Feedback logo">
        <div>
          <strong>PLP Feedback</strong>
          <span>Student &middot; <?= htmlspecialchars($college) ?></span>
        </div>
      </div>

      <nav class="student-sidebar-nav">
        <a href="<?= htmlspecialchars($dashboard) ?>"<?= $activePage === 'dashboard' ? ' class="active"' : '' ?>><svg class="student-sidebar-icon" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5"></path><path d="M5 10v10h14V10"></path></svg>Dashboard</a>
        <a href="<?= htmlspecialchars($submit) ?>"<?= $activePage === 'submit' ? ' class="active"' : '' ?>><svg class="student-sidebar-icon" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>Submit Feedback</a>
        <a href="<?= htmlspecialchars($profile) ?>"<?= $activePage === 'profile' ? ' class="active"' : '' ?>><svg class="student-sidebar-icon" viewBox="0 0 24 24"><path d="M20 21a8 8 0 0 0-16 0"></path><circle cx="12" cy="7" r="4"></circle></svg>Profile</a>
      </nav>

      <div class="student-sidebar-footer"><a href="../../index.html"><svg class="student-sidebar-icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="m16 17 5-5-5-5"></path><path d="M21 12H9"></path></svg>Logout</a></div>
    </aside>
    <?php
}
