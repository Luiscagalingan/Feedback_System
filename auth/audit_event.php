<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/access.php';
require_roles(['admin','dean','nonacademic']);
$input=json_decode(file_get_contents('php://input'),true)?:$_POST;
$action=(string)($input['action']??'');
if(!in_array($action,['report_generated','records_exported'],true))json_response(['success'=>false,'message'=>'Invalid audit event.'],422);
audit_log($conn,$action,substr(trim((string)($input['details']??'')),0,500));
json_response(['success'=>true]);
