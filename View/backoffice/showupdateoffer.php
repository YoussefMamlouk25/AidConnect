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
                    <h4 class="card-title mb-0">Updates Offres</h4>
                    <p class="text-muted mb-0">Liste complète des mises à jour des offres AidConnect.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID update</th>
                                <th scope="col">ID offre</th>
                                <th scope="col">Auteur</th>
                                <th scope="col">Texte</th>
                                <th scope="col">Média</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($updates)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Aucune mise à jour pour le moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($updates as $update): 
                                    $offer = $update['id_offer'] ? $offerCtrl->getOfferById($update['id_offer']) : null; 
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($update['id_update_offer']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($update['id_offer'] ?? '—') ?>
                                        <?php if ($offer): ?><br><small class="text-muted"><?= htmlspecialchars($offer['nom']) ?></small><?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= $update['id_user'] ? 'User #'.(int)$update['id_user'] : 'Anonyme' ?>
                                        </span>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars(mb_strimwidth($update['texte'],0,120,'…'))) ?></td>
                                    <td>
                                        <?php
                                            $mediaFiles = json_decode($update['media'], true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles) && !empty($mediaFiles)) {
                                                foreach (array_slice($mediaFiles, 0, 1) as $mediaFile) {
                                                    $mediaUrl = '../../public/uploads/' . $mediaFile;
                                                    $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                                    if (in_array($ext, ['mp4','mov','webm'])) {
                                                        echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls style="width:52px;height:52px;object-fit:cover;border-radius:4px;"></video>';
                                                    } else {
                                                        echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Media" style="width:52px;height:52px;object-fit:cover;border-radius:4px;">';
                                                    }
                                                }
                                            } elseif (!empty($update['media'])) {
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
                                    <td class="text-end">
                                        <a href="edit_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>&ajax=1" class="btn btn-sm btn-outline-primary me-1" onclick="if(typeof loadContent==='function'){ loadContent('edit_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>&ajax=1'); return false; }" title="Éditer">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="delete_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>&ajax=1" class="btn btn-sm btn-outline-danger" onclick="if(typeof loadContent==='function'){ loadContent('delete_updateoffer.php?id=<?= (int)$update['id_update_offer'] ?>&ajax=1'); return false; }" title="Supprimer">
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
    renderBackofficeLayout('Updates Offres', $content);
} else {
    // Si AJAX, inclure le CSS si nécessaire
    echo '<link rel="stylesheet" href="assets/css/showofferback.css">';
}
?>