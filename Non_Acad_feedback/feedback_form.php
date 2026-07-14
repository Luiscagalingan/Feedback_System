<?php
$query = $_SERVER['QUERY_STRING'] ?? '';
header('Location: ../non_academic/feedback/feedback_form.php' . ($query ? '?' . $query : ''), true, 302);
exit;
