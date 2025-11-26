<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once 'layout.php';

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

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body text-center">
                <a href="addpost.php" class="btn btn-primary">
                    <i class="ri-add-circle-line me-2"></i> Ajouter un Post
                </a>
            </div>
        </div>

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
                    $mediaRaw = $row['media'] ?? '';
                    $mediaItems = [];
                    if (!empty($mediaRaw)) {
                        $decoded = json_decode($mediaRaw, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $mediaItems = array_values(array_filter($decoded));
                        } else {
                            $mediaItems[] = $mediaRaw;
                        }
                    }
                    $mediaCount = count($mediaItems);
                    $idPost = $row['id_post'];
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: 600;">
                                    <?php echo $initials; ?>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo $authorName; ?></h6>
                                    <small class="text-muted">
                                        <?php if (!empty($region)): ?>
                                            <i class="ri-map-pin-line me-1"></i><?php echo $region; ?>
                                        <?php endif; ?>
                                        <?php if (!empty($dateFormatted)): ?>
                                            <i class="ri-time-line ms-2 me-1"></i><?php echo $dateFormatted; ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>

                            <h5 class="mb-2"><?php echo $titre; ?></h5>

                            <?php if (!empty($description)): ?>
                                <p class="mb-3"><?php echo nl2br($description); ?></p>
                            <?php endif; ?>

                            <?php if ($mediaCount > 0): ?>
                                <div class="mb-3">
                                    <div class="fb-media-grid count-<?php echo $mediaCount >= 4 ? 4 : $mediaCount; ?>">
                                        <?php foreach ($mediaItems as $index => $mediaFile): ?>
                                            <?php if ($index >= 4) break; ?>
                                            <?php $mediaPath = '../../public/uploads/' . htmlspecialchars($mediaFile); ?>
                                            <div class="grid-item">
                                                <img src="<?php echo $mediaPath; ?>" alt="Photo du post" class="img-fluid rounded">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($email) || !empty($numero)): ?>
                                <div class="bg-light p-3 rounded mb-3">
                                    <?php if (!empty($email)): ?>
                                        <div class="mb-2">
                                            <i class="ri-mail-line me-2"></i><strong>Email:</strong> <?php echo $email; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($numero)): ?>
                                        <div>
                                            <i class="ri-phone-line me-2"></i><strong>Téléphone:</strong> <?php echo $numero; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2 flex-wrap">
                                <a href="addpostupdate.php?id_post=<?php echo $idPost; ?>" class="btn btn-sm btn-success">
                                    <i class="ri-checkbox-circle-line me-1"></i> Résoudre
                                </a>
                                <a href="updatepost.php?id=<?php echo $idPost; ?>" class="btn btn-sm btn-info">
                                    <i class="ri-edit-line me-1"></i> Modifier
                                </a>
                                <a href="deletepost.php?id=<?php echo $idPost; ?>" class="btn btn-sm btn-danger">
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
                        <i class="ri-inbox-line" style="font-size: 4rem; color: #ccc;"></i>
                        <h5 class="mt-3">Aucun post pour le moment</h5>
                        <p class="text-muted">Soyez le premier à créer un post !</p>
                        <a href="addpost.php" class="btn btn-primary">
                            <i class="ri-add-circle-line me-2"></i> Créer le premier post
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
                    <p class="text-muted">Erreur lors de la récupération des posts</p>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Posts', $content);
?>
