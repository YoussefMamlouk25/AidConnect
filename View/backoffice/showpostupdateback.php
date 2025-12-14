<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/postupdate.php';
require_once '../../Controller/postupdatecontroller.php';
require_once '../../Controller/postcontroller.php';

$postUpdateController = new PostUpdateController();
$postController = new PostController();
$list = $postUpdateController->getAllPostUpdates();

// Convertir $list en tableau
$updates = [];
if ($list) {
    if (is_object($list) && method_exists($list, 'fetchAll')) {
        $updates = $list->fetchAll(PDO::FETCH_ASSOC);
    } elseif (is_array($list)) {
        $updates = $list;
    }
}

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
    <title>AidConnect - Liste des Posts Résolus</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/showofferback.css">
    <link rel="stylesheet" href="assets/css/offer_actions.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
<?php
endif;
?>
<link rel="stylesheet" href="assets/css/showofferback.css">
<div class="offers-fragment">
  <h2>Mises à jour / Résolutions</h2>
  <div class="container">
    <table class="post-table">
      <thead>
        <tr>
          <th>ID update</th>
          <th>ID post</th>
          <th>Auteur (user)</th>
          <th>Texte</th>
          <th>Média</th>
          <th>Intervention réalisée par</th>
          <th>Temps réponse (jours)</th>
          <th>Temps résolution (jours)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($updates as $update): 
          $post = $update['id_post'] ? $postController->getPostById($update['id_post']) : null; ?>
        <tr>
          <td><?= htmlspecialchars($update['id_update_post'] ?? '—') ?></td>
          <td>
            <?= htmlspecialchars($update['id_post'] ?? '—') ?>
            <?php if ($post): ?><br><small><?= htmlspecialchars($post['titre'] ?? '') ?></small><?php endif; ?>
          </td>
          <td><?= $update['id_user'] ? 'User #'.(int)$update['id_user'] : 'Anonyme' ?></td>
          <td><?= nl2br(htmlspecialchars(mb_strimwidth($update['texte'] ?? '',0,120,'…'))) ?></td>
          <td>
          <?php
            if (!empty($update['media'])) {
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
          <td><?= !empty($update['realiser']) ? nl2br(htmlspecialchars($update['realiser'])) : '<em>Non spécifié</em>' ?></td>
          <td><?= htmlspecialchars($update['temp_reponse'] ?? '—') ?></td>
          <td><?= htmlspecialchars($update['temp_resolue'] ?? '—') ?></td>
          <td class="table-actions">
            <a class="btn-action btn-edit"
               href="updatepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>"
               onclick="return typeof loadContent==='function' ? (loadContent('updatepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>&ajax=1'), false) : true;">
               ✎ Éditer
            </a>
            <a class="btn-action btn-delete"
               href="deletepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>"
               onclick="return typeof loadContent==='function' ? (loadContent('deletepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>&ajax=1'), false) : confirm('Supprimer cette mise à jour ?');">
               🗑 Supprimer
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($updates)): ?>
          <tr><td colspan="9" class="no-data">Aucune mise à jour</td></tr>
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
