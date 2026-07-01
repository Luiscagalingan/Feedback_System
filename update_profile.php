<?php
session_start();
header('Content-Type: application/json');
include 'admin/dbinit.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$name = trim($input['name'] ?? '');
if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Name is required.']);
    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

switch ($role) {
    case 'student':
        $tableName = 'students';
        $nameField = 'student_name';
        break;
    case 'academic':
    case 'dean':
        $tableName = 'academic_teachers';
        $nameField = 'full_name';
        break;
    case 'nonacademic':
        $tableName = 'non_academic_teachers';
        $nameField = 'full_name';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid user role.']);
        exit;
}

$stmt = $conn->prepare("UPDATE $tableName SET $nameField = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
$stmt->bind_param('si', $name, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.', 'name' => $name]);
} else {
    echo json_encode(['success' => false, 'message' => 'Unable to update profile.']);
}

$stmt->close();
$conn->close();
?>