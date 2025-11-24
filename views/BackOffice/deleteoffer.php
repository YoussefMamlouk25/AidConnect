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
    // Delete the media files if they exist
    if (!empty($offerData['media'])) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $mediaFiles = [];
        $tmp = json_decode($offerData['media'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
            $mediaFiles = $tmp;
        } else {
            $mediaFiles = [$offerData['media']];
        }
        
        foreach ($mediaFiles as $mediaFile) {
            $mediaPath = $uploadDir . $mediaFile;
            if (file_exists($mediaPath)) {
                @unlink($mediaPath);
            }
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
?>
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="offer_actions.css">
<link rel="stylesheet" href="updateoffer.css">

<?php if ($embed): ?>
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
    <link rel="stylesheet" href="updateoffer.css">
</head>
<body class="delete-offer-page">
    <div class="layout">
        <aside class="sidebar">
            <h3>AidConnect</h3>
            <nav>
                <button class="sidebar-link" onclick="window.location.href='dashboard.php'">🏠 Dashboard</button>
                <button class="sidebar-link" onclick="window.location.href='showoffer.php'">🏷️ Offres</button>
                <button class="sidebar-link" onclick="window.location.href='showupdateoffer.php'">✏️ Updates Offres</button>
            </nav>
        </aside>
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
                    <?php 
                        $mediaFiles = [];
                        if (!empty($offerData['media'])) {
                            $tmp = json_decode($offerData['media'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                                $mediaFiles = $tmp;
                            } else {
                                $mediaFiles = [$offerData['media']];
                            }
                        }
                    ?>
                    <?php if (!empty($mediaFiles)): ?>
                        <div class="mt-3">
                            <strong>Média (<?= count($mediaFiles); ?> fichier<?= count($mediaFiles) > 1 ? 's' : ''; ?>):</strong>
                            <div class="delete-media-grid mt-3">
                                <?php foreach ($mediaFiles as $mediaFile): 
                                    $mediaUrl = '../../public/uploads/' . $mediaFile;
                                    $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                ?>
                                    <div class="delete-media-item">
                                        <?php if (in_array($ext, ['mp4','mov','webm'])): ?>
                                            <video src="<?= e($mediaUrl); ?>" controls class="delete-media-preview"></video>
                                        <?php else: ?>
                                            <img src="<?= e($mediaUrl); ?>" alt="Offer media" class="delete-media-preview">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
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
<?php if (!$embed): ?>
        </main>
    </div>
</body>
</html>
<?php endif; ?>
