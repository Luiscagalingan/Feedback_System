<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/access.php';
$session=require_roles(['dean']);
$input=json_decode(file_get_contents('php://input'),true)?:[];
$id=(int)($input['id']??0);$studentId=trim((string)($input['student_id']??''));$name=trim((string)($input['student_name']??''));$program=trim((string)($input['program']??''));$section=trim((string)($input['section']??''));$email=strtolower(trim((string)($input['email']??'')));
if($studentId===''||$name===''||$program===''||$section===''||!filter_var($email,FILTER_VALIDATE_EMAIL))json_response(['success'=>false,'message'=>'Complete all student fields with a valid email.'],422);
try{
 if($id){$stmt=$conn->prepare('UPDATE students SET student_id=?,student_name=?,program=?,section=?,email=? WHERE id=? AND college=?');$stmt->bind_param('sssssis',$studentId,$name,$program,$section,$email,$id,$session['college']);$action='student_updated';}
 else{$password=(string)($input['password']??'');if(strlen($password)<8)json_response(['success'=>false,'message'=>'Temporary password must be at least 8 characters.'],422);$hash=password_hash($password,PASSWORD_DEFAULT);$stmt=$conn->prepare("INSERT INTO students(student_id,student_name,program,section,email,password,college,status)VALUES(?,?,?,?,?,?,?,'active')");$stmt->bind_param('sssssss',$studentId,$name,$program,$section,$email,$hash,$session['college']);$action='student_created';}
 $stmt->execute();if($id&&$stmt->affected_rows<1)json_response(['success'=>false,'message'=>'Student was not found in your college or no changes were made.'],404);audit_log($conn,$action,$studentId.' in '.$session['college']);json_response(['success'=>true,'message'=>$id?'Student updated successfully.':'Student created successfully.']);
}catch(mysqli_sql_exception $e){json_response(['success'=>false,'message'=>'Student ID or email already exists.'],409);}
