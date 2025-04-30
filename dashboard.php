<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
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
$sql = "SELECT * FROM ProjectsDetails WHERE assigned_to = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentUser);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TrackSmart Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <h2>TrackSmart</h2>
    <ul>
      <li class="tab-btn active" data-target="home"> Home</li>
      <li class="tab-btn" data-target="projects"> My Projects</li>
      <li class="tab-btn" data-target="time-tracker"> Time Tracker</li>
      <li class="tab-btn" data-target="attendence"> Attendence</li>
      <li class="tab-btn" data-target="team-overview"> Team Overview</li>
      <li class="tab-btn" data-target="reports"> Reports</li>
      <li class="tab-btn" data-target="settings"> Settings</li>
    </ul>
  </div>

  <!-- Hamburger icon -->
  <div class="hamburger" id="hamburger">☰</div>

  <!-- Main Content -->
  <div class="main-content">
    <header>
      <h1>Dashboard</h1>
      <div>
        <div class="mode-toggle">
          <span>Dark Mode</span>
          <label class="switch">
            <input type="checkbox" id="modeToggle">
            <span class="slider"></span>
          </label>
        </div>
        <form action="logout.php" method="POST" style="display: inline;">
  <button class="logout-btn" type="submit">Logout</button>
</form>

      </div>
    </header>

    <!-- Home Section -->
    <section id="home" class="content-section active">
      <div class="home-welcome">
      <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <p>This is your personalized dashboard where you can:</p>
        <ul>
          <li>Manage your projects</li>
          <li>Track your working time</li>
          <li>View reports and team performance</li>
          <li>Update your settings</li>
        </ul>
        <p class="quote">"Success is the sum of small efforts repeated day in and day out."</p>
      </div>
    </section>

    <!-- Other Sections -->
    <section id="projects" class="content-section">
      <div class="widgets">
      <?php if (!empty($projects)) : ?>
  <?php foreach ($projects as $project) : ?>
    <div class="card">
      <h3><?php echo htmlspecialchars($project['name']); ?></h3>
      <p><?php echo htmlspecialchars($project['description']); ?></p>
      <p><strong>Deadline:</strong> <?php echo htmlspecialchars($project['deadline']); ?></p>
      <p><strong>Status:</strong> 
  <span class="badge <?php echo strtolower(str_replace(' ', '-', $project['status'])); ?>">
    <?php echo htmlspecialchars($project['status']); ?>
  </span>
</p>

    </div>
  <?php endforeach; ?>
<?php else : ?>
  <p>No projects assigned yet.</p>
<?php endif; ?>


      </div>
    </section>

    <section id="time-tracker" class="content-section">
  <h2 class="section-title">Time Tracker</h2>
  <div class="tracker-controls">
    <button id="startBtn">Start</button>
    <button id="stopBtn" disabled>Stop</button>
    <p id="timerDisplay">00:00:00</p>
    <p id="statusMessage"></p>
  </div>
</section>

    <section id="attendence" class="content-section">
    <h2 class="section-title">Attendance</h2>
    <div class="attendance-controls">
        <button id="checkInBtn" class="btn">Check In</button>
        <button id="checkOutBtn" class="btn" disabled>Check Out</button>
    </div>
    <div id="attendanceStatus" class="status-text">
        Not checked in today.
        <p>Ask you admin to check for the attendence and click on the checkin button</p>
    </div>
</section>


    <section id="team-overview" class="content-section">
      <div class="card">Team Overview Content</div>
      <p>Current Team Members:</p>  
      <ul class="team-list" font-size="20px">
        <li>John Doe</li>
        <li>Jane Smith</li>
        <li>Emily Johnson</li>
        <li>Michael Brown</li>
        <li>Sarah Davis</li>
    </section>

    <section id="reports" class="content-section">
      <div class="card report-card">
        <h2>Performance Reports</h2>
        
        <div class="report-filters">
          <select id="reportType">
            <option value="weekly">Weekly Report</option>
            <option value="monthly">Monthly Report</option>
            <option value="quarterly">Quarterly Report</option>
          </select>
          <button class="generate-btn">Generate Report</button>
        </div>
        
        <div class="report-summary">
          <div class="summary-item">
            <h3>Hours Logged</h3>
            <div class="chart-container">
              <div class="chart-bar" style="height: 65%;" data-value="32.5">
                <span class="chart-value">32.5h</span>
              </div>
              <div class="chart-bar" style="height: 80%;" data-value="40">
                <span class="chart-value">40h</span>
              </div>
              <div class="chart-bar" style="height: 50%;" data-value="25">
                <span class="chart-value">25h</span>
              </div>
              <div class="chart-bar" style="height: 70%;" data-value="35">
                <span class="chart-value">35h</span>
              </div>
            </div>
            <div class="chart-labels">
              <span>Week 1</span>
              <span>Week 2</span>
              <span>Week 3</span>
              <span>Week 4</span>
            </div>
          </div>
          
          <div class="summary-item">
            <h3>Project Completion</h3>
            <div class="progress-container">
              <div class="progress-item">
                <span class="project-name">Website Redesign</span>
                <div class="progress-bar">
                  <div class="progress" style="width: 75%;">75%</div>
                </div>
              </div>
              <div class="progress-item">
                <span class="project-name">Mobile App Development</span>
                <div class="progress-bar">
                  <div class="progress" style="width: 45%;">45%</div>
                </div>
              </div>
              <div class="progress-item">
                <span class="project-name">Database Migration</span>
                <div class="progress-bar">
                  <div class="progress" style="width: 90%;">90%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="recent-activity">
          <h3>Recent Activity</h3>
          <table class="activity-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Project</th>
                <th>Hours</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2023-11-15</td>
                <td>Website Redesign</td>
                <td>4.5</td>
                <td><span class="status in-progress">In Progress</span></td>
              </tr>
              <tr>
                <td>2023-11-14</td>
                <td>Mobile App Development</td>
                <td>6.0</td>
                <td><span class="status in-progress">In Progress</span></td>
              </tr>
              <tr>
                <td>2023-11-13</td>
                <td>Database Migration</td>
                <td>3.5</td>
                <td><span class="status completed">Completed</span></td>
              </tr>
              <tr>
                <td>2023-11-12</td>
                <td>Website Redesign</td>
                <td>5.0</td>
                <td><span class="status in-progress">In Progress</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section id="settings" class="content-section">
      <div class="card">Settings Content</div>
    </section>
  </div>

  <script>
    let startTime, interval;

document.getElementById('startBtn').addEventListener('click', function () {
    startTime = new Date();
    interval = setInterval(updateDisplay, 1000);
    document.getElementById('startBtn').disabled = true;
    document.getElementById('stopBtn').disabled = false;
    document.getElementById('statusMessage').textContent = "Timer started.";
});

document.getElementById('stopBtn').addEventListener('click', function () {
    clearInterval(interval);
    const endTime = new Date();
    const duration = new Date(endTime - startTime).toISOString().substr(11, 8);

    fetch('save_time.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            start: startTime.toISOString(),
            end: endTime.toISOString(),
            duration: duration
        })
    }).then(res => res.text())
      .then(data => {
        document.getElementById('statusMessage').textContent = data;
      });

    document.getElementById('startBtn').disabled = false;
    document.getElementById('stopBtn').disabled = true;
    document.getElementById('timerDisplay').textContent = "00:00:00";
});

function updateDisplay() {
    const elapsed = new Date(new Date() - startTime).toISOString().substr(11, 8);
    document.getElementById('timerDisplay').textContent = elapsed;
}
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    const tabButtons = document.querySelectorAll('.tab-btn');
    const sections = document.querySelectorAll('.content-section');
  
    // Toggle Sidebar when hamburger is clicked
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('active');
    });
  
    // Hide sidebar when a tab is clicked
    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        sections.forEach(sec => sec.classList.remove('active'));
  
        btn.classList.add('active');
        document.getElementById(btn.getAttribute('data-target')).classList.add('active');
  
        // Close sidebar on smaller screens
        sidebar.classList.remove('active');
      });
    });
  
    // Close sidebar when clicking outside it
    document.addEventListener('click', (e) => {
      const isClickInsideSidebar = sidebar.contains(e.target);
      const isClickOnHamburger = hamburger.contains(e.target);
      if (!isClickInsideSidebar && !isClickOnHamburger) {
        sidebar.classList.remove('active');
      }
    });
  
    // Dark Mode Toggle
    const modeToggle = document.getElementById('modeToggle');
    modeToggle.addEventListener('change', () => {
      document.body.classList.toggle('dark-mode');
    });
  </script>
  
</body>
</html>
