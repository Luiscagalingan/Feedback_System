<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function respond(bool $success, string $message, int $status = 200): never
{
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.', 405);
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.', 422);
}

$stmt = $conn->prepare('SELECT id FROM students WHERE LOWER(email) = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    respond(false, 'No student account is registered with that email address.', 404);
}

$mailConfig = [];
$localConfig = __DIR__ . '/config/mail.php';
if (is_file($localConfig)) {
    $mailConfig = require $localConfig;
}

$gmailUser = (string) ($mailConfig['username'] ?? getenv('GMAIL_USERNAME') ?: '');
$gmailPassword = (string) ($mailConfig['app_password'] ?? getenv('GMAIL_APP_PASSWORD') ?: '');
$fromName = (string) ($mailConfig['from_name'] ?? 'PLP Feedback System');

if ($gmailUser === '' || $gmailPassword === '') {
    respond(false, 'Email sending is not configured. Add your Gmail address and app password in config/mail.php.', 503);
}

$otp = (string) random_int(100000, 999999);

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $gmailUser;
    $mail->Password = $gmailPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($gmailUser, $fromName);
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your password reset verification code';
    $mail->Body = '<h2>Password reset</h2><p>Your verification code is:</p><p style="font-size:28px;font-weight:bold;letter-spacing:6px">' . $otp . '</p><p>This code expires in 10 minutes. If you did not request it, you can ignore this email.</p>';
    $mail->AltBody = "Your password reset verification code is {$otp}. It expires in 10 minutes.";
    $mail->send();
} catch (Exception $exception) {
    error_log('Password reset email failed: ' . $mail->ErrorInfo);
    respond(false, 'The verification email could not be sent. Check the Gmail configuration and try again.', 502);
}

$_SESSION['password_reset'] = [
    'student_id' => (int) $student['id'],
    'email' => $email,
    'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
    'expires_at' => time() + 600,
    'attempts' => 0,
    'verified' => false,
];

respond(true, 'Verification code sent. Please check your Gmail inbox.');
