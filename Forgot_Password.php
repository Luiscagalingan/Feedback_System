<?php
session_start();

function send_json_response($success, $message) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedbackdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    send_json_response(false, 'Database connection failed.');
}

// PHPMailer
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method.');
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json_response(false, 'Please provide a valid email address.');
}

// ✅ Correct DB column
$sql = "SELECT student_id FROM students WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    send_json_response(true, 'If an account with that email exists, a verification code has been sent.');
}

// Generate OTP
$otp = rand(100000, 999999);

// ✅ Correct update query
$update_stmt = $conn->prepare("UPDATE students SET otp = ? WHERE email = ?");
$update_stmt->bind_param("ss", $otp, $email);
$update_stmt->execute();

// Send Email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'YOUR_GMAIL@gmail.com';
    $mail->Password = 'YOUR_APP_PASSWORD';

    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('YOUR_GMAIL@gmail.com', 'Feedback System');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Password Reset OTP";
    $mail->Body = "<h2>Your OTP is: <b>$otp</b></h2><p>This code is valid for 10 minutes.</p>";

    $mail->send();

    send_json_response(true, 'Verification code sent successfully.');

} catch (Exception $e) {
    send_json_response(false, "Mailer Error: {$mail->ErrorInfo}");
}
?>
