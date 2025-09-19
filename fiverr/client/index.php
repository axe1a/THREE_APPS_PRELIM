<?php require_once 'classloader.php'; ?>
<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

if (!$userObj->isClient()) {
  if ($userObj->isFreelancer()) {
    header("Location: ../freelancer/index.php");
    exit;
  }
  if ($userObj->isFiverrAdministrator()) {
    header("Location: ../admin/index.php");
    exit;
  }
  // Fallback: go home
  header("Location: ../index.php");
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
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
      body {
        font-family: "Arial";
      }
      .proposal-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-radius: 8px;
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="display-4 text-center">Hello there and welcome! <span class="text-success"><?php echo $_SESSION['username']; ?>. </span> Double click to edit your offers and then press enter to save!</div>
      <div class="text-center">
        <?php  
          if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

            if ($_SESSION['status'] == "200") {
              echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
            }

            else {
              echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>"; 
            }

          }
          unset($_SESSION['message']);
          unset($_SESSION['status']);
        ?>
      </div>
      <div class="row justify-content-center">
        <div class="col-md-12">
          <?php 
          // Handle filtering by category or subcategory
          if (isset($_GET['category'])) {
            $getProposals = $proposalObj->getProposalsByCategory($_GET['category']);
          } elseif (isset($_GET['subcategory'])) {
            $getProposals = $proposalObj->getProposalsBySubcategory($_GET['subcategory']);
          } else {
            $getProposals = $proposalObj->getProposals();
          }
          ?>
          <?php foreach ($getProposals as $proposal) { ?>
          <div class="card shadow mt-4 mb-4">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h2><a href="other_profile_view.php?user_id=<?php echo $proposal['user_id'] ?>"><?php echo $proposal['username']; ?></a></h2>
                  <img src="<?php echo '../images/'.$proposal['image']; ?>" class="proposal-image" alt="Proposal Image">
                  <?php if ($proposal['category_name']): ?>
                  <p class="mt-2"><strong>Category:</strong> <?php echo $proposal['category_name']; ?>
                  <?php if ($proposal['subcategory_name']): ?>
                    - <?php echo $proposal['subcategory_name']; ?>
                  <?php endif; ?>
                  </p>
                  <?php endif; ?>
                  <p class="mt-4 mb-4"><?php echo $proposal['description']; ?></p>
                  <h4><i><?php echo number_format($proposal['min_price']) . " - " . number_format($proposal['max_price']);?> PHP</i></h4>
                </div>
                <div class="col-md-6">
                  <div class="card" style="height: 600px;">
                    <div class="card-header"><h2>All Offers</h2></div>
                    <div class="card-body overflow-auto">

                      <?php $getOffersByProposalID = $offerObj->getOffersByProposalID($proposal['proposal_id']); ?>
                      <?php foreach ($getOffersByProposalID as $offer) { ?>
                      <div class="offer">
                        <h4><?php echo $offer['username']; ?> <span class="text-primary">( <?php echo $offer['contact_number']; ?> )</span></h4>
                        <small><i><?php echo $offer['offer_date_added']; ?></i></small>
                        <p><?php echo $offer['description']; ?></p>

                        <?php if ($offer['user_id'] == $_SESSION['user_id']) { ?>
                          <form action="core/handleForms.php" method="POST">
                            <div class="form-group">
                              <input type="hidden" class="form-control" value="<?php echo $offer['offer_id']; ?>" name="offer_id" >
                              <input type="submit" class="btn btn-danger" value="Delete" name="deleteOfferBtn">
                            </div>
                          </form>

                          <form action="core/handleForms.php" method="POST" class="updateOfferForm d-none">
                            <div class="form-group">
                              <label for="#">Description</label>
                              <input type="text" class="form-control" value="<?php echo $offer['description']; ?>" name="description">
                              <input type="hidden" class="form-control" value="<?php echo $offer['offer_id']; ?>" name="offer_id" >
                              <input type="submit" class="btn btn-primary form-control" name="updateOfferBtn">
                            </div>
                          </form>
                        <?php } ?>
                        <hr>
                      </div>
                      <?php } ?>
                    </div>
                    <div class="card-footer">
                      <?php 
                        // Check if current user already submitted an offer for this proposal
                        $hasExistingOffer = $offerObj->checkExistingOffer($_SESSION['user_id'], $proposal['proposal_id']);
                      ?>
                      <?php if ($hasExistingOffer): ?>
                        <div class="alert alert-warning text-center">
                          <strong>You have already submitted an offer for this proposal.</strong><br>
                          <small>Only one offer per proposal is allowed.</small>
                        </div>
                      <?php else: ?>
                        <form action="core/handleForms.php" method="POST">
                          <div class="form-group">
                            <label for="#">Description</label>
                            <input type="text" class="form-control" name="description" required>
                            <input type="hidden" class="form-control" name="proposal_id" value="<?php echo $proposal['proposal_id']; ?>">
                            <input type="submit" class="btn btn-primary float-right mt-4" name="insertOfferBtn" value="Submit Offer"> 
                          </div>
                        </form>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <script>
       $('.offer').on('dblclick', function (event) {
          var updateOfferForm = $(this).find('.updateOfferForm');
          updateOfferForm.toggleClass('d-none');
        });
    </script>
  </body>
</html>