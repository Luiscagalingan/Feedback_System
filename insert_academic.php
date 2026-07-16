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
            echo "<script src='shared-alerts.js'></script><script>AppAlert.success('Account Created','Academic account created successfully.').then(()=>window.location.href='admin_dashboard.html');</script>";
        } else {
            echo "<script src='shared-alerts.js'></script><script>AppAlert.error('Account Creation Failed','Email already exists or the record could not be saved.').then(()=>window.history.back());</script>";
        }
        $stmt->close();
    } else {
        echo "Database Error: " . $conn->error;
    }

    $conn->close();
}
?>
