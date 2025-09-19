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
  <title>Category Management - Admin Panel</title>
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
        <li class="nav-item active">
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
        <h2 class="mb-4">Category Management</h2>
        
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
        
        <!-- Category Management -->
        <div class="card mb-4">
          <div class="card-header">
            <h4>Add New Category</h4>
          </div>
          <div class="card-body">
            <!-- Add Category Form -->
            <form method="POST" action="core/handleForms.php" class="mb-4">
              <div class="row">
                <div class="col-md-4">
                  <input type="text" class="form-control" name="category_name" placeholder="Category Name" required>
                </div>
                <div class="col-md-6">
                  <input type="text" class="form-control" name="category_description" placeholder="Category Description (Optional)">
                </div>
                <div class="col-md-2">
                  <button type="submit" name="addCategoryBtn" class="btn btn-primary">Add Category</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Categories List -->
        <div class="card">
          <div class="card-header">
            <h4>All Categories</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $categories = $categoryObj->getCategories(false);
                  foreach ($categories as $category) {
                    echo "<tr>";
                    echo "<td>{$category['category_id']}</td>";
                    echo "<td>{$category['category_name']}</td>";
                    echo "<td>" . ($category['description'] ?: '<em>No description</em>') . "</td>";
                    echo "<td>" . ($category['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>') . "</td>";
                    echo "<td>" . date('M d, Y', strtotime($category['date_created'])) . "</td>";
                    echo "<td>";
                    echo "<a href='edit_category.php?id={$category['category_id']}' class='btn btn-sm btn-warning'>Edit</a> ";
                    if ($category['is_active']) {
                      echo "<a href='core/handleForms.php?deactivateCategory={$category['category_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Deactivate</a>";
                    } else {
                      echo "<a href='core/handleForms.php?activateCategory={$category['category_id']}' class='btn btn-sm btn-success' onclick='return confirm(\"Are you sure?\")'>Activate</a>";
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
        
        <!-- Subcategory Management -->
        <div class="card mt-4">
          <div class="card-header">
            <h4>Subcategory Management</h4>
          </div>
          <div class="card-body">
            <!-- Add Subcategory Form -->
            <form method="POST" action="core/handleForms.php" class="mb-4">
              <div class="row">
                <div class="col-md-3">
                  <select class="form-control" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    $categories = $categoryObj->getCategories();
                    foreach ($categories as $category) {
                      echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <input type="text" class="form-control" name="subcategory_name" placeholder="Subcategory Name" required>
                </div>
                <div class="col-md-4">
                  <input type="text" class="form-control" name="subcategory_description" placeholder="Subcategory Description (Optional)">
                </div>
                <div class="col-md-2">
                  <button type="submit" name="addSubcategoryBtn" class="btn btn-primary">Add Subcategory</button>
                </div>
              </div>
            </form>
            
            <!-- Subcategories List -->
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $subcategories = $subcategoryObj->getAllSubcategories(false);
                  foreach ($subcategories as $subcategory) {
                    echo "<tr>";
                    echo "<td>{$subcategory['subcategory_id']}</td>";
                    echo "<td>{$subcategory['category_name']}</td>";
                    echo "<td>{$subcategory['subcategory_name']}</td>";
                    echo "<td>" . ($subcategory['description'] ?: '<em>No description</em>') . "</td>";
                    echo "<td>" . ($subcategory['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>') . "</td>";
                    echo "<td>" . date('M d, Y', strtotime($subcategory['date_created'])) . "</td>";
                    echo "<td>";
                    echo "<a href='edit_subcategory.php?id={$subcategory['subcategory_id']}' class='btn btn-sm btn-warning'>Edit</a> ";
                    if ($subcategory['is_active']) {
                      echo "<a href='core/handleForms.php?deactivateSubcategory={$subcategory['subcategory_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Deactivate</a>";
                    } else {
                      echo "<a href='core/handleForms.php?activateSubcategory={$subcategory['subcategory_id']}' class='btn btn-sm btn-success' onclick='return confirm(\"Are you sure?\")'>Activate</a>";
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
