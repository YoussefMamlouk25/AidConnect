<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/offer.php';
require_once '../../Controller/offercontroller.php';

$offerController = new OfferController();

$id_offer = isset($_GET['id_offer']) ? (int)$_GET['id_offer'] : 0;

if ($id_offer <= 0) {
    header('Location: dashboard.php');
    exit();
}

$offerData = $offerController->getOfferById($id_offer);

if (!$offerData) {
    header('Location: dashboard.php');
    exit();
}

$error = null;
$embed = isset($_GET['embed']) && $_GET['embed'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Delete the media file if it exists
    if (!empty($offerData['media'])) {
        $mediaPath = __DIR__ . '/../../public/uploads/' . $offerData['media'];
        if (file_exists($mediaPath)) {
            @unlink($mediaPath);
        }
    }

    $result = $offerController->deleteOffer($id_offer);

    if ($result) {
        header('Location: dashboard.php?deleted=1');
        exit();
    } else {
        $error = "Erreur lors de la suppression. Veuillez réessayer.";
    }
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;
?>
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="offer_actions.css">

<?php if ($embed || $isAjax): ?>
<div class="fragment-delete-offer">
<?php else: ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer l'offre</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="offer_actions.css">
</head>
<body class="delete-offer-page">
    <div class="layout">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
<?php endif; ?>
    <div class="fragment-delete-offer">
        <div class="mb-4">
            <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showoffer.php'); return false; }">
                <i class="fas fa-arrow-left me-2"></i>Retour aux Offres
            </a>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4><i class="fas fa-exclamation-triangle me-2"></i>Supprimer l'Offre</h4>
            </div>
            <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Attention!</strong> Vous êtes sur le point de supprimer définitivement cette offre. Cette action ne peut pas être annulée.
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Détails de l'Offre</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <?php echo e($offerData['id_offer']); ?></p>
                            <p><strong>Nom:</strong> <?php echo e($offerData['nom']); ?></p>
                            <p><strong>Email:</strong> <?php echo e($offerData['email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Numéro:</strong> <?php echo e($offerData['num']); ?></p>
                            <p><strong>Type de Don:</strong> <?php echo e($offerController->getTypeDonLabel($offerData['type_don'])); ?></p>
                            <p><strong>Description:</strong> <?php echo nl2br(e($offerData['description'])); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($offerData['media'])): ?>
                        <div class="mt-3">
                            <strong>Média:</strong>
                            <div class="mt-2">
                                <?php
                                    $mediaUrl = '../../public/uploads/' . $offerData['media'];
                                    $ext = strtolower(pathinfo($offerData['media'], PATHINFO_EXTENSION));
                                    if (in_array($ext, ['mp4','mov','webm'])) {
                                        echo '<video src="'.e($mediaUrl).'" controls class="img-fluid" style="max-width: 300px;"></video>';
                                    } else {
                                        echo '<img src="'.e($mediaUrl).'" alt="Offer media" class="img-fluid" style="max-width: 300px;">';
                                    }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <form method="POST" action="deleteoffer.php?id_offer=<?= urlencode($id_offer) ?><?= $embed ? '&embed=1' : '' ?>">
                <input type="hidden" name="confirm_delete" value="1">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Confirmer la Suppression
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showoffer.php'); return false; }">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="offer.js"></script>
<?php if ($showFullPage): ?>
        </main>
    </div>
</body>
</html>
<?php endif; ?>
