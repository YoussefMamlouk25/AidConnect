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
  <link rel="stylesheet" href="assets/css/libs.min.css">
  <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0">
  <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/libs.min.js"></script>
  <script src="assets/js/app.js"></script>
  <link rel="stylesheet" href="assets/css/media-lightbox.css">
</head>
<body>
<header class="header bg-white shadow-sm p-3 mb-4">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <h4 class="text-primary mb-0">AidConnect</h4>
    <a href="volunteer-form.php" class="btn btn-primary">Register New Volunteer</a>
  </div>
</header>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
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
        <p class="text-muted text-center">No volunteers registered yet</p>
      <?php else: ?>
        <div class="posts-container">
          <?php foreach ($offers as $offer): ?>
            <div class="facebook-post">
              <div class="post-header">
                <div class="post-avatar" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);">
                  <?= strtoupper(substr(OfferController::escape($offer['nom']), 0, 1)) ?>
                </div>
                <div>
                  <strong class="text-dark"><?= OfferController::escape($offer['nom']) ?></strong><br>
                  <small class="text-muted"><?= OfferController::escape($offer['email']) ?> • Volunteer</small>
                </div>
              </div>
              <div class="post-content">
                <p><strong>Type de don:</strong> <?= OfferController::escape($offer['type_don']) ?></p>
                <p><strong>Numéro:</strong> <?= OfferController::escape($offer['num']) ?></p>
                <p><?= nl2br(OfferController::escape($offer['description'])) ?></p>
                <?php if (!empty($offer['media'])): ?>
                  <?php
                    $mediaFiles = json_decode($offer['media'], true);
                    if (!(json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles))) {
                      $mediaFiles = [$offer['media']];
                    }
                    $images = [];
                    $videos = [];
                    foreach ($mediaFiles as $m) {
                      $ext = strtolower(pathinfo($m, PATHINFO_EXTENSION));
                      if (in_array($ext, ['mp4','mov','webm'])) {
                        $videos[] = $m;
                      } else {
                        $images[] = $m;
                      }
                    }
                    // Render videos first (full width)
                    if (!empty($videos)) {
                      foreach ($videos as $v) {
                        $vUrl = '../../public/uploads/' . $v;
                        echo '<div class="post-media" style="margin-bottom:8px;"><video src="'.OfferController::escape($vUrl).'" controls style="width:100%;height:auto;max-height:480px;border-radius:6px;" preload="none"></video></div>';
                      }
                    }
                    // Render images in a Facebook-like grid
                    if (!empty($images)) {
                      $imgCount = count($images);
                      $showCount = $imgCount > 4 ? 4 : $imgCount;
                      $extra = $imgCount - $showCount;
                      $display = array_slice($images, 0, $showCount);
                      echo '<div class="fb-media-grid count-'.(int)$showCount.'">';
                      foreach ($display as $i => $img) {
                        $imgUrl = '../../public/uploads/' . $img;
                        echo '<div class="grid-item">';
                        echo '<img src="'.OfferController::escape($imgUrl).'" alt="Volunteer image">';
                        if ($i === $showCount - 1 && $extra > 0) {
                          echo '<div class="more-overlay">+' . (int)$extra . '</div>';
                        }
                        echo '</div>';
                      }
                      echo '</div>';
                    }
                  ?>
                <?php endif; ?>
              </div>
              <div class="post-footer">
                <div class="post-action" onclick="alert('Liked!')">Like <span class="like-count">0</span></div>
                <?php if ($offer['is_creator']): ?>
                  <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="post-action text-decoration-none">Update</a>
                <?php else: ?>
                  <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="post-action text-decoration-none">Reply</a>
                <?php endif; ?>
                <a href="update-offer-view.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="post-action text-decoration-none">
                  Updates 
                  <?php if ($offer['updates_count'] > 0): ?>
                    <span class="badge bg-primary"><?= $offer['updates_count'] ?></span>
                  <?php endif; ?>
                </a>
                <a href="volunteer-edit.php?id=<?= (int)$offer['id_offer'] ?>" class="post-action text-decoration-none">Edit</a>
                <a href="volunteer-delete.php?id=<?= (int)$offer['id_offer'] ?>" class="post-action text-decoration-none">Delete</a>
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
<div id="image-modal" class="image-modal" aria-hidden="true" role="dialog" aria-label="Image preview">
  <button class="close-btn" aria-label="Close">&times;</button>
  <button class="nav prev" aria-label="Previous">&#10094;</button>
  <div class="media-wrap" role="document"></div>
  <button class="nav next" aria-label="Next">&#10095;</button>
</div>
<script src="assets/js/media-lightbox.js"></script>
<footer class="bg-light text-center text-muted py-3 mt-4">
  <p>© 2025 AidConnect. Edit/Delete Enabled.</p>
</footer>
</body>
</html>