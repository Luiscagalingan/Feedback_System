<?php
declare(strict_types=1);

require_once __DIR__ . '/access.php';
start_secure_session();
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$timeout = 7200;
if (!empty($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > $timeout) {
    $_SESSION = [];
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired.']);
    exit;
}

$role = $_SESSION['role'] ?? '';
$path = $_SESSION['dashboard_path'] ?? '';

if (empty($_SESSION['user_id']) || $role === '' || $path === '') {
    http_response_code(401);
    echo json_encode(['success' => false]);
    exit;
}

$_SESSION['last_activity'] = time();

echo json_encode(['success' => true, 'role' => $role, 'dashboard_path' => $path]);
