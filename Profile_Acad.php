<?php
session_start();
include 'admin/dbinit.php';

// Redirect if not logged in or not an academic user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'academic') {
    header("Location: Login_System.html");
    exit;
}

$userId = $_SESSION['user_id'];
$userName = "Academic Teacher"; // Default name
$userEmail = "N/A"; // Default email

$stmt = $conn->prepare("SELECT full_name, email FROM academic_teachers WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $userName = htmlspecialchars($row['full_name']);
    $userEmail = htmlspecialchars($row['email']);
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Profile - Feedback System</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Arial', sans-serif;
      background: url('Background.jpg') center/cover no-repeat fixed;
      color: #222;
      min-height: 100vh;
      opacity: 0;
      transition: opacity 0.6s ease-in-out;
      padding-top: 80px;
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

    /* Navbar */
    .navbar {
      background: linear-gradient(90deg, #001F3F, #004080);
      padding: 15px 0;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      backdrop-filter: blur(10px);
      border-radius: 0 0 20px 20px;
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

    .navbar-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 24px;
      color: #ffffff;
      cursor: pointer;
    }

    .container {
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      overflow: hidden;
      position: relative;
      z-index: 1;
    }

    .logo-header {
      background: linear-gradient(90deg, #001F3F, #004080);
      padding: 40px 30px 20px;
      text-align: center;
      color: white;
      border-bottom: 1px solid #e9ecef;
    }

    .logos {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      margin-bottom: 15px;
      position: relative;
    }

    .logo-header img {
      max-width: 120px;
      max-height: 120px;
      border-radius: 50%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .logo-header img:hover {
      transform: scale(1.05);
    }

    #uploadBtn {
      position: absolute;
      bottom: -10px;
      right: calc(50% - 60px);
      background-color: #ffcc00;
      border: none;
      padding: 5px 10px;
      border-radius: 8px;
      font-size: 0.85em;
      cursor: pointer;
      font-weight: bold;
    }

    #uploadInput {
      display: none;
    }

    .logo-header h2 {
      font-size: 1.8em;
      margin: 0;
      color: #ffffff;
      font-weight: bold;
    }

    .logo-header p {
      margin-top: 5px;
      font-size: 0.95em;
      color: #e0e0e0;
    }

    .profile-section {
      padding: 30px;
      background-color: #ffffff;
      text-align: center;
    }

    .profile-display {
      margin-top: 20px;
      padding: 20px;
      background-color: #f8f9fa;
      border-radius: 8px;
      border: 1px solid #e9ecef;
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .profile-display h3 {
      color: #003366;
      margin-bottom: 15px;
      font-size: 1.4em;
    }

    .profile-display p {
      margin: 10px 0;
      font-size: 1em;
      color: #333;
    }

    .profile-display strong {
      color: #004080;
    }

    .footer-note {
      text-align: center;
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #e9ecef;
      background-color: #ffffff;
    }

    .footer-note p {
      color: #6c757d;
      font-size: 0.9em;
      margin: 0;
    }

    @media (max-width: 768px) {
      .navbar-menu {
        display: none;
        position: absolute;
        top: 60px;
        left: 0;
        right: 0;
        background: linear-gradient(90deg, #001F3F, #004080);
        flex-direction: column;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border-radius: 0 0 15px 15px;
      }

      .navbar-menu.active {
        display: flex;
      }

      .navbar-toggle {
        display: block;
      }

      .logout-btn {
        width: 100%;
        margin-top: 10px;
      }
    }
  </style>
</head>

<body class="fade-in">
  <nav class="navbar" id="navbar">
    <div class="navbar-content">
      <div class="navbar-brand">Student Feedback System</div>
      <button class="navbar-toggle" id="navbarToggle">&#9776;</button>
      <ul class="navbar-menu" id="navbarMenu">
        <li><a href="#" class="nav-link" id="homeLink">Home</a></li>
        <li><button class="logout-btn" id="logoutBtn">Logout</button></li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <div class="logo-header">
      <div class="logos">
        <img src="logoooo.jfif" alt="PLP logo" id="profilePic">
        <button id="uploadBtn">Upload</button>
        <input type="file" id="uploadInput" accept="image/*">
      </div>
      <h2>Student Profile System</h2>
      <p>Your student profile details.</p>
    </div>

    <div class="profile-section">
      <div class="profile-display">
        <h3>Your Teacher Profile</h3>
        <p><strong>Name:</strong> <span id="profileName"><?php echo $userName; ?></span></p>
        <p><strong>Email:</strong> <span id="profileEmail"><?php echo $userEmail; ?></span></p>
        <p><strong>Role:</strong> Academic Teacher</p>
      </div>

      <div class="footer-note">
        <p>This profile is displayed for verification purposes.</p>
      </div>
    </div>
  </div>

  <div id="loadingOverlay" style="
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(255,255,255,0.9);
      z-index: 3000;
      backdrop-filter: blur(5px);
      justify-content: center;
      align-items: center;
      font-size: 22px;
      color: #004080;
      font-weight: bold;
      transition: opacity 0.3s ease;
  ">Loading...</div>

  <script>
    window.addEventListener("load", () => {
      document.body.classList.add("fade-in");
    });

    const loadingOverlay = document.getElementById("loadingOverlay");
    const navbarToggle = document.getElementById("navbarToggle");
    const navbarMenu = document.getElementById("navbarMenu");

    navbarToggle.addEventListener("click", () => {
      navbarMenu.classList.toggle("active");
    });

    document.getElementById("homeLink").addEventListener("click", (e) => {
      e.preventDefault();
      loadingOverlay.textContent = "Loading dashboard...";
      loadingOverlay.style.display = "flex";
      document.body.style.opacity = "0.7";
      setTimeout(() => {
        window.location.href = "Acad_Dashboard.html";
      }, 1000);
    });

    document.getElementById("logoutBtn").addEventListener("click", async () => {
      if (await AppAlert.confirm({title:'Log out?',text:'Are you sure you want to log out?',confirmText:'Logout'})) {
        loadingOverlay.textContent = "Logging out...";
        loadingOverlay.style.display = "flex";
        document.body.style.opacity = "0.7";
        setTimeout(() => {
          window.location.href = "auth/logout.php";
        }, 1200);
      }
    });

    // Profile picture upload
    const profilePic = document.getElementById("profilePic");
    const uploadBtn = document.getElementById("uploadBtn");
    const uploadInput = document.getElementById("uploadInput");

    uploadBtn.addEventListener("click", () => uploadInput.click());

    uploadInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          profilePic.src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });
  </script>
<script src="shared-alerts.js"></script></body>
</html>
