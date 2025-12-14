<?php
/**
 * event.php - Router pour le système d'événements
 * Intègre le dossier event dans aidconnect!!
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../config.php';
require_once '../../Controller/eventcontroller.php';
require_once '../../Controller/inscriptioncontroller.php';

// Déterminer le contrôleur et l'action
$controller = $_GET['controller'] ?? 'event';
$action = $_GET['action'] ?? 'liste';

// Actions nécessitant authentification admin
$backoffice_actions = ['manage', 'create', 'store', 'edit', 'update', 'delete', 'liste_inscriptions', 'confirmer'];

if (in_array($action, $backoffice_actions)) {
    if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: login.php?error=' . urlencode('Accès refusé.'));
        exit;
    }
}

// Routage
try {
    switch($controller) {
        case 'event':
            $eventController = new EventController();
            
            switch($action) {
                case 'liste': 
                    $eventController->liste(); 
                    break;
                case 'detail': 
                    $eventController->detail(); 
                    break;
                case 'search': 
                    $eventController->search(); 
                    break;
                case 'manage': 
                    $eventController->manage(); 
                    break;
                case 'create': 
                    $eventController->create(); 
                    break;
                case 'store': 
                    $eventController->store(); 
                    break;
                case 'edit': 
                    $eventController->edit(); 
                    break;
                case 'update': 
                    $eventController->update(); 
                    break;
                case 'delete': 
                    $eventController->delete(); 
                    break;
                default: 
                    $eventController->liste(); 
                    break;
            }
            break;
            
        case 'inscription':
            $inscriptionController = new InscriptionController();
            
            switch($action) {
                case 'create': 
                    $inscriptionController->create(); 
                    break;
                case 'store': 
                    $inscriptionController->store(); 
                    break;
                case 'liste': 
                    $inscriptionController->liste(); 
                    break;
                case 'mes_inscriptions': 
                    $inscriptionController->mesInscriptions(); 
                    break;
                case 'edit': 
                    $inscriptionController->edit(); 
                    break;
                case 'update': 
                    $inscriptionController->update(); 
                    break;
                case 'delete': 
                    $inscriptionController->delete(); 
                    break;
                case 'confirmer': 
                    $inscriptionController->confirmer(); 
                    break;
                default: 
                    $inscriptionController->liste(); 
                    break;
            }
            break;
            
        default:
            header('Location: event.php?controller=event&action=liste');
            exit;
    }
    
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage();
    error_log("Erreur application : " . $e->getMessage());
}
?>

