<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
session_start();
}
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';
require_once __DIR__ . '/layout.php';

$updateOfferC = new UpdateOfferController();
$offerC = new OfferController();

// Récupérer l'ID de l'utilisateur connecté
$currentUserId = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : null;

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

// Fonction pour obtenir le chemin de la photo de profil
function getProfilePic($storedProfile) {
    $defaultPic = 'assets/images/default_profile.png';
    if (empty($storedProfile)) {
        return $defaultPic;
    }
    $normalized = ltrim(trim($storedProfile), '/');
    $relativePath = '../../' . $normalized;
    $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
    if ($absolutePath !== false && file_exists($absolutePath)) {
        return $relativePath;
    }
    return $defaultPic;
}

// Start output buffering to capture content
ob_start();
?>

<div class="row justify-content-center py-4">
  <div class="col-12 col-lg-10 col-xl-8">
      <?php if ($id_offer > 0 && $offerData): 
        $typeDonLabel = $offerC->getTypeDonLabel($offerData['type_don']);
        $initials = strtoupper(substr($offerData['nom'], 0, 1));
      ?>
        <div class="offer-header-card card">
          <div class="card-header">
            <h5>
              <i class="ri-file-list-3-line"></i>
              Original Offer Details
            </h5>
          </div>
          <div class="card-body">
            <div class="offer-info-grid">
              <div class="info-item">
                <i class="ri-user-line"></i>
                <div>
                  <strong>Name:</strong>
                  <span><?php echo OfferController::escape($offerData['nom']); ?></span>
                </div>
              </div>
              <div class="info-item">
                <i class="ri-mail-line"></i>
                <div>
                  <strong>Email:</strong>
                  <a href="mailto:<?php echo OfferController::escape($offerData['email']); ?>" class="text-decoration-none">
                    <?php echo OfferController::escape($offerData['email']); ?>
                  </a>
                </div>
              </div>
              <div class="info-item">
                <i class="ri-gift-line"></i>
                <div>
                  <strong>Type:</strong>
                  <span class="badge bg-primary"><?php echo OfferController::escape($typeDonLabel); ?></span>
                </div>
              </div>
              <?php if (!empty($offerData['num'])): ?>
              <div class="info-item">
                <i class="ri-phone-line"></i>
                <div>
                  <strong>Phone:</strong>
                  <a href="tel:<?php echo OfferController::escape($offerData['num']); ?>" class="text-decoration-none">
                    <?php echo OfferController::escape($offerData['num']); ?>
                  </a>
                </div>
              </div>
              <?php endif; ?>
            </div>

            <div class="description-section">
              <h6 class="mb-2"><i class="ri-file-text-line me-2"></i>Description:</h6>
              <p class="text-muted mb-3" style="line-height: 1.8; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                <?php echo nl2br(OfferController::escape($offerData['description'])); ?>
              </p>
            </div>

            <?php if (!empty($offerData['media'])): ?>
              <div class="media-section">
                <h6 class="mb-2"><i class="ri-image-line me-2"></i>Media:</h6>
                <div class="media-gallery">
                  <?php
                    $mediaFiles = json_decode($offerData['media'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                      foreach ($mediaFiles as $mediaFile) {
                        $mediaUrl = '../../public/uploads/' . $mediaFile;
                        $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                        if (in_array($ext, ['mp4','mov','webm'])) {
                          echo '<div class="media-item"><video src="'.OfferController::escape($mediaUrl).'" controls></video></div>';
                        } else {
                          echo '<div class="media-item"><img src="'.OfferController::escape($mediaUrl).'" alt="Offer media"></div>';
                        }
                      }
                    } else {
                      $mediaUrl = '../../public/uploads/' . $offerData['media'];
                      $ext = strtolower(pathinfo($offerData['media'], PATHINFO_EXTENSION));
                      if (in_array($ext, ['mp4','mov','webm'])) {
                        echo '<div class="media-item"><video src="'.OfferController::escape($mediaUrl).'" controls></video></div>';
                      } else {
                        echo '<div class="media-item"><img src="'.OfferController::escape($mediaUrl).'" alt="Offer media"></div>';
                      }
                    }
                  ?>
                </div>
              </div>
            <?php endif; ?>

            <div class="mt-4">
              <a href="update-offer-form.php?id_offer=<?php echo $id_offer; ?>" class="btn btn-primary btn-action">
                <i class="ri-reply-line"></i>
                Reply to This Offer
              </a>
              <a href="volunteer-view.php" class="btn btn-outline-secondary btn-action">
                <i class="ri-arrow-left-line"></i>
                Back to Offers
              </a>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="section-title">
        <i class="ri-message-3-line"></i>
        <h4 class="mb-0">Replies & Updates</h4>
        <?php if (!empty($updates)): ?>
          <span class="badge bg-primary ms-auto"><?php echo count($updates); ?> <?php echo count($updates) == 1 ? 'Reply' : 'Replies'; ?></span>
        <?php endif; ?>
      </div>
      <?php if ($deleted): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="ri-checkbox-circle-line me-2"></i>
          <div>Reply deleted successfully!</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (empty($updates)): ?>
        <div class="empty-state">
          <i class="ri-inbox-line"></i>
          <h5 class="text-muted">No replies yet</h5>
          <p class="text-muted mb-3">Be the first to reply to this offer!</p>
          <?php if ($id_offer > 0): ?>
            <a href="update-offer-form.php?id_offer=<?php echo $id_offer; ?>" class="btn btn-primary btn-action">
              <i class="ri-add-line"></i>
              Add First Reply
            </a>
          <?php endif; ?>
        </div>
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
            <div class="facebook-post reply-card-enhanced <?php echo $is_creator_update ? '' : 'reply-type'; ?>">
              <div class="post-header">
                <?php 
                  $storedProfile = trim($update['profile_pic'] ?? '');
                  $profilePic = getProfilePic($storedProfile);
                  if ($profilePic !== 'assets/images/default_profile.png' && file_exists(realpath(__DIR__ . '/../../' . ltrim($storedProfile, '/')))):
                ?>
                  <img src="<?php echo htmlspecialchars($profilePic); ?>" 
                       alt="Profile" 
                       class="post-avatar rounded-circle" 
                       style="width: 50px; height: 50px; object-fit: cover; border: 2px solid <?php echo $is_creator_update ? '#667eea' : '#28a745'; ?>; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <?php else: ?>
                <div class="post-avatar" style="background: linear-gradient(135deg, <?php echo $is_creator_update ? '#667eea 0%, #764ba2 100%' : '#28a745 0%, #20c997 100%'; ?>); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                  <?php if ($is_creator_update): ?>
                    <i class="ri-user-star-fill" style="font-size: 1.5rem;"></i>
                  <?php else: ?>
                    <i class="ri-user-heart-fill" style="font-size: 1.5rem;"></i>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
                <div style="flex: 1;">
                  <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <strong class="text-dark d-flex align-items-center gap-1" style="font-size: 1.1rem;">
                      <?php if ($is_creator_update): ?>
                        <i class="ri-verified-badge-fill text-primary"></i>
                        <span>Offer Update</span>
                      <?php else: ?>
                        <i class="ri-reply-line text-success"></i>
                        <span>Reply</span>
                      <?php endif; ?>
                    </strong>
                    <span class="badge <?php echo $is_creator_update ? 'bg-primary' : 'bg-success'; ?>">
                      <i class="ri-hashtag"></i>#<?php echo OfferController::escape($update['id_update_offer']); ?>
                    </span>
                  </div>
                  <div class="reply-stats">
                    <?php if ($update['id_user'] !== null && $update['id_user'] > 0): ?>
                      <div class="reply-stats-item">
                        <i class="ri-user-line"></i>
                        <span>User #<?php echo OfferController::escape($update['id_user']); ?></span>
                      </div>
                    <?php else: ?>
                      <div class="reply-stats-item">
                        <i class="ri-user-unfollow-line"></i>
                        <span>Anonymous</span>
                      </div>
                    <?php endif; ?>
                    <div class="reply-stats-item">
                      <i class="ri-time-line"></i>
                      <span>Just now</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="post-content">
                <div class="reply-text" style="border-left-color: <?php echo $is_creator_update ? '#667eea' : '#28a745'; ?>;" data-reply-id="<?= (int)$update['id_update_offer'] ?>">
                  <p style="white-space: pre-wrap; word-wrap: break-word; margin: 0; line-height: 1.8; color: #1c1e21;">
                    <?php echo nl2br(OfferController::escape($update['texte'])); ?>
                  </p>
                </div>
                <?php if (!empty($update['media'])): ?>
                  <div class="reply-media-section">
                    <h6 class="mb-2 d-flex align-items-center" style="font-size: 0.875rem; color: #6c757d; font-weight: 600;">
                      <i class="ri-image-line me-2" style="color: <?php echo $is_creator_update ? '#667eea' : '#28a745'; ?>;"></i>
                      Attached Media
                      <span class="badge bg-secondary ms-2"><?php 
                        $mediaCount = 0;
                        $mediaFiles = json_decode($update['media'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                          $mediaCount = count($mediaFiles);
                        } else if (!empty($update['media'])) {
                          $mediaCount = 1;
                        }
                        echo $mediaCount;
                      ?></span>
                    </h6>
                    <div class="media-gallery">
                      <?php
                        $mediaFiles = json_decode($update['media'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                          foreach ($mediaFiles as $mediaFile) {
                            $mediaUrl = '../../public/uploads/' . $mediaFile;
                            $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                            if (in_array($ext, ['mp4','mov','webm'])) {
                              echo '<div class="media-item"><video src="'.OfferController::escape($mediaUrl).'" controls></video></div>';
                            } else {
                              echo '<div class="media-item"><img src="'.OfferController::escape($mediaUrl).'" alt="Reply media"></div>';
                            }
                          }
                        } else {
                          $mediaUrl = '../../public/uploads/' . $update['media'];
                          $ext = strtolower(pathinfo($update['media'], PATHINFO_EXTENSION));
                          if (in_array($ext, ['mp4','mov','webm'])) {
                            echo '<div class="media-item"><video src="'.OfferController::escape($mediaUrl).'" controls></video></div>';
                          } else {
                            echo '<div class="media-item"><img src="'.OfferController::escape($mediaUrl).'" alt="Reply media"></div>';
                          }
                        }
                      ?>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
              <div class="post-engagement-bar">
                <div class="engagement-actions d-flex gap-3 flex-wrap">
                  <button class="btn btn-sm btn-link text-decoration-none p-0 like-btn" data-reply-id="<?= (int)$update['id_update_offer'] ?>" onclick="likeReply(<?= (int)$update['id_update_offer'] ?>)" style="color: #6c757d;">
                    <i class="ri-heart-line me-1"></i>Like
                    <span class="like-count ms-1" data-reply-id="<?= (int)$update['id_update_offer'] ?>">0</span>
                  </button>
                  <button class="btn btn-sm btn-link text-decoration-none p-0" onclick="shareReply(<?= (int)$update['id_update_offer'] ?>)" style="color: #6c757d;">
                    <i class="ri-share-line me-1"></i>Share
                  </button>
                  <button class="btn btn-sm btn-link text-decoration-none p-0" onclick="copyReplyText(<?= (int)$update['id_update_offer'] ?>)" style="color: #6c757d;">
                    <i class="ri-file-copy-line me-1"></i>Copy
                  </button>
                </div>
              </div>
              <?php 
                // Vérifier si l'utilisateur connecté est l'auteur de l'update
                $updateUserId = isset($update['id_user']) ? (int)$update['id_user'] : null;
                $isOwner = ($currentUserId !== null && $updateUserId !== null && $currentUserId === $updateUserId);
              ?>
              <?php if ($isOwner): ?>
              <div class="post-footer">
                <a href="update-offer-edit.php?id=<?= (int)$update['id_update_offer'] ?>" class="btn btn-sm btn-outline-primary btn-action">
                  <i class="ri-edit-line"></i>
                  Edit Reply
                </a>
                <a href="update-offer-delete.php?id=<?= (int)$update['id_update_offer'] ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('Are you sure you want to delete this reply?');">
                  <i class="ri-delete-bin-line"></i>
                  Delete
                </a>
                <button class="btn btn-sm btn-outline-info btn-action" onclick="window.print()" title="Print this reply">
                  <i class="ri-printer-line"></i>
                  Print
                </button>
              </div>
              <?php else: ?>
              <div class="post-footer">
                <button class="btn btn-sm btn-outline-info btn-action" onclick="window.print()" title="Print this reply">
                  <i class="ri-printer-line"></i>
                  Print
                </button>
              </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();

// Define additional styles and scripts
$additional_styles = ['assets/css/template.css', 'assets/css/update-offer-view.css'];
$additional_scripts = ['assets/js/update-offer-view.js'];

// Render the layout
renderLayout('Offer Replies', $content, $additional_styles, $additional_scripts);
?>

