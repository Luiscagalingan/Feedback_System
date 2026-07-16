<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth/access.php';
$session=require_roles(['admin','dean']);
$id=(int)($_GET['id']??0);if($id<1)json_response(['success'=>false,'message'=>'Invalid student record.'],422);
$sql="UPDATE students SET status='active', archived_at=NULL WHERE id=? AND status='archived'".($session['role']==='dean'?' AND college=?':'');
$stmt=$conn->prepare($sql);if($session['role']==='dean')$stmt->bind_param('is',$id,$session['college']);else$stmt->bind_param('i',$id);$stmt->execute();
if($stmt->affected_rows<1)json_response(['success'=>false,'message'=>'Archived student was not found in your college.'],404);
audit_log($conn,'student_reactivated','Student record ID '.$id.' reactivated in '.($session['college']??'system-wide').' scope.');
json_response(['success'=>true,'message'=>'Student reactivated successfully.']);
