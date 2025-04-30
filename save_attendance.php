<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "Error: Not logged in";
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['check_in']) || !isset($data['check_out']) || !isset($data['duration'])) {
    echo "Error: Invalid data received";
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "tracksmart";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    echo "Error: Database connection failed";
    exit;
}

// Get current user
$currentUser = $_SESSION['username'];
$checkIn = $data['check_in'];
$checkOut = $data['check_out'];
$duration = $data['duration'];
$date = date('Y-m-d');

// Insert attendance record
$sql = "INSERT INTO attendance (username, date, check_in, check_out, duration) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $currentUser, $date, $checkIn, $checkOut, $duration);

if ($stmt->execute()) {
    echo "Attendance recorded successfully. Duration: $duration";
} else {
    echo "Error recording attendance: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>