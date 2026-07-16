<?php
require_once __DIR__ . '/auth/access.php';
start_secure_session('student');
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Not logged in as student']);
    exit;
}

include 'admin/dbinit.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT student_id FROM students WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($student = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'student_id' => $student['student_id']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
}

$stmt->close();
$conn->close();
?>
