<?php
$message = $_GET['message'] ?? 'Something went wrong!';
$type = $_GET['type'] ?? 'error'; // 'success' or 'error'
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TrackSmart - Message</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #667eea, #764ba2);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: white;
      margin: 0;
    }
    .message-box {
      background-color: rgba(0, 0, 0, 0.4);
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      max-width: 500px;
    }
    .message-box i {
      font-size: 60px;
      margin-bottom: 20px;
      color: <?= $type === 'success' ? '#00e676' : '#ff5252' ?>;
    }
    .message-box h2 {
      margin-bottom: 15px;
      font-size: 28px;
    }
    .message-box a {
      color: white;
      padding: 10px 20px;
      border: 1px solid white;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
      margin-top: 20px;
      transition: background 0.3s;
    }
    .message-box a:hover {
      background: white;
      color: #333;
    }
  </style>
</head>
<body>
  <div class="message-box">
    <i class="bx <?= $type === 'success' ? 'bx-check-circle' : 'bx-error-circle' ?>"></i>
    <h2><?= $type === 'success' ? 'Success!' : 'Oops!' ?></h2>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="<?= $type === 'success' ? 'Login.php' : 'javascript:history.back()' ?>">Go Back</a>
  </div>
</body>
</html>
