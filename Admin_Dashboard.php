<?php
session_start();

// Redirect if not logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: Index.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedbackdata";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed.");
}

$adminName = "Admin"; // Default name
$stmt = $conn->prepare("SELECT full_name FROM admins WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if ($result) { $adminName = $result['full_name']; }
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Feedback Satisfaction System - Dashboard</title>

  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      width: 100%;
      overflow-x: hidden;
    }

    body {
      font-family: 'Arial', sans-serif;
      background: url('Background.jpg') center/cover no-repeat fixed;
      color: #222;
      min-height: 100vh;
      opacity: 0;
      transition: opacity 0.6s ease-in-out;
    }

    body.fade-in {
      opacity: 1;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-color: rgba(255, 255, 255, 0.75);
      z-index: -1;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
      position: relative;
      z-index: 1;
      overflow-x: hidden;
    }

    /* Navbar */
    .navbar {
      background: linear-gradient(90deg, #001F3F, #004080);
      padding: 15px 0;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
      position: sticky;
      top: 0;
      z-index: 1000;
      backdrop-filter: blur(10px);
      border-radius: 0 0 20px 20px;
      width: 100%;
    }

    .navbar-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar-brand {
      font-size: 20px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: 1px;
    }

    .navbar-menu {
      display: flex;
      gap: 30px;
      list-style: none;
      align-items: center;
      flex-wrap: wrap;
    }

    .navbar-menu a {
      color: #f1f1f1;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease, transform 0.2s ease;
      cursor: pointer;
    }

    .navbar-menu a:hover,
    .navbar-menu a.active {
      color: #ffcc00;
      transform: translateY(-2px);
    }

    .logout-btn {
      background-color: #ff4b5c;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 8px;
      font-size: 0.95em;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .logout-btn:hover {
      background-color: #e03e4e;
      transform: translateY(-1px);
    }

    #dashboardPage {
      display: block;
      padding-top: 30px;
      animation: fadeIn 0.5s ease-in;
    }

    /* Dashboard Header */
    .dashboard-header {
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(10px);
      text-align: center;
    }

    .dashboard-header h1 {
      color: #003366;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .dashboard-header p {
      color: #555;
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
      overflow-x: hidden;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: 1px solid #e9ecef;
      text-align: center;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .stat-card-value {
      font-size: 32px;
      font-weight: 700;
      color: #003366;
      margin-bottom: 5px;
    }

    .stat-card-label {
      font-size: 13px;
      color: #555;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      white-space: nowrap;
    }

    /* ✅ Account Buttons */
    .account-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 25px;
    }

    .create-btn {
      background: linear-gradient(90deg, #004080, #0074D9);
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 10px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
      box-shadow: 0 5px 15px rgba(0, 64, 128, 0.3);
    }

    .create-btn:hover {
      background: linear-gradient(90deg, #0059b3, #339CFF);
      transform: translateY(-2px);
    }

    /* Loading Overlay */
    #loadingOverlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.9);
      z-index: 3000;
      backdrop-filter: blur(5px);
      justify-content: center;
      align-items: center;
      font-size: 22px;
      color: #004080;
      font-weight: bold;
      transition: opacity 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>

<body>
  <nav class="navbar" id="navbar">
    <div class="navbar-content">
      <div class="navbar-brand">Student Feedback System</div>
      <ul class="navbar-menu" id="navbarMenu">
        <li><a href="#" class="nav-link active" data-page="dashboard">Home</a></li>
        <li><a href="#" class="nav-link" id="profileLink">Profile</a></li>
        <li><button class="logout-btn" id="logoutBtn">Logout</button></li>
      </ul>
    </div>
  </nav>

  <!-- Dashboard Page -->
  <div id="dashboardPage">
    <div class="container">
      <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($adminName); ?>!</h1>
        <p>Here's an overview of the feedback sentiment analysis</p>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-value" id="positiveVal">0%</div>
          <div class="stat-card-label">😊 Positive Feedback</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-value" id="neutralVal">0%</div>
          <div class="stat-card-label">😐 Neutral Feedback</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-value" id="negativeVal">0%</div>
          <div class="stat-card-label">😞 Negative Feedback</div>
        </div>
      </div>

      <!-- ✅ Account Creation Buttons -->
      <div class="account-buttons">
        <button class="create-btn" id="addAcademicBtn">+ Add Academic Account</button>
        <button class="create-btn" id="addNonAcademicBtn">+ Add Non-Academic Account</button>
      </div>
    </div>
  </div>

  <div id="loadingOverlay">Loading...</div>

  <script>
    window.addEventListener("load", () => {
      document.body.classList.add("fade-in");
    });

    const loadingOverlay = document.getElementById("loadingOverlay");

    // Logout
    document.getElementById("logoutBtn").addEventListener("click", () => {
      if (confirm("Are you sure you want to log out?")) {
        loadingOverlay.textContent = "Logging out...";
        loadingOverlay.style.display = "flex";
        document.body.style.opacity = "0.7";
        setTimeout(() => {
          window.location.href = "index.html";
        }, 1200);
      }
    });

    // Profile Redirect
    document.getElementById("profileLink").addEventListener("click", () => {
      loadingOverlay.textContent = "Loading profile...";
      loadingOverlay.style.display = "flex";
      document.body.style.opacity = "0.7";
      setTimeout(() => {
        window.location.href = "Profile_Admin.html";
      }, 1000);
    });

    // ✅ Redirect to account creation pages
    document.getElementById("addAcademicBtn").addEventListener("click", () => {
      loadingOverlay.textContent = "Loading Academic Account Form...";
      loadingOverlay.style.display = "flex";
      document.body.style.opacity = "0.7";
      setTimeout(() => {
        window.location.href = "Create_Academic.html";
      }, 1000);
    });

    document.getElementById("addNonAcademicBtn").addEventListener("click", () => {
      loadingOverlay.textContent = "Loading Non-Academic Account Form...";
      loadingOverlay.style.display = "flex";
      document.body.style.opacity = "0.7";
      setTimeout(() => {
        window.location.href = "Create_NonAcad.html";
      }, 1000);
    });

    // Fetch data for dashboard stats
    fetch('dashboard_data.php?role=admin')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('positiveVal').textContent = data.data["Positive Feedback"] + "%";
          document.getElementById('neutralVal').textContent = data.data["Neutral Feedback"] + "%";
          document.getElementById('negativeVal').textContent = data.data["Negative Feedback"] + "%";
        } else {
          console.error("Failed to load dashboard data:", data.message);
        }
      })
      .catch(error => console.error("Fetch error:", error));
  </script>
</body>
</html>
