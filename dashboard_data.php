<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/auth/access.php';
$session = require_roles(['admin', 'dean', 'nonacademic']);

$where = ["f.status = 'submitted'"];
$types = '';
$values = [];
if ($session['role'] === 'dean') {
    $where[] = "f.category = 'academic'";
    $where[] = 's.college = ?';
    $types .= 's'; $values[] = $session['college'];
} elseif ($session['role'] === 'nonacademic') {
    $where[] = 'f.category = ?';
    $types .= 's'; $values[] = ($session['office_category'] ?? 'nonacademic') === 'academic' ? 'academic' : 'nonacademic';
    if (($session['office_key'] ?? 'all') !== 'all') {
        $where[] = 'f.office_key = ?';
        $types .= 's'; $values[] = $session['office_key'];
    }
}

$sql = "SELECT f.id, f.student_id, s.student_name, s.program, s.section, s.college,
               f.category, f.office_key, f.office_name, f.section_title AS question_name,
               f.rating_average, f.positive_feedback_percentage, f.neutral_feedback_percentage,
               f.negative_feedback_percentage, f.answer_text, f.review_result,
               f.created_at, f.created_at AS last_updated
        FROM office_feedback f
        INNER JOIN students s ON s.student_id = f.student_id
        WHERE " . implode(' AND ', $where) . ' ORDER BY f.created_at DESC, f.id DESC';
$stmt = $conn->prepare($sql);
if ($types !== '') $stmt->bind_param($types, ...$values);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$count = count($rows); $positiveTotal = 0.0; $negativeTotal = 0.0;
$reviews = ['positive'=>0, 'negative'=>0, 'neutral'=>0];
foreach ($rows as $row) {
    $positiveTotal += (float) $row['positive_feedback_percentage'];
    $negativeTotal += (float) $row['negative_feedback_percentage'];
    $sentiment = strtolower((string) $row['review_result']);
    if (isset($reviews[$sentiment])) $reviews[$sentiment]++;
}
$reviewCount = array_sum($reviews);
json_response(['success'=>true, 'role'=>$session['role'], 'data'=>[
    'percentages'=>['Positive Feedback'=>$count ? round($positiveTotal/$count,2):0, 'Negative Feedback'=>$count ? round($negativeTotal/$count,2):0],
    'review'=>['Positive Review'=>$reviewCount ? round($reviews['positive']/$reviewCount*100,2):0, 'Negative Review'=>$reviewCount ? round($reviews['negative']/$reviewCount*100,2):0, 'Neutral Review'=>$reviewCount ? round($reviews['neutral']/$reviewCount*100,2):0],
    'rows'=>$rows, 'row_count'=>$count, 'review_count'=>$reviewCount
]]);
