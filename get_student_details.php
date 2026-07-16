<?php
header('Content-Type: application/json');
include 'admin/dbinit.php';
require_once __DIR__ . '/auth/access.php';
$session = require_roles(['admin', 'dean']);

$studentId = $_GET['id'] ?? null;

if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'No student ID provided.']);
    exit;
}

$sql = "SELECT id, student_id, student_name, program, section, email FROM students WHERE id = ?" . ($session['role'] === 'dean' ? ' AND college = ?' : '');
$stmt = $conn->prepare($sql);
if ($session['role'] === 'dean') $stmt->bind_param("is", $studentId, $session['college']); else $stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

if ($student = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'student' => $student]);
} else {
    echo json_encode(['success' => false, 'message' => 'Student not found.']);
}

$stmt->close();
$conn->close();
?>
