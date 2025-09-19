<?php
session_start();
require_once '../classes/User.php';
require_once '../classes/Admin.php';
require_once '../classes/Student.php';
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
        header('Location: index.php');
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
  <title>Login - Attendance System</title>
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
      <h2 class="card-title">Login</h2>

      <?php if (!empty($error)): ?>
        <p class="note" style="color: var(--danger);"><?= $error ?></p>
      <?php endif; ?>

      <form method="post" class="form">
        <div class="row">
          <input type="email" id="email" name="email" required placeholder="Email">
        </div>

        <div class="row">
          <input type="password" id="password" name="password" required placeholder="Password">
        </div>

        <button type="submit" class="btn primary">Login</button>
      </form>

      <div class="form-footer">
        <p>Don’t have an account? <a href="register.php">Register.</a></p>
      </div>
    </div>
  </main>

</body>
</html>
