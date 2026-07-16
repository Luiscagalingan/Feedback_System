<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth/access.php';
start_secure_session('student');
require_once dirname(__DIR__) . '/config/database.php';
require __DIR__ . '/feedback_functions.php';
require_once __DIR__ . '/../auth/access.php';

$category = $_POST['category'] ?? '';
$redirect = $category === 'academic' ? '../academic/feedback/feedback_form.php' : '../non_academic/feedback/feedback_form.php';
if (!in_array($category, ['academic', 'nonacademic'], true) || !hash_equals($_SESSION['feedback_csrf'] ?? '', $_POST['csrf'] ?? '')) {
    header('Location: ' . $redirect . '?error=invalid-request'); exit;
}
$student = feedback_require_student($conn);
$offices = $category === 'academic' ? require __DIR__ . '/../academic/feedback/academic_feedback_data.php' : require __DIR__ . '/../non_academic/feedback/non_academic_feedback_data.php';
$officeKey = $_POST['office'] ?? '';
if (!isset($offices[$officeKey])) { header('Location: ' . $redirect . '?error=invalid-office'); exit; }
$ratings = $_POST['ratings'] ?? [];
if (!is_array($ratings) || count($ratings) !== 4) { header('Location: ' . $redirect . '?office=' . rawurlencode($officeKey) . '&error=complete-questions'); exit; }
$answers = [];
foreach ($offices[$officeKey]['questions'] as $index => [$question, $options]) {
    $value = filter_var($ratings[$index] ?? null, FILTER_VALIDATE_INT);
    if ($value === false || !in_array($value, array_values($options), true)) { header('Location: ' . $redirect . '?office=' . rawurlencode($officeKey) . '&error=invalid-response'); exit; }
    $answers[] = $value;
}
[$average, $positive, $neutral, $negative] = feedback_rating_sentiment($answers);
$comment = trim((string) ($_POST['comments'] ?? ''));
require_once __DIR__ . '/../SentimentAnalysis/SentimentAnalyzer.php';
$review = getSentimentType($comment);
$office = $offices[$officeKey]; $responses = json_encode($ratings, JSON_THROW_ON_ERROR);
$duplicate = $conn->prepare("SELECT id FROM office_feedback WHERE student_id = ? AND category = ? AND office_key = ? AND created_at >= NOW() - INTERVAL 2 MINUTE LIMIT 1");
$duplicate->bind_param('sss', $student['student_id'], $category, $officeKey);
$duplicate->execute();
if ($duplicate->get_result()->fetch_assoc()) {
    header('Location: ' . $redirect . '?office=' . rawurlencode($officeKey) . '&error=duplicate-submission'); exit;
}
$stmt = $conn->prepare('INSERT INTO office_feedback (student_id, category, office_key, office_name, section_title, responses_json, rating_average, positive_feedback_percentage, neutral_feedback_percentage, negative_feedback_percentage, answer_text, review_result) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('ssssssddddss', $student['student_id'], $category, $officeKey, $office['name'], $office['section'], $responses, $average, $positive, $neutral, $negative, $comment, $review);
if (!$stmt->execute()) { header('Location: ' . $redirect . '?office=' . rawurlencode($officeKey) . '&error=save-failed'); exit; }
unset($_SESSION['feedback_csrf']);
audit_log($conn, 'feedback_submission', $category . ' feedback submitted for ' . $office['name'] . '.');
header('Location: ' . $redirect . '?office=' . rawurlencode($officeKey) . '&success=1');
