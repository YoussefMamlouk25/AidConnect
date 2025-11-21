<?php
require_once '../../config.php';
require_once '../../Model/updateoffer.php';
require_once '../../Controller/updateoffercontroller.php';
require_once '../../Controller/offercontroller.php';

$updateCtrl = new UpdateOfferController();
$offerCtrl  = new OfferController();
$updates    = $updateCtrl->getAllUpdateOffers();

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';

if (!$embed):
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
    <aside class="sidebar">
        <h3>AidConnect</h3>
        <nav>
            <button class="sidebar-link" onclick="window.location.href='dashboard.php'">🏠 Dashboard</button>
            <button class="sidebar-link" onclick="window.location.href='showoffer_fragment.php'">🏷️ Offres</button>
            <button class="sidebar-link" onclick="window.location.href='showupdateoffer_fragment.php'">🔁 Updates Offres</button>
        </nav>
    </aside>
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
          <td><?= $update['media'] ?: 'Aucun' ?></td>
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
<?php if (!$embed): ?>
    </div>
</div>
</body>
</html>
<?php endif; ?>