<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/config/database.php';

function respond(bool $success, string $message, int $status = 200): never
{
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(false, 'Invalid request method.', 405);
$reset = $_SESSION['password_reset'] ?? null;
if (!is_array($reset) || empty($reset['verified'])) respond(false, 'Verify your email code first.', 403);
if (($reset['expires_at'] ?? 0) < time() || ($reset['verified_at'] ?? 0) < time() - 600) {
    unset($_SESSION['password_reset']);
    respond(false, 'Your reset session expired. Please start again.', 410);
}

$password = (string) ($_POST['password'] ?? '');
$confirm = (string) ($_POST['confirm_password'] ?? '');
if (strlen($password) < 8) respond(false, 'Password must be at least 8 characters.', 422);
if ($password !== $confirm) respond(false, 'Passwords do not match.', 422);

$hash = password_hash($password, PASSWORD_DEFAULT);
$studentId = (int) $reset['student_id'];
$stmt = $conn->prepare('UPDATE students SET password = ?, otp = NULL WHERE id = ?');
$stmt->bind_param('si', $hash, $studentId);
if (!$stmt->execute() || $stmt->affected_rows < 1) respond(false, 'Password could not be updated.', 500);

unset($_SESSION['password_reset']);
session_regenerate_id(true);
respond(true, 'Your password has been changed. You can now sign in.');
