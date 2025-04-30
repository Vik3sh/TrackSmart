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

// Delete user if requested
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $userId");
    header("Location: manage_users.php");
    exit();
}

$sql = "SELECT id, UserName, `E-mail`, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Users</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ccc; }
    th { background: #2e3a59; color: white; }
    .btn { padding: 6px 12px; margin-right: 5px; border: none; border-radius: 5px; cursor: pointer; }
    .edit-btn { background: #007bff; color: white; }
    .delete-btn { background: #dc3545; color: white; }
  </style>
</head>
<body>
  <h2>Manage Users</h2>
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
      <?php while ($user = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($user['id']) ?></td>
          <td><?= htmlspecialchars($user['UserName']) ?></td>
          <td><?= htmlspecialchars($user['E-mail']) ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
          <td>
            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn edit-btn">Edit</a>
            <a href="manage_users.php?delete=<?= $user['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>

<?php $conn->close(); ?>
