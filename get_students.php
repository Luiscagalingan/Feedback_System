<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth/access.php';
$session = require_roles(['admin', 'dean']);

$search = trim((string) ($_GET['search'] ?? ''));
$status = ($_GET['status'] ?? 'active') === 'archived' ? 'archived' : 'active';
$table = 'students';
$conditions = [$status === 'archived' ? "status = 'archived'" : "status = 'active'"];
$types = ''; $values = [];
if ($session['role'] === 'dean') {
    $conditions[] = 'college = ?'; $types .= 's'; $values[] = $session['college'];
}
if ($search !== '') {
    $conditions[] = '(student_name LIKE ? OR student_id LIKE ? OR section LIKE ? OR program LIKE ? OR email LIKE ?)';
    $pattern = '%' . $search . '%'; $types .= 'sssss';
    array_push($values, $pattern, $pattern, $pattern, $pattern, $pattern);
}
$stmt = $conn->prepare("SELECT id, student_id, student_name, email, section, program, college FROM `$table` WHERE " . implode(' AND ', $conditions) . ' ORDER BY student_name');
if ($types !== '') $stmt->bind_param($types, ...$values);
$stmt->execute();
json_response(['success'=>true, 'students'=>$stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
