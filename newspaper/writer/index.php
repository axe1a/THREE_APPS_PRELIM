<?php require_once 'classloader.php'; 
require_once '../helpers.php'; // ✅ include helper
?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if ($userObj->isAdmin()) {
  header("Location: ../admin/index.php");
  exit;
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
        <div class="col-md-6">
          <div class="card p-4 mb-4 shadow-sm">
            <h3 class="text-center mb-4"><i class="fas fa-plus-circle me-2"></i> Create New Article</h3>
            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <label for="title"><i class="fas fa-heading me-2"></i> Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Enter article title" required>
              </div>
              <div class="form-group">
                <label for="description"><i class="fas fa-align-left me-2"></i> Content</label>
                <textarea name="description" id="description" class="form-control" rows="5" placeholder="Write your article content here" required></textarea>
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
                <label for="article_image"><i class="fas fa-image me-2"></i> Article Image</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input" id="article_image" name="article_image" accept="image/*">
                  <label class="custom-file-label" for="article_image">Choose image</label>
                </div>
                <img id="new-article-preview" class="article-image d-none mt-3" alt="Selected image preview" />
              </div>
              <button type="submit" class="btn btn-primary btn-block mt-3" name="insertArticleBtn">
                <i class="fas fa-paper-plane me-2"></i> Submit
              </button>
            </form>
          </div>

          <h4 class="text-center mb-3 mt-2 section-title" style="border-bottom-width:2px;">
            <i class="fas fa-newspaper me-2"></i> All Articles
          </h4>
          <?php $articles = $articleObj->getActiveArticles(); ?>
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
              </div>
              <p class="card-text"><?php echo nl2br(htmlspecialchars($article['content'])); ?> </p>

              <?php if ($article['author_id'] != $_SESSION['user_id']) { ?>
              <?php 
                $status = $articleObj->getEditRequestStatus($article['article_id'], $_SESSION['user_id']);
                $disabled = $status === 'pending' || $status === 'accepted';
                $label = 'Request Edit Access';
                if ($status === 'pending') { $label = 'Request Pending'; }
                if ($status === 'accepted') { $label = 'Edit Access Granted'; }
                if ($status === 'rejected') { $label = 'Request Rejected - Request Again'; }
              ?>
              <form action="core/handleForms.php" method="POST" class="mt-2">
                <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                <button type="submit" class="btn btn-outline-primary" name="requestEdit" <?php echo $disabled ? 'disabled' : ''; ?>>
                  <i class="fas fa-handshake me-1"></i> <?php echo $label; ?>
                </button>
              </form>
              <?php } ?>
            </div>
          </div>  
          <?php } ?> 
        </div>
      </div>
    </div>

    <script>
      // Show filename and preview in file input
      $('.custom-file-input').on('change', function() {
        const file = this.files && this.files[0];
        if (file) {
          $(this).next('.custom-file-label').addClass("selected").html(file.name);
          const url = URL.createObjectURL(file);
          $('#new-article-preview').attr('src', url).removeClass('d-none');
        } else {
          $(this).next('.custom-file-label').removeClass("selected").html('Choose image');
          $('#new-article-preview').attr('src', '').addClass('d-none');
        }
      });
    </script>
  </body>
</html>