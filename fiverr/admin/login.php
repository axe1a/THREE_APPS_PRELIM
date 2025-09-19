<?php require_once 'classloader.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <title>Admin Login</title>
    <style>
      body { font-family: Arial; background:#f7f7f9; }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 p-5">
          <div class="card shadow">
            <div class="card-header">
              <h2>Administrator Login</h2>
            </div>
            <form action="core/handleForms.php" method="POST">
              <div class="card-body">
                <?php  
                if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
                  if ($_SESSION['status'] == "200") {
                    echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
                  } else {
                    echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>"; 
                  }
                  unset($_SESSION['message']);
                  unset($_SESSION['status']);
                }
                ?>
                <div class="form-group">
                  <label>Email</label>
                  <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" class="form-control" name="password" required>
                </div>
                <input type="submit" class="btn btn-primary float-right mt-2" name="loginAdminBtn" value="Login">
                <div class="form-group">
                  <p>Don't have an account yet? You may <a href="register.php"> register here!</a></p>
              </div>
              </div>
              
            </form>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
