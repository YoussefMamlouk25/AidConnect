<?php
/**
 * Router pour les actions d'événements depuis le backoffice
 * Corrige les chemins relatifs
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Vérifier si admin
if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../frontoffice/login.php?error=' . urlencode('Accès refusé.'));
    exit;
}

require_once '../../Controller/eventcontroller.php';

$action = $_GET['action'] ?? 'manage';
$controller = new EventController();

// Router vers la méthode appropriée
switch($action) {
    case 'manage':
        $controller->manage();
        break;
    case 'create':
        $controller->create();
        break;
    case 'store':
        $controller->store();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        $controller->manage();
        break;
}

