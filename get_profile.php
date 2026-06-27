<?php
session_start();
header('Content-Type: application/json');
include 'admin/dbinit.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];
$tableName = '';
$nameField = '';
$hasEmail = true; // default for roles that have email

switch ($role) {
    case 'student':
        $tableName = 'students';
        $nameField = 'student_name';
        break;
    case 'academic':
        $tableName = 'academic_teachers';   
        $nameField = 'full_name';
        break;
    case 'nonacademic':
        $tableName = 'non_academic_teachers';
        $nameField = 'full_name';
        break;
    case 'admin':
        $tableName = 'admins';
        $nameField = 'username'; // use 'username' for admin
        $hasEmail = false; // admin does not need email
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid user role.']);
        exit;
}

if ($hasEmail) {
    $stmt = $conn->prepare("SELECT $nameField, email FROM $tableName WHERE id = ?");
} else {
    $stmt = $conn->prepare("SELECT $nameField FROM $tableName WHERE id = ?");
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if ($role === 'admin') {
        echo json_encode(['success' => true, 'username' => $user[$nameField]]);
    } else {
        echo json_encode(['success' => true, 'name' => $user[$nameField], 'email' => $user['email']]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}

$stmt->close();
$conn->close();
?>
