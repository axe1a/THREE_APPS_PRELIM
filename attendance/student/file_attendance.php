<?php
session_start();
require_once '../classes/Student.php';
$conn = new mysqli('localhost', 'root', '', 'pt2attendance');

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'student') {
    header('Location: login.php');
    exit;
}
$data = $_SESSION['user_data'];
$student = new Student(
    $data['id'],
    $data['name'],
    $data['email'],
    $data['role'],
    $data['course_id'],
    $data['year_level']
);

// check if already filed
$alreadyFiled = $student->hasFiledToday($conn);

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyFiled) {
    if ($student->fileAttendance($conn)) {
        $message = "Attendance filed!";
        $alreadyFiled = true; // prevent re-filing
    } else {
        $message = "Error filing attendance.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>File Attendance</title>
  <link rel="stylesheet" href="../styles.css">
    <style>
    :root {
      --accent: #16a34a;
      --accent-2: #15803d;
    }
  </style>
</head>
<body class="student-theme">

  <!-- Header -->
  <header class="site-header">
    <div class="header-inner">
      <a href="index.php" class="brand">
        <div class="logo">SA</div>
        <h1>Student Attendance</h1>
      </a>
      <div class="header-actions">
        <a href="index.php" class="secondary">Back</a>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="main">
    <div class="auth-card" style="text-align:center;">
      <h2 class="card-title">File Attendance</h2>

      <?php if ($message): ?>
        <p class="note" style="color: <?= strpos($message, 'filed') !== false ? 'green' : 'var(--danger)' ?>">
          <?= htmlspecialchars($message) ?>
        </p>
      <?php endif; ?>

      <form method="post" class="form">
        <button type="submit" class="btn primary" <?= $alreadyFiled ? 'disabled' : '' ?>>
          <?= $alreadyFiled ? 'Already Filed Today' : 'File Attendance' ?>
        </button>
      </form>
    </div>
  </main>

</body>
</html>
