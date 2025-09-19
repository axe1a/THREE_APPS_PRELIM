<?php
session_start();
require_once '../classes/Student.php';
require_once '../classes/User.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'student') {
    header('Location: ../login.php');
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
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Dashboard</title>
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
        <a href="logout.php" class="secondary">Logout</a>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="main">
    <div class="auth-card" style="width:100%; max-width:500px; text-align:center;">
      <h2 class="card-title">Welcome, <?= htmlspecialchars($student->getName()); ?>!</h2>

      <div class="form" style="margin-top:20px;">
        <a href="file_attendance.php" class="btn primary">File Attendance</a>
        <a href="attendance_history.php" class="btn primary">View Attendance History</a>
        <a href="submit_excuse.php" class="btn primary">Submit Excuse Letter</a>
        <a href="excuse_status.php" class="btn primary">Check Excuse Status</a>
      </div>
    </div>
  </main>

</body>
</html>
