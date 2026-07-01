<?php
header('Content-Type: application/json');
include 'admin/dbinit.php';

$studentId = $_GET['id'] ?? null;
if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'No student ID provided.']);
    exit;
}

$conn->begin_transaction();

$stmt = $conn->prepare("SELECT student_id, student_name, program, section, email, password, college, created_at, otp FROM archived_students WHERE id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Archived student not found.']);
    $conn->close();
    exit;
}

$insert = $conn->prepare("INSERT INTO students (student_id, student_name, program, section, email, password, college, created_at, otp, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
$insert->bind_param(
    "sssssssss",
    $student['student_id'],
    $student['student_name'],
    $student['program'],
    $student['section'],
    $student['email'],
    $student['password'],
    $student['college'],
    $student['created_at'],
    $student['otp']
);

if (!$insert->execute()) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to restore student: ' . $insert->error]);
    $insert->close();
    $conn->close();
    exit;
}
$insert->close();

$delete = $conn->prepare("DELETE FROM archived_students WHERE id = ?");
$delete->bind_param("i", $studentId);
if (!$delete->execute()) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to remove student from archive: ' . $delete->error]);
    $delete->close();
    $conn->close();
    exit;
}
$delete->close();

$conn->commit();
echo json_encode(['success' => true, 'message' => 'Student activated successfully.']);
$conn->close();
?>