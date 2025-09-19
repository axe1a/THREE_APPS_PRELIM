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
        <div class="col-md-8 col-lg-6">
          <div class="card p-4 mb-4 shadow-sm">
            <h3 class="text-center mb-4"><i class="fas fa-plus-circle me-2"></i> Create New Article</h3>
            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <label for="title"><i class="fas fa-heading me-2"></i> Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter article title">
              </div>
              <div class="form-group">
                <label for="description"><i class="fas fa-align-left me-2"></i> Content</label>
                <textarea name="description" id="description" class="form-control" rows="5" placeholder="Write your article content here"></textarea>
              </div>
              <div class="form-group">
                <label for="category_id"><i class="fas fa-tag me-2"></i> Category</label>
                <select class="form-control" id="category_id" name="category_id">
                  <option value="">Select a category (optional)</option>
                  <?php 
                  $categoryObj = new Category();
                  $categories = $categoryObj->getCategories();
                  foreach ($categories as $category) {
                    echo '<option value="' . $category['category_id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label for="article_image"><i class="fas fa-image me-2"></i> Article Image</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="article_image" name="article_image" accept="image/*">
                  <label class="custom-file-label" for="article_image">Choose image</label>
                </div>
                <img id="new-article-preview" class="article-image d-none mt-3" alt="Selected image preview" />
              </div>
              <button type="submit" class="btn btn-primary btn-block mt-3" name="insertAdminArticleBtn">
                <i class="fas fa-paper-plane me-2"></i> Publish Article
              </button>
            </form>
          </div>

          <h4 class="text-center mb-3 mt-2 section-title" style="border-bottom-width:2px;">
            <i class="fas fa-clock me-2"></i> Pending Articles
          </h4>
          
          <?php $articles = $articleObj->getArticles(); ?>
          <?php if (empty($articles)): ?>
            <div class="alert alert-info text-center">
              <i class="fas fa-info-circle me-2"></i>No pending articles at the moment.
            </div>
          <?php else: ?>
            <?php foreach ($articles as $article) { ?>
            <div class="card mt-4 shadow articleCard">
              <div class="card-body">
                <!-- Fixed image display -->
                <?php if (!empty($article['image_url'])): ?>
                  <img src="<?php echo htmlspecialchars(getImagePath($article['image_url'])); ?>" class="article-image" alt="Article image">
                <?php else: ?>
                  <div class="no-image-placeholder">
                    <i class="fas fa-image me-2"></i>No image available
                  </div>
                <?php endif; ?>
                
                <h3 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h3> 
                <?php if (!empty($article['category_name'])): ?>
                  <span class="badge badge-info mb-2">
                    <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($article['category_name']); ?>
                  </span>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <small class="text-muted">
                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($article['username']); ?> 
                    <i class="fas fa-clock ms-3 me-1"></i> <?php echo $article['created_at']; ?>
                  </small>
                  <?php if ($article['is_active'] == 0) { ?>
                    <span class="status-badge bg-warning text-dark">
                      <i class="fas fa-clock me-1"></i> PENDING
                    </span>
                  <?php } ?>
                  <?php if ($article['is_active'] == 1) { ?>
                    <span class="status-badge bg-success">
                      <i class="fas fa-check-circle me-1"></i> ACTIVE
                    </span>
                  <?php } ?>
                </div>
                
                <p class="card-text"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                
                <div class="action-buttons">
                  <form class="deleteArticleForm">
                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                    <button type="submit" class="btn btn-danger deleteArticleBtn">
                      <i class="fas fa-trash me-1"></i> Delete
                    </button>
                  </form>
                  
                  <form class="updateArticleStatus">
                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                    <div class="input-group">
                      <select name="is_active" class="custom-select is_active_select" article_id="<?php echo $article['article_id']; ?>">
                        <option value="">Change Status</option>
                        <option value="0" <?php echo $article['is_active'] == 0 ? 'selected' : ''; ?>>Pending</option>
                        <option value="1" <?php echo $article['is_active'] == 1 ? 'selected' : ''; ?>>Active</option>
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">
                          <i class="fas fa-sync-alt"></i>
                        </button>
                      </div>
                    </div>
                  </form>
                  
                  <button class="btn btn-outline-primary edit-toggle-btn" data-article-id="<?php echo $article['article_id']; ?>">
                    <i class="fas fa-edit me-1"></i> Edit
                  </button>
                </div>
                
                <div class="updateArticleForm mt-4 d-none" id="edit-form-<?php echo $article['article_id']; ?>">
                  <h4><i class="fas fa-edit me-2"></i> Edit Article</h4>
                  <form action="core/handleForms.php" method="POST">
                    <div class="form-group">
                      <label for="edit-title-<?php echo $article['article_id']; ?>">Title</label>
                      <input type="text" class="form-control" id="edit-title-<?php echo $article['article_id']; ?>" name="title" value="<?php echo htmlspecialchars($article['title']); ?>">
                    </div>
                    <div class="form-group">
                      <label for="edit-description-<?php echo $article['article_id']; ?>">Content</label>
                      <textarea name="description" id="edit-description-<?php echo $article['article_id']; ?>" class="form-control" rows="5"><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>
                    <div class="form-group">
                      <label for="edit-category-<?php echo $article['article_id']; ?>">Category</label>
                      <select class="form-control" id="edit-category-<?php echo $article['article_id']; ?>" name="category_id">
                        <option value="">Select a category (optional)</option>
                        <?php 
                        $categories = $categoryObj->getCategories();
                        foreach ($categories as $category) {
                          $selected = ($category['category_id'] == $article['category_id']) ? 'selected' : '';
                          echo '<option value="' . $category['category_id'] . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
                        }
                        ?>
                      </select>
                      <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                    </div>
                    <div class="d-flex justify-content-end">
                      <button type="submit" class="btn btn-primary" name="editArticleBtn">
                        <i class="fas fa-save me-1"></i>Save Changes
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>  
            <?php } ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <script>
      // Show filename and preview in file input
      $('.custom-file-input').on('change', function() {
        const file = this.files && this.files[0];
        if (file) {
          // Update label with real file name
          $(this).next('.custom-file-label').addClass("selected").html(file.name);
          // Show preview
          const url = URL.createObjectURL(file);
          $('#new-article-preview').attr('src', url).removeClass('d-none');
        } else {
          $(this).next('.custom-file-label').removeClass("selected").html('Choose image');
          $('#new-article-preview').attr('src', '').addClass('d-none');
        }
      });

      // Toggle edit form
      $('.edit-toggle-btn').on('click', function() {
        var articleId = $(this).data('article-id');
        $('#edit-form-' + articleId).toggleClass('d-none');
      });

      // Delete article
      $('.deleteArticleForm').on('submit', function(event) {
        event.preventDefault();
        var formData = {
          article_id: $(this).find('.article_id').val(),
          deleteArticleBtn: 1
        }
        if (confirm("Are you sure you want to delete this article?")) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(data) {
              if (data) {
                location.reload();
              } else {
                alert("Deletion failed");
              }
            }
          })
        }
      })

      // Update article status
      $('.updateArticleStatus').on('submit', function(event) {
        event.preventDefault();
        var formData = {
          article_id: $(this).find('.article_id').val(),
          status: $(this).find('.is_active_select').val(),
          updateArticleVisibility: 1
        }

        if (formData.article_id != "" && formData.status != "") {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(data) {
              if (data) {
                location.reload();
              } else {
                alert("Status update failed");
              }
            }
          })
        }
      })
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
  </body>
</html>