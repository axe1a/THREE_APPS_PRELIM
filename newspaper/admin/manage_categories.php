<?php require_once 'classloader.php'; ?>
<?php require_once '../helpers.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
}  
?>
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
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
          
          <!-- Add New Category Form -->
          <div class="card p-4 mb-4 shadow-sm">
            <h3 class="text-center mb-4"><i class="fas fa-plus-circle me-2"></i> Add New Category</h3>
            <form action="core/handleForms.php" method="POST">
              <div class="form-group">
                <label for="category_name"><i class="fas fa-tag me-2"></i> Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" required>
              </div>
              <div class="form-group">
                <label for="category_description"><i class="fas fa-align-left me-2"></i> Description (Optional)</label>
                <textarea name="category_description" id="category_description" class="form-control" rows="3" placeholder="Enter category description"></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-block mt-3" name="addCategoryBtn">
                <i class="fas fa-plus me-2"></i> Add Category
              </button>
            </form>
          </div>

          <!-- Categories List -->
          <h4 class="text-center mb-3 mt-2 section-title" style="border-bottom-width:2px;">
            <i class="fas fa-list me-2"></i> Manage Categories
          </h4>
          
          <?php 
          $categoryObj = new Category();
          $categories = $categoryObj->getCategoriesWithCount(); 
          ?>
          <?php if (empty($categories)): ?>
            <div class="alert alert-info text-center">
              <i class="fas fa-info-circle me-2"></i> No categories found.
            </div>
          <?php else: ?>
            <div class="row">
              <?php foreach ($categories as $category) { ?>
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm h-100">
                  <div class="card-body">
                    <h5 class="card-title">
                      <i class="fas fa-tag me-2"></i> <?php echo htmlspecialchars($category['name']); ?>
                    </h5>
                    <?php if (!empty($category['description'])): ?>
                      <p class="card-text text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center">
                      <small class="text-muted">
                        <i class="fas fa-file-alt me-1"></i> <?php echo $category['article_count']; ?> articles
                      </small>
                      <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary btn-sm edit-category-btn" 
                                data-category-id="<?php echo $category['category_id']; ?>"
                                data-category-name="<?php echo htmlspecialchars($category['name']); ?>"
                                data-category-description="<?php echo htmlspecialchars($category['description']); ?>">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm delete-category-btn" 
                                data-category-id="<?php echo $category['category_id']; ?>"
                                data-category-name="<?php echo htmlspecialchars($category['name']); ?>">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editCategoryModalLabel"><i class="fas fa-edit me-2"></i> Edit Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="core/handleForms.php" method="POST">
            <div class="modal-body">
              <div class="form-group">
                <label for="edit_category_name">Category Name</label>
                <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
              </div>
              <div class="form-group">
                <label for="edit_category_description">Description (Optional)</label>
                <textarea name="category_description" id="edit_category_description" class="form-control" rows="3"></textarea>
              </div>
              <input type="hidden" name="category_id" id="edit_category_id">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary" name="updateCategoryBtn">
                <i class="fas fa-save me-1"></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <script>
      // Edit category button click
      $('.edit-category-btn').on('click', function() {
        var categoryId = $(this).data('category-id');
        var categoryName = $(this).data('category-name');
        var categoryDescription = $(this).data('category-description');
        
        $('#edit_category_id').val(categoryId);
        $('#edit_category_name').val(categoryName);
        $('#edit_category_description').val(categoryDescription);
        
        $('#editCategoryModal').modal('show');
      });

      // Delete category button click
      $('.delete-category-btn').on('click', function() {
        var categoryId = $(this).data('category-id');
        var categoryName = $(this).data('category-name');
        
        if (confirm("Are you sure you want to delete the category '" + categoryName + "'? This action cannot be undone.")) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: {
              category_id: categoryId,
              deleteCategoryBtn: 1
            },
            success: function(data) {
              if (data) {
                location.reload();
              } else {
                alert("Failed to delete category. It may be in use by articles.");
              }
            }
          });
        }
      });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
  </body>
</html>
