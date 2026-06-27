<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'admin/dbinit.php';

// Initialize default percentages
$percentages = [
    "Positive Feedback" => 0,
    "Negative Feedback" => 0,
];

// Get role from query parameter
$role = $_GET['role'] ?? '';

// Define table mapping
$nonAcademicTables = [
    'Registrar_Feedback',
    'Non_Academic_Library_Feedback',
    'Student_Affairs_Feedback',
    'facility_feedback',
    'guidance_services_feedback',
    'health_services_feedback',
    'guard_feedback'
];

$academicTables = [
    'learning_feedback',
    'Curriculum_Feedback',
    'Assessment_Feedback',
    'academic_library_feedback',
    'Academic_Facilities_Feedback'
];

// Choose tables based on role
$tables = [];
if ($role === 'academic') {
    $tables = $academicTables;
} elseif ($role === 'nonacademic') {
    $tables = $nonAcademicTables;
} elseif ($role === 'admin') {
    $tables = array_merge($academicTables, $nonAcademicTables);
}

// Prepare aggregation variables
$rows = [];
$positiveTotal = 0;
$negativeTotal = 0;
$count = 0;
$reviewCount = 0;

$positiveReview = 0;
$negativeReview = 0;
$neutralReview  = 0;

// Loop through each relevant table and fetch data
foreach ($tables as $table) {
    $sql = "SELECT * FROM `$table`";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
            $positiveTotal += $row['positive_feedback_percentage'] ?? 0;
            $negativeTotal += $row['negative_feedback_percentage'] ?? 0;
            $count++;

            $reviewResult = strtolower($row['review_result'] ?? '');
            if (!empty($reviewResult)) {
                $reviewCount++;
                switch ($reviewResult) {
                    case 'positive':
                        $positiveReview++;
                        break;
                    case 'negative':
                        $negativeReview++;
                        break;
                    case 'neutral':
                        $neutralReview++;
                        break;
                }
            }
        }
    }
}

// Calculate feedback percentages
$percentages = [
    "Positive Feedback" => $count ? round($positiveTotal / $count, 2) : 0,
    "Negative Feedback" => $count ? round($negativeTotal / $count, 2) : 0
];

$totalReview = $reviewCount ?: 1;

$reviewPercentages = [
    "Positive Review" => round(($positiveReview / $totalReview) * 100, 2),
    "Negative Review" => round(($negativeReview / $totalReview) * 100, 2),
    "Neutral Review"  => round(($neutralReview  / $totalReview) * 100, 2)
];

$conn->close(); // Close the connection

// Return JSON response
echo json_encode([
    "success" => true,
    "role" => $role,
    "data" => [
        "percentages" => $percentages,
        "review" => $reviewPercentages,
        "rows" => $rows,
        "row_count" => $count,
        "review_count" => $reviewCount
    ]
]);
?>
