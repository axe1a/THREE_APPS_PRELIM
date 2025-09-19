<?php 
require_once 'classloader.php'; 
require_once '../helpers.php'; // ✅ include helper

if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
}

if ($userObj->isAdmin()) {
  header("Location: ../admin/index.php");
}  
?>
<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <input type="text" class="form-control mt-4" name="title" placeholder="Input title here">
          </div>
          <div class="form-group">
            <textarea name="description" class="form-control mt-4" placeholder="Submit an article!"></textarea>
          </div>
          <div class="form-group">
            <label for="category_id"><i class="fas fa-tag me-2"></i> Category</label>
            <select class="form-control" id="category_id" name="category_id">
              <option value="">Select a category (optional)</option>
              <?php 
              $categories = $categoryObj->getCategories();
              foreach ($categories as $category) {
                echo '<option value="' . $category['category_id'] . '">' . htmlspecialchars($category['name']) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="article_image"><i class="fas fa-image me-2"></i> Upload Image</label>
            <input type="file" id="article_image" name="article_image" class="form-control-file mt-2">
          </div>
          <input type="submit" class="btn btn-primary form-control float-right mt-4 mb-4" name="insertArticleBtn" value="Submit">
        </form>

        <h4 class="text-center section-title" style="border-bottom-width:2px;">
          <i class="fas fa-file-pen me-2"></i> Your submitted articles
        </h4>
        <?php $articles = $articleObj->getArticlesByUserID($_SESSION['user_id']); ?>
        <?php foreach ($articles as $article) { ?>
        <div class="card mt-4 shadow article-card">
          <div class="card-body">
            <?php displayImage($article['image_url']); ?>

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

            <p class="card-text"><?php echo nl2br(htmlspecialchars($article['content'])); ?> </p>

            <div class="action-buttons">
              <form class="deleteArticleForm">
                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>" class="article_id">
                <button type="submit" class="btn btn-danger">
                  <i class="fas fa-trash me-1"></i> Delete
                </button>
              </form>
              
              <button class="btn btn-outline-primary edit-toggle-btn" type="button">
                <i class="fas fa-edit me-1"></i> Edit
              </button>
            </div>

            <div class="updateArticleForm d-none mt-4">
              <h4><i class="fas fa-pen-to-square me-2"></i> Edit the article</h4>
              <form action="core/handleForms.php" method="POST">
                <div class="form-group mt-4">
                  <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($article['title']); ?>">
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
                </div>
                <div class="form-group">
                  <textarea name="description" class="form-control"><?php echo htmlspecialchars($article['content']); ?></textarea>
                  <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                  <button type="submit" class="btn btn-primary float-right mt-4" name="editArticleBtn">
                    <i class="fas fa-save me-1"></i> Save Changes
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>  
        <?php } ?> 
      </div>
    </div>
  </div>
  <script>
    // Toggle edit form
    $('.article-card').on('click', '.edit-toggle-btn', function () {
      var updateArticleForm = $(this).closest('.article-card').find('.updateArticleForm');
      updateArticleForm.toggleClass('d-none');
    });

    // Delete article
    $('.article-card').on('submit', '.deleteArticleForm', function (event) {
      event.preventDefault();
      var formData = {
        article_id: $(this).closest('.article-card').find('.article_id').val(),
        deleteArticleBtn: 1
      }
      if (confirm("Are you sure you want to delete this article?")) {
        var card = $(this).closest('.article-card');
        $.ajax({
          type:"POST",
          url: "core/handleForms.php",
          data:formData,
          success: function (data) {
            if (data == '1') {
              card.fadeOut(300, function () { $(this).remove(); });
            }
            else{
              alert("Deletion failed");
            }
          },
          error: function () {
            alert("An error occurred. Please try again.");
          }
        })
      }
    })
  </script>
</body>
</html>
