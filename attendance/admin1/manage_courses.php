<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Course.php';
$conn = new mysqli('localhost', 'root', '', 'pt2attendance');

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
    header('Location: ../student/login.php');
    exit;
}
$data = $_SESSION['user_data'];
$admin = new Admin($data['id'], $data['name'], $data['email'], $data['role'], $data['course_id'], $data['year_level']);

// Add course
if (isset($_POST['add'])) {
    $admin->addCourse($conn, $_POST['course_name']);
}

// Edit course
if (isset($_POST['edit'])) {
    $admin->editCourse($conn, $_POST['course_id'], $_POST['course_name']);
}

$courses = Course::getAllCourses($conn);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Courses</title>
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
    <div class="auth-card" style="width:100%; max-width:800px;">
      <h2 class="card-title">Manage Courses</h2>

      <!-- Add Course -->
      <form method="post" class="form" style="margin-bottom:20px;">
        <div class="row">
          <label for="course_name">Course Name</label>
          <input type="text" id="course_name" name="course_name" placeholder="Enter course name" required>
        </div>
        <button type="submit" name="add" class="btn primary">Add Course</button>
      </form>

      <!-- Courses Table -->
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr style="background:#f3f4f6; text-align:left;">
            <th style="padding:10px;">ID</th>
            <th style="padding:10px;">Name</th>
            <th style="padding:10px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($courses as $course): ?>
          <tr style="border-bottom:1px solid #e5e7eb;">
            <form method="post">
              <td style="padding:10px;"><?= $course['id'] ?></td>
              <td style="padding:10px;">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <input type="text" name="course_name" value="<?= htmlspecialchars($course['name']) ?>" style="padding:6px 8px; border-radius:8px; border:1px solid #d1d5db; width:100%;">
              </td>
              <td style="padding:10px;">
                <button type="submit" name="edit" class="btn ghost">Edit</button>
              </td>
            </form>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

            <!-- Back Button -->
      <div style="margin-top:20px; text-align:center;">
        <a href="index.php" class="btn ghost">← Back to Dashboard</a>
      </div>
      
    </div>
  </main>

</body>
</html>
