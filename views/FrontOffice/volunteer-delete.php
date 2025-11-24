<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/offer.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$offerC = new OfferController();

// Get the offer ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: volunteer-view.php');
    exit();
}

// Fetch the offer to delete
$offerData = $offerC->getOfferById($id);

if (!$offerData) {
    header('Location: volunteer-view.php');
    exit();
}

$error = null;
$success = null;

// Handle form submission for deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Delete the media files if they exist
    if (!empty($offerData['media'])) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $mediaFiles = [];
        $tmp = json_decode($offerData['media'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
            $mediaFiles = $tmp;
        } else {
            $mediaFiles = [$offerData['media']];
        }
        
        foreach ($mediaFiles as $mediaFile) {
            $mediaPath = $uploadDir . $mediaFile;
            if (file_exists($mediaPath)) {
                @unlink($mediaPath);
            }
        }
    }
    
    // Delete the offer from database
    $result = $offerC->deleteOffer($id);
    
    if ($result) {
        header('Location: volunteer-view.php?deleted=1');
        exit();
    } else {
        $error = "Erreur lors de la suppression de l'offre. Veuillez réessayer.";
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AidConnect | Delete Volunteer</title>

    <link rel="stylesheet" href="assets/css/libs.min.css">
    <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0">
    <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/volunteer-delete.css">
    <script src="assets/js/volunteer.js"></script>
  </head>

  <body>
    <header class="header bg-white shadow-sm">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-12">
            <a href="volunteer-view.php" class="d-flex align-items-center text-decoration-none">
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
                  <i class="fas fa-exclamation-triangle me-2"></i>Delete Volunteer
                </h4>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo OfferController::escape($error); ?>
                    </div>
                <?php endif; ?>

                <div class="warning-box">
                  <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                  <p class="mb-0 mt-2">You are about to permanently delete this volunteer. This action cannot be undone.</p>
                </div>

                <div class="volunteer-info">
                  <h5 class="mb-3">Volunteer Information</h5>
                  <div class="row">
                    <div class="col-md-6 mb-2">
                      <strong><i class="fas fa-user me-2"></i>Name:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($offerData['nom']); ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                      <strong><i class="fas fa-envelope me-2"></i>Email:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($offerData['email']); ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                      <strong><i class="fas fa-phone me-2"></i>Phone:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($offerData['num']); ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                      <strong><i class="fas fa-gift me-2"></i>Donation Type:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($offerData['type_don']); ?></p>
                    </div>
                    <div class="col-12 mb-2">
                      <strong><i class="fas fa-align-left me-2"></i>Description:</strong>
                      <p class="mb-0"><?php echo nl2br(OfferController::escape($offerData['description'])); ?></p>
                    </div>
                    <?php 
                      $mediaFiles = [];
                      if (!empty($offerData['media'])) {
                        $tmp = json_decode($offerData['media'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                          $mediaFiles = $tmp;
                        } else {
                          $mediaFiles = [$offerData['media']];
                        }
                      }
                    ?>
                    <?php if (!empty($mediaFiles)): ?>
                      <div class="col-12 mb-2">
                        <strong><i class="fas fa-image me-2"></i>Media (<?= count($mediaFiles); ?> fichier<?= count($mediaFiles) > 1 ? 's' : ''; ?>):</strong>
                        <div class="delete-media-grid mt-3">
                          <?php foreach ($mediaFiles as $mediaFile): 
                            $mediaUrl = '../../public/uploads/' . $mediaFile;
                            $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                          ?>
                            <div class="delete-media-item">
                              <?php if (in_array($ext, ['mp4','mov','webm'])): ?>
                                <video src="<?= OfferController::escape($mediaUrl); ?>" controls class="delete-media-preview"></video>
                              <?php else: ?>
                                <img src="<?= OfferController::escape($mediaUrl); ?>" alt="Volunteer media" class="delete-media-preview">
                              <?php endif; ?>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <form method="POST" id="deleteForm">
                  <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? OfferController::escape($_SESSION['csrf_token']) : ''; ?>">
                  <input type="hidden" name="confirm_delete" value="1">
                  
                  <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-danger btn-lg" id="deleteBtn">
                      <i class="fas fa-trash me-2"></i>Confirm Delete
                    </button>
                    <a href="volunteer-view.php" class="btn btn-outline-secondary">
                      <i class="fas fa-times me-2"></i>Cancel
                    </a>
                  </div>
                </form>

                <div class="text-center mt-3">
                  <a href="volunteer-view.php" class="text-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Volunteers List
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
