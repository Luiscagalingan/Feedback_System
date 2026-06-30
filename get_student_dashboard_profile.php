<?php
session_start();
header('Content-Type: application/json');
include 'admin/dbinit.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Not logged in as a student.']);
    exit;
}

$userId = $_SESSION['user_id'];

function detectCollege($program) {
    $value = strtolower($program ?? '');

    if (strpos($value, 'accountancy') !== false || strpos($value, 'entrepreneurship') !== false || strpos($value, 'business') !== false || strpos($value, 'marketing') !== false || strpos($value, 'bsa') !== false) {
        return ['code' => 'CBA', 'name' => 'College of Business and Accountancy', 'color' => '#FFB000'];
    }

    if (strpos($value, 'education') !== false || strpos($value, 'filipino') !== false || strpos($value, 'mathematics') !== false || strpos($value, 'english') !== false || strpos($value, 'beed') !== false || strpos($value, 'bsed') !== false) {
        return ['code' => 'COED', 'name' => 'College of Education', 'color' => '#0041C2'];
    }

    if (strpos($value, 'engineering') !== false || strpos($value, 'electronics') !== false || strpos($value, 'ece') !== false) {
        return ['code' => 'COE', 'name' => 'College of Engineering', 'color' => '#D78C3D'];
    }

    if (strpos($value, 'psychology') !== false || strpos($value, 'ab psych') !== false) {
        return ['code' => 'CAS', 'name' => 'College of Arts and Sciences', 'color' => '#7600BC'];
    }

    if (strpos($value, 'nursing') !== false) {
        return ['code' => 'CON', 'name' => 'College of Nursing', 'color' => '#E7ACCF'];
    }

    if (strpos($value, 'information technology') !== false || strpos($value, 'computer science') !== false || strpos($value, 'bsit') !== false || strpos($value, 'bscs') !== false) {
        return ['code' => 'CCS', 'name' => 'College of Computer Studies', 'color' => '#C5C6C7'];
    }

    if (strpos($value, 'hospitality') !== false || strpos($value, 'hm') !== false) {
        return ['code' => 'CIHM', 'name' => 'College of Hospitality Management', 'color' => '#9D2933'];
    }

    return ['code' => 'CAS', 'name' => 'College of Arts and Sciences', 'color' => '#7600BC'];
}

$stmt = $conn->prepare("SELECT id, student_id, student_name, program, section, email FROM students WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($student = $result->fetch_assoc()) {
    $college = detectCollege($student['program']);

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
