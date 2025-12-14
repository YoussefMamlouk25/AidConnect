<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php?error=' . urlencode('Veuillez vous connecter pour supprimer un post.'));
    exit;
}

$currentUserId = (int) $_SESSION['id_user'];
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int) $_GET['id'];

    $postController = new PostController();
    $postData = $postController->getPostById($id);

    if (!$postData) {
        $_SESSION['flash_error'] = "Post introuvable.";
        header('Location: showPosts.php');
        exit();
    }

    $postOwnerId = isset($postData['id_user']) ? (int) $postData['id_user'] : null;
    if (!$isAdmin && ($postOwnerId === null || $postOwnerId !== $currentUserId)) {
        $_SESSION['flash_error'] = "Vous ne pouvez supprimer que vos propres posts.";
        header('Location: showPosts.php');
        exit();
    }

    $ownerConstraint = $isAdmin ? null : $currentUserId;

    if (!$postController->deletePost($id, $ownerConstraint)) {
        $_SESSION['flash_error'] = "Impossible de supprimer ce post.";
    }

    header('Location: showPosts.php');
    exit();
}

header('Location: showPosts.php');
exit();
?>
