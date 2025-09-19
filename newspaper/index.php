<?php require_once 'writer/classloader.php'; ?>
<?php require_once 'helpers.php'; ?>
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
    <link rel="stylesheet" href="styles.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #355E3B;">
      <a class="navbar-brand" href="#">School Publication</a>
      <div class="ml-auto">
        <a href="writer/login.php" class="btn btn-light mr-2"><i class="fas fa-pen-nib me-1"></i> Writer Login</a>
        <a href="admin/login.php" class="btn btn-outline-light"><i class="fas fa-shield-halved me-1"></i> Admin Login</a>
      </div>
    </nav>
    <div class="container-fluid">
      
      <p class="text-center mb-3 mt-2 section-title-1" style="border-bottom-width:2px;">
        All Articles
      </p>
      <div class="row justify-content-center">
        <div class="col-md-6">
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
            </div>
          </div>  
          <?php } ?>   
        </div>
      </div>
    </div>
  </body>
  </html>