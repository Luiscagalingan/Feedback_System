<?php
require_once __DIR__ . '/auth/access.php';
start_secure_session();
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
    case 'dean':
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

if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT username, name FROM admins WHERE id = ?");
} elseif ($role === 'nonacademic') {
    $stmt = $conn->prepare("SELECT full_name, email, office_key, office_category, service FROM non_academic_teachers WHERE id = ?");
} elseif ($hasEmail) {
    $stmt = $conn->prepare("SELECT $nameField, email FROM $tableName WHERE id = ?");
} else {
    $stmt = $conn->prepare("SELECT $nameField FROM $tableName WHERE id = ?");
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $response = [
        'success' => true,
        'name' => $user[$nameField],
        'email' => $user['email'] ?? '',
        'college' => $_SESSION['college'] ?? '',
        'role' => 'User'
    ];

    if ($role === 'admin') {
        $response['name'] = $user['name'] ?: $user['username'];
        $response['username'] = $user['username'];
        $response['role'] = 'Admin';
        $response['email'] = '';
    } elseif ($role === 'academic') {
        $response['role'] = 'Academic Teacher';
    } elseif ($role === 'dean') {
        $response['role'] = 'Dean';
    } elseif ($role === 'student') {
        $response['role'] = 'Student';
    } elseif ($role === 'nonacademic') {
        $response['role'] = 'Office Account';
        $response['office_key'] = $user['office_key'] ?? '';
        $response['office_category'] = $user['office_category'] ?? 'nonacademic';
        $response['service'] = $user['service'] ?? '';
    }

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}

$stmt->close();
$conn->close();
?>
