<?php
header('Content-Type: application/json');
include 'admin/dbinit.php';

$studentId = $_GET['id'] ?? null;

if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'No student ID provided.']);
    exit;
}

// We use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("UPDATE students SET status = 'archived' WHERE id = ?");
$stmt->bind_param("i", $studentId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Student archived successfully.']);
} else {
    // Provide a more specific error if execution fails
    echo json_encode(['success' => false, 'message' => 'Failed to archive student: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>