<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Controller/postcommentcontroller.php';
require_once '../../Controller/postcontroller.php';

$commentController = new PostCommentController();
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

// Récupérer les commentaires
$comments = $commentController->getCommentsByPost($id_post);
$commentsCount = count($comments);

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
                <i class="ri-chat-3-fill text-primary me-2"></i>
                Commentaires de ce post
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
                <i class="ri-chat-3-fill me-1"></i>
                <?php echo $commentsCount; ?> commentaire<?php echo $commentsCount > 1 ? 's' : ''; ?>
            </span>
        </div>
        
        <?php if ($commentsCount > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Commentaire</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($comment['id_comment'] ?? 'N/A'); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($comment['name'] ?? 'Utilisateur supprimé'); ?></strong>
                                    <br>
                                    <small class="text-muted">ID: <?php echo htmlspecialchars($comment['id_user'] ?? 'N/A'); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($comment['email'] ?? 'N/A'); ?></td>
                                <td>
                                    <div style="max-width: 400px; word-wrap: break-word;">
                                        <?php echo nl2br(htmlspecialchars($comment['comment_text'] ?? '')); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($comment['date_comment'])) {
                                        $date = new DateTime($comment['date_comment']);
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
                <i class="ri-chat-3-line" style="font-size: 4rem; color: #ccc;"></i>
                <h5 class="mt-3">Aucun commentaire pour ce post</h5>
                <p class="text-muted">Ce post n'a pas encore reçu de commentaires.</p>
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
    renderBackofficeLayout('Commentaires du Post', $content, ['showofferback.css']);
}
?>

