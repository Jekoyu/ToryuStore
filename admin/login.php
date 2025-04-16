<?php
// Start session
session_start();

include 'koneksi.php';

// Handle form submission
$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user = $_POST['username'];
  $pass = $_POST['password'];

  // Query to check user
  $sql = "SELECT * FROM admin WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $user);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Verify password
    if (password_verify($pass, $row['password'])) {
      // Save user data to session
      $_SESSION['admin_id'] = $row['id'];
      $_SESSION['username'] = $row['username'];

      // Redirect to dashboard
      header("Location: ds_sidebar_p.php");
      exit;
    } else {
      $error_message = "Invalid password.";
    }
  } else {
    $error_message = "Invalid username.";
  }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <!-- Link ke Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-container {
      max-width: 400px;
      width: 100%;
      padding: 20px;
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .login-container h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #343a40;
    }

    .login-container .btn-primary {
      width: 100%;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <h3>Login</h3>
    <?php if ($error_message): ?>
      <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form action="ds_sidebar_p.php" method="POST">
      <!-- Username Input -->
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
      </div>
      <!-- Password Input -->
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
      </div>
      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary">Login</button>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>