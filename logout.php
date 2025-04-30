<?php
session_start();
session_unset();
session_destroy();
header("Location: Login.php"); // ✅ redirect to your styled login page
exit();
?>
