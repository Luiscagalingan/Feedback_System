<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedbackdata";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Fetch data from the correct table
$sql = "SELECT question_name, positive_feedback_percentage, neutral_feedback_percentage, negative_feedback_percentage FROM learning_feedback";
$result = $conn->query($sql);

$feedback = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
}

echo json_encode(["success" => true, "feedback" => $feedback]);

$conn->close();
?>
