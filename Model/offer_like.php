<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Controller/offerlikecontroller.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
    exit;
}

$likeController = new OfferLikeController();
$id_user = (int)$_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $id_offer = isset($data['id_offer']) ? (int)$data['id_offer'] : 0;
    
    if ($id_offer <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID offre invalide']);
        exit;
    }
    
    switch ($action) {
        case 'toggle':
            $result = $likeController->toggleLike($id_offer, $id_user);
            if ($result !== false) {
                $isLiked = ($result === 'added');
                $count = $likeController->getLikeCount($id_offer);
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
            $isLiked = $likeController->isLiked($id_offer, $id_user);
            $count = $likeController->getLikeCount($id_offer);
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

