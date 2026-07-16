<?php
require_once __DIR__ . '/auth/access.php';
start_secure_session('student');
header('Content-Type: application/json');
include 'admin/dbinit.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Not logged in as a student.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Prepare a secure query to fetch student details
$stmt = $conn->prepare("SELECT student_name, student_id, program, section FROM students WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($student = $result->fetch_assoc()) {
    // Send a success response with the student's data
    echo json_encode(['success' => true, 'name' => $student['student_name'], 'student_number' => $student['student_id'], 'program' => $student['program'] . ' ' . $student['section']]);
} else {
    // Handle case where user ID from session is not found in the database
    echo json_encode(['success' => false, 'message' => 'Student profile not found.']);
}

$stmt->close();
$conn->close();
?>
