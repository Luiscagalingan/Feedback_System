<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/access.php';
require_roles(['admin']);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $deans = $conn->query("SELECT id, full_name name, email, CASE WHEN college='BSA' THEN 'CBA' ELSE college END college, program, CONCAT(UCASE(LEFT(status,1)),SUBSTRING(status,2)) status FROM academic_teachers WHERE status <> 'archived' ORDER BY full_name")->fetch_all(MYSQLI_ASSOC);
    $staff = $conn->query("SELECT id, full_name name, email, office_key office, office_category category, service, CONCAT(UCASE(LEFT(status,1)),SUBSTRING(status,2)) status FROM non_academic_teachers WHERE status <> 'archived' ORDER BY full_name")->fetch_all(MYSQLI_ASSOC);
    $logs = $conn->query("SELECT user_id, created_at date, user_name user, role, action, module, status, details FROM audit_logs ORDER BY created_at DESC LIMIT 500")->fetch_all(MYSQLI_ASSOC);
    json_response(['success'=>true, 'deans'=>$deans, 'staff'=>$staff, 'audit_logs'=>$logs]);
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$requestedType = (string) ($input['type'] ?? $_GET['type'] ?? '');
$type = $requestedType === 'dean' ? 'dean' : ($requestedType === 'staff' ? 'staff' : '');
if ($type === '') json_response(['success'=>false,'message'=>'Invalid account type.'],422);
$table = $type === 'dean' ? 'academic_teachers' : 'non_academic_teachers';
$id = (int) ($input['id'] ?? $_GET['id'] ?? 0);

if ($method === 'DELETE') {
    if ($id < 1) json_response(['success'=>false,'message'=>'Invalid account.'],422);
    $lookup=$conn->prepare("SELECT full_name,email FROM `$table` WHERE id=?");$lookup->bind_param('i',$id);$lookup->execute();$account=$lookup->get_result()->fetch_assoc();
    if(!$account)json_response(['success'=>false,'message'=>'Account not found.'],404);
    $stmt=$conn->prepare("DELETE FROM `$table` WHERE id=?"); $stmt->bind_param('i',$id); $stmt->execute();
    audit_log($conn,'account_deleted',$type.' account deleted: '.$account['full_name'].' <'.$account['email'].'>.'); json_response(['success'=>true,'message'=>'Account deleted successfully.']);
}

if ($method !== 'POST') json_response(['success'=>false,'message'=>'Method not allowed.'],405);
$name=trim((string)($input['name']??'')); $email=strtolower(trim((string)($input['email']??'')));
$status=strtolower((string)($input['status']??'active')); if(!in_array($status,['active','inactive'],true))$status='active';
if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)) json_response(['success'=>false,'message'=>'Valid name and email are required.'],422);
$password=trim((string)($input['password']??''));if($password!==''&&strlen($password)<8)json_response(['success'=>false,'message'=>'Password must be at least 8 characters.'],422);
$duplicate=$conn->prepare("SELECT id FROM `$table` WHERE email=? AND id<>? LIMIT 1");$duplicate->bind_param('si',$email,$id);$duplicate->execute();if($duplicate->get_result()->fetch_assoc())json_response(['success'=>false,'message'=>'That email address is already assigned to another account.'],409);
if ($type === 'dean') {
    $college=normalize_college_code((string)($input['college']??'')); if($college==='')json_response(['success'=>false,'message'=>'Valid college is required.'],422);
    if($id&&$password!==''){$hash=password_hash($password,PASSWORD_DEFAULT);$stmt=$conn->prepare('UPDATE academic_teachers SET full_name=?,email=?,college=?,program=NULL,status=?,password=? WHERE id=?');$stmt->bind_param('sssssi',$name,$email,$college,$status,$hash,$id);}
    elseif($id){$stmt=$conn->prepare('UPDATE academic_teachers SET full_name=?,email=?,college=?,program=NULL,status=? WHERE id=?');$stmt->bind_param('ssssi',$name,$email,$college,$status,$id);}
    else{$initial=$password!==''?$password:'ChangeMe123!';$hash=password_hash($initial,PASSWORD_DEFAULT);$stmt=$conn->prepare('INSERT INTO academic_teachers(full_name,email,college,program,password,status)VALUES(?,?,?,NULL,?,?)');$stmt->bind_param('sssss',$name,$email,$college,$hash,$status);}
} else {
    $office=trim((string)($input['office']??''));if($office==='')json_response(['success'=>false,'message'=>'Assigned office is required.'],422);
    $academicOffices=['library','college-enrollment','teacher-consultation'];$nonAcademicOffices=['registrar','guidance','clinic','accounting','student-organizations','security','mis','admission','student-services','vp-administration-finance'];
    if(!in_array($office,array_merge($academicOffices,$nonAcademicOffices),true))json_response(['success'=>false,'message'=>'Select a valid service office.'],422);
    $category=in_array($office,$academicOffices,true)?'academic':'nonacademic';
    $serviceNames=['library'=>'Library Office','college-enrollment'=>'College Enrollment Offices','teacher-consultation'=>'Faculty / Teacher Consultation Services','registrar'=>"Registrar's Office",'guidance'=>'Guidance Office','clinic'=>'Clinic Office','accounting'=>'Accounting Office','student-organizations'=>'Office of Student Organizations','security'=>'Security Office','mis'=>'Management Information Systems (MIS) Office','admission'=>'Admission Services Office','student-services'=>'Student Services Office (SSO)','vp-administration-finance'=>'Office of the VP for Administration and Finance'];
    $service=$serviceNames[$office];
    $officeCheck=$conn->prepare('SELECT id FROM non_academic_teachers WHERE office_key=? AND id<>? LIMIT 1');$officeCheck->bind_param('si',$office,$id);$officeCheck->execute();if($officeCheck->get_result()->fetch_assoc())json_response(['success'=>false,'message'=>'That service office already has an assigned account.'],409);
    if($id&&$password!==''){$hash=password_hash($password,PASSWORD_DEFAULT);$stmt=$conn->prepare('UPDATE non_academic_teachers SET full_name=?,email=?,office_key=?,office_category=?,service=?,status=?,password=? WHERE id=?');$stmt->bind_param('sssssssi',$name,$email,$office,$category,$service,$status,$hash,$id);}
    elseif($id){$stmt=$conn->prepare('UPDATE non_academic_teachers SET full_name=?,email=?,office_key=?,office_category=?,service=?,status=? WHERE id=?');$stmt->bind_param('ssssssi',$name,$email,$office,$category,$service,$status,$id);}
    else{$initial=$password!==''?$password:'ChangeMe123!';$hash=password_hash($initial,PASSWORD_DEFAULT);$stmt=$conn->prepare('INSERT INTO non_academic_teachers(full_name,email,office_key,office_category,service,password,status)VALUES(?,?,?,?,?,?,?)');$stmt->bind_param('sssssss',$name,$email,$office,$category,$service,$hash,$status);}
}
try{$stmt->execute();}catch(mysqli_sql_exception $e){json_response(['success'=>false,'message'=>'Email already exists or the data is invalid.'],409);}
audit_log($conn,$id?'account_updated':'account_created',$type.' account: '.$email);
json_response(['success'=>true,'message'=>$id?'Account updated.':('Account created. '.($password===''?'Default password: ChangeMe123!':'The selected password is active.'))]);

function normalize_college_code(string $value): string {
    $value=strtoupper(trim($value)); return ['CBA'=>'BSA','BSA'=>'BSA','CAS'=>'CAS','CCS'=>'CCS','CIHM'=>'CIHM','COE'=>'COE','COED'=>'COED','CON'=>'CON'][$value]??'';
}
