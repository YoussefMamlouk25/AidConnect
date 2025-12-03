<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php?error=' . urlencode('Veuillez vous connecter pour supprimer une offre.'));
    exit();
}

$currentUserId = (int) $_SESSION['id_user'];
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/offer.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';
require_once __DIR__ . '/layout.php';

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

$offerOwnerId = isset($offerData['id_user']) ? (int) $offerData['id_user'] : null;
if (!$isAdmin && ($offerOwnerId === null || $offerOwnerId !== $currentUserId)) {
    $_SESSION['flash_error'] = "Vous ne pouvez supprimer que vos propres offres.";
    header('Location: volunteer-view.php');
    exit();
}

$error = null;

// Handle form submission for deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Delete the media file(s) if they exist
    if (!empty($offerData['media'])) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $mediaEntries = json_decode($offerData['media'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($mediaEntries)) {
            foreach ($mediaEntries as $mediaFile) {
                $mediaPath = $uploadDir . $mediaFile;
                if (is_file($mediaPath)) {
                    @unlink($mediaPath);
                }
            }
        } else {
            $mediaPath = $uploadDir . $offerData['media'];
            if (is_file($mediaPath)) {
                @unlink($mediaPath);
            }
        }
    }
    
    // Delete the offer from database
    $ownerConstraint = $isAdmin ? null : $currentUserId;
    $result = $offerC->deleteOffer($id, $ownerConstraint);
    
    if ($result) {
        header('Location: volunteer-view.php?deleted=1');
        exit();
    } else {
        $error = "Erreur lors de la suppression de l'offre. Veuillez réessayer.";
    }
}

// Start output buffering to capture content
ob_start();
?>

<div class="row justify-content-center">
  <div class="col-md-8 pt-5 pb-5">
              <div class="delete-card p-4">
                <h4 class="mb-4 text-danger">
                  <i class="ri-error-warning-line me-2"></i>Delete Volunteer
                </h4>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="ri-error-warning-line me-2"></i><?php echo OfferController::escape($error); ?>
                    </div>
                <?php endif; ?>

                <div class="warning-box">
                  <strong><i class="ri-alert-line me-2"></i>Warning!</strong>
                  <p class="mb-0 mt-2">You are about to permanently delete this volunteer. This action cannot be undone.</p>
                </div>

                <div class="volunteer-info">
                  <h5 class="mb-3"><i class="ri-information-line me-2"></i>Volunteer Information</h5>
                  <div class="row">
                    <div class="col-md-6 mb-2">
                      <strong><i class="ri-user-line me-2"></i>Name:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($offerData['nom']); ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                      <strong><i class="ri-mail-line me-2"></i>Email:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($offerData['email']); ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                      <strong><i class="ri-phone-line me-2"></i>Phone:</strong>
                      <p class="mb-0"><?php echo OfferController::escape($offerData['num']); ?></p>
                    </div>
                    <div class="col-md-6 mb-2">
                      <strong><i class="ri-gift-line me-2"></i>Donation Type:</strong>
                      <p class="mb-0"><i class="ri-price-tag-line me-1"></i><?php echo OfferController::escape($offerData['type_don']); ?></p>
                    </div>
                    <div class="col-12 mb-2">
                      <strong><i class="ri-file-text-line me-2"></i>Description:</strong>
                      <p class="mb-0"><?php echo nl2br(OfferController::escape($offerData['description'])); ?></p>
                    </div>
                    <?php if (!empty($offerData['media'])): ?>
                      <div class="col-12 mb-2">
                        <strong><i class="ri-image-line me-2"></i>Media:</strong>
                        <div class="mt-2">
                          <?php
                            $mediaFiles = json_decode($offerData['media'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                              // Multiple media files (JSON array)
                              echo '<div class="media-gallery">';
                              foreach ($mediaFiles as $index => $mediaFile) {
                                if (empty($mediaFile)) continue;
                                $mediaUrl = '../../public/uploads/' . $mediaFile;
                                $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                $isVideo = in_array($ext, ['mp4','mov','webm']);
                                if ($isVideo) {
                                  echo '<div class="media-item" onclick="openMediaLightbox(\''.OfferController::escape($mediaUrl).'\', \'video\')"><video src="'.OfferController::escape($mediaUrl).'" controls></video></div>';
                                } else {
                                  echo '<div class="media-item" onclick="openMediaLightbox(\''.OfferController::escape($mediaUrl).'\', \'image\')"><img src="'.OfferController::escape($mediaUrl).'" alt="Volunteer media"></div>';
                                }
                              }
                              echo '</div>';
                            } else {
                              // Single media file (string)
                              $mediaUrl = '../../public/uploads/' . $offerData['media'];
                              $ext = strtolower(pathinfo($offerData['media'], PATHINFO_EXTENSION));
                              $isVideo = in_array($ext, ['mp4','mov','webm']);
                              if ($isVideo) {
                                echo '<div class="media-item" onclick="openMediaLightbox(\''.OfferController::escape($mediaUrl).'\', \'video\')"><video src="'.OfferController::escape($mediaUrl).'" controls class="media-preview-video"></video></div>';
                              } else {
                                echo '<div class="media-item" onclick="openMediaLightbox(\''.OfferController::escape($mediaUrl).'\', \'image\')"><img src="'.OfferController::escape($mediaUrl).'" alt="Volunteer media" class="media-preview"></div>';
                              }
                            }
                          ?>
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
                      <i class="ri-delete-bin-line me-2"></i>Confirm Delete
                    </button>
                    <a href="volunteer-view.php" class="btn btn-outline-secondary">
                      <i class="ri-close-line me-2"></i>Cancel
                    </a>
                  </div>
                </form>

                <div class="text-center mt-3">
                  <a href="volunteer-view.php" class="text-primary">
                    <i class="ri-arrow-left-line me-1"></i>Back to Volunteers List
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lightbox Modal -->
        <div id="mediaLightbox" class="image-modal" onclick="closeMediaLightbox(event)">
          <button class="close-btn" onclick="closeMediaLightbox(event)" aria-label="Close">
            <i class="ri-close-line"></i>
          </button>
          <div class="media-wrap" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="Media" style="display: none;">
            <video id="lightboxVideo" src="" controls style="display: none;"></video>
          </div>
        </div>

        <script>
          function openMediaLightbox(mediaUrl, type) {
            const lightbox = document.getElementById('mediaLightbox');
            const lightboxImage = document.getElementById('lightboxImage');
            const lightboxVideo = document.getElementById('lightboxVideo');
            
            if (type === 'image') {
              lightboxImage.src = mediaUrl;
              lightboxImage.style.display = 'block';
              lightboxVideo.style.display = 'none';
            } else {
              lightboxVideo.src = mediaUrl;
              lightboxVideo.style.display = 'block';
              lightboxImage.style.display = 'none';
            }
            
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
          }
          
          function closeMediaLightbox(event) {
            if (event && event.target !== event.currentTarget && !event.target.classList.contains('close-btn') && !event.target.closest('.close-btn')) {
              return;
            }
            
            const lightbox = document.getElementById('mediaLightbox');
            const lightboxImage = document.getElementById('lightboxImage');
            const lightboxVideo = document.getElementById('lightboxVideo');
            
            lightbox.classList.remove('open');
            document.body.style.overflow = '';
            
            // Pause video when closing
            if (lightboxVideo) {
              lightboxVideo.pause();
              lightboxVideo.src = '';
            }
          }
          
          // Close on Escape key
          document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
              closeMediaLightbox(e);
            }
          });
        </script>

<?php
$content = ob_get_clean();

// Define additional styles and scripts
$additional_styles = ['assets/css/media-lightbox.css'];
$additional_scripts = ['assets/js/volunteer.js'];

// Render the layout
renderLayout('Delete Volunteer', $content, $additional_styles, $additional_scripts);
?>
