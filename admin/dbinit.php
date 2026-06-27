<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedbackdata";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}
?>