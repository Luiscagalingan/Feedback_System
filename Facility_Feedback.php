<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include 'admin/dbinit.php';

// Read JSON
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "message" => "Invalid JSON received."]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_primary_id = $_SESSION['user_id']; // numeric ID from login

// Fetch the REAL student_id string
$fetch = $conn->prepare("SELECT student_id FROM students WHERE id = ?");
$fetch->bind_param("i", $user_primary_id);
$fetch->execute();
$res = $fetch->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Student not found"]);
    exit;
}

$row = $res->fetch_assoc();
$student_id = $row['student_id'];  // string ID like 2023-00055

$classroom_cleanliness = intval($data['classroom_cleanliness']);
$hallway_cleanliness = intval($data['hallway_cleanliness']);
$facility_satisfaction = intval($data['facility_satisfaction']);
$additional_comments = trim($data['additional_comments']);

require __DIR__ . '/SentimentAnalysis/SentimentAnalyzer.php';
$sentimentType = getSentimentType($additional_comments);

// Insert
$stmt = $conn->prepare("
    INSERT INTO facility_feedback
    (student_id, classroom_cleanliness, hallway_cleanliness, facility_satisfaction,
     positive_feedback, negative_feedback, answer_text, review_result)
    VALUES (?, ?, ?, ?, '', '', ?, ?)
");

$stmt->bind_param(
    "siiiss",
    $student_id,
    $classroom_cleanliness,
    $hallway_cleanliness,
    $facility_satisfaction,
    $additional_comments,
    $sentimentType
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Feedback submitted"]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
