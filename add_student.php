<?php
session_start();
include 'admin/dbinit.php';

// Check form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id   = trim($_POST['student_id']);
    $student_name = trim($_POST['student_name']);
    $program_section = trim($_POST['program']);
    $email        = trim($_POST['email']);
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Split 'BSIT 1A' into 'BSIT' and '1A'
    list($program, $section) = sscanf($program_section, "%s %s");

    // Check for duplicate
    $stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
    $stmt->bind_param("ss", $student_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Student ID or Email already exists'); window.history.back();</script>";
        $stmt->close();
        $conn->close();
        exit;
    }

    // Insert new student with separate program and section
    $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, program, section, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $student_id, $student_name, $program, $section, $email, $password);

    if ($stmt->execute()) {
        // Determine which dashboard to return to
        $returnPage = 'Acad_Dashboard.html'; // Default
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $returnPage = 'admin_dashboard.html';
        }
        echo "<script>alert('Student added successfully!'); window.location.href='$returnPage';</script>";
    } else {
        echo "<script>alert('Failed to add student'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
