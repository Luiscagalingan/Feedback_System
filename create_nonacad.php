<?php
// create_nonacad.php
include 'db_connection.php'; // your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<script src='shared-alerts.js'></script>";
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO non_academic_accounts (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $full_name, $email, $password);

    if ($stmt->execute()) {
        echo "<script>
          AppAlert.success('Account Created','Non-Academic account created successfully!').then(()=>window.location.href='admin_dashboard.html');
        </script>";
    } else {
        echo "<script>
          AppAlert.error('Account Creation Failed','Unable to create account. Email may already exist.').then(()=>window.history.back());
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
