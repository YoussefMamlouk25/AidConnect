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
        <div class="row">
          <?php foreach ($offers as $offer): ?>
            <div class="col-lg-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: 600;">
                      <?= strtoupper(substr(OfferController::escape($offer['nom']), 0, 1)) ?>
                    </div>
                    <div>
                      <h6 class="mb-0"><?= OfferController::escape($offer['nom']) ?></h6>
                      <small class="text-muted"><?= OfferController::escape($offer['email']) ?> • Volunteer</small>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <span class="badge bg-info me-2">Type: <?= OfferController::escape($offer['type_don']) ?></span>
                    <span class="badge bg-secondary">Numéro: <?= OfferController::escape($offer['num']) ?></span>
                  </div>
                  
                  <p><?= nl2br(OfferController::escape($offer['description'])) ?></p>
                  <?php if (!empty($offer['media'])): ?>
                    <div class="mb-3">
                      <?php
                        $mediaFiles = json_decode($offer['media'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                          foreach ($mediaFiles as $mediaFile) {
                            $mediaUrl = '../../public/uploads/' . $mediaFile;
                            $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                            if (in_array($ext, ['mp4','mov','webm'])) {
                              echo '<video src="'.OfferController::escape($mediaUrl).'" controls class="img-fluid rounded mb-2" style="max-width: 100%;"></video>';
                            } else {
                              echo '<img src="'.OfferController::escape($mediaUrl).'" alt="Volunteer media" class="img-fluid rounded mb-2" style="max-width: 100%; max-height: 300px; object-fit: cover;">';
                            }
                          }
                        } else {
                          $mediaUrl = '../../public/uploads/' . $offer['media'];
                          $ext = strtolower(pathinfo($offer['media'], PATHINFO_EXTENSION));
                          if (in_array($ext, ['mp4','mov','webm'])) {
                            echo '<video src="'.OfferController::escape($mediaUrl).'" controls class="img-fluid rounded mb-2" style="max-width: 100%;"></video>';
                          } else {
                            echo '<img src="'.OfferController::escape($mediaUrl).'" alt="Volunteer media" class="img-fluid rounded mb-2" style="max-width: 100%; max-height: 300px; object-fit: cover;">';
                          }
                        }
                      ?>
                    </div>
                  <?php endif; ?>
                  
                  <div class="d-flex gap-2 flex-wrap">
                    <?php if ($offer['is_creator']): ?>
                      <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-info">
                        <i class="ri-edit-line me-1"></i> Update
                      </a>
                    <?php else: ?>
                      <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-primary">
                        <i class="ri-reply-line me-1"></i> Reply
                      </a>
                    <?php endif; ?>
                    <a href="update-offer-view.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-secondary">
                      <i class="ri-file-list-3-line me-1"></i> Updates
                      <?php if ($offer['updates_count'] > 0): ?>
                        <span class="badge bg-primary"><?= $offer['updates_count'] ?></span>
                      <?php endif; ?>
                    </a>
                    <a href="volunteer-edit.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-warning">
                      <i class="ri-pencil-line me-1"></i> Edit
                    </a>
                    <a href="volunteer-delete.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-danger">
                      <i class="ri-delete-bin-line me-1"></i> Delete
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
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