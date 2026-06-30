<?php
header('Content-Type: application/json');
session_start(); // Start the session
include 'admin/dbinit.php';

// Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "test";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     echo json_encode(["success" => false, "message" => "Database connection failed."]);
//     exit;
// }

// Read input (supports JSON or regular POST)
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

// ✅ Read fields from frontend
$identifier = trim($input["username"] ?? "");
$password = trim($input["password"] ?? "");

// Validate input
if (empty($identifier) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Missing ID or Password"]);
    exit;
}

/* 🛠️ ADMIN LOGIN */
$sql_admin = "SELECT * FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql_admin);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result_admin = $stmt->get_result();

if ($result_admin->num_rows > 0) {
    $admin = $result_admin->fetch_assoc();

    if ($password === $admin["password"]) {
        // Set session variables
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['role'] = 'admin';
        echo json_encode(["success" => true, "role" => "admin", "message" => "Admin login successful"]);

    } else {
        echo json_encode(["success" => false, "message" => "Invalid admin password"]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

/* 👨‍🎓 STUDENT LOGIN (via student_id only) */
$sql_student = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql_student);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result_student = $stmt->get_result();

if ($result_student->num_rows > 0) {
    $student = $result_student->fetch_assoc();

    if ($password === $student["password"]) {
        // Set session variables
        $_SESSION['user_id'] = $student['id'];
        $_SESSION['role'] = 'student';
        echo json_encode([
            "success" => true,
            "role" => "student",
            "message" => "Student login successful",
            "redirect_url" => "Student Dashboard/index.html"
        ]);

    } else {
        echo json_encode(["success" => false, "message" => "Invalid student password"]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

/* 👨‍🏫 ACADEMIC TEACHER LOGIN (via full_name only) */
$sql_academic = "SELECT * FROM academic_teachers WHERE full_name = ?";
$stmt = $conn->prepare($sql_academic);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result_academic = $stmt->get_result();

if ($result_academic->num_rows > 0) {
    $academic = $result_academic->fetch_assoc();

    if ($password === $academic["password"]) {
        // Set session variables
        $_SESSION['user_id'] = $academic['id'];
        $_SESSION['role'] = 'academic';
        echo json_encode(["success" => true, "role" => "academic", "message" => "Academic teacher login successful"]);

    } else {
        echo json_encode(["success" => false, "message" => "Invalid academic teacher password"]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

/* 🧑‍🔧 NON-ACADEMIC TEACHER LOGIN (via full_name only) */
$sql_nonacademic = "SELECT * FROM non_academic_teachers WHERE full_name = ?";
$stmt = $conn->prepare($sql_nonacademic);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result_nonacademic = $stmt->get_result();

if ($result_nonacademic->num_rows > 0) {
    $nonacademic = $result_nonacademic->fetch_assoc();

    if ($password === $nonacademic["password"]) {
        // Set session variables
        $_SESSION['user_id'] = $nonacademic['id'];
        $_SESSION['role'] = 'nonacademic';
        echo json_encode(["success" => true, "role" => "nonacademic", "message" => "Non-academic teacher login successful"]);

    } else {
        echo json_encode(["success" => false, "message" => "Invalid non-academic teacher password"]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

/* ❌ No matching account found */
echo json_encode(["success" => false, "message" => "Account not found"]);
$stmt->close();
$conn->close();
?>
