<?php
declare(strict_types=1);

function role_session_name(string $role): string {
    return match ($role) {
        'admin' => 'PLP_ADMIN_SESSION',
        'student' => 'PLP_STUDENT_SESSION',
        'dean', 'academic' => 'PLP_DEAN_SESSION',
        'nonacademic' => 'PLP_OFFICE_SESSION',
        default => 'PLP_PUBLIC_SESSION'
    };
}

function detected_session_role(array $allowed = []): string {
    $explicit = strtolower(trim((string) ($_SERVER['HTTP_X_PLP_ROLE'] ?? $_GET['session_role'] ?? '')));
    if ($explicit === 'office') $explicit = 'nonacademic';
    if ($explicit !== '' && (!$allowed || in_array($explicit, $allowed, true))) return $explicit;
    $source = strtolower(urldecode((string) ($_SERVER['HTTP_REFERER'] ?? '') . ' ' . (string) ($_SERVER['REQUEST_URI'] ?? '')));
    $detected = str_contains($source, 'admin_dashboard') || str_contains($source, '/admin/') ? 'admin'
        : (str_contains($source, 'acad_dashboards') || str_contains($source, 'profile_acad') ? 'dean'
        : (str_contains($source, 'non_acad_dashboard') || str_contains($source, 'profile_non_acad') ? 'nonacademic'
        : (str_contains($source, 'student dashboard') || str_contains($source, 'student%20dashboard') || str_contains($source, '/academic/feedback') || str_contains($source, '/non_academic/feedback') ? 'student' : '')));
    if ($detected !== '' && (!$allowed || in_array($detected, $allowed, true))) return $detected;
    if (count($allowed) === 1) return $allowed[0];
    foreach ($allowed as $role) if (isset($_COOKIE[role_session_name($role)])) return $role;
    return '';
}

function start_secure_session(string $role = '', array $allowed = []): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $role = $role !== '' ? $role : detected_session_role($allowed);
    session_name(role_session_name($role));
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}

function json_response(array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function require_roles(array $roles): array {
    start_secure_session('', $roles);
    if (empty($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', $roles, true)) {
        json_response(['success' => false, 'message' => 'Your session expired or you do not have permission.'], 403);
    }
    return $_SESSION;
}

function audit_log(mysqli $conn, string $action, string $details = '', string $module = 'system', string $status = 'success'): void {
    start_secure_session();
    if ($module === 'system') {
        if (preg_match('/^(login|logout|password_)/', $action)) $module = 'authentication';
        elseif (str_starts_with($action, 'student_')) $module = 'student_management';
        elseif (str_starts_with($action, 'account_')) $module = 'account_management';
        elseif (str_starts_with($action, 'feedback_')) $module = 'feedback';
        elseif (str_starts_with($action, 'profile_')) $module = 'profile';
        elseif (preg_match('/^(report_|records_)/', $action)) $module = 'reports';
    }
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    $role = (string) ($_SESSION['role'] ?? 'guest');
    $name = (string) ($_SESSION['user_name'] ?? 'Unknown user');
    $ip = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    $stmt = $conn->prepare('INSERT INTO audit_logs (user_id, role, user_name, action, module, status, details, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssssss', $userId, $role, $name, $action, $module, $status, $details, $ip);
    $stmt->execute();
}
