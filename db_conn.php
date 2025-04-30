<?php
$conn = new mysqli("localhost", "root", "", "tracksmart");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
