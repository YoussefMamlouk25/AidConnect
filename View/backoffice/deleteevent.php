<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../config.php';
require_once '../../Model/Event.php';

$id_event = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_event <= 0) {
    header('Location: showevent.php');
    exit();
}

$db = Config::getConnexion();
$eventModel = new Event($db);
$eventModel->setIdEvent($id_event);

if (!$eventModel->readOne()) {
    header('Location: showevent.php');
    exit();
}

$error = null;
$embed = isset($_GET['embed']) && $_GET['embed'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Récupérer l'image pour la supprimer
    $image = $eventModel->getImage();
    
    // Supprimer l'image si elle existe
    if ($image && file_exists(__DIR__ . '/../../' . $image)) {
        @unlink(__DIR__ . '/../../' . $image);
    }
    
    // Supprimer l'événement
    $result = $eventModel->delete();
    
    if ($result) {
        // Détecter si c'est une requête AJAX
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                  || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
                  || $embed;
        
        if ($isAjax) {
            // Si AJAX, utiliser JavaScript pour recharger la page showevent
            echo '<script>if(typeof loadContent==="function"){ loadContent("showevent.php"); } else { window.location.href="showevent.php"; }</script>';
            exit();
        } else {
            header('Location: showevent.php');
            exit();
        }
    } else {
        $error = "Erreur lors de la suppression. Veuillez réessayer.";
    }
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;

// Récupérer les données de l'événement pour l'affichage
$eventTitle = $eventModel->getTitre();
$eventDate = $eventModel->getDateEvent();
$eventLieu = $eventModel->getLieu();
$eventType = $eventModel->getTypeEvent();
$eventPlaces = $eventModel->getNombrePlaces();
$eventDescription = $eventModel->getDescription();
$eventImage = $eventModel->getImage();
$inscriptionsCount = $eventModel->countInscriptions();
?>
<link rel="stylesheet" href="assets/css/dashboard.css">
<link rel="stylesheet" href="assets/css/offer_actions.css">

<?php if ($embed || $isAjax): ?>
<div class="fragment-delete-event">
<?php else: ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer l'événement</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/offer_actions.css">
</head>
<body class="delete-event-page">
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
<?php endif; ?>
    <div class="fragment-delete-event">
        <div class="mb-4">
            <a href="showevent.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showevent.php'); return false; }">
                <i class="fas fa-arrow-left me-2"></i>Retour aux Événements
            </a>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4><i class="fas fa-exclamation-triangle me-2"></i>Supprimer l'Événement</h4>
            </div>
            <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Attention!</strong> Vous êtes sur le point de supprimer définitivement cet événement. Cette action ne peut pas être annulée.
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Détails de l'Événement</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <?php echo e($id_event); ?></p>
                            <p><strong>Titre:</strong> <?php echo e($eventTitle); ?></p>
                            <p><strong>Date:</strong> <?php echo e(date('d/m/Y H:i', strtotime($eventDate))); ?></p>
                            <p><strong>Lieu:</strong> <?php echo e($eventLieu); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Type:</strong> <?php echo e($eventType); ?></p>
                            <p><strong>Places:</strong> <?php echo e($eventPlaces); ?></p>
                            <p><strong>Inscrits:</strong> <?php echo e($inscriptionsCount); ?></p>
                            <p><strong>Description:</strong> <?php echo nl2br(e($eventDescription)); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($eventImage)): 
                        $imageUrl = '../../' . $eventImage;
                        $imagePath = __DIR__ . '/../../' . $eventImage;
                        if (file_exists($imagePath)):
                            $ext = strtolower(pathinfo($eventImage, PATHINFO_EXTENSION));
                    ?>
                        <div class="mt-3">
                            <strong>Image:</strong>
                            <div class="mt-2">
                                <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="<?php echo e($imageUrl); ?>" alt="Event image" class="img-fluid" style="max-width: 300px;">
                                <?php else: ?>
                                    <p>Fichier image</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endif; ?>
                </div>
            </div>

            <form method="POST" action="deleteevent.php?id=<?= urlencode($id_event) ?><?= $embed ? '&embed=1' : '' ?>">
                <input type="hidden" name="confirm_delete" value="1">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Confirmer la Suppression
                    </button>
                    <a href="showevent.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showevent.php'); return false; }">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/js/offer.js"></script>
<?php if ($showFullPage): ?>
        </main>
    </div>
</body>
</html>
<?php endif; ?>
