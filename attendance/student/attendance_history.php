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
$history = $student->getAttendanceHistory($conn);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Attendance History</title>
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
    <div class="auth-card" style="width:100%; max-width:700px;">
      <h2 class="card-title">Attendance History</h2>

      <table style="width:100%; border-collapse:collapse; margin-top:10px;">
        <thead>
          <tr style="background:#f3f4f6; text-align:left;">
            <th style="padding:10px;">Date</th>
            <th style="padding:10px;">Time In</th>
            <th style="padding:10px;">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($history as $row): ?>
            <tr style="border-bottom:1px solid #e5e7eb;">
              <td style="padding:10px;"><?= htmlspecialchars($row['date']) ?></td>
              <td style="padding:10px;"><?= htmlspecialchars($row['time_in']) ?></td>
              <td style="padding:10px;"><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>
