<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | TrackSmart</title>
  <link rel="stylesheet" href="Login.css" type="text/css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <div class="wrapper">
    <form action="login_process.php" method="POST">
      <h1>Login</h1>

      <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo "<p style='color:red; text-align:center;'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
      ?>

      <div class="input-box">
        <input type="email" name="email" placeholder="Email" required>
        <i class="bx bx-envelope"></i>
      </div>

      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required>
        <i class="bx bxs-lock-alt"></i>
      </div>

      <div class="remember-forgot">
        <label><input type="checkbox" name="remember"> Remember Me</label>
        <a href="#">Forgot Password?</a>
      </div>

      <button type="submit" class="btn">Login</button>

      <div class="register-link">
        <p>Don't have an account? <a href="Signup.html">Register</a></p>
      </div>
    </form>
  </div>
</body>
</html>
