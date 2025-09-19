<?php require_once 'classloader.php'; ?>
<?php require_once '../helpers.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if (!$userObj->isAdmin()) {
  header("Location: ../writer/index.php");
  exit;
}  

$pending = $articleObj->getPendingEditRequests();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link rel="stylesheet" href="../styles.css">
    <title>Edit Requests</title>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <h4 class="text-center mb-3 mt-2 section-title" style="border-bottom-width:2px;">
            <i class="fas fa-handshake me-2"></i> Edit Requests
          </h4>
          <?php if (empty($pending)) { ?>
            <div class="alert alert-info text-center"><i class="fas fa-info-circle me-1"></i> No pending edit requests.</div>
          <?php } else { ?>
            <?php foreach ($pending as $req) { ?>
            <div class="card mt-3 shadow">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <div><strong><?php echo htmlspecialchars($req['title']); ?></strong></div>
                  <small class="text-muted">
                    <i class="fas fa-user me-1"></i> Requester: <?php echo htmlspecialchars($req['requester_name']); ?>
                    <i class="fas fa-clock ms-3 me-1"></i> <?php echo $req['created_at']; ?>
                  </small>
                </div>
                <div class="btn-group">
                  <button class="btn btn-success btn-accept" data-article-id="<?php echo $req['article_id']; ?>" data-requester-id="<?php echo $req['requester_id']; ?>">
                    <i class="fas fa-check me-1"></i> Accept
                  </button>
                  <button class="btn btn-outline-danger btn-reject" data-article-id="<?php echo $req['article_id']; ?>" data-requester-id="<?php echo $req['requester_id']; ?>">
                    <i class="fas fa-times me-1"></i> Reject
                  </button>
                </div>
              </div>
            </div>
            <?php } ?>
          <?php } ?>
        </div>
      </div>
    </div>

    <script>
      function respond(articleId, requesterId, decision) {
        $.ajax({
          type: 'POST',
          url: 'core/handleForms.php',
          data: { respondEditRequest: 1, article_id: articleId, requester_id: requesterId, decision: decision },
          success: function (res) {
            if (res == '1') { location.reload(); }
            else { alert('Failed to update request'); }
          },
          error: function () { alert('An error occurred'); }
        })
      }

      $('.btn-accept').on('click', function() {
        respond($(this).data('article-id'), $(this).data('requester-id'), 'accepted');
      });
      $('.btn-reject').on('click', function() {
        respond($(this).data('article-id'), $(this).data('requester-id'), 'rejected');
      });
    </script>
  </body>
</html> 