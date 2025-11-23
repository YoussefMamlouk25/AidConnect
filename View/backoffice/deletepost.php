<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression de Post</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">

<?php
// Vérifier si l'ID est présent dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id']; // Convertir en entier pour la sécurité
    
    $postController = new PostController();
    
    // Supprimer le post
    $postController->deletePost($id);
    
    // Rediriger vers la liste des posts
    header('Location: showpostback.php');
    exit();
} else {
    // Si pas d'ID, rediriger vers la liste
    header('Location: showpostback.php');
    exit();
}
?>
    </div>
</div>
    <script src="dashboard.js"></script>
</body>
</html>
