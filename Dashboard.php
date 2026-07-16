<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "feedbackdata";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed.");
}

// Default name with debugging
$studentName = "Guest";
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    error_log("Fetching data for user_id: " . $user_id);  // Log for debugging
    $stmt = $conn->prepare("SELECT student_name FROM students WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $studentName = $row['student_name'];
        } else {
            error_log("No student found for user_id: " . $user_id);
        }
        $stmt->close();
    } else {
        error_log("Query preparation failed: " . $conn->error);
    }
} else {
    error_log("Session user_id is not set in dashboard.php");
}

// Initialize and fetch feedback percentages
$percentages = [
    "Positive Feedback" => 0,
    "Neutral Feedback" => 0,
    "Negative Feedback" => 0
];

$sql = "SELECT question_name, positive_feedback_percentage, neutral_feedback_percentage, negative_feedback_percentage FROM feedback_summary";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $totalPositive = 0;
    $totalNeutral = 0;
    $totalNegative = 0;
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $totalPositive += floatval($row["positive_feedback_percentage"] ?? 0);
        $totalNeutral += floatval($row["neutral_feedback_percentage"] ?? 0);
        $totalNegative += floatval($row["negative_feedback_percentage"] ?? 0);
        $count++;
    }
    if ($count > 0) {
        $percentages["Positive Feedback"] = round($totalPositive / $count, 2);
        $percentages["Neutral Feedback"] = round($totalNeutral / $count, 2);
        $percentages["Negative Feedback"] = round($totalNegative / $count, 2);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Feedback Satisfaction System - Dashboard</title>

  <style>
    /* (Your CSS remains the same) */
  </style>
</head>

<body>
  <nav class="navbar" id="navbar">
    <!-- (Your navbar HTML remains the same) -->
  </nav>

  <div id="dashboardPage">
    <div class="container">
      <div class="dashboard-header">
        <h1>Welcome back, <span id="studentName"><?php echo htmlspecialchars($studentName); ?></span>!</h1>
        <p>Here's an overview of the feedback sentiment analysis</p>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-value"><?php echo htmlspecialchars($percentages['Positive Feedback']); ?>%</div>
          <div class="stat-card-label">😊 Positive Feedback</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-value"><?php echo htmlspecialchars($percentages['Neutral Feedback']); ?>%</div>
          <div class="stat-card-label">😐 Neutral Feedback</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-value"><?php echo htmlspecialchars($percentages['Negative Feedback']); ?>%</div>
          <div class="stat-card-label">😞 Negative Feedback</div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("logoutBtn").addEventListener("click", () => AppAlert.logout('auth/logout.php'));
    document.getElementById("profileLink").addEventListener("click", () => {
      window.location.href = "Profile_System.html";
    });
  </script><script src="shared-alerts.js"></script>
</body>
</html>
