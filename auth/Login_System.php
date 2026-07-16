<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/database.php';
require_once __DIR__ . '/auth_helpers.php';
require_once __DIR__ . '/access.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$identifier = trim((string) ($input['username'] ?? ''));
$password = (string) ($input['password'] ?? '');
if ($identifier === '' || $password === '') json_response(['success' => false, 'message' => 'Username and password are required.'], 422);

function matches_password(string $input, string $stored): bool {
    return password_verify($input, $stored) || (password_get_info($stored)['algo'] === null && hash_equals($stored, $input));
}

function finish_login(mysqli $conn, array $account, string $role, string $path, string $table, string $displayName, string $college = '', string $office = '', string $officeCategory = ''): never {
    start_secure_session($role);
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $account['id'];
    $_SESSION['role'] = $role;
    $_SESSION['user_name'] = $displayName;
    $_SESSION['college'] = $college;
    $_SESSION['office_key'] = $office;
    $_SESSION['office_category'] = $officeCategory;
    $_SESSION['dashboard_path'] = $path;
    $_SESSION['last_activity'] = time();
    if (password_get_info((string) $account['password'])['algo'] === null) {
        $hash = password_hash((string) $GLOBALS['password'], PASSWORD_DEFAULT);
        $accountId = (int) $account['id'];
        $upgrade = $conn->prepare("UPDATE `$table` SET password = ? WHERE id = ?");
        $upgrade->bind_param('si', $hash, $accountId);
        $upgrade->execute();
    }
    audit_log($conn, 'login', 'Successful login.');
    json_response(['success' => true, 'role' => $role, 'college' => $college, 'message' => 'Login successful.', 'redirect_url' => $path]);
}

$stmt = $conn->prepare('SELECT id, username, name, password FROM admins WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $identifier); $stmt->execute(); $account = $stmt->get_result()->fetch_assoc();
if ($account && matches_password($password, $account['password'])) finish_login($conn, $account, 'admin', 'admin_dashboard.html', 'admins', $account['name'] ?: $account['username']);

$stmt = $conn->prepare("SELECT id, student_id, student_name, password, college, status FROM students WHERE student_id = ? AND status = 'active' LIMIT 1");
$stmt->bind_param('s', $identifier); $stmt->execute(); $account = $stmt->get_result()->fetch_assoc();
if ($account && matches_password($password, $account['password'])) {
    $college = normalize_college($account['college']); $path = student_dashboard_path($college);
    if (!$path) json_response(['success' => false, 'message' => 'Your account has no valid college assignment.'], 403);
    finish_login($conn, $account, 'student', $path, 'students', $account['student_name'], $college);
}

$stmt = $conn->prepare("SELECT id, full_name, email, password, college, status FROM academic_teachers WHERE (full_name = ? OR email = ?) AND status = 'active' LIMIT 1");
$stmt->bind_param('ss', $identifier, $identifier); $stmt->execute(); $account = $stmt->get_result()->fetch_assoc();
if ($account && matches_password($password, $account['password'])) {
    $college = normalize_college($account['college']);
    $supported = ['BSA','CAS','CCS','CIHM','COE','COED','CON'];
    if (!$college || !in_array($college, $supported, true)) json_response(['success' => false, 'message' => 'Dean account has no valid college assignment.'], 403);
    finish_login($conn, $account, 'dean', 'Acad_Dashboards/dean-dashboard.html', 'academic_teachers', $account['full_name'], $college);
}

$stmt = $conn->prepare("SELECT id, full_name, email, password, office_key, office_category, status FROM non_academic_teachers WHERE (full_name = ? OR email = ?) AND status = 'active' LIMIT 1");
$stmt->bind_param('ss', $identifier, $identifier); $stmt->execute(); $account = $stmt->get_result()->fetch_assoc();
if ($account && matches_password($password, $account['password'])) finish_login($conn, $account, 'nonacademic', 'Non_Acad_Dashboard.html', 'non_academic_teachers', $account['full_name'], '', $account['office_key'] ?: 'all', $account['office_category'] ?: 'nonacademic');

$failed=$conn->prepare("INSERT INTO audit_logs(user_id,role,user_name,action,module,status,details,ip_address) VALUES(NULL,'guest',?,'login_failed','authentication','failed','Invalid credentials or inactive account.',?)");
$ip=substr((string)($_SERVER['REMOTE_ADDR']??''),0,45);$failed->bind_param('ss',$identifier,$ip);$failed->execute();
json_response(['success' => false, 'message' => 'Invalid credentials or inactive account.'], 401);
