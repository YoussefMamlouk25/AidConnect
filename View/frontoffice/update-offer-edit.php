<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/updateoffer.php';
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';
require_once __DIR__ . '/layout.php';

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
        
        // Handle media upload
        $media = $updateData['media']; // Keep existing media by default
        
        if (isset($_FILES['media'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            if (!empty($updateData['media'])) {
                $oldMediaFiles = json_decode($updateData['media'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($oldMediaFiles)) {
                    foreach ($oldMediaFiles as $oldMediaFile) {
                        $oldMediaPath = $uploadDir . $oldMediaFile;
                        if (file_exists($oldMediaPath)) {
                            @unlink($oldMediaPath);
                        }
                    }
                } else {
                    $oldMediaPath = $uploadDir . $updateData['media'];
                    if (file_exists($oldMediaPath)) {
                        @unlink($oldMediaPath);
                    }
                }
            }
            
            $uploadedMedia = [];
            $errors = [];
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
            if (count($uploadedMedia) > 0) {
                $media = json_encode($uploadedMedia);
            }
        }

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

// Start output buffering to capture content
ob_start();
?>

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
                  <input type="file" class="form-control" id="media" name="media[]" accept="image/*,video/*" multiple>
                  <small class="text-muted d-block mt-2">
                    Leave empty to keep current media. Upload new files to replace them.
                  </small>
                  <?php if (!empty($currentMedia)): ?>
                    <div class="mt-3">
                      <label class="text-muted small">Current Media:</label>
                      <?php
                        $mediaFiles = json_decode($currentMedia, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                          foreach ($mediaFiles as $mediaFile) {
                            $mediaUrl = '../../public/uploads/' . $mediaFile;
                            $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                            if (in_array($ext, ['mp4','mov','webm'])) {
                              echo '<div class="mt-2"><video src="' . OfferController::escape($mediaUrl) . '" controls class="media-preview-video"></video></div>';
                            } else {
                              echo '<div class="mt-2"><img src="' . OfferController::escape($mediaUrl) . '" alt="Current media" class="media-preview"></div>';
                            }
                          }
                        } else {
                          $mediaUrl = '../../public/uploads/' . $currentMedia;
                          $ext = strtolower(pathinfo($currentMedia, PATHINFO_EXTENSION));
                          if (in_array($ext, ['mp4','mov','webm'])) {
                            echo '<div class="mt-2"><video src="' . OfferController::escape($mediaUrl) . '" controls class="media-preview-video"></video></div>';
                          } else {
                            echo '<div class="mt-2"><img src="' . OfferController::escape($mediaUrl) . '" alt="Current media" class="media-preview"></div>';
                          }
                        }
                      ?>
                    </div>
                  <?php endif; ?>
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

<?php
$content = ob_get_clean();

// Define additional styles and scripts
$additional_styles = ['assets/css/update-offer-edit.css'];
$additional_scripts = ['assets/js/javascript.js'];

// Render the layout
renderLayout('Edit Reply', $content, $additional_styles, $additional_scripts);
?>

