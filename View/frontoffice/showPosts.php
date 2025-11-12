<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';

$postController = new PostController();
$list = $postController->getAllPosts();

// Fonction pour obtenir les initiales d'un nom
function getInitials($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}

// Fonction pour formater la date
function formatDate($date) {
    if (empty($date)) return '';
    $dateObj = new DateTime($date);
    $now = new DateTime();
    $diff = $now->diff($dateObj);
    
    if ($diff->days == 0) {
        return "Aujourd'hui";
    } elseif ($diff->days == 1) {
        return "Hier";
    } elseif ($diff->days < 7) {
        return "Il y a " . $diff->days . " jours";
    } else {
        return $dateObj->format('d/m/Y');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidConnect - Posts</title>
    <link rel="stylesheet" href="showpost.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>AidConnect</h1>
            <a href="addpost.php" class="btn-add-post">➕ Créer un post</a>
        </div>
    </div>

    <div class="container">
        <?php
        if ($list && is_object($list) && method_exists($list, 'rowCount')) {
            $rowCount = $list->rowCount();
            if ($rowCount > 0) {
                foreach ($list as $row) {
                    $numero = isset($row['Numéro']) ? $row['Numéro'] : (isset($row['numero']) ? $row['numero'] : '');
                    $authorName = htmlspecialchars($row['nom'] ?? 'Anonyme');
                    $initials = getInitials($authorName);
                    $titre = htmlspecialchars($row['titre'] ?? '');
                    $description = htmlspecialchars($row['description'] ?? '');
                    $region = htmlspecialchars($row['region'] ?? '');
                    $email = htmlspecialchars($row['email'] ?? '');
                    $dateFormatted = formatDate($row['date_creation'] ?? '');
                    $hasImage = !empty($row['media']);
                    $imagePath = $hasImage ? '../../public/uploads/' . htmlspecialchars($row['media']) : '';
                    $idPost = $row['id_post'];
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
                                    <?php if (!empty($dateFormatted)): ?>
                                        <span class="date">🕒 <?php echo $dateFormatted; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="post-title"><?php echo $titre; ?></div>

                        <?php if (!empty($description)): ?>
                            <div class="post-description"><?php echo nl2br($description); ?></div>
                        <?php endif; ?>

                        <?php if ($hasImage): ?>
                            <div class="post-image-container">
                                <img src="<?php echo $imagePath; ?>" alt="Photo du post" class="post-image" onclick="window.open(this.src, '_blank')">
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($email) || !empty($numero)): ?>
                            <div class="post-contact">
                                <?php if (!empty($email)): ?>
                                    <div class="contact-item">
                                        <strong>📧 Email:</strong>
                                        <span><?php echo $email; ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($numero)): ?>
                                    <div class="contact-item">
                                        <strong>📞 Téléphone:</strong>
                                        <span><?php echo $numero; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="post-actions">
                            <a href="updatepost.php?id=<?php echo $idPost; ?>" class="btn-action btn-edit">✏️ Modifier</a>
                            <a href="deletepost.php?id=<?php echo $idPost; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?');">🗑️ Supprimer</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <div class="empty-state-text">Aucun post pour le moment</div>
                    <a href="addpost.php" class="btn-add-post">Créer le premier post</a>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="error-state">
                <p>❌ Erreur lors de la récupération des posts</p>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
