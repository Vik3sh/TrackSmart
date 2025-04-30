<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "tracksmart";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentUser = $_SESSION['username'];

// Fetch Projects
$projectSql = "SELECT * FROM ProjectsDetails";
$projectResult = $conn->query($projectSql);
$projects = [];
while ($row = $projectResult->fetch_assoc()) {
    $projects[] = $row;
}

// Fetch Team Count
$teamSql = "SELECT COUNT(*) AS total_employees FROM users WHERE role = 'employee'";
$teamResult = $conn->query($teamSql);
$teamData = $teamResult->fetch_assoc();
$totalEmployees = $teamData['total_employees'];

// Fetch Users
$userSql = "SELECT id, UserName, `E-mail`, role FROM users";
$userResult = $conn->query($userSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TrackSmart Admin Dashboard</title>
  <link rel="stylesheet" href="AdminDashboard1.css">
</head>

<body>

<div class="sidebar" id="sidebar">
  <h2>TrackSmart</h2>
  <ul>
    <li class="tab-btn active" data-target="home">Home</li>
    <li class="tab-btn" data-target="projects">Projects</li>
    <li class="tab-btn" data-target="team-overview">Team Overview</li>
    <li class="tab-btn" data-target="reports">Reports</li>
    <li class="tab-btn" data-target="settings">Settings</li>
    <li class="tab-btn">
      <a href="manage_users.php" style="color: white; text-decoration: none;">Manage Users</a>
    </li>
    <li class="tab-btn" data-target="assign-projects">Assign Projects</li>
  </ul>
</div>

<div class="hamburger" id="hamburger">☰</div>

<div class="main-content" id="mainContent">
  <header>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <div style="display: flex; align-items: center; gap: 20px;">
      <div class="mode-toggle">
        <span>Dark Mode</span>
        <label class="switch">
          <input type="checkbox" id="modeToggle">
          <span class="slider"></span>
        </label>
      </div>
      <p id="timerDisplay" style="margin: 0; font-weight: bold; font-size: 16px;">00:00:00</p>
      <form action="logout.php" method="POST" id="logoutForm">
        <button class="logout-btn" type="submit">Logout</button>
      </form>
    </div>
  </header>

  <section id="home" class="content-section active">
    <div class="card">
      <h3>Admin Dashboard</h3>
      <p>Use the menu to manage projects, employees, and more.</p>
    </div>
  </section>

  <section id="projects" class="content-section">
      <?php if (!empty($projects)) : foreach ($projects as $project): ?>
        <div class="card">
          <h3><?php echo htmlspecialchars($project['name']); ?></h3>
          <p><?php echo htmlspecialchars($project['description']); ?></p>
          <p><strong>Deadline:</strong> <?php echo htmlspecialchars($project['deadline']); ?></p>
          <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
        </div>
      <?php endforeach; else: ?>
        <div class="card">No projects available.</div>
      <?php endif; ?>
    </section>

  <section id="time-tracker" class="content-section">
    <div class="card">
      <h3>Time Tracker</h3>
      <p id="timerDisplay">00:00:00</p>
      <p id="statusMessage"></p>
    </div>
  </section>

  <section id="assign-projects" class="content-section">
  <div class="card">
    <h2>Assign Project to Employee</h2>
    <form action="assign_project.php" method="POST">
      <label for="project">Select Project:</label>
      <select name="project_id" id="project" required>
        <?php
        $projectQuery = "SELECT id, name FROM ProjectsDetails";
        $projectResult = $conn->query($projectQuery);
        while ($proj = $projectResult->fetch_assoc()) {
          echo "<option value='{$proj['id']}'>" . htmlspecialchars($proj['name']) . "</option>";
        }
        ?>
      </select>

      <br><br>

      <label for="employee">Assign to Employee:</label>
      <select name="employee_username" id="employee" required>
        <?php
        $userQuery = "SELECT UserName FROM users WHERE role = 'employee'";
        $userResult = $conn->query($userQuery);
        while ($emp = $userResult->fetch_assoc()) {
          echo "<option value='{$emp['UserName']}'>" . htmlspecialchars($emp['UserName']) . "</option>";
        }
        ?>
      </select>

      <br><br>
      <button type="submit">Assign Project</button>
    </form>
  </div>
</section>

<section id="settings" class="content-section">
  <div class="card">
    <h2>System Settings</h2>
    
    <div class="settings-container">
      <div class="setting-item">
        <label for="defaultCheckInTime">Default Check-In Time:</label>
        <input type="time" id="defaultCheckInTime" value="09:00">
        <button id="saveCheckInTime" class="btn">Save</button>
        <span id="checkInTimeStatus" class="status-message"></span>
      </div>
      
      <!-- Add more settings here as needed -->
    </div>
  </div>
</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Timer functionality
  const timerDisplay = document.getElementById('timerDisplay');
  const statusMessage = document.getElementById('statusMessage');
  const startTime = new Date();

  const timerInterval = setInterval(() => {
    const elapsed = new Date(new Date() - startTime).toISOString().substr(11, 8);
    timerDisplay.textContent = elapsed;
  }, 1000);

  // Logout form handling
  const logoutForm = document.getElementById('logoutForm');
  if (logoutForm) {
    logoutForm.addEventListener('submit', (e) => {
      clearInterval(timerInterval);
      const endTime = new Date();
      const duration = new Date(endTime - startTime).toISOString().substr(11, 8);
      const timeData = JSON.stringify({
        start: startTime.toISOString(),
        end: endTime.toISOString(),
        duration: duration
      });
      navigator.sendBeacon('save_time.php', timeData);
      statusMessage.textContent = `Session Duration: ${duration}`;
    });
  }

  // Tab switching functionality
  const tabButtons = document.querySelectorAll('.tab-btn');
  tabButtons.forEach(button => {
    if (!button.dataset.target) return; // Skip if no target (like the Manage Users link)
    
    button.addEventListener('click', () => {
      tabButtons.forEach(btn => btn.classList.remove('active'));
      document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
      button.classList.add('active');
      const targetSection = document.getElementById(button.dataset.target);
      if (targetSection) {
        targetSection.classList.add('active');
      }
      
      // If settings tab is clicked, load settings
      if (button.dataset.target === 'settings') {
        loadSettings();
      }
      
      // On mobile, close the sidebar when a tab is clicked
      if (window.innerWidth <= 768) {
        sidebar.classList.remove('active');
        mainContent.classList.remove('expanded');
      }
    });
  });

  // Dark mode toggle
  const modeToggle = document.getElementById('modeToggle');
  if (modeToggle) {
    modeToggle.addEventListener('change', () => {
      document.body.classList.toggle('dark-mode');
    });
  }

  // Hamburger menu toggle - IMPROVED
  const hamburger = document.getElementById('hamburger');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');
  
  // Show hamburger on mobile
  function updateHamburgerVisibility() {
    if (window.innerWidth <= 768) {
      hamburger.style.display = 'flex';
      sidebar.classList.remove('active');
      mainContent.classList.remove('expanded');
    } else {
      hamburger.style.display = 'none';
      sidebar.classList.remove('active'); // Reset to default state
      mainContent.classList.remove('expanded');
    }
  }
  
  // Initial setup
  updateHamburgerVisibility();
  
  // Update on window resize
  window.addEventListener('resize', updateHamburgerVisibility);
  
  if (hamburger && sidebar) {
    hamburger.addEventListener('click', function() {
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('expanded');
      console.log('Hamburger clicked, sidebar toggled'); // Debug log
    });
  }
  document.addEventListener('click', function(event) {
    // Only apply this behavior on mobile
    if (window.innerWidth <= 768) {
      // Check if sidebar is active and the click is outside the sidebar and hamburger
      if (
        sidebar.classList.contains('active') && 
        !sidebar.contains(event.target) && 
        event.target !== hamburger &&
        !hamburger.contains(event.target)
      ) {
        sidebar.classList.remove('active');
        mainContent.classList.remove('expanded');
        console.log('Clicked outside, sidebar closed'); // Debug log
      }
    }
  });
  
  // Settings functionality
  function loadSettings() {
    fetch('get_settings.php')
      .then(response => response.json())
      .then(data => {
        if (data.settings) {
          // Set default check-in time
          if (data.settings.default_check_in_time) {
            document.getElementById('defaultCheckInTime').value = 
              data.settings.default_check_in_time.value.substring(0, 5); // Format HH:MM
          }
        }
      })
      .catch(error => console.error('Error loading settings:', error));
  }

  // Save default check-in time
  const saveCheckInTimeBtn = document.getElementById('saveCheckInTime');
  if (saveCheckInTimeBtn) {
    saveCheckInTimeBtn.addEventListener('click', function() {
      const timeValue = document.getElementById('defaultCheckInTime').value + ':00'; // Add seconds
      
      fetch('update_settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          setting_name: 'default_check_in_time',
          setting_value: timeValue
        })
      })
      .then(response => response.json())
      .then(data => {
        const statusElement = document.getElementById('checkInTimeStatus');
        if (data.success) {
          statusElement.textContent = 'Saved successfully!';
          statusElement.className = 'status-message success';
          
          // Clear the message after 3 seconds
          setTimeout(() => {
            statusElement.textContent = '';
          }, 3000);
        } else {
          statusElement.textContent = data.error || 'Failed to save';
          statusElement.className = 'status-message error';
        }
      })
      .catch(error => {
        console.error('Error saving setting:', error);
        document.getElementById('checkInTimeStatus').textContent = 'Error saving setting';
        document.getElementById('checkInTimeStatus').className = 'status-message error';
      });
    });
  }
});
</script>

</body>
</html>
