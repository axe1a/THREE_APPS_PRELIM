<?php require_once 'classloader.php'; ?>
<?php 
// Simple image display function
function displayImage($image_url) {
    if (!empty($image_url)) {
        echo '<img src="../' . htmlspecialchars($image_url) . '" class="article-image" alt="Article image">';
    } else {
        echo '<div class="no-image-placeholder">';
        echo '<i class="fas fa-image me-2"></i>No image available';
        echo '</div>';
    }
}
?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
  exit;
}  
?>
<!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Pending Articles</title>

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
          <h4 class="text-center mb-3 mt-2 section-title" style="border-bottom-width:2px;">
            <i class="fas fa-clock me-2"></i> Pending Articles
          </h4>
          
          <?php 
          try {
            $articles = $articleObj->getArticles(); 
          } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error loading articles: ' . $e->getMessage() . '</div>';
            $articles = [];
          }
          ?>
          
          <?php if (empty($articles)): ?>
            <div class="alert alert-info text-center">
              <i class="fas fa-info-circle me-2"></i>No pending articles at the moment.
            </div>
          <?php else: ?>
            <?php foreach ($articles as $article) { ?>
            <div class="card shadow article-card">
              <div class="card-body">
                <!-- Fixed image display -->
                <?php displayImage($article['image_url']); ?>
                
                <h3 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h3> 
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <small class="text-muted">
                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($article['username']); ?> 
                    <i class="fas fa-clock ms-3 me-1"></i><?php echo $article['created_at']; ?>
                  </small>
                  <?php if ($article['is_active'] == 0) { ?>
                    <span class="status-badge bg-warning text-dark">
                      <i class="fas fa-clock me-1"></i>PENDING
                    </span>
                  <?php } ?>
                  <?php if ($article['is_active'] == 1) { ?>
                    <span class="status-badge bg-success">
                      <i class="fas fa-check-circle me-1"></i>ACTIVE
                    </span>
                  <?php } ?>
                </div>
                
                <p class="card-text"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                
                <div class="action-buttons">
                  <form class="delete-article-form">
                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                    <button type="submit" class="btn btn-danger">
                      <i class="fas fa-trash me-1"></i>Delete
                    </button>
                  </form>
                  
                  <form class="update-article-status">
                    <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                    <div class="input-group">
                      <select name="status" class="custom-select status-select">
                        <option value="">Change Status</option>
                        <option value="0" <?php echo $article['is_active'] == 0 ? 'selected' : ''; ?>>Pending</option>
                        <option value="1" <?php echo $article['is_active'] == 1 ? 'selected' : ''; ?>>Active</option>
                      </select>
                      <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit">
                          <i class="fas fa-save"></i>
                        </button>
                      </div>
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
      // Delete article
      $('.delete-article-form').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var formData = {
          article_id: form.find('input[name="article_id"]').val(),
          deleteArticleBtn: 1
        }
        
        if (confirm("Are you sure you want to delete this article?")) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(response) {
              if (response == "1") {
                form.closest('.article-card').fadeOut(300, function() {
                  $(this).remove();
                  if ($('.article-card').length === 0) {
                    location.reload();
                  }
                });
              } else {
                alert("Deletion failed. Please try again.");
              }
            },
            error: function() {
              alert("An error occurred. Please try again.");
            }
          });
        }
      });

      // Update article status
      $('.update-article-status').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var formData = {
          article_id: form.find('input[name="article_id"]').val(),
          status: form.find('.status-select').val(),
          updateArticleVisibility: 1
        }

        if (formData.status === "") {
          alert("Please select a status");
          return;
        }

        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(response) {
            if (response == "1") {
              alert("Status updated successfully!");
              location.reload();
            } else {
              alert("Status update failed. Please try again.");
            }
          },
          error: function() {
            alert("An error occurred. Please try again.");
          }
        });
      });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
  </body>
</html>