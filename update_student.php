<?php
include 'admin/dbinit.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $program = $_POST['program'];
    $section = $_POST['section'];
    $email = $_POST['email'];

    // Basic validation
    if (empty($id) || empty($student_id) || empty($student_name) || empty($program) || empty($section) || empty($email)) {
        die("Error: All fields are required.");
    }

    $stmt = $conn->prepare("UPDATE students SET student_id = ?, student_name = ?, program = ?, section = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $student_id, $student_name, $program, $section, $email, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Student details updated successfully!'); window.location.href='Acad_Dashboard.html';</script>";
    } else {
        echo "<script>alert('Error updating record: " . htmlspecialchars($stmt->error) . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>