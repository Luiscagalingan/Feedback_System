<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'admin/dbinit.php';
// Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "test";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die(json_encode(["success" => false, "message" => "Database connection failed."]));
// }

// Get search term and role
$search = trim($_GET['search'] ?? '');
$role = $_GET['role'] ?? '';

$requestedStatus = trim($_GET['status'] ?? 'active');
$statusFilter = in_array($requestedStatus, ['active', 'archived'], true) ? $requestedStatus : 'active';

$tableName = $statusFilter === 'archived' ? 'archived_students' : 'students';

if ($tableName === 'students') {
    $sql = "SELECT id, student_id, student_name, email, section, program, college
            FROM students
            WHERE status = 'active'";
} else {
    $sql = "SELECT id, student_id, student_name, email, section, program, college
            FROM archived_students
            WHERE 1=1";
}

$collegeRole = strtoupper(trim($role));
$collegeCodes = ['BSA', 'CAS', 'CCS', 'CIHM', 'COE', 'CON'];
if (in_array($collegeRole, $collegeCodes, true)) {
    $sql .= " AND college = ?";
} elseif ($role === 'academic' || $role === 'dean') {
    $sql .= " AND (LOWER(program) LIKE '%bsit%' OR LOWER(program) LIKE '%bscs%')";
}

if ($search !== '') {
    $safeSearch = $conn->real_escape_string($search);
    $searchPattern = strtolower($safeSearch);
    $sql .= " AND (
        LOWER(student_name) LIKE '%$searchPattern%' OR
        LOWER(student_id) LIKE '%$searchPattern%' OR
        LOWER(section) LIKE '%$searchPattern%' OR
        LOWER(program) LIKE '%$searchPattern%' OR
        LOWER(email) LIKE '%$searchPattern%'
    )";
}

$sql .= " ORDER BY student_name ASC";
$stmt = $conn->prepare($sql);
if (in_array($collegeRole, $collegeCodes, true)) {
    $stmt->bind_param('s', $collegeRole);
}
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to load students.",
        "error" => $conn->error
    ]);
    $conn->close();
    exit;
}

$students = $result->fetch_all(MYSQLI_ASSOC);

// Return JSON
echo json_encode([
    "success" => true,
    "search_received" => $search,
    "role" => $role,
    "students" => $students
]);

$conn->close();
?>
