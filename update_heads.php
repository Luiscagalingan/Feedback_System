<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: admin.php"); exit; }

$conn = new mysqli("localhost", "root", "", "feedbackdb");
$dean = $_POST['dean_name'];
$dean_email = $_POST['dean_email'];
$non_acad = $_POST['non_acad_head_name'];
$non_acad_email = $_POST['non_acad_email'];

$conn->query("UPDATE heads_info SET 
    dean_name='$dean', 
    dean_email='$dean_email', 
    non_acad_head_name='$non_acad', 
    non_acad_email='$non_acad_email' 
    WHERE id=1");

header("Location: admin.php");
exit;
?>
