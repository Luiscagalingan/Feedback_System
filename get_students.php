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

// Get search term
$search = $_GET['search'] ?? '';

if (empty($search)) {
    die(json_encode(["success" => false, "message" => "Search term not provided."]));
}

// Prepare query with placeholders
$sql = "SELECT id, student_id, student_name, email, section, program 
        FROM students
        WHERE (student_name LIKE ? 
           OR student_id LIKE ? 
           OR section LIKE ?) AND status = ?
        ORDER BY student_name ASC";

$stmt = $conn->prepare($sql);

// Add wildcards for LIKE
$likeSearch = "%$search%";
$status = 'active';

// Bind parameters (4 placeholders = 4 variables)
$stmt->bind_param("ssss", $likeSearch, $likeSearch, $likeSearch, $status);

$stmt->execute();
$result = $stmt->get_result();

$students = $result->fetch_all(MYSQLI_ASSOC);

// Return JSON
echo json_encode([
    "success" => true,
    "search_received" => $search,
    "students" => $students
]);

$stmt->close();
$conn->close();
?>
