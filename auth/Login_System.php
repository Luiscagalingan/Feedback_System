<?php
header('Content-Type: application/json');
session_start(); // Start the session
require_once dirname(__DIR__) . '/admin/dbinit.php';
require_once __DIR__ . '/auth_helpers.php';

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

// Maps a college code (stored on the student record) to the
// dashboard file that college's students should land on.
// Any code that isn't in this list falls back to the CAS dashboard file.
$collegeDashboards = [
    'BSA'  => 'dashboard_BSA.html',
    'CBA'  => 'dashboard_BSA.html',
    'COED' => 'dashboard_COED.html',
    'COE'  => 'dashboard_COE.html',
    'CAS'  => 'dashbaord_CAS.html',
    'CCS'  => 'dashboard_CCS.html',
    'CON'  => 'dashboard_CON.html',
    'CIHM' => 'dashboard_CIHM.html',
];

$academicCollegeDashboards = [
    'BSA'  => 'BSA_Dashboard.html',
    'CAS'  => 'CAS_Dashboard.html',
    'CCS'  => 'CCS_Dashboard.html',
    'COE'  => 'COE_Dashboard.html',
    'CON'  => 'CON_Dashboard.html',
    'CIHM' => 'CIHM_Dashboard.html',
    'COED' => 'COED_Dashboard.html',
];

function detectCollegeFromAccount($value) {
    $normalized = strtolower(trim((string) $value));
    // COED must be checked before COE because "coe" is part of "coed".
    $codes = ['coed', 'bsa', 'cas', 'ccs', 'cihm', 'coe', 'con'];

    foreach ($codes as $code) {
        if (strpos($normalized, $code) !== false) {
            return strtoupper($code);
        }
    }

    return null;
}

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

function passwordMatches($inputPassword, $storedPassword) {
    if (password_verify($inputPassword, $storedPassword)) {
        return true;
    }
    return $inputPassword === $storedPassword;
}

/* 🛠️ ADMIN LOGIN */
$sql_admin = "SELECT * FROM admins WHERE username = ?";
$stmt = $conn->prepare($sql_admin);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result_admin = $stmt->get_result();

if ($result_admin->num_rows > 0) {
    $admin = $result_admin->fetch_assoc();

    if (passwordMatches($password, $admin["password"])) {
        session_regenerate_id(true);
        // Set session variables
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['role'] = 'admin';
        $_SESSION['college'] = '';
        $_SESSION['dashboard_path'] = 'admin_dashboard.html';
        echo json_encode(["success" => true, "role" => "admin", "message" => "Admin login successful", "redirect_url" => $_SESSION['dashboard_path']]);

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

    if (passwordMatches($password, $student["password"])) {
        session_regenerate_id(true);
        // Set session variables
        $_SESSION['user_id'] = $student['id'];
        $_SESSION['role'] = 'student';

        // Pick the dashboard file for this student's college.
        // Falls back to CAS if the stored value is blank or unrecognized.
        $studentCollege = normalize_college($student['college'] ?? '');
        if ($studentCollege === '') {
            echo json_encode(["success" => false, "message" => "Your student account has no recognized college assignment."]);
            exit;
        }
        $_SESSION['college'] = $studentCollege;
        $dashboardFile = student_dashboard_file($studentCollege);
        $_SESSION['dashboard_path'] = student_dashboard_path($studentCollege);

        echo json_encode([
            "success" => true,
            "role" => "student",
            "message" => "Student login successful",
            "college" => $studentCollege,
            "redirect_url" => $_SESSION['dashboard_path']
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

    if (passwordMatches($password, $academic["password"])) {
        session_regenerate_id(true);
        $fullName = $academic['full_name'] ?? '';
        $email = $academic['email'] ?? '';
        $accountText = $fullName . ' ' . $email;
        $detectedCollege = detectCollegeFromAccount($accountText);
        $isDean = $detectedCollege !== null && (strpos(strtolower($fullName), 'dean') !== false || strpos(strtolower($email), 'dean') !== false);
        $role = $isDean ? 'dean' : 'academic';

        // Set session variables
        $_SESSION['user_id'] = $academic['id'];
        $_SESSION['role'] = $role;
        $_SESSION['college'] = normalize_college($detectedCollege ?? '');
        $_SESSION['dashboard_path'] = $role === 'dean' && isset($academicCollegeDashboards[$_SESSION['college']])
            ? 'Acad_Dashboards/' . $academicCollegeDashboards[$_SESSION['college']]
            : 'Dashboard.html';

        $response = [
            "success" => true,
            "role" => $role,
            "message" => $isDean ? "Dean login successful" : "Academic teacher login successful"
        ];

        $response["college"] = $_SESSION['college'];
        $response["redirect_url"] = $_SESSION['dashboard_path'];

        echo json_encode($response);

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

    if (passwordMatches($password, $nonacademic["password"])) {
        session_regenerate_id(true);
        // Set session variables
        $_SESSION['user_id'] = $nonacademic['id'];
        $_SESSION['role'] = 'nonacademic';
        $_SESSION['college'] = '';
        $_SESSION['dashboard_path'] = 'Non_Acad_Dashboard.html';
        echo json_encode(["success" => true, "role" => "nonacademic", "message" => "Non-academic teacher login successful", "redirect_url" => $_SESSION['dashboard_path']]);

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
