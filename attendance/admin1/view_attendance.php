<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Course.php';
$conn = new mysqli('localhost', 'root', '', 'pt2attendance');

// ✅ use user_data instead of user object
if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
    header('Location: ../admin1/login.php'); // redirect to admin login instead
    exit;
}

$data = $_SESSION['user_data'];
$admin = new Admin(
    $data['id'],
    $data['name'],
    $data['email'],
    $data['role'],
    $data['course_id'],
    $data['year_level']
);

$courses = Course::getAllCourses($conn);

$attendances = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendances = $admin->getAttendancesByCourseYear($conn, $_POST['course_id'], $_POST['year_level']);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Attendance</title>
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
    <div class="auth-card" style="width:100%; max-width:900px;">
      <h2 class="card-title">View Attendance by Course / Year</h2>

      <!-- Form -->
      <form method="post" class="form" style="margin-bottom:20px;">
        <div class="row">
          <label for="course_id">Course</label>
          <select id="course_id" name="course_id" required>
            <?php foreach ($courses as $course): ?>
              <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="row">
          <label for="year_level">Year Level</label>
          <input type="number" id="year_level" name="year_level" min="1" max="5" required>
        </div>

        <button type="submit" class="btn primary">View</button>
      </form>

      <!-- Table -->
      <?php if ($attendances): ?>
        <table style="width:100%; border-collapse:collapse; margin-top:10px;">
          <thead>
            <tr style="background:#f3f4f6; text-align:left;">
              <th style="padding:10px;">Student Name</th>
              <th style="padding:10px;">Date</th>
              <th style="padding:10px;">Time In</th>
              <th style="padding:10px;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($attendances as $row): ?>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:10px;"><?= htmlspecialchars($row['name']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($row['date']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($row['time_in']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($row['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

      <!-- Back Button -->
      <div style="margin-top:20px; text-align:center;">
        <a href="index.php" class="btn ghost">← Back to Dashboard</a>
      </div>

    </div>
  </main>


</body>
</html>
