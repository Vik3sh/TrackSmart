<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_conn.php';

$data = json_decode(file_get_contents("php://input"), true);

$userId = $_SESSION['user_id'];
$start = $data['start'];
$end = $data['end'];
$duration = $data['duration'];

$sql = "INSERT INTO TimeTracker (user_id, start_time, end_time, total_duration)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $userId, $start, $end, $duration);

if ($stmt->execute()) {
    echo "Time log saved successfully.";
} else {
    echo "Error: " . $stmt->error;
}
?>
