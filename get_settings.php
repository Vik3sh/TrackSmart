<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
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

// Get requested setting
$setting = $_GET['setting'] ?? '';

if (empty($setting)) {
    // If no specific setting is requested, return all settings
    $sql = "SELECT setting_name, setting_value, setting_description FROM settings";
    $result = $conn->query($sql);
    
    $settings = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_name']] = [
                'value' => $row['setting_value'],
                'description' => $row['setting_description']
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['settings' => $settings]);
} else {
    // Return specific setting
    $sql = "SELECT setting_value FROM settings WHERE setting_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $setting);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode(['value' => $row['setting_value']]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Setting not found']);
    }
    
    $stmt->close();
}

$conn->close();
?>