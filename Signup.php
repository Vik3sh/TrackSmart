<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tracksmart";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (!empty($username) && !empty($email) && !empty($pass)) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (`UserName`, `E-mail`, `Password`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_pass);

        if ($stmt->execute()) {
            header("Location: message.php?type=success&message=Signup successful! You can now login.");
            exit();
        } else {
            header("Location: message.php?type=error&message=" . urlencode("Signup failed: " . $stmt->error));
            exit();
        }

        $stmt->close();
    } else {
        header("Location: message.php?type=error&message=Please fill in all fields.");
        exit();
    }
}

$conn->close();
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tracksmart";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (!empty($username) && !empty($email) && !empty($pass)) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (`UserName`, `E-mail`, `Password`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_pass);

        if ($stmt->execute()) {
            header("Location: message.php?type=success&message=Signup successful! You can now login.");
            exit();
        } else {
            header("Location: message.php?type=error&message=" . urlencode("Signup failed: " . $stmt->error));
            exit();
        }

        $stmt->close();
    } else {
        header("Location: message.php?type=error&message=Please fill in all fields.");
        exit();
    }
}

$conn->close();
?>
