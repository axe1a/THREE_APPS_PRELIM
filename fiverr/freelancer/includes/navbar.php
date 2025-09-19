<?php
require_once 'classloader.php';
$categoryObj = new Category();
$categories = $categoryObj->getCategoriesWithSubcategories();
?>

<nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #0077B6;">
  <a class="navbar-brand" href="index.php">Freelancer Panel</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      
      <!-- Categories Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Categories
        </a>
        <div class="dropdown-menu p-2" aria-labelledby="categoriesDropdown" style="max-height: 60vh; overflow:auto; min-width: 280px;">
          <?php
          $current_category = null;
          foreach ($categories as $row) {
            if ($current_category !== $row['category_id']) {
              $current_category = $row['category_id'];
              echo '<div class="dropdown-header">' . htmlspecialchars($row['category_name']) . '</div>';
            }
            if (!empty($row['subcategory_id'])) {
              echo '<a class="dropdown-item" href="index.php?subcategory=' . $row['subcategory_id'] . '">' . htmlspecialchars($row['subcategory_name']) . '</a>';
            }
          }
          ?>
        </div>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" href="profile.php">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="your_proposals.php">Your Proposals</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="offers_from_clients.php">Offers From Clients</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="core/handleForms.php?logoutUserBtn=1">Logout</a>
      </li>
    </ul>
  </div>
</nav>

<style>

@media (min-width: 992px) {
  .navbar .dropdown:hover > .dropdown-menu { display: block; }
}

.dropdown-header {
  font-weight: 600;
  color: #111;
}
</style>