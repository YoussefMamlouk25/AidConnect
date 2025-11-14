<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/postupdate.php';
require_once '../../Controller/postupdatecontroller.php';

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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidConnect - Posts Résolus</title>
    <link rel="stylesheet" href="showpost.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>AidConnect - Posts Résolus</h1>
            <a href="showPosts.php" class="btn-add-post">📋 Voir tous les posts</a>
        </div>
    </div>

    <div class="container">
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
                    $realiser = htmlspecialchars($row['realiser'] ?? '');
                    $hasImage = !empty($row['media']);
                    $imagePath = $hasImage ? '../../public/uploads/' . htmlspecialchars($row['media']) : '';
                    $idUpdatePost = $row['id_update_post'];
                    ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div class="avatar"><?php echo $initials; ?></div>
                            <div class="post-info">
                                <div class="post-author"><?php echo $authorName; ?></div>
                                <div class="post-meta">
                                    <?php if (!empty($region)): ?>
                                        <span class="region">📍 <?php echo $region; ?></span>
                                    <?php endif; ?>
                                    <span class="status-resolved">✅ Résolu</span>
                                </div>
                            </div>
                        </div>

                        <div class="post-title"><?php echo $titre; ?></div>

                        <?php if (!empty($texte)): ?>
                            <div class="post-description">
                                <strong>Résolution :</strong><br>
                                <?php echo nl2br($texte); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasImage): ?>
                            <div class="post-image-container">
                                <img src="<?php echo $imagePath; ?>" alt="Photo de la résolution" class="post-image" onclick="window.open(this.src, '_blank')">
                            </div>
                        <?php endif; ?>

                        <div class="post-resolution-info">
                            <?php if (!empty($realiser)): ?>
                                <div class="resolution-item">
                                    <strong>Intervention réalisée par :</strong>
                                    <span><?php echo $realiser; ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($tempReponse)): ?>
                                <div class="resolution-item">
                                    <strong>Temps de réponse :</strong>
                                    <span><?php echo $tempReponse; ?> jour(s)</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($tempResolue)): ?>
                                <div class="resolution-item">
                                    <strong>Temps de résolution :</strong>
                                    <span><?php echo $tempResolue; ?> jour(s)</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="post-actions">
                            <a href="deletepostupdate.php?id=<?php echo $idUpdatePost; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce post résolu ?');">🗑️ Supprimer</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="empty-state">
                    <div class="empty-state-icon">✅</div>
                    <div class="empty-state-text">Aucun post résolu pour le moment</div>
                    <a href="showPosts.php" class="btn-add-post">Voir les posts</a>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="error-state">
                <p>❌ Erreur lors de la récupération des posts résolus</p>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
