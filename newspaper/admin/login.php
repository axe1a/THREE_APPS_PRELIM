<!doctype html>
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
    <title>Admin Login</title>
  </head>
  <body>
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-header text-center">
              <h3><i class="fas fa-shield-halved me-2"></i> Admin Login</h3>
            </div>
            <form action="core/handleForms.php" method="POST">
              <div class="card-body">
                <?php  
                if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
                  $isOk = $_SESSION['status'] == "200";
                  echo '<div class="alert ' . ($isOk ? 'alert-success' : 'alert-danger') . '">' . $_SESSION['message'] . '</div>';
                }
                unset($_SESSION['message']);
                unset($_SESSION['status']);
                ?>
                <div class="form-group">
                  <label>Email</label>
                  <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3" name="loginUserBtn">
                  <i class="fas fa-right-to-bracket me-1"></i> Sign In
                </button>
                <p class="mt-3 text-center">Are you a writer? <a href="../writer/login.php">Go to writer login</a></p>
                <p class="text-center">Don't have an account yet? <a href="register.php">Register here</a></p>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>