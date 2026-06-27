<?php
include 'admin/dbinit.php'; // Make sure this connects to your MySQL DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Securely hash password

    $query = "INSERT INTO academic_accounts (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $full_name, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Academic account created successfully!'); window.location.href='Admin_Dashboard.html';</script>";
    } else {
        echo "<script>alert('Error: Email already exists or failed to insert.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
