<?php
/**
 * index.php - VERSION SÉCURISÉE
 */



$controller = $_GET['controller'] ?? 'event';
$action = $_GET['action'] ?? 'liste';

// Actions nécessitant authentification
$backoffice_actions = [
    'event' => ['manage', 'create', 'store', 'edit', 'update', 'delete'],
    'inscription' => ['liste', 'confirmer', 'delete']
];

// Vérifier authentification


// Routagea
try {
    switch($controller) {
        case 'event':
            require_once __DIR__ . '/Controller/EventController.php';
            $eventController = new EventController();
            
            switch($action) {
                case 'liste': $eventController->liste(); break;
                case 'detail': $eventController->detail(); break;
                case 'search': $eventController->search(); break;
                case 'manage': $eventController->manage(); break;
                case 'create': $eventController->create(); break;
                case 'store': $eventController->store(); break;
                case 'edit': $eventController->edit(); break;
                case 'update': $eventController->update(); break;
                case 'delete': $eventController->delete(); break;
                default: $eventController->liste(); break;
            }
            break;
            
        case 'inscription':
            require_once __DIR__ . '/Controller/InscriptionController.php';
            $inscriptionController = new InscriptionController();
            
            switch($action) {
                case 'create': $inscriptionController->create(); break;
                case 'store': $inscriptionController->store(); break;
                case 'liste': $inscriptionController->liste(); break;
                case 'edit': $inscriptionController->edit(); break;
                case 'update': $inscriptionController->update(); break;
                case 'delete': $inscriptionController->delete(); break;
                case 'confirmer': $inscriptionController->confirmer(); break;
                default: $inscriptionController->liste(); break;
            }
            break;
            
        default:
            header('Location: index.php?controller=event&action=liste');
            exit;
    }
    
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage();
    error_log("Erreur application : " . $e->getMessage());
}
?>