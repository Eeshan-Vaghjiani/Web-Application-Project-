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
$success_message = "";
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $confirm_password = trim($_POST['confirm_password']);
  $email = trim($_POST['email']);

  if ($password !== $confirm_password) {
    $error_message = "Passwords do not match";
  } else {
    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $error_message = "Username already taken";
    } else {
      // Hash the password
      $hashed_password = password_hash($password, PASSWORD_BCRYPT);

      // Insert the new user into the database
      $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $username, $hashed_password, $email);

      if ($stmt->execute()) {
        // Automatically log in the user and redirect to the dashboard
        $_SESSION['username'] = $username;
        header("Location: u_index.php");
        exit();
      } else {
        $error_message = "Error: " . $stmt->error;
      }
    }
    $stmt->close();
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="Style.css">
  <title>Register</title>
  <style>
    .message {
      position: absolute;
      top: calc(100% + 20px); /* Position right below the sign-up form */
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
      <input id="tab-1" type="radio" name="tab" class="sign-in">
      <label for="tab-1" class="tab">Sign In</label>
      <input id="tab-2" type="radio" name="tab" class="sign-up" checked>
      <label for="tab-2" class="tab">Sign Up</label>
      <div class="login-form">
        <!-- Sign Up Form -->
        <form method="post" action="" class="sign-up-htm">
          <div class="group">
            <label for="user" class="label">Username</label>
            <input name="username" id="user" type="text" class="input" value="<?php echo htmlspecialchars($username); ?>" required>
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
            <input name="email" id="email" type="email" class="input" value="<?php echo htmlspecialchars($email); ?>" required>
          </div>
          <div class="group">
            <input type="submit" class="button" value="Sign Up">
          </div>
          <div class="hr"></div>
          <div class="foot-lnk">
            <label for="tab-1">Already Member?</label>
          </div>
          <!-- Error or Success Message -->
          <?php if (!empty($error_message)) { ?>
          <div class="message"><?php echo htmlspecialchars($error_message); ?></div>
          <?php } ?>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
