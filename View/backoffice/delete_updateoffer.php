<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/updateoffer.php';
require_once '../../Controller/updateoffercontroller.php';
require_once '../../Controller/offercontroller.php';

$updateCtrl = new UpdateOfferController();
$offerCtrl  = new OfferController();

$id_update = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_update <= 0) {
    header('Location: dashboard.php');
    exit();
}

$updateData = $updateCtrl->getUpdateOfferById($id_update);
if (!$updateData) {
    header('Location: dashboard.php');
    exit();
}

$offerData = $updateData['id_offer'] ? $offerCtrl->getOfferById($updateData['id_offer']) : null;
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    if (!empty($updateData['media'])) {
        $mediaPath = __DIR__ . '/../../public/uploads/' . $updateData['media'];
        if (file_exists($mediaPath)) {
            @unlink($mediaPath);
        }
    }

    if ($updateCtrl->deleteUpdateOffer($id_update)) {
        $success = "Mise à jour supprimée avec succès.";
    } else {
        $error = "Erreur lors de la suppression.";
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;
?>
<link rel="stylesheet" href="assets/css/dashboard.css">
<link rel="stylesheet" href="assets/css/offer_actions.css">

<?php if ($embed || $isAjax): ?>
<div class="fragment-updateoffer-delete">
<?php else: ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer la mise à jour</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/offer_actions.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
<?php endif; ?>

    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h4>Supprimer la mise à jour #<?= e($id_update); ?></h4>
            <?php if ($offerData): ?>
                <small>Offre #<?= e($offerData['id_offer']); ?> - <?= e($offerData['nom']); ?></small>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success); ?></div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Attention!</strong> Cette action supprimera définitivement la mise à jour.
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <h5>Détails</h5>
                <p><strong>Texte:</strong> <?= nl2br(e($updateData['texte'])); ?></p>
                <p><strong>User ID:</strong> <?= e($updateData['id_user'] ?? 'Anonyme'); ?></p>
                <?php if ($offerData): ?>
                    <p><strong>Offre:</strong> #<?= e($offerData['id_offer']); ?> - <?= e($offerData['nom']); ?></p>
                <?php endif; ?>
                <?php if (!empty($updateData['media'])): ?>
                    <div class="mt-3">
                        <strong>Média:</strong>
                        <div class="mt-2">
                            <?php
                                $mediaUrl = '../../public/uploads/' . $updateData['media'];
                                $ext = strtolower(pathinfo($updateData['media'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['mp4','mov','webm'])) {
                                    echo '<video src="'.e($mediaUrl).'" controls class="img-fluid" style="max-width: 300px;"></video>';
                                } else {
                                    echo '<img src="'.e($mediaUrl).'" alt="Update media" class="img-fluid" style="max-width: 300px;">';
                                }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!$success): ?>
                <form method="POST" action="delete_updateoffer.php?id=<?= urlencode($id_update); ?><?= $embed ? '&embed=1' : ''; ?>">
                    <input type="hidden" name="confirm_delete" value="1">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">Confirmer</button>
                        <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent==='function'){ loadContent('showupdateoffer.php?embed=1'); return false; }">Annuler</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

<?php if ($embed || $isAjax): ?>
</div>
<?php else: ?>
    </main>
</div>
</body>
</html>
<?php endif; ?>

<script src="assets/js/updateoffer.js"></script>

