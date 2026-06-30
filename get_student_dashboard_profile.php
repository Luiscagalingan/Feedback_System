<?php
session_start();
header('Content-Type: application/json');
include 'admin/dbinit.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Not logged in as a student.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Looks up the display name + theme color for a college code.
// Keep these colors in sync with the dashboard_*.html files.
function collegeInfo($code) {
    $map = [
        'BSA'  => ['code' => 'BSA',  'name' => 'College of Business and Accountancy', 'color' => '#FFB000'],
        'COED' => ['code' => 'COED', 'name' => 'College of Education',                'color' => '#294789'],
        'COE'  => ['code' => 'COE',  'name' => 'College of Engineering',              'color' => '#FB7528'],
        'CAS'  => ['code' => 'CAS',  'name' => 'College of Arts and Sciences',        'color' => '#7600BC'],
        'CON'  => ['code' => 'CON',  'name' => 'College of Nursing',                  'color' => '#fe86cd'],
        'CCS'  => ['code' => 'CCS',  'name' => 'College of Computer Studies',         'color' => '#C5C6C7'],
        'CIHM' => ['code' => 'CIHM', 'name' => 'College of Hospitality Management',   'color' => '#491615'],
    ];

    $key = strtoupper(trim($code ?? ''));
    return $map[$key] ?? $map['CAS'];
}

$stmt = $conn->prepare("SELECT id, student_id, student_name, program, section, email, college FROM students WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($student = $result->fetch_assoc()) {
    $college = collegeInfo($student['college']);

    echo json_encode([
        'success' => true,
        'student' => [
            'id' => $student['id'],
            'student_id' => $student['student_id'],
            'name' => $student['student_name'],
            'program' => $student['program'],
            'section' => $student['section'],
            'email' => $student['email'],
            'college' => $college
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Student profile not found.']);
}

$stmt->close();
$conn->close();
?>