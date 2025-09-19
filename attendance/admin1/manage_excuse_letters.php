<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/User.php';
require_once '../classes/ExcuseLetter.php';
require_once '../classes/Course.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'admin') {
    header('Location: ../index.php');
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

$conn = new mysqli('localhost', 'root', '', 'pt2attendance');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$courses = Course::getAllCourses($conn);
$selected_course = $_GET['course'] ?? '';
$selected_status = $_GET['status'] ?? '';

$excuse_letters = [];
if ($selected_course && $selected_status) {
    $excuse_letters = ExcuseLetter::getByCourseAndStatus($conn, $selected_course, $selected_status);
} elseif ($selected_course) {
    $excuse_letters = ExcuseLetter::getByCourse($conn, $selected_course);
} elseif ($selected_status) {
    $excuse_letters = ExcuseLetter::getByStatus($conn, $selected_status);
} else {
    $excuse_letters = ExcuseLetter::getPending($conn);
}

// Handle messages from process_excuse.php
$message = $_GET['message'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Excuse Letters</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
    :root {
      --accent: #0b79d0;
      --accent-2: #0a6ab3;
    }
    textarea {
      font-family: inherit;
      font-size: inherit;
    }
  </style>
</head>
<body class="admin-theme">

  <!-- Header -->
  <header class="site-header">
    <div class="header-inner">
      <a href="../index.php" class="brand">
        <div class="logo">AS</div>
        <h1>Attendance System</h1>
      </a>
      <div class="header-actions">
        <a href="index.php" class="secondary">Dashboard</a>
        <a href="logout.php" class="secondary">Logout</a>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="main">
    <div class="auth-card" style="width:100%; max-width:1000px;">
      <h2 class="card-title">Manage Excuse Letters</h2>

      <?php if ($message): ?>
        <div class="alert <?= $success ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <!-- Filter Section -->
      <div class="filter-section">
        <h3>Filter Excuse Letters</h3>
        <form method="GET" class="filter-row">
          <div class="filter-group">
            <label for="course">Course/Program:</label>
            <select id="course" name="course">
              <option value="">All Courses</option>
              <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>" <?= $selected_course == $course['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($course['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
              <option value="">All Status</option>
              <option value="pending" <?= $selected_status == 'pending' ? 'selected' : '' ?>>Pending</option>
              <option value="approved" <?= $selected_status == 'approved' ? 'selected' : '' ?>>Approved</option>
              <option value="rejected" <?= $selected_status == 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
          </div>
          
          <div class="filter-group">
            <button type="submit" class="btn primary">Filter</button>
            <a href="manage_excuse_letters.php" class="btn secondary">Clear</a>
          </div>
        </form>
      </div>

      <!-- Results -->
      <?php if (empty($excuse_letters)): ?>
        <div class="alert alert-info">
          <p>No excuse letters found matching your criteria.</p>
        </div>
      <?php else: ?>
        <div class="form" style="margin-bottom: 20px;">
          <p><strong><?= count($excuse_letters) ?></strong> excuse letter(s) found</p>
        </div>

        <?php foreach ($excuse_letters as $excuse): ?>
          <div class="excuse-card">
            <div class="excuse-header">
              <h3 class="excuse-subject"><?= htmlspecialchars($excuse['subject']) ?></h3>
              <span class="status-badge status-<?= $excuse['status'] ?>">
                <?= ucfirst($excuse['status']) ?>
              </span>
            </div>
            
            <div class="excuse-details">
              <strong>Student:</strong> <?= htmlspecialchars($excuse['student_name']) ?> (Year <?= $excuse['year_level'] ?>)<br>
              <strong>Course:</strong> <?= htmlspecialchars($excuse['course_name']) ?><br>
              <strong>Date of Absence:</strong> <?= date('F j, Y', strtotime($excuse['date_of_absence'])) ?><br>
              <strong>Submitted:</strong> <?= date('F j, Y g:i A', strtotime($excuse['submitted_at'])) ?>
            </div>

            <div class="excuse-reason">
              <strong>Reason:</strong><br>
              <?= nl2br(htmlspecialchars($excuse['reason'])) ?>
            </div>

            <?php if ($excuse['supporting_document']): ?>
              <div class="excuse-details">
                <strong>Supporting Document:</strong> 
                <a href="../uploads/excuse_letters/<?= htmlspecialchars($excuse['supporting_document']) ?>" target="_blank" class="btn secondary btn-small">View Document</a>
              </div>
            <?php endif; ?>

            <?php if ($excuse['status'] === 'pending'): ?>
              <div class="approval-form">
                <h4>Admin Action Required</h4>
                <form method="POST" action="process_excuse.php">
                  <input type="hidden" name="excuse_id" value="<?= $excuse['id'] ?>">
                  <textarea name="admin_comment" placeholder="Add your comment (optional)" rows="3"></textarea>
                  <div class="approval-actions">
                    <button type="submit" name="action" value="approve" class="btn primary btn-small">Approve</button>
                    <button type="submit" name="action" value="reject" class="btn secondary btn-small">Reject</button>
                  </div>
                </form>
              </div>
            <?php elseif ($excuse['admin_comment']): ?>
              <div class="approval-form">
                <h4>Admin Response:</h4>
                <p><?= nl2br(htmlspecialchars($excuse['admin_comment'])) ?></p>
                <small>Reviewed on <?= date('F j, Y g:i A', strtotime($excuse['reviewed_at'])) ?></small>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
