<?php
session_start();
require_once '../classes/Student.php';
require_once '../classes/User.php';
require_once '../classes/ExcuseLetter.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['role'] !== 'student') {
    header('Location: ../index.php');
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

$conn = new mysqli('localhost', 'root', '', 'pt2attendance');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$excuse_letters = ExcuseLetter::getByStudent($conn, $student->getId());
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Excuse Letter Status</title>
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
        <a href="index.php" class="secondary">Dashboard</a>
        <a href="logout.php" class="secondary">Logout</a>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="main">
    <div class="auth-card" style="width:100%; max-width:800px;">
      <h2 class="card-title">My Excuse Letters</h2>

      <?php if (empty($excuse_letters)): ?>
        <div class="alert alert-info">
          <p>You haven't submitted any excuse letters yet.</p>
          <a href="submit_excuse.php" class="btn primary">Submit Your First Excuse Letter</a>
        </div>
      <?php else: ?>
        <div class="form" style="margin-bottom: 20px;">
          <a href="submit_excuse.php" class="btn primary">Submit New Excuse Letter</a>
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
                <a href="../uploads/excuse_letters/<?= htmlspecialchars($excuse['supporting_document']) ?>" target="_blank" class="btn secondary" style="padding: 4px 8px; font-size: 0.75rem;">View Document</a>
              </div>
            <?php endif; ?>

            <?php if ($excuse['status'] !== 'pending' && $excuse['admin_comment']): ?>
              <div class="admin-comment">
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
