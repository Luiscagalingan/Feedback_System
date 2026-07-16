<?php
include 'admin/dbinit.php';
require_once __DIR__ . '/auth/access.php';
$session = require_roles(['admin', 'dean']);

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

    $sql = "UPDATE students SET student_id = ?, student_name = ?, program = ?, section = ?, email = ? WHERE id = ?" . ($session['role'] === 'dean' ? ' AND college = ?' : '');
    $stmt = $conn->prepare($sql);
    if ($session['role'] === 'dean') $stmt->bind_param("sssssis", $student_id, $student_name, $program, $section, $email, $id, $session['college']); else $stmt->bind_param("sssssi", $student_id, $student_name, $program, $section, $email, $id);

    if ($stmt->execute()) {
        audit_log($conn, 'student_updated', 'Updated student ' . $student_id . '.');
        $returnPage = $session['role'] === 'admin' ? 'admin_dashboard.html' : ($session['dashboard_path'] ?? 'index.html');
        echo "<script src='shared-alerts.js'></script><script>AppAlert.success('Student Updated','Student details updated successfully.').then(()=>window.location.href=" . json_encode($returnPage) . ");</script>";
    } else {
        echo "<script src='shared-alerts.js'></script><script>AppAlert.error('Update Failed'," . json_encode('Error updating record: ' . $stmt->error) . ").then(()=>window.history.back());</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
