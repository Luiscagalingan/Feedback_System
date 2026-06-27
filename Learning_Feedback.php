<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include 'admin/dbinit.php';

// Read JSON from AJAX
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No data received"]);
    exit;
}

// Extract variables safely
$student_id = $data['student_id'] ?? '';
$question_name = $data['question_name'] ?? '';
$questions_score = floatval($data['questions_score'] ?? 0);
$positive_feedback_percentage = $questions_score;
$neutral_feedback_percentage = 0.00;
$negative_feedback_percentage = 0.00;
$answer_text = trim($data['additional_comments'] ?? '');
$review = $data['review'] ?? '';

// Sentiment Analyzer
require __DIR__ . '/SentimentAnalysis/SentimentAnalyzer.php';
$sentimentType = getSentimentType($answer_text);

// Insert feedback
$stmt = $conn->prepare("
    INSERT INTO learning_feedback
    (student_id, question_name, positive_feedback_percentage, neutral_feedback_percentage, negative_feedback_percentage, answer_text, review_result)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssdddss",
    $student_id,
    $question_name,
    $positive_feedback_percentage,
    $neutral_feedback_percentage,
    $negative_feedback_percentage,
    $answer_text,
    $sentimentType
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Feedback submitted successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Database insert failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
