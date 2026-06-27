<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedbackdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Detect feedback type
$category = $_POST['category'] ?? 'learning';

// Common fields
$positive = $_POST['positive_feedback'] ?? '';
$neutral = $_POST['neutral_feedback'] ?? '';
$negative = $_POST['negative_feedback'] ?? '';
$additional = $_POST['additional_comments'] ?? '';
$email = $_POST['email'] ?? null;

// Learning feedback fields
$understanding = $_POST['understanding'] ?? null;
$clarity = $_POST['clarity'] ?? null;
$satisfaction = $_POST['satisfaction'] ?? null;

// Guard feedback fields
$daytime_safety = $_POST['daytime_safety'] ?? null;
$patrol_effectiveness = $_POST['patrol_effectiveness'] ?? null;
$overall_satisfaction = $_POST['overall_satisfaction'] ?? null;

$sql = "INSERT INTO feedback 
(category, understanding, clarity, satisfaction, daytime_safety, patrol_effectiveness, overall_satisfaction, positive_feedback, neutral_feedback, negative_feedback, additional_comments, email) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssississssss",
    $category,
    $understanding,
    $clarity,
    $satisfaction,
    $daytime_safety,
    $patrol_effectiveness,
    $overall_satisfaction,
    $positive,
    $neutral,
    $negative,
    $additional,
    $email
);

if ($stmt->execute()) { 
    echo "success";
} else {
    echo "error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
