<?php
session_start();
require_once __DIR__ . '/../../Controller/offercontroller.php';
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/layout.php';

$offerC = new OfferController();
$updateOfferC = new UpdateOfferController();

$offers = $offerC->getAllOffers();

$current_user_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');

foreach ($offers as &$offer) {
    $offer['updates_count'] = count($updateOfferC->getUpdateOffersByOfferId($offer['id_offer']));
    $offer['is_creator'] = ($current_user_id > 0 && $offer['id_user'] == $current_user_id);
    $offer['can_manage'] = $offer['is_creator'] || $isAdmin;
}
unset($offer);

$deleted = isset($_GET['deleted']) && $_GET['deleted'] == '1';
<<<<<<< HEAD
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
=======
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb

// Start output buffering to capture content
ob_start();
?>

<div class="row justify-content-center">
  <div class="col-lg-6 col-xl-5">
      <div class="text-center mb-4">
        <a href="volunteer-form.php" class="btn-add-offer">
          <i class="ri-add-circle-line me-2"></i> Ajouter une Offre
        </a>
      </div>
      
      <h5 class="mb-3 text-center">Volunteer Posts</h5>
      <?php if ($deleted): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Volunteer deleted successfully!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($flashError) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= OfferController::escape($error) ?></div>
      <?php endif; ?>
      <?php if (empty($offers)): ?>
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="ri-inbox-line" style="font-size: 4rem; color: #ccc;"></i>
            <h5 class="mt-3">Aucune offre pour le moment</h5>
            <p class="text-muted">Soyez le premier à créer une offre !</p>
            <a href="volunteer-form.php" class="btn btn-primary">
              <i class="ri-add-circle-line me-2"></i> Créer la première offre
            </a>
          </div>
        </div>
      <?php else: ?>
            <?php foreach ($offers as $offer): 
              $nom = OfferController::escape($offer['nom']);
              $email = OfferController::escape($offer['email']);
              $typeDon = OfferController::escape($offer['type_don']);
              $typeDonLabel = $offerC->getTypeDonLabel($typeDon);
              $num = OfferController::escape($offer['num']);
              $description = OfferController::escape($offer['description']);
              $initials = strtoupper(substr($nom, 0, 1));
              
              // Parse media files
              $mediaFiles = [];
              if (!empty($offer['media'])) {
                $decoded = json_decode($offer['media'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                  $mediaFiles = array_values(array_filter($decoded));
                } else {
                  $mediaFiles[] = $offer['media'];
                }
              }
              $mediaCount = count($mediaFiles);
            ?>
              <div class="post-card mb-3">
                <!-- Post Header -->
                <div class="post-header">
                  <div class="avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><?= $initials ?></div>
                  <div class="post-info">
                    <div class="post-author">
                      <?= $nom ?>
                      <?php if ($offer['is_creator']): ?>
                        <i class="ri-verified-badge-fill text-primary ms-1" title="Post Creator"></i>
                      <?php endif; ?>
                    </div>
                    <div class="post-meta">
                      <span class="badge bg-primary">
                        <i class="ri-gift-line me-1"></i><?= $typeDonLabel ?>
                      </span>
                      <span class="badge bg-success ms-2">
                        <i class="ri-user-heart-line me-1"></i>Volunteer
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Post Content -->
                <div class="post-content">
                  <p class="post-description"><?= nl2br($description) ?></p>
                  
                  <!-- Media Gallery -->
                  <?php if ($mediaCount > 0): ?>
                    <div class="post-media-container mb-3">
                      <div class="fb-media-grid count-<?= $mediaCount >= 4 ? 4 : $mediaCount ?>">
                        <?php foreach ($mediaFiles as $index => $mediaFile): ?>
                          <?php if ($index >= 4) break; ?>
                          <?php 
                            $mediaUrl = '../../public/uploads/' . htmlspecialchars($mediaFile);
                            $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                            $isVideo = in_array($ext, ['mp4','mov','webm']);
                          ?>
                          <div class="grid-item">
                            <?php if ($isVideo): ?>
                              <video src="<?= $mediaUrl ?>" controls class="img-fluid"></video>
                            <?php else: ?>
                              <img src="<?= $mediaUrl ?>" alt="Offer media" class="img-fluid">
                            <?php endif; ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <!-- Contact Info -->
                  <div class="post-contact">
                    <div class="contact-item">
                      <i class="ri-mail-line me-2 text-primary"></i>
                      <strong>Email:</strong> 
                      <a href="mailto:<?= $email ?>"><?= $email ?></a>
                    </div>
                    <div class="contact-item">
                      <i class="ri-phone-line me-2 text-success"></i>
                      <strong>Téléphone:</strong> 
                      <a href="tel:<?= $num ?>"><?= $num ?></a>
                    </div>
                    <div class="mt-2">
                      <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?= $email ?>')" title="Copy Email">
                        <i class="ri-file-copy-line me-1"></i>Copy Email
                      </button>
                      <button class="btn btn-sm btn-outline-success" onclick="copyToClipboard('<?= $num ?>')" title="Copy Phone">
                        <i class="ri-file-copy-line me-1"></i>Copy Phone
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Engagement Bar -->
                <div class="post-engagement">
                  <div class="engagement-stats">
                    <button class="btn-link like-post-btn" data-offer-id="<?= (int)$offer['id_offer'] ?>" onclick="likePost(<?= (int)$offer['id_offer'] ?>)" title="Like this post">
                      <i class="ri-heart-line me-1"></i>Like
                      <span class="like-count" data-offer-id="<?= (int)$offer['id_offer'] ?>">0</span>
                    </button>
                    <button class="btn-link" onclick="sharePost(<?= (int)$offer['id_offer'] ?>)" title="Share this post">
                      <i class="ri-share-line me-1"></i>Share
                    </button>
                  </div>
                  <div class="engagement-counts">
                    <?php if ($offer['updates_count'] > 0): ?>
                      <span class="badge bg-info">
                        <i class="ri-message-3-line me-1"></i><?= $offer['updates_count'] ?> Response<?= $offer['updates_count'] > 1 ? 's' : '' ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Post Actions -->
                <div class="post-actions">
                  <?php if ($offer['is_creator']): ?>
                    <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-primary">
                      <i class="ri-edit-line me-1"></i> Update
                    </a>
                  <?php else: ?>
                    <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-primary">
                      <i class="ri-reply-line me-1"></i> Reply
                    </a>
                  <?php endif; ?>
                  <a href="update-offer-view.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-info">
                    <i class="ri-file-list-3-line me-1"></i> View Replies
                    <?php if ($offer['updates_count'] > 0): ?>
                      <span class="badge bg-primary ms-1"><?= $offer['updates_count'] ?></span>
                    <?php endif; ?>
                  </a>
<<<<<<< HEAD
                  <?php if ($offer['can_manage']): ?>
                    <a href="volunteer-edit.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-warning">
                      <i class="ri-pencil-line me-1"></i> Edit
                    </a>
                    <a href="volunteer-delete.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-danger">
                      <i class="ri-delete-bin-line me-1"></i> Delete
                    </a>
                  <?php endif; ?>
=======
                  <a href="volunteer-edit.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-warning">
                    <i class="ri-pencil-line me-1"></i> Edit
                  </a>
                  <a href="volunteer-delete.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-danger">
                    <i class="ri-delete-bin-line me-1"></i> Delete
                  </a>
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
                  <button class="btn btn-sm btn-outline-secondary" onclick="window.print()" title="Print this post">
                    <i class="ri-printer-line me-1"></i> Print
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>


<?php
$content = ob_get_clean();

// Define additional styles and scripts
$additional_styles = ['assets/css/template.css', 'assets/css/showpost.css', 'assets/css/volunteer-view.css'];
$additional_scripts = ['assets/js/volunteer-view.js', 'assets/js/form.js', 'assets/js/javascript.js'];

// Render the layout
renderLayout('Volunteers', $content, $additional_styles, $additional_scripts);
?>