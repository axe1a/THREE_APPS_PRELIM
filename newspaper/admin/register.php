<?php require_once 'classloader.php'; ?>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <link rel="stylesheet" href="../styles.css">
  <title>Admin Register</title>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
         <?php  
         if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
          $isOk = $_SESSION['status'] == "200";
          echo '<div class="alert ' . ($isOk ? 'alert-success' : 'alert-danger') . '">' . $_SESSION['message'] . '</div>';
          unset($_SESSION['message']);
          unset($_SESSION['status']);
        }
        ?>
        <div class="card-header text-center">
          <h3><i class="fas fa-user-shield me-2"></i> Register as Admin</h3>
        </div>
        <form action="core/handleForms.php" method="POST">
          <div class="card-body">
            <div class="form-group">
              <label>Username</label>
              <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-2" name="insertNewUserBtn">
              <i class="fas fa-user-plus me-1"></i> Create Account
            </button>
            <p class="mt-3 text-center">Already have an account? <a href="login.php">Sign in</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>