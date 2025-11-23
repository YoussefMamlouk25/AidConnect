<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/postupdate.php';
require_once '../../Controller/postupdatecontroller.php';
require_once 'layout.php';

$postUpdateController = new PostUpdateController();
$list = $postUpdateController->getAllPostUpdates();

// Fonction pour obtenir les initiales d'un nom
function getInitials($name) {
    if (empty($name)) return 'AN';
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php
        if ($list && is_object($list) && method_exists($list, 'rowCount')) {
            $rowCount = $list->rowCount();
            if ($rowCount > 0) {
                foreach ($list as $row) {
                    $authorName = htmlspecialchars($row['nom'] ?? 'Anonyme');
                    $initials = getInitials($authorName);
                    $titre = htmlspecialchars($row['titre'] ?? '');
                    $texte = htmlspecialchars($row['texte'] ?? '');
                    $region = htmlspecialchars($row['region'] ?? '');
                    $tempReponse = htmlspecialchars($row['temp_reponse'] ?? '');
                    $tempResolue = htmlspecialchars($row['temp_resolue'] ?? '');
                    $realiserRaw = $row['realiser'] ?? '';
                    $realiser = !empty($realiserRaw) ? htmlspecialchars($realiserRaw) : '';
                    $hasImage = !empty($row['media']);
                    $imagePath = $hasImage ? '../../public/uploads/' . htmlspecialchars($row['media']) : '';
                    $idUpdatePost = $row['id_update_post'];
                    ?>
                    <div class="card mb-3 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: 600;">
                                    <?php echo $initials; ?>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $authorName; ?></h6>
                                    <small class="text-muted">
                                        <?php if (!empty($region)): ?>
                                            <i class="ri-map-pin-line me-1"></i><?php echo $region; ?>
                                        <?php endif; ?>
                                        <span class="badge bg-success ms-2">
                                            <i class="ri-checkbox-circle-line me-1"></i>Résolu
                                        </span>
                                    </small>
                                </div>
                            </div>

                            <h5 class="mb-2"><?php echo $titre; ?></h5>

                            <?php if (!empty($texte)): ?>
                                <div class="alert alert-success mb-3">
                                    <strong>Résolution :</strong><br>
                                    <?php echo nl2br($texte); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($hasImage): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $imagePath; ?>" alt="Photo de la résolution" class="img-fluid rounded" style="max-height: 500px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                                </div>
                            <?php endif; ?>

                            <div class="bg-light p-3 rounded mb-3">
                                <?php if (!empty($realiser)): ?>
                                    <div class="mb-2">
                                        <i class="ri-user-line me-2"></i><strong>Intervention réalisée par :</strong> <?php echo $realiser; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($tempReponse)): ?>
                                    <div class="mb-2">
                                        <i class="ri-time-line me-2"></i><strong>Temps de réponse :</strong> <?php echo $tempReponse; ?> jour(s)
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($tempResolue)): ?>
                                    <div>
                                        <i class="ri-calendar-check-line me-2"></i><strong>Temps de résolution :</strong> <?php echo $tempResolue; ?> jour(s)
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <form method="POST" action="updatepostupdate.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $idUpdatePost; ?>">
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <i class="ri-edit-line me-1"></i> Modifier
                                    </button>
                                </form>
                                <a href="deletepostupdate.php?id=<?php echo $idUpdatePost; ?>" class="btn btn-sm btn-danger">
                                    <i class="ri-delete-bin-line me-1"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ri-checkbox-circle-line" style="font-size: 4rem; color: #28a745;"></i>
                        <h5 class="mt-3">Aucun post résolu pour le moment</h5>
                        <p class="text-muted">Les posts résolus apparaîtront ici</p>
                        <a href="showPosts.php" class="btn btn-primary">
                            <i class="ri-file-list-3-line me-2"></i> Voir les posts
                        </a>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-danger">Erreur</h5>
                    <p class="text-muted">Erreur lors de la récupération des posts résolus</p>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Posts Résolus', $content);
?>
