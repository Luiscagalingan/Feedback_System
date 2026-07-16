<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/access.php';
$session=require_roles(['dean']);
$college=(string)$session['college'];
$stmt=$conn->prepare("SELECT program FROM students WHERE college=? AND program<>'' GROUP BY program ORDER BY program LIMIT 1");
$stmt->bind_param('s',$college);$stmt->execute();
$program=(string)($stmt->get_result()->fetch_assoc()['program']??'VALID COLLEGE PROGRAM');
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$college.'_student_import_template.csv"');
echo "\xEF\xBB\xBF";
$out=fopen('php://output','wb');
fputcsv($out,['student_id','student_name','program','section','email','password','college']);
fputcsv($out,['23-12345','Juan Dela Cruz',$program,'1A','juan.delacruz@example.edu.ph','ChangeMe123!',$college]);
fclose($out);
