<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Controller/postlikecontroller.php';
require_once '../../Controller/postcontroller.php';

$likeController = new PostLikeController();
$postController = new PostController();

$id_post = isset($_GET['id_post']) ? (int)$_GET['id_post'] : 0;

if ($id_post <= 0) {
    echo '<div class="alert alert-danger">ID post invalide</div>';
    exit;
}

// Récupérer les informations du post
$post = $postController->getPostById($id_post);
if (!$post) {
    echo '<div class="alert alert-danger">Post introuvable</div>';
    exit;
}

// Récupérer les utilisateurs qui ont liké
$usersWhoLiked = $likeController->getUsersWhoLiked($id_post);
$likesCount = count($usersWhoLiked);

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

if (!$embed && !$isAjax) {
    require_once 'layout.php';
    ob_start();
}
?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title mb-0">
                <i class="ri-heart-fill text-danger me-2"></i>
                Utilisateurs qui ont aimé ce post
            </h4>
            <small class="text-muted">Post: <?php echo htmlspecialchars($post['titre'] ?? 'N/A'); ?></small>
        </div>
        <?php if (!$embed && !$isAjax): ?>
        <button type="button" class="btn-close" onclick="if(typeof loadContent==='function'){ loadContent('showpostback.php'); return false; } else { window.history.back(); }"></button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                <i class="ri-heart-fill me-1"></i>
                <?php echo $likesCount; ?> like<?php echo $likesCount > 1 ? 's' : ''; ?>
            </span>
        </div>
        
        <?php if ($likesCount > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Utilisateur</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Date du like</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usersWhoLiked as $like): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($like['id_user'] ?? 'N/A'); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($like['name'] ?? 'Utilisateur supprimé'); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($like['email'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                    if (!empty($like['date_like'])) {
                                        $date = new DateTime($like['date_like']);
                                        echo $date->format('d/m/Y à H:i');
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="ri-heart-line" style="font-size: 4rem; color: #ccc;"></i>
                <h5 class="mt-3">Aucun like pour ce post</h5>
                <p class="text-muted">Ce post n'a pas encore reçu de likes.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-secondary" onclick="if(typeof loadContent==='function'){ loadContent('showpostback.php'); return false; } else { window.history.back(); }">
            <i class="ri-arrow-left-line me-1"></i> Retour
        </button>
    </div>
</div>

<?php
if (!$embed && !$isAjax) {
    $content = ob_get_clean();
    renderBackofficeLayout('Likes du Post', $content, ['showofferback.css']);
}
?>

