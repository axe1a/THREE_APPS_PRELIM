<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/User.php';
require_once '../classes/ExcuseLetter.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $excuse_id = (int)$_POST['excuse_id'];
    $action = $_POST['action'];
    $admin_comment = trim($_POST['admin_comment'] ?? '');

    if ($action === 'approve') {
        $success = ExcuseLetter::approve($conn, $excuse_id, $admin->getId(), $admin_comment);
        $message = $success ? 'Excuse letter approved successfully!' : 'Failed to approve excuse letter.';
    } elseif ($action === 'reject') {
        $success = ExcuseLetter::reject($conn, $excuse_id, $admin->getId(), $admin_comment);
        $message = $success ? 'Excuse letter rejected successfully!' : 'Failed to reject excuse letter.';
    } else {
        $message = 'Invalid action.';
        $success = false;
    }

    // Redirect back to manage page with message
    $redirect_url = 'manage_excuse_letters.php';
    if (isset($_GET['course']) && $_GET['course']) {
        $redirect_url .= '?course=' . urlencode($_GET['course']);
    }
    if (isset($_GET['status']) && $_GET['status']) {
        $redirect_url .= (strpos($redirect_url, '?') !== false ? '&' : '?') . 'status=' . urlencode($_GET['status']);
    }
    
    header('Location: ' . $redirect_url . '?message=' . urlencode($message) . '&success=' . ($success ? '1' : '0'));
    exit;
}

// If not POST, redirect to manage page
header('Location: manage_excuse_letters.php');
exit;
?>
