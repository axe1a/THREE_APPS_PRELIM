<?php require_once 'classloader.php'; ?>
<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: ../client/login.php");
}

if (!$userObj->isFiverrAdministrator()) {
  header("Location: ../client/index.php");
} 
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <title>User Management - Admin Panel</title>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #6c757d;">
    <a class="navbar-brand" href="index.php">Admin Panel</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="categories.php">Categories</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="users.php">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../client/index.php">Client Panel</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../freelancer/index.php">Freelancer Panel</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="core/handleForms.php?logoutUserBtn=1">Logout</a>
        </li>
      </ul>
    </div>
  </nav>
  
  <div class="container-fluid mt-4">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-4">User Management</h2>
        
        <!-- Success/Error Messages -->
        <?php  
        if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
          if ($_SESSION['status'] == "200") {
            echo "<div class='alert alert-success'>{$_SESSION['message']}</div>";
          } else {
            echo "<div class='alert alert-danger'>{$_SESSION['message']}</div>"; 
          }
          unset($_SESSION['message']);
          unset($_SESSION['status']);
        }
        ?>
        
        <!-- Users List -->
        <div class="card">
          <div class="card-header">
            <h4>All Users</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Role</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $users = $userObj->getUsers();
                  foreach ($users as $user) {
                    $roleBadgeClass = '';
                    switch($user['role_name']) {
                      case 'fiverr_administrator':
                        $roleBadgeClass = 'badge-danger';
                        break;
                      case 'freelancer':
                        $roleBadgeClass = 'badge-info';
                        break;
                      case 'client':
                        $roleBadgeClass = 'badge-success';
                        break;
                      default:
                        $roleBadgeClass = 'badge-secondary';
                    }
                    
                    echo "<tr>";
                    echo "<td>{$user['user_id']}</td>";
                    echo "<td>{$user['username']}</td>";
                    echo "<td>{$user['email']}</td>";
                    echo "<td>" . ($user['contact_number'] ?: '<em>Not provided</em>') . "</td>";
                    echo "<td><span class='badge {$roleBadgeClass}'>" . ucfirst(str_replace('_', ' ', $user['role_name'])) . "</span></td>";
                    echo "<td>" . date('M d, Y', strtotime($user['date_added'])) . "</td>";
                    echo "<td>";
                    echo "<a href='edit_user.php?id={$user['user_id']}' class='btn btn-sm btn-warning'>Edit</a> ";
                    if ($user['user_id'] != $_SESSION['user_id']) {
                      echo "<a href='core/handleForms.php?deleteUser={$user['user_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
