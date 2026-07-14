<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'feedbackdata';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    exit('Database connection failed.');
}
