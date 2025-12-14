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

// Si c'est une page complète, utiliser le layout
if ($showFullPage) {
    require_once 'layout.php';
    ob_start();
}
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Posts Résolus</h4>
                    <p class="text-muted mb-0">Liste complète des posts résolus AidConnect.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID update</th>
                                <th scope="col">ID post</th>
                                <th scope="col">Auteur</th>
                                <th scope="col">Texte</th>
                                <th scope="col">Média</th>
                                <th scope="col">Intervention</th>
                                <th scope="col">Temps réponse</th>
                                <th scope="col">Temps résolution</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($updates)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Aucune mise à jour pour le moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($updates as $update): 
                                    $post = $update['id_post'] ? $postController->getPostById($update['id_post']) : null; 
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($update['id_update_post'] ?? '—') ?></td>
                                    <td>
                                        <?= htmlspecialchars($update['id_post'] ?? '—') ?>
                                        <?php if ($post): ?><br><small class="text-muted"><?= htmlspecialchars($post['titre'] ?? '') ?></small><?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= $update['id_user'] ? 'User #'.(int)$update['id_user'] : 'Anonyme' ?>
                                        </span>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars(mb_strimwidth($update['texte'] ?? '',0,120,'…'))) ?></td>
                                    <td>
                                        <?php
                                            if (!empty($update['media'])) {
                                                $mediaUrl = '../../public/uploads/' . $update['media'];
                                                $ext = strtolower(pathinfo($update['media'], PATHINFO_EXTENSION));
                                                if (in_array($ext, ['mp4','mov','webm'])) {
                                                    echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls style="width:52px;height:52px;object-fit:cover;border-radius:4px;"></video>';
                                                } else {
                                                    echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Media" style="width:52px;height:52px;object-fit:cover;border-radius:4px;">';
                                                }
                                            } else {
                                                echo '—';
                                            }
                                        ?>
                                    </td>
                                    <td><?= !empty($update['realiser']) ? nl2br(htmlspecialchars($update['realiser'])) : '<em class="text-muted">Non spécifié</em>' ?></td>
                                    <td><?= htmlspecialchars($update['temp_reponse'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($update['temp_resolue'] ?? '—') ?></td>
                                    <td class="text-end">
                                        <a href="updatepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>&ajax=1" class="btn btn-sm btn-outline-primary me-1" onclick="if(typeof loadContent==='function'){ loadContent('updatepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>&ajax=1'); return false; }" title="Éditer">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="deletepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>&ajax=1" class="btn btn-sm btn-outline-danger" onclick="if(typeof loadContent==='function'){ loadContent('deletepostupdate.php?id=<?= (int)($update['id_update_post'] ?? 0) ?>&ajax=1'); return false; }" title="Supprimer">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if ($showFullPage) {
    $content = ob_get_clean();
    renderBackofficeLayout('Posts Résolus', $content);
} else {
    // Si AJAX, inclure le CSS si nécessaire
    echo '<link rel="stylesheet" href="assets/css/showofferback.css">';
}
