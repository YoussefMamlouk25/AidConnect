<?php
session_start();
require_once __DIR__ . '/../../Controller/offercontroller.php';
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';

$offerC = new OfferController();
$updateOfferC = new UpdateOfferController();

$offers = $offerC->getAllOffers();

$current_user_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;

foreach ($offers as &$offer) {
    $offer['updates_count'] = count($updateOfferC->getUpdateOffersByOfferId($offer['id_offer']));
    $offer['is_creator'] = ($current_user_id > 0 && $offer['id_user'] == $current_user_id);
}
unset($offer);

$deleted = isset($_GET['deleted']) && $_GET['deleted'] == '1';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>AidConnect • Volunteers</title>
  <link rel="stylesheet" href="template.css">
  <link rel="stylesheet" href="assets/css/libs.min.css">
  <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0">
  <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="showpost.css">
  <style>
    /* Facebook-style Media Grid for Offers */
    .fb-media-grid {
      display: grid;
      gap: 2px;
      border-radius: 8px;
      overflow: hidden;
      max-height: 500px;
    }
    
    .fb-media-grid.count-1 {
      grid-template-columns: 1fr;
    }
    
    .fb-media-grid.count-2 {
      grid-template-columns: 1fr 1fr;
    }
    
    .fb-media-grid.count-3 {
      grid-template-columns: 1fr 1fr;
      grid-template-rows: 1fr 1fr;
    }
    
    .fb-media-grid.count-3 .grid-item:first-child {
      grid-row: 1 / 3;
    }
    
    .fb-media-grid.count-4 {
      grid-template-columns: 1fr 1fr;
      grid-template-rows: 1fr 1fr;
    }
    
    .fb-media-grid .grid-item {
      position: relative;
      overflow: hidden;
      background: #000;
    }
    
    .fb-media-grid .grid-item img,
    .fb-media-grid .grid-item video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    
    .fb-media-grid.count-1 .grid-item {
      height: 400px;
    }
    
    .fb-media-grid.count-2 .grid-item {
      height: 300px;
    }
    
    .fb-media-grid.count-3 .grid-item,
    .fb-media-grid.count-4 .grid-item {
      height: 250px;
    }
  </style>
  <script src="assets/js/libs.min.js"></script>
  <script src="assets/js/app.js"></script>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-body text-center">
          <a href="volunteer-form.php" class="btn btn-primary">
            <i class="ri-add-circle-line me-2"></i> Ajouter une Offre
          </a>
        </div>
      </div>
      <h5 class="mb-3 text-center">Volunteer Posts</h5>
      <?php if ($deleted): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Volunteer deleted successfully!
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
        <div class="row justify-content-center">
          <div class="col-lg-8">
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
                  <div class="avatar"><?= $initials ?></div>
                  <div class="post-info">
                    <div class="post-author"><?= $nom ?></div>
                    <div class="post-meta">
                      <span class="badge bg-primary"><?= $typeDonLabel ?></span>
                      <span class="text-muted ms-2">Volunteer</span>
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
                      <i class="ri-mail-line me-2"></i>
                      <strong>Email:</strong> <?= $email ?>
                    </div>
                    <div class="contact-item">
                      <i class="ri-phone-line me-2"></i>
                      <strong>Téléphone:</strong> <?= $num ?>
                    </div>
                  </div>
                </div>

                <!-- Post Actions -->
                <div class="post-actions">
                  <?php if ($offer['is_creator']): ?>
                    <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-action btn-outline-primary">
                      <i class="ri-edit-line me-1"></i> Modifier
                    </a>
                  <?php else: ?>
                    <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-action btn-outline-primary">
                      <i class="ri-reply-line me-1"></i> Répondre
                    </a>
                  <?php endif; ?>
                  <a href="update-offer-view.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-action btn-outline-secondary">
                    <i class="ri-file-list-3-line me-1"></i> Réponses
                    <?php if ($offer['updates_count'] > 0): ?>
                      <span class="badge bg-primary ms-1"><?= $offer['updates_count'] ?></span>
                    <?php endif; ?>
                  </a>
                  <a href="volunteer-edit.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-action btn-outline-warning">
                    <i class="ri-pencil-line me-1"></i> Éditer
                  </a>
                  <a href="volunteer-delete.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-action btn-outline-danger">
                    <i class="ri-delete-bin-line me-1"></i> Supprimer
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="form.js"></script>
<script src="javascript.js"></script>
<footer class="bg-light text-center text-muted py-3 mt-4">
  <p>© 2025 AidConnect. Edit/Delete Enabled.</p>
</footer>
</body>
</html>