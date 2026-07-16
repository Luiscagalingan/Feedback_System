<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/auth/access.php';
$session = require_roles(['dean']);
$college = (string)$session['college'];
$stmt = $conn->prepare("SELECT DISTINCT program FROM students WHERE college=? AND TRIM(program)<>'' ORDER BY program");
$stmt->bind_param('s', $college); $stmt->execute();
$programs = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'program');
json_response(['success'=>true,'college'=>$college,'programs'=>$programs]);
