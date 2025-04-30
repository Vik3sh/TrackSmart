<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tracksmart";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $project_id = $_POST['project_id'];
  $employee = $_POST['employee_username'];

  $stmt = $conn->prepare("UPDATE ProjectsDetails SET assigned_to = ? WHERE id = ?");
  $stmt->bind_param("si", $employee, $project_id);

  if ($stmt->execute()) {
    echo "✅ Project assigned successfully.";
  } else {
    echo "❌ Failed to assign project.";
  }

  $stmt->close();
}
$conn->close();
?>
