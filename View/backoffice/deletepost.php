<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';

$postController = new PostController();

// Récupérer l'ID du post
$id_post = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_post <= 0) {
    header('Location: showpostback.php');
    exit();
}

// Récupérer les données du post
$postData = $postController->getPostById($id_post);

if (!$postData) {
    header('Location: showpostback.php');
    exit();
}

$error = null;
$embed = isset($_GET['embed']) && $_GET['embed'] === '1';

// Gestion de la confirmation de suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Supprimer les fichiers média s'ils existent
    if (!empty($postData['media'])) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $mediaFiles = json_decode($postData['media'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
            // Format JSON (multiple fichiers)
            foreach ($mediaFiles as $mediaFile) {
                $mediaPath = $uploadDir . $mediaFile;
                if (file_exists($mediaPath)) {
                    @unlink($mediaPath);
                }
            }
        } else {
            // Ancien format: chaîne simple
            $mediaPath = $uploadDir . $postData['media'];
            if (file_exists($mediaPath)) {
                @unlink($mediaPath);
            }
        }
    }

    $postController->deletePost($id_post);

    // Détecter si c'est une requête AJAX
    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
              || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
              || (isset($_POST['ajax']) && $_POST['ajax'] == '1');

    // Redirection vers la liste des posts
    if ($isAjax || $embed) {
        // Si AJAX, utiliser JavaScript pour recharger la page showpostback
        echo '<script>if(typeof loadContent==="function"){ loadContent("showpostback.php"); } else { window.location.href="showpostback.php"; }</script>';
        exit();
    } else {
        header('Location: showpostback.php');
        exit();
    }
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
?>
<link rel="stylesheet" href="dashboard.css">

<?php if ($embed || $isAjax): ?>
<div class="fragment-delete-post">
<?php else: ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer le Post</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="delete-post-page">
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
<?php endif; ?>
    <div class="fragment-delete-post">
        <div class="mb-4">
            <a href="showpostback.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showpostback.php'); return false; }">
                <i class="fas fa-arrow-left me-2"></i>Retour aux Posts
            </a>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4><i class="fas fa-exclamation-triangle me-2"></i>Supprimer le Post</h4>
            </div>
            <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Attention!</strong> Vous êtes sur le point de supprimer définitivement ce post. Cette action ne peut pas être annulée.
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Détails du Post</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <?php echo e($postData['id_post']); ?></p>
                            <p><strong>Titre:</strong> <?php echo e($postData['titre']); ?></p>
                            <p><strong>Nom:</strong> <?php echo e($postData['nom']); ?></p>
                            <p><strong>Email:</strong> <?php echo e($postData['email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Numéro:</strong> <?php echo e(isset($postData['Numéro']) ? $postData['Numéro'] : (isset($postData['numero']) ? $postData['numero'] : '')); ?></p>
                            <p><strong>Région:</strong> <?php echo e($postData['region']); ?></p>
                            <p><strong>Description:</strong> <?php echo nl2br(e($postData['description'])); ?></p>
                            <p><strong>Date de création:</strong> <?php echo e($postData['date_creation']); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($postData['media'])): ?>
                        <div class="mt-3">
                            <strong>Média:</strong>
                            <div class="mt-2">
                                <?php
                                    $mediaUrl = '../../public/uploads/' . $postData['media'];
                                    $ext = strtolower(pathinfo($postData['media'], PATHINFO_EXTENSION));
                                    if (in_array($ext, ['mp4','mov','webm'])) {
                                        echo '<video src="'.e($mediaUrl).'" controls class="img-fluid" style="max-width: 300px;"></video>';
                                    } else {
                                        echo '<img src="'.e($mediaUrl).'" alt="Post media" class="img-fluid" style="max-width: 300px;">';
                                    }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <form method="POST" action="deletepost.php?id=<?= urlencode($id_post) ?><?= $embed ? '&embed=1' : '' ?><?= $isAjax ? '&ajax=1' : '' ?>">
                <input type="hidden" name="confirm_delete" value="1">
                <?php if ($isAjax): ?>
                    <input type="hidden" name="ajax" value="1">
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Confirmer la Suppression
                    </button>
                    <a href="showpostback.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showpostback.php'); return false; }">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if (!$embed && !$isAjax): ?>
        </main>
    </div>
</body>
</html>
<?php endif; ?>
