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
                // Handle existing media retention/removal + new uploads
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Parse existing media from DB into array
                $existingMedia = [];
                if (!empty($updateData['media'])) {
                    $tmp = json_decode($updateData['media'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                        $existingMedia = $tmp;
                    } else {
                        $existingMedia = [$updateData['media']];
                    }
                }

                // Determine kept existing media sent from client (JSON array)
                $keptExisting = $existingMedia;
                if (isset($_POST['existing_media']) && $_POST['existing_media'] !== '') {
                    $posted = json_decode($_POST['existing_media'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($posted)) {
                        $keptExisting = $posted;
                    }
                }

                // Remove files that were not kept
                foreach ($existingMedia as $oldFile) {
                    if (!in_array($oldFile, $keptExisting, true)) {
                        $oldPath = $uploadDir . $oldFile;
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                }

                // Process newly uploaded files and append to keptExisting
                $uploadedMedia = [];
                $errors = [];
                if (isset($_FILES['media'])) {
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
                }

                $finalMediaList = array_values(array_merge($keptExisting, $uploadedMedia));
                $media = count($finalMediaList) > 0 ? json_encode($finalMediaList) : null;

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
<link rel="stylesheet" href="updateoffer.css">

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
    <link rel="stylesheet" href="updateoffer.css">
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
                    <?php
                        $existingFiles = [];
                        if (!empty($updateData['media'])) {
                            $tmp = json_decode($updateData['media'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                                $existingFiles = $tmp;
                            } else {
                                $existingFiles = [$updateData['media']];
                            }
                        }
                    ?>
                    <input type="hidden" id="existing_media" name="existing_media" value='<?= htmlspecialchars(json_encode($existingFiles), ENT_QUOTES, "UTF-8") ?>'>
                    
                    <?php if (!empty($existingFiles)): ?>
                        <div class="current-media-previews mt-2 mb-3">
                            <div class="existing-media-label">Médias existants</div>
                            <div id="existingMediaGrid" class="media-preview-grid">
                            <?php foreach ($existingFiles as $mediaFile) {
                                $mediaUrl = '../../public/uploads/' . $mediaFile;
                                $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                if (in_array($ext, ['mp4','mov','webm'])) {
                                    echo '<div class="media-preview-item" data-filename="'.htmlspecialchars($mediaFile, ENT_QUOTES).'"><video src="' . htmlspecialchars($mediaUrl) . '" controls class="media-preview-video"></video><button type="button" class="media-remove-btn" aria-label="Remove existing media">&times;</button></div>';
                                } else {
                                    echo '<div class="media-preview-item" data-filename="'.htmlspecialchars($mediaFile, ENT_QUOTES).'"><img src="' . htmlspecialchars($mediaUrl) . '" alt="Current media" /><button type="button" class="media-remove-btn" aria-label="Remove existing media">&times;</button></div>';
                                }
                            } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="media-dropzone" id="mediaDropzone">
                        <input type="file" class="media-file-input" id="media" name="media[]" accept="image/*,video/*" multiple>
                        <div class="dropzone-content">
                            <div class="dropzone-icon">📁</div>
                            <div class="dropzone-text">Glissez-déposez vos fichiers ici</div>
                            <div class="dropzone-link">ou cliquez pour parcourir</div>
                            <div class="dropzone-hint">Formats supportés: JPG, PNG, MP4, MOV, WEBM</div>
                        </div>
                    </div>
                    <div id="mediaPreview" class="media-preview-grid mt-3"></div>
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

<script src="edit_updateoffer.js"></script>
<script src="updateoffer.js"></script>

