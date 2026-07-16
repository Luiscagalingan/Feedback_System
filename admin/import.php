<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/access.php';
$session=require_roles(['admin','dean']);
header('Content-Type: text/plain; charset=utf-8');
if(empty($_FILES['csv_file']['tmp_name'])||!is_uploaded_file($_FILES['csv_file']['tmp_name'])){http_response_code(422);exit('No valid CSV file uploaded.');}
$handle=fopen($_FILES['csv_file']['tmp_name'],'rb');if(!$handle){http_response_code(422);exit('Could not open the CSV file.');}
fgetcsv($handle);$inserted=0;$skipped=0;
$stmt=$conn->prepare("INSERT INTO students(student_id,student_name,program,section,email,password,college,status) VALUES(?,?,?,?,?,?,?,'active')");
$conn->begin_transaction();
try{
 while(($data=fgetcsv($handle))!==false){
  if(count($data)<6){$skipped++;continue;}
  [$studentId,$name,$program,$section,$email,$plain]=$data;
  $college=$session['role']==='dean'?$session['college']:strtoupper(trim((string)($data[6]??'')));
  if(trim($studentId)===''||trim($name)===''||!filter_var(trim($email),FILTER_VALIDATE_EMAIL)||$college===''){$skipped++;continue;}
  $hash=password_hash((string)$plain,PASSWORD_DEFAULT);$stmt->bind_param('sssssss',$studentId,$name,$program,$section,$email,$hash,$college);
  try{$stmt->execute();$inserted++;}catch(mysqli_sql_exception $e){$skipped++;}
 }
 $conn->commit();audit_log($conn,'student_import','Imported '.$inserted.' students; skipped '.$skipped.'.');
}catch(Throwable $e){$conn->rollback();http_response_code(500);exit('Import failed; no rows were committed.');}
fclose($handle);echo "CSV import complete: $inserted added, $skipped skipped.";
