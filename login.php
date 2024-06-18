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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
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
      $_SESSION['error_message'] = "Invalid password";
    }
  } else {
    $_SESSION['error_message'] = "User not found";
  }

  $stmt->close();
}

$conn->close();
?>
