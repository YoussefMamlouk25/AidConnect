<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';

// Vérifier si l'ID est présent dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id']; // Convertir en entier pour la sécurité
    
    $postController = new PostController();
    
    // Supprimer le post
    $postController->deletePost($id);
    
    // Rediriger vers la liste des posts
    header('Location: dashboard.php');
    exit();
} else {
    // Si pas d'ID, rediriger vers la liste
    header('Location: showpostback.php');
    exit();
}
?>

