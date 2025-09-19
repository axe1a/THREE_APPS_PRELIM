<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Admin.php';
require_once '../classes/Student.php';
require_once '../classes/Course.php';
$conn = new mysqli('localhost', 'root', '', 'pt2attendance');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($password, $result['password'])) {
        $_SESSION['user_data'] = [
            'id' => $result['id'],
            'name' => $result['name'],
            'email' => $result['email'],
            'role' => $result['role'],
            'course_id' => $result['course_id'],
            'year_level' => $result['year_level']
        ];
        header('Location: ' . ($result['role'] === 'student' ? 'index.php' : '../admin1/index.php'));
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Login</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
    /* student-specific accent */
    :root {
      --accent: #16a34a;
      --accent-2: #15803d;
    }
  </style>
</head>
<body class="student-theme">

  <header class="site-header">
    <div class="header-inner">
      <a href="../index.php" class="brand">
        <div class="logo">SA</div>
        <h1>Student Attendance</h1>
      </a>
    </div>
  </header>

  <main class="main">
    <div class="auth-card">
      <h2 class="card-title">Student Login</h2>

      <?php if (!empty($error)): ?>
        <p class="note" style="color: var(--danger);"><?= $error ?></p>
      <?php endif; ?>

      <form method="post" class="form">
        <div class="row">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required placeholder="Enter your email">
        </div>

        <div class="row">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required placeholder="Enter your password">
        </div>

        <button type="submit" class="btn primary">Login</button>
      </form>

      <div class="form-footer">
        <p>Don’t have an account? <a href="register.php">Register</a></p>
      </div>
    </div>
  </main>

</body>
</html>
