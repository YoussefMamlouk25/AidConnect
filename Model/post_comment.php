<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controller/postcommentcontroller.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
    exit;
}

$commentController = new PostCommentController();
$id_user = (int)$_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $id_post = isset($data['id_post']) ? (int)$data['id_post'] : 0;
            $comment_text = isset($data['comment_text']) ? trim($data['comment_text']) : '';
            
            if ($id_post <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID post invalide']);
                exit;
            }
            
            if (empty($comment_text)) {
                echo json_encode(['success' => false, 'error' => 'Le commentaire ne peut pas être vide']);
                exit;
            }
            
            $comment_id = $commentController->addComment($id_post, $id_user, $comment_text);
            if ($comment_id !== false) {
                // Récupérer le commentaire avec les infos utilisateur
                $comments = $commentController->getCommentsByPost($id_post);
                $new_comment = null;
                foreach ($comments as $comment) {
                    if ($comment['id_comment'] == $comment_id) {
                        $new_comment = $comment;
                        break;
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'comment' => $new_comment,
                    'count' => count($comments)
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'ajout du commentaire']);
            }
            break;
            
        case 'get':
            $id_post = isset($data['id_post']) ? (int)$data['id_post'] : 0;
            
            if ($id_post <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID post invalide']);
                exit;
            }
            
            $comments = $commentController->getCommentsByPost($id_post);
            $count = $commentController->getCommentCount($id_post);
            
            echo json_encode([
                'success' => true,
                'comments' => $comments,
                'count' => $count
            ]);
            break;
            
        case 'delete':
            $id_comment = isset($data['id_comment']) ? (int)$data['id_comment'] : 0;
            
            if ($id_comment <= 0) {
                echo json_encode(['success' => false, 'error' => 'ID commentaire invalide']);
                exit;
            }
            
            $result = $commentController->deleteComment($id_comment, $id_user);
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Action invalide']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}

