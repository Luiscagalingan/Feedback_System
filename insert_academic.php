<?php
include 'admin/dbinit.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    // LINE 10 - Force-swapped to academic_teachers
    $query = "INSERT INTO academic_teachers (full_name, email, password) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $full_name, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Academic account created successfully!'); window.location.href='Admin_Dashboard.html';</script>";
        } else {
            echo "<script>alert('Error: Email already exists or failed to insert.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "Database Error: " . $conn->error;
    }

    $conn->close();
}
?>