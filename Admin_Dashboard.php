<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.html");
    exit;
}

readfile(__DIR__ . DIRECTORY_SEPARATOR . 'admin_dashboard.html');
?>
