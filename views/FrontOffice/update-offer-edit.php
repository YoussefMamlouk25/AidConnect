<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/updateoffer.php';
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$updateOfferC = new UpdateOfferController();

// Get the update ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: update-offer-view.php');
    exit();
}

// Fetch the update to edit
$updateData = $updateOfferC->getUpdateOfferById($id);

if (!$updateData) {
    header('Location: update-offer-view.php');
    exit();
}

$error = null;

// Handle form submission
if (isset($_POST['texte'])) {
    if (!empty($_POST['texte'])) {
        $texte = trim($_POST['texte']);
        
        // Handle existing media retention/removal + new uploads
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!file_exists($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }

        // Parse existing media from DB into array
        $existingMedia = [];
        if (!empty($updateData['media'])) {
          $tmp = json_decode($updateData['media'], true);
          if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
            $existingMedia = $tmp;
          } else {
            $existingMedia = [$updateData['media']];
          }
        }

        // Determine kept existing media sent from client (JSON array)
        $keptExisting = $existingMedia;
        if (isset($_POST['existing_media']) && $_POST['existing_media'] !== '') {
          $posted = json_decode($_POST['existing_media'], true);
          if (json_last_error() === JSON_ERROR_NONE && is_array($posted)) {
            $keptExisting = $posted;
          }
        }

        // Remove files that were not kept
        foreach ($existingMedia as $oldFile) {
          if (!in_array($oldFile, $keptExisting, true)) {
            $oldPath = $uploadDir . $oldFile;
            if (file_exists($oldPath)) {
              @unlink($oldPath);
            }
          }
        }

        // Process newly uploaded files and append to keptExisting
        $uploadedMedia = [];
        $errors = [];
        if (isset($_FILES['media'])) {
          foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['media']['error'][$key] === UPLOAD_ERR_OK) {
              $original = basename($_FILES['media']['name'][$key]);
              $mediaName = time() . '_' . $key . '_' . $original;
              $targetPath = $uploadDir . $mediaName;
              if (move_uploaded_file($tmpName, $targetPath)) {
                $uploadedMedia[] = $mediaName;
              } else {
                $errors[] = "Erreur lors du téléchargement du fichier: " . $original;
              }
            }
          }
          if (count($errors) > 0) {
            $error = implode("\n", $errors);
          }
        }

        $finalMediaList = array_values(array_merge($keptExisting, $uploadedMedia));
        $media = count($finalMediaList) > 0 ? json_encode($finalMediaList) : null;

        if (!isset($error)) {
            $updateOffer = new UpdateOffer(
                $updateData['id_offer'],
                $updateData['id_user'],
                $texte,
                $media,
                $id
            );
            
            $result = $updateOfferC->updateUpdateOffer($updateOffer, $id);
            
            if ($result) {
                header('Location: update-offer-view.php?id_offer=' . urlencode($updateData['id_offer']));
                exit();
            } else {
                $error = "Erreur lors de la mise à jour. Veuillez réessayer.";
            }
        }
    } else {
        $error = "Le texte est requis.";
    }
}

// Use POST data if available (for form persistence on error), otherwise use database data
$texte = isset($_POST['texte']) ? htmlspecialchars($_POST['texte']) : htmlspecialchars($updateData['texte']);
$currentMedia = $updateData['media'];

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AidConnect | Edit Reply</title>

    <link rel="stylesheet" href="assets/css/libs.min.css">
    <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0">
    <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/update-offer-edit.css">
    <script src="javascript.js"></script>
  </head>

  <body class="volunteer-form-body">
    <header class="header bg-white shadow-sm">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-4">
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
            <div class="col-md-6 pt-5 pb-5 d-flex align-items-center justify-content-center">
              <form method="POST" enctype="multipart/form-data"
                    class="sign-in-from bg-white p-4 rounded shadow-lg volunteer-form-container"
                    id="updateForm">

                <h4 class="mb-3 text-primary">Edit Reply</h4>
                <p class="text-muted">Update your reply below.</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo OfferController::escape($error); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group mb-3">
                  <label for="texte" class="fw-bold">Reply Text</label>
                  <textarea class="form-control" id="texte" name="texte" rows="5" required><?php echo $texte; ?></textarea>
                </div>

                <div class="form-group mb-4">
                  <label for="media" class="fw-bold">Media</label>
                  <?php
                    $existingFiles = [];
                    if (!empty($currentMedia)) {
                      $tmp = json_decode($currentMedia, true);
                      if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                        $existingFiles = $tmp;
                      } else {
                        $existingFiles = [$currentMedia];
                      }
                    }
                  ?>
                  <input type="hidden" id="existing_media" name="existing_media" value='<?= htmlspecialchars(json_encode($existingFiles), ENT_QUOTES, "UTF-8") ?>'>
                  
                  <?php if (!empty($existingFiles)): ?>
                    <div class="mb-3">
                      <label class="text-muted small fw-bold">Médias existants:</label>
                      <div id="existingMediaGrid" class="media-preview-grid mt-2">
                        <?php foreach ($existingFiles as $mediaFile):
                          $mediaUrl = '../../public/uploads/' . $mediaFile;
                          $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                          if (in_array($ext, ['mp4','mov','webm'])) {
                            echo '<div class="media-preview-item" data-filename="'.htmlspecialchars($mediaFile, ENT_QUOTES).'"><video src="'.htmlspecialchars($mediaUrl).'" controls class="media-preview-video"></video><button type="button" class="media-remove-btn" aria-label="Remove existing media">&times;</button></div>';
                          } else {
                            echo '<div class="media-preview-item" data-filename="'.htmlspecialchars($mediaFile, ENT_QUOTES).'"><img src="'.htmlspecialchars($mediaUrl).'" alt="Current media"><button type="button" class="media-remove-btn" aria-label="Remove existing media">&times;</button></div>';
                          }
                        endforeach; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                  
                  <div class="media-dropzone" id="mediaDropzone">
                    <input type="file" class="media-file-input" id="media" name="media[]" accept="image/*,video/*" multiple>
                    <div class="dropzone-content">
                      <div class="dropzone-icon">📁</div>
                      <div class="dropzone-text">Glissez-déposez vos fichiers ici</div>
                      <div class="dropzone-link">ou cliquez pour parcourir</div>
                      <div class="dropzone-hint">Formats supportés: JPG, PNG, MP4, MOV, WEBM</div>
                    </div>
                  </div>
                  <div id="mediaPreview" class="media-preview-grid mt-3"></div>
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Update Reply
                  </button>
                  <a href="update-offer-view.php<?php echo $updateData['id_offer'] ? '?id_offer=' . $updateData['id_offer'] : ''; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                  </a>
                </div>

                <div class="text-center mt-3">
                  <a href="update-offer-view.php<?php echo $updateData['id_offer'] ? '?id_offer=' . $updateData['id_offer'] : ''; ?>" class="text-primary">← Back to Replies</a>
                </div>

              </form>
            </div>
          </div>
        </div>
      </section>
    </div>
  </body>
  <script src="assets/js/update-offer-edit.js"></script>
</html>

