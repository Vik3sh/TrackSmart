<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tracksmart";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (!empty($email) && !empty($pass)) {
        $sql = "SELECT * FROM users WHERE `E-mail` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($pass, $user['Password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['UserName'];
                $_SESSION['role']=$user['role'];
                
                if ($user['role'] === 'admin') {
                    header("Location: AdminDashboard1.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password.";
            }
        } else {
            $_SESSION['error'] = "User not found.";
        }
    } else {
        $_SESSION['error'] = "Please fill in all fields.";
    }

    header("Location: Login.html");
    exit();
}

$conn->close();
?>
