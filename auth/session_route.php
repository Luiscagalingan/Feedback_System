<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json');

$role = $_SESSION['role'] ?? '';
$path = $_SESSION['dashboard_path'] ?? '';

if (empty($_SESSION['user_id']) || $role === '' || $path === '') {
    http_response_code(401);
    echo json_encode(['success' => false]);
    exit;
}

echo json_encode(['success' => true, 'role' => $role, 'dashboard_path' => $path]);
