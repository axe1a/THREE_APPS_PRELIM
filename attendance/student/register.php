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
    $role = 'student'; // always student
    $course_id = $_POST['course_id'] ?? null;
    $year_level = $_POST['year_level'] ?? null;

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, course_id, year_level) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $name, $email, $password, $role, $course_id, $year_level);
    if ($stmt->execute()) {
        $success = "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        $error = "Registration failed: " . $conn->error;
    }
}

$courses = Course::getAllCourses($conn);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Registration</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
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
      <h2 class="card-title">Student Registration</h2>

      <?php if (!empty($success)): ?>
        <p class="note" style="color: green;"><?= $success ?></p>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <p class="note" style="color: var(--danger);"><?= $error ?></p>
      <?php endif; ?>

      <form method="post" class="form">
        <div class="row">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required placeholder="Enter your full name">
        </div>

        <div class="row">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required placeholder="Enter your email">
        </div>

        <div class="row">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required placeholder="Enter your password">
        </div>

        <div class="row">
          <label for="course_id">Course</label>
          <select id="course_id" name="course_id" required>
            <option value="">-- Select --</option>
            <?php foreach ($courses as $course): ?>
              <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="row">
          <label for="year_level">Year Level</label>
          <input type="number" id="year_level" name="year_level" min="1" max="5" required>
        </div>

        <input type="hidden" name="role" value="student">

        <button type="submit" class="btn primary">Register</button>
      </form>

      <div class="form-footer">
        <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </div>
  </main>

</body>
</html>
