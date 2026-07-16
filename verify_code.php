<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

function respond(bool $success, string $message, int $status = 200): never
{
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(false, 'Invalid request method.', 405);
$otp = trim((string) ($_POST['otp'] ?? ''));
$reset = $_SESSION['password_reset'] ?? null;

if (!is_array($reset)) respond(false, 'Start a new password reset request.', 400);
if (($reset['expires_at'] ?? 0) < time()) {
    unset($_SESSION['password_reset']);
    respond(false, 'This code has expired. Request a new code.', 410);
}
if (($reset['attempts'] ?? 0) >= 5) {
    unset($_SESSION['password_reset']);
    respond(false, 'Too many incorrect attempts. Request a new code.', 429);
}

$_SESSION['password_reset']['attempts']++;
if (!preg_match('/^\d{6}$/', $otp) || !password_verify($otp, (string) $reset['otp_hash'])) {
    respond(false, 'The verification code is incorrect.', 422);
}

$_SESSION['password_reset']['verified'] = true;
$_SESSION['password_reset']['verified_at'] = time();
respond(true, 'Code verified.');
