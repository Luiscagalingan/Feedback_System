<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedbackdata";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ========== LOGIN ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: admin.php");
            exit;
        } else {
            echo "<script src='shared-alerts.js'></script><script>AppAlert.error('Login Failed','Invalid password.').then(()=>window.history.back());</script>";
            exit;
        }
    } else {
        echo "<script src='shared-alerts.js'></script><script>AppAlert.error('Login Failed','Admin account not found.').then(()=>window.history.back());</script>";
        exit;
    }
}

// ========== LOGOUT ==========
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// ========== SHOW LOGIN IF NOT LOGGED IN ==========
if (!isset($_SESSION['admin'])):
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f9;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
input {
    display: block;
    width: 100%;
    margin: 10px 0;
    padding: 10px;
}
button {
    width: 100%;
    padding: 10px;
    background: #0066cc;
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
}
</style>
</head>
<body>
<form method="POST" action="">
    <h2>Admin Login</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
</body>
</html>
<?php
exit;
endif;

// ========== FETCH DASHBOARD DATA ==========
$guard = $conn->query("SELECT AVG(positive_feedback_percentage) AS pos, AVG(neutral_feedback_percentage) AS neu, AVG(negative_feedback_percentage) AS neg FROM guard_feedback")->fetch_assoc();
$learning = $conn->query("SELECT AVG(positive_feedback_percentage) AS pos, AVG(neutral_feedback_percentage) AS neu, AVG(negative_feedback_percentage) AS neg FROM learning_feedback")->fetch_assoc();
$summary = $conn->query("SELECT AVG(positive_feedback_percentage) AS pos FROM feedback_summary")->fetch_assoc();

$overall_positive = round(($guard['pos'] + $learning['pos'] + $summary['pos']) / 3, 2);
$overall_neutral   = round(($guard['neu'] + $learning['neu']) / 2, 2);
$overall_negative  = round(($guard['neg'] + $learning['neg']) / 2, 2);

// ========== FETCH STUDENTS ==========
$students = $conn->query("SELECT id, student_name, profile_picture FROM students ORDER BY id DESC");

// ========== FETCH HEADS ==========
$heads = $conn->query("SELECT * FROM heads_info LIMIT 1"); // a table with dean_name, dean_email, non_acad_head_name, non_acad_email
$headData = $heads ? $heads->fetch_assoc() : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f4f4f9;
  margin: 0;
  padding: 20px;
}
h1 { text-align: center; }
.card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  margin-bottom: 20px;
  max-width: 1000px;
  margin: 20px auto;
}
table { width: 100%; border-collapse: collapse; }
th, td {
  text-align: center;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}
th { background: #0066cc; color: white; }
.logout {
  display: block;
  text-align: center;
  margin: 10px auto;
  color: #0066cc;
  text-decoration: none;
}
.logout:hover { text-decoration: underline; }
input, button {
  padding: 8px; border-radius: 5px; border: 1px solid #ccc;
}
button {
  background: #007bff; color: white; border: none; cursor: pointer;
}
button:hover { background: #0056b3; }
img.profile {
  width: 50px; height: 50px; border-radius: 50%;
}
</style>
</head>
<body>

<h1>📊 Admin Dashboard</h1>
<a class="logout" href="?logout=true">Logout</a>

<div class="card">
  <h2>Overall Feedback Summary</h2>
  <table>
    <tr><th>Category</th><th>Positive</th><th>Neutral</th><th>Negative</th></tr>
    <tr><td>Guard Feedback</td><td><?=round($guard['pos'],2)?></td><td><?=round($guard['neu'],2)?></td><td><?=round($guard['neg'],2)?></td></tr>
    <tr><td>Learning Feedback</td><td><?=round($learning['pos'],2)?></td><td><?=round($learning['neu'],2)?></td><td><?=round($learning['neg'],2)?></td></tr>
    <tr><td>Summary Feedback</td><td><?=round($summary['pos'],2)?></td><td>—</td><td>—</td></tr>
    <tr style="background:#e0f7e0;font-weight:bold;"><td>Overall Combined</td><td><?=$overall_positive?></td><td><?=$overall_neutral?></td><td><?=$overall_negative?></td></tr>
  </table>
  <canvas id="chart"></canvas>
</div>

<!-- 👩‍🎓 Student Editor -->
<div class="card">
  <h2>Manage Students</h2>
  <table>
    <tr><th>ID</th><th>Profile</th><th>Name</th><th>Action</th></tr>
    <?php while($s = $students->fetch_assoc()): ?>
      <tr>
        <td><?=$s['id']?></td>
        <td><img src="<?=$s['profile_picture'] ?: 'default.png'?>" class="profile"></td>
        <td><?=$s['student_name']?></td>
        <td><a href="edit_student.php?id=<?=$s['id']?>">✏️ Edit</a></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

<!-- 👨‍💼 Edit Dean / Non-Acad Head -->
<div class="card">
  <h2>Edit Dean & Non-Academic Head Info</h2>
  <form method="POST" action="update_heads.php">
    <label>Dean Name:</label><br>
    <input type="text" name="dean_name" value="<?=htmlspecialchars($headData['dean_name'] ?? '')?>" required><br><br>
    <label>Dean Email:</label><br>
    <input type="email" name="dean_email" value="<?=htmlspecialchars($headData['dean_email'] ?? '')?>" required><br><br>

    <label>Non-Acad Head Name:</label><br>
    <input type="text" name="non_acad_head_name" value="<?=htmlspecialchars($headData['non_acad_head_name'] ?? '')?>" required><br><br>
    <label>Non-Acad Head Email:</label><br>
    <input type="email" name="non_acad_email" value="<?=htmlspecialchars($headData['non_acad_email'] ?? '')?>" required><br><br>

    <button type="submit">💾 Save Changes</button>
  </form>
</div>

<script>
const ctx = document.getElementById('chart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Positive', 'Neutral', 'Negative'],
    datasets: [{
      label: 'Overall Feedback (%)',
      data: [<?= $overall_positive ?>, <?= $overall_neutral ?>, <?= $overall_negative ?>],
      backgroundColor: ['#4caf50','#ffeb3b','#f44336']
    }]
  },
  options: { scales: { y: { beginAtZero: true, max: 100 } } }
});
</script>

</body>
</html>
