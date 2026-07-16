<?php
declare(strict_types=1);

require_once __DIR__ . '/auth/access.php';
start_secure_session('student');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth/auth_helpers.php';

if (($_SESSION['role'] ?? '') !== 'student' || empty($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

$stmt = $conn->prepare('SELECT college FROM students WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    http_response_code(403);
    exit('Student profile not found.');
}

$college = normalize_college($student['college']);
$dashboard = student_dashboard_file($college);
if ($college === '' || $dashboard === null) {
    session_unset();
    session_destroy();
    header('Location: index.html');
    exit;
}
$_SESSION['college'] = $college;
$_SESSION['dashboard_path'] = student_dashboard_path($college);
$page = $_GET['page'] ?? 'dashboard';
$page = in_array($page, ['dashboard', 'submit', 'profile'], true) ? $page : 'dashboard';

header('Location: Student%20Dashboard/' . $dashboard . '#' . $page);
exit;
