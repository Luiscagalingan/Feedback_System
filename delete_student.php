<?php
header('Content-Type: application/json');
include 'admin/dbinit.php';

$studentId = $_GET['id'] ?? null;
if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'No student ID provided.']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Student deleted successfully.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM archived_students WHERE id = ?");
$stmt->bind_param("i", $studentId);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Archived student deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
}
$stmt->close();
$conn->close();
?>