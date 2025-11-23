<?php
session_start();
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$updateOfferC = new UpdateOfferController();
$offerC = new OfferController();

// Get the offer ID from URL (optional - to filter by offer)
$id_offer = isset($_GET['id_offer']) ? (int)$_GET['id_offer'] : 0;

// Fetch updates
if ($id_offer > 0) {
    $updates = $updateOfferC->getUpdateOffersByOfferId($id_offer);
    $offerData = $offerC->getOfferById($id_offer);
} else {
    $updates = $updateOfferC->getAllUpdateOffers();
    $offerData = null;
}

// Check if deletion was successful
$deleted = isset($_GET['deleted']) && $_GET['deleted'] == '1';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>AidConnect • Offer Replies</title>
  <link rel="stylesheet" href="template.css">
  <link rel="stylesheet" href="assets/css/libs.min.css">
  <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0">
  <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/libs.min.js"></script>
  <script src="assets/js/app.js"></script>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <?php if ($id_offer > 0 && $offerData): ?>
        <div class="card mb-4 border-primary">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Original Offer</h5>
          </div>
          <div class="card-body">
            <p><strong>Name:</strong> <?php echo OfferController::escape($offerData['nom']); ?></p>
            <p><strong>Email:</strong> <?php echo OfferController::escape($offerData['email']); ?></p>
            <p><strong>Type:</strong> <?php echo OfferController::escape($offerData['type_don']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(OfferController::escape($offerData['description'])); ?></p>
            <a href="update-offer-form.php?id_offer=<?php echo $id_offer; ?>" class="btn btn-sm btn-success mt-2">
              Reply to This Offer
            </a>
          </div>
        </div>
      <?php endif; ?>

      <h5 class="mb-3 text-center">Replies & Updates</h5>
      <?php if ($deleted): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Reply deleted successfully!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (empty($updates)): ?>
        <p class="text-muted text-center">No replies yet.</p>
      <?php else: ?>
        <div class="posts-container">
          <?php foreach ($updates as $update): ?>
            <?php 
              // Check if this update is from the offer creator
              $is_creator_update = ($offerData && 
                                    $update['id_user'] !== null && 
                                    $offerData['id_user'] !== null && 
                                    $update['id_user'] == $offerData['id_user']);
            ?>
            <div class="facebook-post">
              <div class="post-header">
                <div class="post-avatar" style="background: linear-gradient(45deg, <?php echo $is_creator_update ? '#667eea 0%, #764ba2 100%' : '#28a745 0%, #20c997 100%'; ?>);">
                  <?php if ($is_creator_update): ?>
                    U
                  <?php else: ?>
                    R
                  <?php endif; ?>
                </div>
                <div>
                  <strong class="text-dark">
                    <?php if ($is_creator_update): ?>
                      Offer Update #<?php echo OfferController::escape($update['id_update_offer']); ?>
                    <?php else: ?>
                      Reply #<?php echo OfferController::escape($update['id_update_offer']); ?>
                    <?php endif; ?>
                  </strong><br>
                  <small class="text-muted">
                    <?php if ($update['id_user'] !== null && $update['id_user'] > 0): ?>
                      User ID: <?php echo OfferController::escape($update['id_user']); ?>
                    <?php else: ?>
                      Anonymous
                    <?php endif; ?>
                    <?php if ($update['id_offer']): ?>
                      • Offer ID: <?php echo OfferController::escape($update['id_offer']); ?>
                    <?php endif; ?>
                  </small>
                </div>
              </div>
              <div class="post-content">
                <p><?php echo nl2br(OfferController::escape($update['texte'])); ?></p>
                <?php if (!empty($update['media'])): ?>
                  <div class="post-media">
                    <?php
                      $mediaFiles = json_decode($update['media'], true);
                      if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                        foreach ($mediaFiles as $mediaFile) {
                          $mediaUrl = '../../public/uploads/' . $mediaFile;
                          $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                          if (in_array($ext, ['mp4','mov','webm'])) {
                            echo '<video src="'.OfferController::escape($mediaUrl).'" controls></video>';
                          } else {
                            echo '<img src="'.OfferController::escape($mediaUrl).'" alt="Reply media">';
                          }
                        }
                      } else {
                        $mediaUrl = '../../public/uploads/' . $update['media'];
                        $ext = strtolower(pathinfo($update['media'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['mp4','mov','webm'])) {
                          echo '<video src="'.OfferController::escape($mediaUrl).'" controls></video>';
                        } else {
                          echo '<img src="'.OfferController::escape($mediaUrl).'" alt="Reply media">';
                        }
                      }
                    ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="post-footer">
                <a href="update-offer-edit.php?id=<?= (int)$update['id_update_offer'] ?>" class="post-action text-decoration-none">Edit</a>
                <a href="update-offer-delete.php?id=<?= (int)$update['id_update_offer'] ?>" class="post-action text-decoration-none">Delete</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<footer class="bg-light text-center text-muted py-3 mt-4">
  <p>© 2025 AidConnect. Reply System.</p>
</footer>
</body>
</html>

