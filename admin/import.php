<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/access.php';
require_once __DIR__ . '/../auth/auth_helpers.php';
$session = require_roles(['dean']);

function import_error(string $message, int $status = 422): never {
    json_response(['success'=>false,'message'=>$message,'inserted'=>0,'skipped'=>0,'errors'=>[]], $status);
}

function csv_rows(string $path): array {
    $handle = fopen($path, 'rb');
    if (!$handle) import_error('The uploaded CSV file could not be opened.');
    $rows = [];
    while (($row = fgetcsv($handle)) !== false) $rows[] = $row;
    fclose($handle);
    return $rows;
}

function xlsx_rows(string $path): array {
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) import_error('The Excel file is invalid or corrupted.');
    $shared = [];
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml !== false) {
        $document = new DOMDocument();
        if (@$document->loadXML($sharedXml, LIBXML_NONET)) {
            $xpath = new DOMXPath($document);
            foreach ($xpath->query('//*[local-name()="si"]') as $item) {
                $text = '';
                foreach ($xpath->query('.//*[local-name()="t"]', $item) as $textNode) $text .= $textNode->textContent;
                $shared[] = $text;
            }
        }
    }
    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();
    if ($sheetXml === false) import_error('The Excel workbook does not contain a first worksheet.');
    $document = new DOMDocument();
    if (!@$document->loadXML($sheetXml, LIBXML_NONET)) import_error('The first Excel worksheet could not be read.');
    $xpath = new DOMXPath($document);
    $rows = [];
    foreach ($xpath->query('//*[local-name()="sheetData"]/*[local-name()="row"]') as $row) {
        $values = [];
        foreach ($xpath->query('./*[local-name()="c"]', $row) as $cell) {
            $ref = $cell->getAttribute('r');
            preg_match('/^[A-Z]+/', $ref, $match);
            $letters = $match[0] ?? 'A'; $index = 0;
            for ($i=0; $i<strlen($letters); $i++) $index = $index * 26 + (ord($letters[$i]) - 64);
            $index--;
            $type = $cell->getAttribute('t');
            $valueNode = $xpath->query('./*[local-name()="v"]', $cell)->item(0);
            if ($type === 'inlineStr') {
                $value = '';
                foreach ($xpath->query('.//*[local-name()="is"]//*[local-name()="t"]', $cell) as $textNode) $value .= $textNode->textContent;
            } else {
                $value = $valueNode ? $valueNode->textContent : '';
            }
            if ($type === 's') $value = $shared[(int)$value] ?? '';
            $values[$index] = $value;
        }
        if ($values) { ksort($values); $rows[] = array_replace(array_fill(0, max(array_keys($values))+1, ''), $values); }
    }
    return $rows;
}

if (empty($_FILES['student_file']['tmp_name']) || !is_uploaded_file($_FILES['student_file']['tmp_name'])) import_error('Choose a CSV or Excel file to upload.');
if ((int)($_FILES['student_file']['size'] ?? 0) < 1) import_error('The uploaded file is empty.');
if ((int)($_FILES['student_file']['size'] ?? 0) > 5 * 1024 * 1024) import_error('The upload must not exceed 5 MB.');
$extension = strtolower(pathinfo((string)$_FILES['student_file']['name'], PATHINFO_EXTENSION));
if (!in_array($extension, ['csv','xlsx'], true)) import_error('Only .csv and .xlsx files are accepted.');
$rows = $extension === 'xlsx' ? xlsx_rows($_FILES['student_file']['tmp_name']) : csv_rows($_FILES['student_file']['tmp_name']);
if (!$rows) import_error('The uploaded file contains no rows.');

$headers = array_map(fn($value)=>strtolower(trim((string)$value, " \t\n\r\0\x0B\xEF\xBB\xBF")), array_shift($rows));
$required = ['student_id','student_name','program','section','email','password'];
$missing = array_values(array_diff($required, $headers));
if ($missing) import_error('Missing required column(s): '.implode(', ', $missing).'.');
$columns = array_flip($headers);
$college = (string)$session['college'];
$programStmt = $conn->prepare("SELECT DISTINCT program FROM students WHERE college=? AND program<>'' ORDER BY program");
$programStmt->bind_param('s',$college);$programStmt->execute();
$allowedPrograms = array_column($programStmt->get_result()->fetch_all(MYSQLI_ASSOC),'program');
if (!$allowedPrograms) import_error('No valid programs are configured for your assigned college.');
$existingIds=[];$existingEmails=[];
foreach($conn->query('SELECT student_id,email FROM students')->fetch_all(MYSQLI_ASSOC) as $existing){$existingIds[strtolower($existing['student_id'])]=true;$existingEmails[strtolower($existing['email'])]=true;}

$valid=[];$errors=[];$seenIds=[];$seenEmails=[];$rowNumber=1;
foreach ($rows as $row) {
    $rowNumber++;
    if (!array_filter($row, fn($value)=>trim((string)$value)!=='')) continue;
    $value = fn(string $key): string => trim((string)($row[$columns[$key]] ?? ''));
    $studentId=$value('student_id');$name=$value('student_name');$program=$value('program');$section=$value('section');$email=strtolower($value('email'));$password=$value('password');
    $fileCollege = isset($columns['college']) ? strtoupper($value('college')) : '';
    $reasons=[];
    if (!preg_match('/^23-\d{5}$/',$studentId)) $reasons[]='student_id must use 23-00000 format';
    if ($name===''||strlen($name)>100) $reasons[]='student_name is required and must be at most 100 characters';
    if (!in_array($program,$allowedPrograms,true)) $reasons[]='program is not assigned to '.$college;
    if ($section===''||strlen($section)>50) $reasons[]='section is required and must be at most 50 characters';
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>100) $reasons[]='email format is invalid';
    if (strlen($password)<8) $reasons[]='password must contain at least 8 characters';
    if ($fileCollege!=='' && normalize_college($fileCollege)!==$college) $reasons[]='college does not match the Dean assigned college '.$college;
    $idKey=strtolower($studentId);
    if (isset($existingIds[$idKey])||isset($seenIds[$idKey])) $reasons[]='student_id already exists or is duplicated in the file';
    if (isset($existingEmails[$email])||isset($seenEmails[$email])) $reasons[]='email already exists or is duplicated in the file';
    if ($reasons) {$errors[]=['row'=>$rowNumber,'student_id'=>$studentId,'reasons'=>$reasons];continue;}
    $seenIds[$idKey]=true;$seenEmails[$email]=true;
    $valid[]=[$studentId,$name,$program,$section,$email,password_hash($password,PASSWORD_DEFAULT),$college];
}
if (!$valid && !$errors) import_error('The file contains headers but no student data rows.');

$inserted=0;$stmt=$conn->prepare("INSERT INTO students(student_id,student_name,program,section,email,password,college,status) VALUES(?,?,?,?,?,?,?,'active')");
$conn->begin_transaction();
try {
    foreach($valid as $record){[$studentId,$name,$program,$section,$email,$hash,$assignedCollege]=$record;$stmt->bind_param('sssssss',$studentId,$name,$program,$section,$email,$hash,$assignedCollege);$stmt->execute();$inserted++;}
    $conn->commit();
} catch(Throwable $exception) {
    $conn->rollback();
    import_error('Import failed and no students were inserted. Please check the file and try again.',500);
}
$skipped=count($errors);
audit_log($conn,'student_import',"Imported $inserted students; skipped $skipped rows in $college.");
json_response(['success'=>true,'message'=>"Import complete: $inserted added, $skipped skipped.",'inserted'=>$inserted,'skipped'=>$skipped,'errors'=>$errors,'college'=>$college,'allowed_programs'=>$allowedPrograms]);
