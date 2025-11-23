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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texte = trim($_POST['texte'] ?? '');

    if (empty($texte)) {
        $error = "Le texte est requis.";
    } else {
                $media = $updateData['media'];
                if (isset($_FILES['media'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    if (!empty($updateData['media'])) {
                        $oldMediaFiles = json_decode($updateData['media'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($oldMediaFiles)) {
                            foreach ($oldMediaFiles as $oldMediaFile) {
                                $oldMediaPath = $uploadDir . $oldMediaFile;
                                if (file_exists($oldMediaPath)) {
                                    @unlink($oldMediaPath);
                                }
                            }
                        } else {
                            $oldMediaPath = $uploadDir . $updateData['media'];
                            if (file_exists($oldMediaPath)) {
                                @unlink($oldMediaPath);
                            }
                        }
                    }

                    $uploadedMedia = [];
                    $errors = [];
                    foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['media']['error'][$key] === UPLOAD_ERR_OK) {
                            $original = basename($_FILES['media']['name'][$key]);
                            $mediaName = time() . '_' . $key . '_' . $original;
                            $targetPath = $uploadDir . $mediaName;
                            if (move_uploaded_file($tmpName, $targetPath)) {
                                $uploadedMedia[] = $mediaName;
                            } else {
                                $errors[] = "Erreur lors du téléchargement du fichier: " . $original;
                            }
                        }
                    }
                    if (count($errors) > 0) {
                        $error = implode("\n", $errors);
                    }
                    if (count($uploadedMedia) > 0) {
                        $media = json_encode($uploadedMedia);
                    }
                }

        if (!isset($error)) {
            $update = new UpdateOffer(
                $updateData['id_offer'],
                $updateData['id_user'],
                $texte,
                $media,
                $id_update
            );

            if ($updateCtrl->updateUpdateOffer($update, $id_update)) {
                $success = "Mise à jour modifiée avec succès.";
                $updateData = $updateCtrl->getUpdateOfferById($id_update);
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        }
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
?>
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="offer_actions.css">

<?php if ($embed): ?>
<div class="fragment-updateoffer-edit">
<?php else: ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la mise à jour</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="offer_actions.css">
</head>
<body>
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

    <div class="card">
        <div class="card-header">
            <h4>Modifier la mise à jour #<?= e($id_update); ?></h4>
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
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" action="edit_updateoffer.php?id=<?= urlencode($id_update); ?><?= $embed ? '&embed=1' : ''; ?>">
                <div class="mb-3">
                    <label for="texte" class="form-label">Texte *</label>
                    <textarea name="texte" id="texte" rows="4" class="form-control" required><?= e($_POST['texte'] ?? $updateData['texte']); ?></textarea>
                </div>

                <div class="mb-3">

                    <label for="media" class="form-label">Media (Optionnel)</label>
                    <input type="file" class="form-control" id="media" name="media[]" accept="image/*,video/*" multiple>
                    <?php if (!empty($updateData['media'])): ?>
                        <div class="current-media-previews mt-2">
                            <?php
                                $mediaFiles = json_decode($updateData['media'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                                    foreach ($mediaFiles as $mediaFile) {
                                        $mediaUrl = '../../public/uploads/' . $mediaFile;
                                        $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                        if (in_array($ext, ['mp4','mov','webm'])) {
                                            echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls class="media-preview-video"></video>';
                                        } else {
                                            echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Current media" class="media-preview-img">';
                                        }
                                    }
                                } else {
                                    $mediaUrl = '../../public/uploads/' . $updateData['media'];
                                    $ext = strtolower(pathinfo($updateData['media'], PATHINFO_EXTENSION));
                                    if (in_array($ext, ['mp4','mov','webm'])) {
                                        echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls class="media-preview-video"></video>';
                                    } else {
                                        echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Current media" class="media-preview-img">';
                                    }
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>


                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent==='function'){ loadContent('showupdateoffer.php?embed=1'); return false; }">Annuler</a>
                </div>
            </form>
        </div>
    </div>

<?php if ($embed): ?>
</div>
<?php else: ?>
    </main>
</div>
</body>
</html>
<?php endif; ?>

<style>
.media-preview-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 8px;
    margin-bottom: 8px;
}
.media-preview-video {
    width: 140px;
    height: 120px;
    border-radius: 8px;
    margin-right: 8px;
    margin-bottom: 8px;
}
</style>

<script src="updateoffer.js"></script>

