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

$sql = "DELETE FROM students WHERE id = ?" . ($session['role'] === 'dean' ? ' AND college = ?' : '');
$stmt = $conn->prepare($sql);
if ($session['role'] === 'dean') $stmt->bind_param("is", $studentId, $session['college']); else $stmt->bind_param("i", $studentId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    audit_log($conn, 'student_deleted', 'Permanently deleted active student record ID ' . $studentId . '.');
    echo json_encode(['success' => true, 'message' => 'Student deleted successfully.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

$sql = "DELETE FROM archived_students WHERE id = ?" . ($session['role'] === 'dean' ? ' AND college = ?' : '');
$stmt = $conn->prepare($sql);
if ($session['role'] === 'dean') $stmt->bind_param("is", $studentId, $session['college']); else $stmt->bind_param("i", $studentId);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    audit_log($conn, 'student_deleted', 'Permanently deleted archived student record ID ' . $studentId . '.');
    echo json_encode(['success' => true, 'message' => 'Archived student deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
}
$stmt->close();
$conn->close();
?>
