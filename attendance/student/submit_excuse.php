<?php
session_start();
require_once '../classes/Student.php';
require_once '../classes/User.php';
require_once '../classes/ExcuseLetter.php';
require_once '../classes/Course.php';

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

$message = '';
$error = '';

// Handle flash messages from redirect
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $flash_success = $_SESSION['flash_success'] ?? false;
    unset($_SESSION['flash_message'], $_SESSION['flash_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $reason = trim($_POST['reason']);
    $date_of_absence = $_POST['date_of_absence'];
    $supporting_document = null;

    // Handle file upload
    if (isset($_FILES['supporting_document']) && $_FILES['supporting_document']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/excuse_letters/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['supporting_document']['name'], PATHINFO_EXTENSION);
        $filename = 'excuse_' . $student->getId() . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['supporting_document']['tmp_name'], $file_path)) {
            $supporting_document = $filename;
        }
    }

    if (empty($subject) || empty($reason) || empty($date_of_absence)) {
        $error = 'Please fill in all required fields.';
    } else {
        $excuse_letter = new ExcuseLetter(
            null,
            $student->getId(),
            $student->getCourseId(),
            $subject,
            $reason,
            $date_of_absence,
            $supporting_document
        );

        if ($excuse_letter->submit($conn)) {
            // Store success in session and redirect
            $_SESSION['flash_message'] = 'Excuse letter submitted successfully!';
            $_SESSION['flash_success'] = true;
            header("Location: submit_excuse.php");
            exit;
        } else {
            $error = 'Failed to submit excuse letter. Please try again.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Submit Excuse Letter</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
    :root {
      --accent: #16a34a;
      --accent-2: #15803d;
    }
    textarea {
      font-family: inherit;
      font-size: inherit;
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
    <div class="auth-card" style="width:100%; max-width:600px;">
      <h2 class="card-title">Submit Excuse Letter</h2>

      <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="form">
        <div class="form-group">
          <label for="subject">Subject*</label>
          <input type="text" id="subject" name="subject" required 
            value="<?= $error ? htmlspecialchars($_POST['subject'] ?? '') : '' ?>">
        </div>

        <div class="form-group">
          <label for="reason">Reason for Absence*</label>
          <textarea id="reason" name="reason" rows="4" required 
            placeholder="Please provide a detailed reason for your absence..."><?= $error ? htmlspecialchars($_POST['reason'] ?? '') : '' ?></textarea>
        </div>

        <div class="form-group">
          <label for="date_of_absence">Date of Absence*</label>
          <input type="date" id="date_of_absence" name="date_of_absence" required 
            value="<?= $error ? htmlspecialchars($_POST['date_of_absence'] ?? '') : '' ?>">
        </div>

        <div class="form-group">
          <label for="supporting_document">Supporting Document (Optional)</label>
          <input type="file" id="supporting_document" name="supporting_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
          <small>Upload medical certificate, official document, or other supporting evidence (PDF, DOC, DOCX, JPG, PNG)</small>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn primary">Submit Excuse Letter</button>
          <a href="index.php" class="btn secondary">Cancel</a>
        </div>
      </form>
    </div>
  </main>

</body>
</html>
