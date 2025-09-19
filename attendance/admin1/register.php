<?php
require_once '../classes/User.php';
require_once '../classes/Admin.php';
require_once '../classes/Student.php';
require_once '../classes/Course.php';
$conn = new mysqli('localhost', 'root', '', 'pt2attendance');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin'; // always admin

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    if ($stmt->execute()) {
        $success = "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        $error = "Registration failed: " . $conn->error;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Attendance System</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body class="admin-theme">

  <!-- Header -->
  <header class="site-header">
    <div class="header-inner">
      <a href="../index.php" class="brand">
        <div class="logo">AS</div>
        <h1>Attendance System</h1>
      </a>
    </div>
  </header>

  <!-- Main -->
  <main class="main">
    <div class="auth-card">
      <h2 class="card-title">Register as Admin</h2>

      <?php if (!empty($success)): ?>
        <p class="note" style="color: green;"><?= $success ?></p>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <p class="note" style="color: var(--danger);"><?= $error ?></p>
      <?php endif; ?>

      <form method="post" class="form">
        <div class="row">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required placeholder="Full Name">
        </div>

        <div class="row">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required placeholder="Email">
        </div>

        <div class="row">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required placeholder="Password">
        </div>

        <!-- hidden role -->
        <input type="hidden" name="role" value="admin">

        <button type="submit" class="btn primary">Register</button>
      </form>

      <div class="form-footer">
        <p>Already have an account? <a href="login.php">Login.</a></p>
      </div>
    </div>
  </main>

</body>
</html>
