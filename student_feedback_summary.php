<?php
declare(strict_types=1);
require_once __DIR__ . '/auth/access.php';
start_secure_session('student');
header('Content-Type: application/json');
require_once __DIR__ . '/config/database.php';

function reply(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

if (($_SESSION['role'] ?? '') !== 'student' || empty($_SESSION['user_id'])) {
    reply(['success' => false, 'message' => 'Please log in as a student.'], 401);
}

$studentPk = (int) $_SESSION['user_id'];
$studentStmt = $conn->prepare('SELECT student_id FROM students WHERE id = ? LIMIT 1');
$studentStmt->bind_param('i', $studentPk);
$studentStmt->execute();
$student = $studentStmt->get_result()->fetch_assoc();
if (!$student) reply(['success' => false, 'message' => 'Student account not found.'], 404);

$academicOffices = require __DIR__ . '/academic/feedback/academic_feedback_data.php';
$nonAcademicOffices = require __DIR__ . '/non_academic/feedback/non_academic_feedback_data.php';
$availableCount = count($academicOffices) + count($nonAcademicOffices);
$studentId = (string) $student['student_id'];

$summaryStmt = $conn->prepare("SELECT COUNT(*) submitted, COUNT(DISTINCT CONCAT(category, ':', office_key)) completed_offices, SUM(created_at >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')) this_month FROM office_feedback WHERE student_id = ? AND status = 'submitted'");
$summaryStmt->bind_param('s', $studentId);
$summaryStmt->execute();
$summary = $summaryStmt->get_result()->fetch_assoc();

$recentStmt = $conn->prepare("SELECT id, category, office_key, office_name, rating_average, review_result, status, created_at FROM office_feedback WHERE student_id = ? AND status = 'submitted' ORDER BY created_at DESC, id DESC LIMIT 6");
$recentStmt->bind_param('s', $studentId);
$recentStmt->execute();
$recent = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$completed = min($availableCount, (int) ($summary['completed_offices'] ?? 0));
reply(['success' => true, 'data' => [
    'submitted' => (int) ($summary['submitted'] ?? 0),
    'pending' => max(0, $availableCount - $completed),
    'this_month' => (int) ($summary['this_month'] ?? 0),
    'available' => $availableCount,
    'recent' => $recent,
]]);
