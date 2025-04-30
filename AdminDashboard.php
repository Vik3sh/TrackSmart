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
  <li class="tab-btn active" data-target="home">🏠 Home</li>
  <li class="tab-btn" data-target="projects">📁 Projects</li>
  <li class="tab-btn" data-target="time-tracker">⏱️ Time Tracker</li>
  <li class="tab-btn" data-target="team-overview">👥 Team Overview</li>
  <li class="tab-btn" data-target="reports">📊 Reports</li>
  <li class="tab-btn" data-target="settings">⚙️ Settings</li>
  <li class="tab-btn">
    <a href="manage_users.php" style="color: white; text-decoration: none;">👤 Manage Users</a>
  </li>
  <li class="tab-btn" data-target="assign-projects">🧩 Assign Projects</li>
</ul>

  </div>

  <div class="hamburger" id="hamburger">☰</div>

  <div class="main-content" id="mainContent">

    <header>
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
      <div>
        <div class="mode-toggle">
          <span>Dark Mode</span>
          <label class="switch">
            <input type="checkbox" id="modeToggle">
            <span class="slider"></span>
          </label>
        </div>
        <form action="logout.php" method="POST">
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
        <button id="startBtn">Start</button>
        <button id="stopBtn" disabled>Stop</button>
        <p id="timerDisplay">00:00:00</p>
        <p id="statusMessage"></p>
      </div>
    </section>

    <section id="team-overview" class="content-section">
      <div class="card">
        <h3>Total Employees</h3>
        <p><?php echo $totalEmployees; ?></p>
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


    <section id="reports" class="content-section">
      <div class="card">Reports section placeholder.</div>
    </section>

    <section id="settings" class="content-section">
      <div class="card">Settings section placeholder.</div>
    </section>

    <section id="manage-users" class="content-section ">
  <div class="card">
    <h3>Manage Users</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($userResult->num_rows > 0): while ($user = $userResult->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['UserName']); ?></td>
            <td><?php echo htmlspecialchars($user['E-mail']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
              <a href="manage_users.php?action=edit&id=<?php echo $user['id']; ?>" class="action-btn edit-btn">Edit</a>
              <a href="manage_users.php?action=delete&id=<?php echo $user['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="5">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

  </div>

  <script>
document.addEventListener('DOMContentLoaded', () => {
  let startTime, interval;

  // Time Tracker Start
  const startBtn = document.getElementById('startBtn');
  const stopBtn = document.getElementById('stopBtn');
  const timerDisplay = document.getElementById('timerDisplay');
  const statusMessage = document.getElementById('statusMessage');

  if (startBtn && stopBtn) {
    startBtn.onclick = () => {
      startTime = new Date();
      interval = setInterval(updateDisplay, 1000);
      startBtn.disabled = true;
      stopBtn.disabled = false;
      statusMessage.textContent = "Timer started.";
    };

    stopBtn.onclick = () => {
      clearInterval(interval);
      const endTime = new Date();
      const duration = new Date(endTime - startTime).toISOString().substr(11, 8);

      fetch('save_time.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ start: startTime.toISOString(), end: endTime.toISOString(), duration })
      }).then(res => res.text()).then(data => {
        statusMessage.textContent = data;
      });

      startBtn.disabled = false;
      stopBtn.disabled = true;
      timerDisplay.textContent = "00:00:00";
    };

    function updateDisplay() {
      const elapsed = new Date(new Date() - startTime).toISOString().substr(11, 8);
      timerDisplay.textContent = elapsed;
    }
  }

  // Sidebar tab switching
  document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
      document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
      document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
      button.classList.add('active');
      document.getElementById(button.dataset.target).classList.add('active');
    });
  });

  // Dark mode toggle
  const modeToggle = document.getElementById('modeToggle');
  if (modeToggle) {
    modeToggle.addEventListener('change', () => {
      document.body.classList.toggle('dark-mode');
    });
  }

  // Hamburger sidebar toggle
  const hamburger = document.getElementById('hamburger');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');

  if (hamburger && sidebar) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('expanded');
    });
  }
});
</script>



</body>
</html>
