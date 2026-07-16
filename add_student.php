<?php
include 'admin/dbinit.php';
require_once __DIR__ . '/auth/access.php';
$session = require_roles(['admin', 'dean']);

// Check form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id   = trim($_POST['student_id']);
    $student_name = trim($_POST['student_name']);
    $program_section = trim($_POST['program']);
    $email        = trim($_POST['email']);
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $college = $session['role'] === 'dean' ? $session['college'] : strtoupper(trim((string) ($_POST['college'] ?? '')));
    if ($college === '') json_response(['success'=>false,'message'=>'College is required.'],422);

    // Split 'BSIT 1A' into 'BSIT' and '1A'
    list($program, $section) = sscanf($program_section, "%s %s");

    // Check for duplicate
    $stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
    $stmt->bind_param("ss", $student_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script src='shared-alerts.js'></script><script>AppAlert.error('Duplicate Student','Student ID or email already exists.').then(()=>window.history.back());</script>";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Insert new student with separate program and section
    $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, program, section, email, password, college, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param("sssssss", $student_id, $student_name, $program, $section, $email, $password, $college);

    if ($stmt->execute()) {
        audit_log($conn, 'student_created', 'Created student ' . $student_id . '.');
        // Determine which dashboard to return to
        $returnPage = $session['role'] === 'admin' ? 'admin_dashboard.html#students' : ($session['dashboard_path'] ?? 'index.html');
        echo "<script src='shared-alerts.js'></script><script>AppAlert.success('Student Added','Student added successfully.').then(()=>window.location.href=" . json_encode($returnPage) . ");</script>";
    } else {
        echo "<script src='shared-alerts.js'></script><script>AppAlert.error('Add Failed','Failed to add student.').then(()=>window.history.back());</script>";
    }

    $stmt->close();
}

$conn->close();
?>
