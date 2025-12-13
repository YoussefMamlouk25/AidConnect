<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controller/postlikecontroller.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
    exit;
}

$likeController = new PostLikeController();
$id_user = (int)$_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $id_post = isset($data['id_post']) ? (int)$data['id_post'] : 0;
    
    if ($id_post <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID post invalide']);
        exit;
    }
    
    switch ($action) {
        case 'toggle':
            $result = $likeController->toggleLike($id_post, $id_user);
            if ($result !== false) {
                $isLiked = ($result === 'added');
                $count = $likeController->getLikeCount($id_post);
                echo json_encode([
                    'success' => true,
                    'isLiked' => $isLiked,
                    'count' => $count
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erreur lors du toggle']);
            }
            break;
            
        case 'get':
            $isLiked = $likeController->isLiked($id_post, $id_user);
            $count = $likeController->getLikeCount($id_post);
            echo json_encode([
                'success' => true,
                'isLiked' => $isLiked,
                'count' => $count
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Action invalide']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}

