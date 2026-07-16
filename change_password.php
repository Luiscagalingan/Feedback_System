<?php
header('Content-Type: application/json');
include 'admin/dbinit.php';
require_once __DIR__ . '/auth/access.php';
start_secure_session();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$currentPassword = trim($input['currentPassword'] ?? '');
$newPassword = trim($input['newPassword'] ?? '');
$confirmPassword = trim($input['confirmPassword'] ?? '');

if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
    echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'New password and confirm password do not match.']);
    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

switch ($role) {
    case 'student':
        $tableName = 'students';
        break;
    case 'academic':
    case 'dean':
        $tableName = 'academic_teachers';
        break;
    case 'nonacademic':
        $tableName = 'non_academic_teachers';
        break;
    case 'admin':
        $tableName = 'admins';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid user role.']);
        exit;
}

$stmt = $conn->prepare("SELECT password FROM $tableName WHERE id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $hashedPassword = $row['password'];
    $currentMatches = password_verify($currentPassword, $hashedPassword) || $currentPassword === $hashedPassword;
    if (!$currentMatches) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt->close();
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE $tableName SET password = ? WHERE id = ?");
    $updateStmt->bind_param('si', $newHashedPassword, $userId);

    if ($updateStmt->execute()) {
        audit_log($conn, 'password_changed', 'User changed their password.');
        echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unable to update password.']);
    }
    $updateStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}

$conn->close();
?>
