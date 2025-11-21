<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/updateoffer.php';
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$updateOfferC = new UpdateOfferController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: update-offer-view.php');
    exit();
}

$updateData = $updateOfferC->getUpdateOfferById($id);

if (!$updateData) {
    header('Location: update-offer-view.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Delete the media file if it exists
    if (!empty($updateData['media'])) {
        $mediaPath = __DIR__ . '/../../public/uploads/' . $updateData['media'];
        if (file_exists($mediaPath)) {
            @unlink($mediaPath);
        }
    }

    $result = $updateOfferC->deleteUpdateOffer($id);
    
    if ($result) {
        $redirectUrl = $updateData['id_offer'] ? 'update-offer-view.php?id_offer=' . $updateData['id_offer'] . '&deleted=1' : 'update-offer-view.php?deleted=1';
        header('Location: ' . $redirectUrl);
        exit();
    } else {
        $error = "Erreur lors de la suppression. Veuillez réessayer.";
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AidConnect | Delete Reply</title>

    <link rel="stylesheet" href="assets/css/libs.min.css">
    <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0">
    <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/volunteer.js"></script>
  </head>

  <body>
    <header class="header bg-white shadow-sm">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-12">
            <a href="update-offer-view.php<?php echo $updateData['id_offer'] ? '?id_offer=' . $updateData['id_offer'] : ''; ?>" class="d-flex align-items-center text-decoration-none">
              <i class="fas fa-arrow-left text-primary me-2"></i>
              <h4 class="mb-0 text-primary ms-2">AidConnect</h4>
            </a>
          </div>
        </div>
      </div>
    </header>

    <div class="wrapper">
      <section class="sign-up-page">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8 pt-5 pb-5">
              <div class="delete-card p-4">
                <h4 class="mb-4 text-danger">
                  <i class="fas fa-exclamation-triangle me-2"></i>Delete Reply
                </h4>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo OfferController::escape($error); ?>
                    </div>
                <?php endif; ?>

                <div class="warning-box">
                  <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                  <p class="mb-0 mt-2">You are about to permanently delete this reply. This action cannot be undone.</p>
                </div>

                <div class="volunteer-info">
                  <h5 class="mb-3">Reply Information</h5>
                  <div class="row">
                    <div class="col-12 mb-2">
                      <strong><i class="fas fa-comment me-2"></i>Reply Text:</strong>
                      <p class="mb-0"><?php echo nl2br(OfferController::escape($updateData['texte'])); ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                      <strong><i class="fas fa-user me-2"></i>User ID:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($updateData['id_user']); ?></p>
                    </div>
                    <?php if ($updateData['id_offer']): ?>
                      <div class="col-md-6 mb-2">
                        <strong><i class="fas fa-link me-2"></i>Offer ID:</strong>
                        <p class="mb-0"><?php echo OfferController::escape($updateData['id_offer']); ?></p>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($updateData['media'])): ?>
                      <div class="col-12 mb-2">
                        <strong><i class="fas fa-image me-2"></i>Media:</strong>
                        <div class="mt-2">
                          <?php
                            $mediaUrl = '../../public/uploads/' . $updateData['media'];
                            $ext = strtolower(pathinfo($updateData['media'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['mp4','mov','webm'])) {
                              echo '<video src="'.OfferController::escape($mediaUrl).'" controls class="media-preview-video"></video>';
                            } else {
                              echo '<img src="'.OfferController::escape($mediaUrl).'" alt="Reply media" class="media-preview">';
                            }
                          ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <form method="POST" id="deleteForm">
                  <input type="hidden" name="confirm_delete" value="1">
                  
                  <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-danger btn-lg" id="deleteBtn">
                      <i class="fas fa-trash me-2"></i>Confirm Delete
                    </button>
                    <a href="update-offer-view.php<?php echo $updateData['id_offer'] ? '?id_offer=' . $updateData['id_offer'] : ''; ?>" class="btn btn-outline-secondary">
                      <i class="fas fa-times me-2"></i>Cancel
                    </a>
                  </div>
                </form>

                <div class="text-center mt-3">
                  <a href="update-offer-view.php<?php echo $updateData['id_offer'] ? '?id_offer=' . $updateData['id_offer'] : ''; ?>" class="text-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Replies
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </body>
</html>

