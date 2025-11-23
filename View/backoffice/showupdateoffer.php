<?php
require_once '../../config.php';
require_once '../../Model/updateoffer.php';
require_once '../../Controller/updateoffercontroller.php';
require_once '../../Controller/offercontroller.php';

$updateCtrl = new UpdateOfferController();
$offerCtrl  = new OfferController();
$updates    = $updateCtrl->getAllUpdateOffers();

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;

if ($showFullPage):
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mises à jour des Offres</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="showofferback.css">
    <link rel="stylesheet" href="offer_actions.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
<?php
endif;
?>
<link rel="stylesheet" href="showofferback.css">
<div class="offers-fragment">
  <h2>Mises à jour / Réponses</h2>
  <div class="container">
    <table class="post-table">
      <thead>
        <tr>
          <th>ID update</th>
          <th>ID offre</th>
          <th>Auteur (user)</th>
          <th>Texte</th>
          <th>Média</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($updates as $update): 
          $offer = $update['id_offer'] ? $offerCtrl->getOfferById($update['id_offer']) : null; ?>
        <tr>
          <td><?= htmlspecialchars($update['id_update_offer']) ?></td>
          <td>
            <?= htmlspecialchars($update['id_offer'] ?? '—') ?>
            <?php if ($offer): ?><br><small><?= htmlspecialchars($offer['nom']) ?></small><?php endif; ?>
          </td>
          <td><?= $update['id_user'] ? 'User #'.(int)$update['id_user'] : 'Anonyme' ?></td>
          <td><?= nl2br(htmlspecialchars(mb_strimwidth($update['texte'],0,120,'…'))) ?></td>
          <td>
          <?php
            $mediaFiles = json_decode($update['media'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                foreach ($mediaFiles as $mediaFile) {
                    $mediaUrl = '../../public/uploads/' . $mediaFile;
                    $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                    if (in_array($ext, ['mp4','mov','webm'])) {
                        echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls class="media-thumb"></video>';
                    } else {
                        echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Media image" class="media-thumb">';
                    }
                }
            } elseif (!empty($update['media'])) {
                $mediaUrl = '../../public/uploads/' . $update['media'];
                $ext = strtolower(pathinfo($update['media'], PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4','mov','webm'])) {
                    echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls class="media-thumb"></video>';
                } else {
                    echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Media image" class="media-thumb">';
                }
            } else {
                echo 'Aucun';
            }
          ?>
          </td>
          <td class="table-actions">
            <a class="btn-action btn-edit"
               href="edit_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>"
               onclick="return typeof loadContent==='function' ? (loadContent('edit_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>&embed=1'), false) : true;">
               ✎ Éditer
            </a>
            <a class="btn-action btn-delete"
               href="delete_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>"
               onclick="return typeof loadContent==='function' ? (loadContent('delete_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>&embed=1'), false) : confirm('Supprimer cette mise à jour ?');">
               🗑 Supprimer
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($updates)): ?>
          <tr><td colspan="6" class="no-data">Aucune mise à jour</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<style>
.media-thumb {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    margin: 2px;
}
</style>
<?php if ($showFullPage): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>