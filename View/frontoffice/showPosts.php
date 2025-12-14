<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once '../../Controller/postlikecontroller.php';
require_once '../../Controller/postcommentcontroller.php';
require_once 'layout.php';

// Récupérer les informations de l'utilisateur connecté
$current_user_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');

$postController = new PostController();
$likeController = new PostLikeController();
$commentController = new PostCommentController();
// Créer les tables si elles n'existent pas
$likeController->createTableIfNotExists();
$commentController->createTableIfNotExists();
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

// Fonction pour obtenir le chemin de la photo de profil
function getProfilePic($storedProfile) {
    $defaultPic = 'assets/images/default_profile.png';
    if (empty($storedProfile)) {
        return $defaultPic;
    }
    $normalized = ltrim(trim($storedProfile), '/');
    $relativePath = '../../' . $normalized;
    $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
    if ($absolutePath !== false && file_exists($absolutePath)) {
        return $relativePath;
    }
    return $defaultPic;
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
        <div class="text-center mb-4">
            <a href="addpost.php" class="btn-add-offer">
                <i class="ri-add-circle-line me-2"></i> Ajouter un Post
            </a>
        </div>
        
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="postSearchInput" 
                               placeholder="Rechercher un post par titre..." 
                               autocomplete="off">
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                id="clearSearchBtn" 
                                onclick="clearSearch()" 
                                style="display: none;">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="ri-information-line me-1"></i>
                        Tapez pour filtrer les posts par titre
                    </small>
                </div>
            </div>
        </div>

        <?php
        if ($list && is_array($list) && count($list) > 0) {
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
                    
                    // Récupérer la photo de profil
                    $storedProfile = trim($row['profile_pic'] ?? '');
                    $profilePic = getProfilePic($storedProfile);
                    
                    // Vérifier si l'utilisateur peut gérer ce post (auteur ou admin)
                    $postOwnerId = isset($row['id_user']) ? (int)$row['id_user'] : 0;
                    $isCreator = ($current_user_id > 0 && $postOwnerId > 0 && $postOwnerId == $current_user_id);
                    $canManage = $isCreator || $isAdmin;
                    
                    // Vérifier si l'utilisateur a liké ce post
                    $isLiked = false;
                    if ($current_user_id > 0) {
                        $isLiked = $likeController->isLiked($idPost, $current_user_id);
                    }
                    $likesCount = isset($row['likes_count']) ? (int)$row['likes_count'] : 0;
                    $commentsCount = isset($row['comments_count']) ? (int)$row['comments_count'] : 0;
                    ?>
                    <div class="card mb-3 post-item" 
                         data-post-id="<?php echo $idPost; ?>" 
                         data-post-title="<?php echo htmlspecialchars(strtolower($titre)); ?>">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($profilePic !== 'assets/images/default_profile.png' && file_exists(realpath(__DIR__ . '/../../' . ltrim($storedProfile, '/')))): ?>
                                    <img src="<?php echo htmlspecialchars($profilePic); ?>" 
                                         alt="<?php echo htmlspecialchars($authorName); ?>" 
                                         class="rounded-circle me-3" 
                                         style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #e0e0e0;">
                                <?php else: ?>
                                <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: 600;">
                                    <?php echo $initials; ?>
                                </div>
                                <?php endif; ?>
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

                            <!-- Engagement Bar -->
                            <div class="post-engagement mb-3">
                                <div class="engagement-stats d-flex gap-3">
                                    <?php if ($current_user_id > 0): ?>
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 like-post-btn <?php echo $isLiked ? 'liked' : ''; ?>" 
                                            data-post-id="<?php echo $idPost; ?>" 
                                            title="J'aime ce post"
                                            style="color: <?php echo $isLiked ? '#e74c3c' : '#6c757d'; ?>; cursor: pointer; position: relative; z-index: 10; border: none; background: transparent;">
                                        <i class="ri-heart-<?php echo $isLiked ? 'fill' : 'line'; ?> me-1"></i>J'aime
                                        <span class="like-count ms-1" data-post-id="<?php echo $idPost; ?>">
                                            <?php echo $likesCount; ?>
                                        </span>
                                    </button>
                                    <?php else: ?>
                                    <span class="btn btn-sm btn-link text-decoration-none p-0" 
                                          title="Connectez-vous pour aimer ce post"
                                          style="color: #6c757d; cursor: not-allowed;">
                                        <i class="ri-heart-line me-1"></i>J'aime
                                        <span class="like-count ms-1">
                                            <?php echo $likesCount; ?>
                                        </span>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($current_user_id > 0): ?>
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 comment-post-btn" 
                                            data-post-id="<?php echo $idPost; ?>" 
                                            onclick="showComments(<?php echo $idPost; ?>); return false;" 
                                            title="Commenter ce post"
                                            style="color: #6c757d; cursor: pointer;">
                                        <i class="ri-chat-3-line me-1"></i> Commenter
                                        <span class="comment-count ms-1" data-post-id="<?php echo $idPost; ?>">
                                            <?php echo $commentsCount; ?>
                                        </span>
                                    </button>
                                    <?php else: ?>
                                    <span class="btn btn-sm btn-link text-decoration-none p-0" 
                                          title="Connectez-vous pour commenter"
                                          style="color: #6c757d; cursor: not-allowed;">
                                        <i class="ri-chat-3-line me-1"></i> Commenter
                                        <span class="comment-count ms-1">
                                            <?php echo $commentsCount; ?>
                                        </span>
                                    </span>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-link text-decoration-none p-0" 
                                            onclick="sharePost(<?php echo $idPost; ?>)" 
                                            title="Partager ce post"
                                            style="color: #6c757d;">
                                        <i class="ri-share-line me-1"></i>Partager
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Comments Section (hidden by default) -->
                            <div class="comments-section" id="comments-<?php echo $idPost; ?>" style="display: none;">
                                <div class="border-top pt-3 mt-3">
                                    <h6 class="mb-3"><i class="ri-chat-3-line me-2"></i>Commentaires</h6>
                                    <div class="comments-list" id="comments-list-<?php echo $idPost; ?>">
                                        <!-- Comments will be loaded here -->
                                    </div>
                                    <div class="add-comment mt-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="comment-input-<?php echo $idPost; ?>" 
                                                   placeholder="Écrivez un commentaire..." 
                                                   onkeypress="if(event.key === 'Enter') addComment(<?php echo $idPost; ?>);">
                                            <button class="btn btn-primary" type="button" onclick="addComment(<?php echo $idPost; ?>)">
                                                <i class="ri-send-plane-line"></i> Envoyer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap align-items-center post-actions">
                                <?php if ($canManage): ?>
                                <a href="addpostupdate.php?id_post=<?php echo $idPost; ?>" class="btn btn-sm btn-success">
                                    <i class="ri-checkbox-circle-line me-1"></i> Résoudre
                                </a>
                                <a href="updatepost.php?id=<?php echo $idPost; ?>" class="btn btn-sm btn-info">
                                    <i class="ri-edit-line me-1"></i> Modifier
                                </a>
                                <a href="deletepost.php?id=<?php echo $idPost; ?>" class="btn btn-sm btn-danger">
                                    <i class="ri-delete-bin-line me-1"></i> Supprimer
                                </a>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-secondary" onclick="printPost(<?php echo $idPost; ?>)" title="Imprimer ce post">
                                    <i class="ri-printer-line me-1"></i> Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
        } elseif ($list && is_array($list) && count($list) === 0) {
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
renderLayout('Posts', $content, ['assets/css/showpost.css', 'assets/css/volunteer-view.css'], ['assets/js/showpost.js']);
?>
