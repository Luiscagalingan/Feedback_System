<?php
// signup.php

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedbackdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only run when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userType = $_POST['userType']; // Student, Academic, or Non-Academic
    $student_id = isset($_POST['studentId']) ? trim($_POST['studentId']) : null;
    $name = trim($_POST['studentName']);
    $program = isset($_POST['program']) ? trim($_POST['program']) : null;
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Determine which table to use based on user type
    if ($userType === "Student") {
        $table = "students";
        $id_column = "student_id";

        // Check for duplicate Student ID
        $check = $conn->prepare("SELECT * FROM $table WHERE $id_column = ?");
        $check->bind_param("s", $student_id);
    } elseif ($userType === "Academic Teacher") {
        $table = "academic_teachers";
        $check = $conn->prepare("SELECT * FROM $table WHERE email = ?");
        $check->bind_param("s", $email);
    } elseif ($userType === "Non-Academic Teacher") {
        $table = "non_academic_teachers";
        $check = $conn->prepare("SELECT * FROM $table WHERE email = ?");
        $check->bind_param("s", $email);
    } else {
        echo "<script>alert('Invalid user type.'); window.history.back();</script>";
        exit;
    }

    // Run duplicate check
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Account already exists! Please use a different email or ID.'); window.history.back();</script>";
    } else {
        // Insert into correct table
        if ($userType === "Student") {
            $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, program, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $student_id, $name, $program, $email, $hashed_password);
        } elseif ($userType === "Academic Teacher") {
            $stmt = $conn->prepare("INSERT INTO academic_teachers (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
        } elseif ($userType === "Non-Academic Teacher") {
            $stmt = $conn->prepare("INSERT INTO non_academic_teachers (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
        }

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('Error: Unable to register. Please try again.'); window.history.back();</script>";
        }

        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>
