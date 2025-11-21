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
                  <div class="post-media">
                    <?php
                      $mediaUrl = '../../public/uploads/' . $offer['media'];
                      $ext = strtolower(pathinfo($offer['media'], PATHINFO_EXTENSION));
                      if (in_array($ext, ['mp4','mov','webm'])) {
                        echo '<video src="'.OfferController::escape($mediaUrl).'" controls></video>';
                      } else {
                        echo '<img src="'.OfferController::escape($mediaUrl).'" alt="Volunteer media">';
                      }
                    ?>
                  </div>
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
<footer class="bg-light text-center text-muted py-3 mt-4">
  <p>© 2025 AidConnect. Edit/Delete Enabled.</p>
</footer>
</body>
</html>