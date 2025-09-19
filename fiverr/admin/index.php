<?php require_once 'classloader.php'; ?>
<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if (!$userObj->isFiverrAdministrator()) {
  header("Location: login.php");
  exit;
} 
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <title>Admin Panel - Fiverr Clone</title>
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
        <li class="nav-item">
          <a class="nav-link" href="users.php">Users</a>
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
        <h2 class="mb-4">Admin Dashboard</h2>
        
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
        
        <!-- Dashboard Cards -->
        <div class="row">
          <div class="col-md-3">
            <div class="card text-white bg-primary">
              <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <h2><?php echo count($userObj->getUsers()); ?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-white bg-success">
              <div class="card-body">
                <h5 class="card-title">Active Categories</h5>
                <h2><?php echo count($categoryObj->getCategories()); ?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-white bg-info">
              <div class="card-body">
                <h5 class="card-title">Active Subcategories</h5>
                <h2><?php echo count($subcategoryObj->getAllSubcategories()); ?></h2>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-white bg-warning">
              <div class="card-body">
                <h5 class="card-title">Administrators</h5>
                <h2><?php 
                $users = $userObj->getUsers();
                $adminCount = 0;
                foreach ($users as $user) {
                  if ($user['role_name'] === 'fiverr_administrator') {
                    $adminCount++;
                  }
                }
                echo $adminCount;
                ?></h2>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4>Quick Actions</h4>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <a href="categories.php" class="btn btn-primary btn-block">Manage Categories</a>
                  </div>
                  <div class="col-md-3">
                    <a href="users.php" class="btn btn-success btn-block">Manage Users</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
