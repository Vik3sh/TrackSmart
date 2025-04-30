<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['setting_name']) || !isset($data['setting_value'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "tracksmart";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$settingName = $data['setting_name'];
$settingValue = $data['setting_value'];

// Check if setting exists
$checkSql = "SELECT * FROM settings WHERE setting_name = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $settingName);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    // Update existing setting
    $updateSql = "UPDATE settings SET setting_value = ? WHERE setting_name = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ss", $settingValue, $settingName);
    $success = $updateStmt->execute();
} else {
    // Insert new setting
    $insertSql = "INSERT INTO settings (setting_name, setting_value) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ss", $settingName, $settingValue);
    $success = $insertStmt->execute();
}

header('Content-Type: application/json');
if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to save setting']);
}

$conn->close();
?>