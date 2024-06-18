<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  // Prepare and bind
  $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        // Start session and redirect to dashboard
        $_SESSION['username'] = $username;
        $_SESSION['success_message'] = "Login successful! Redirecting...";
        header("Location: u_index.php");
        exit();
      } else {
        $error_message = "Invalid password";
      }
    } else {
      $error_message = "User not found";
    }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="Style.css">
  <title>Login</title>
  <style>
    .message {
      position: absolute;
      top: calc(100% + 20px); /* Position right below the login form */
      right: 20px;
      background-color: #f8d7da;
      color: #721c24;
      padding: 10px;
      border: 1px solid #f5c6cb;
      border-radius: 5px;
    }
    .message.success {
      background-color: #d4edda;
      color: #155724;
      border-color: #c3e6cb;
    }
  </style>
</head>
<body id="main">
  <div class="login-wrap">
    <div class="login-html">
      <input id="tab-1" type="radio" name="tab" class="sign-in" checked>
      <label for="tab-1" class="tab">Sign In</label>
      <input id="tab-2" type="radio" name="tab" class="sign-up">
      <label for="tab-2" class="tab">Sign Up</label>
      <div class="login-form">
        <!-- Sign In Form -->
        <form method="post" action="" class="sign-in-htm">
          <div class="group">
            <label for="user" class="label">Username</label>
            <input name="username" id="user" type="text" class="input" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
          </div>
          <div class="group">
            <label for="pass" class="label">Password</label>
            <input name="password" id="pass" type="password" class="input" data-type="password" required>
          </div>
          <div class="group">
            <input id="check" type="checkbox" class="check" checked>
            <label for="check"><span class="icon"></span> Keep me Signed in</label>
          </div>
          <div class="group">
            <input type="submit" class="button" value="Sign In">
          </div>
          <div class="hr"></div>
          <div class="foot-lnk">
            <a href="#forgot">Forgot Password?</a>
          </div>
          <!-- Error Message -->
          <?php if (!empty($error_message)) { ?>
          <div class="message"><?php echo htmlspecialchars($error_message); ?></div>
          <?php } ?>
        </form>
        <!-- Sign Up Form -->
        <form method="post" action="register.php" class="sign-up-htm">
          <div class="group">
            <label for="user" class="label">Username</label>
            <input name="username" id="user" type="text" class="input" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
          </div>
          <div class="group">
            <label for="pass" class="label">Password</label>
            <input name="password" id="pass" type="password" class="input" data-type="password" required>
          </div>
          <div class="group">
            <label for="confirm_pass" class="label">Repeat Password</label>
            <input name="confirm_password" id="confirm_pass" type="password" class="input" data-type="password" required>
          </div>
          <div class="group">
            <label for="email" class="label">Email Address</label>
            <input name="email" id="email" type="email" class="input" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
          </div>
          <div class="group">
            <input type="submit" class="button" value="Sign Up">
          </div>
          <div class="hr"></div>
          <div class="foot-lnk">
            <label for="tab-1">Already Member?</label>
          </div>
          <!-- Success Message -->
          <?php if (isset($_SESSION['success_message'])) { ?>
          <div class="message success"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
          <?php unset($_SESSION['success_message']); } ?>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
