<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/offer.php';
require_once '../../Controller/offercontroller.php';

$offerController = new OfferController();

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $type_don = trim($_POST['type_don'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($nom) || empty($numero) || empty($email) || empty($type_don) || empty($description)) {
        $error = "Tous les champs sont requis.";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $numero)) {
        $error = "Le numéro doit contenir 8 à 15 chiffres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (!in_array($type_don, ['money','food','clothing','medicines','equipment','services'], true)) {
        $error = "Type de don invalide.";
    } else {
        $media = null;

        // Handle media upload if provided
        if (isset($_FILES['media'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
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
            $offer = new Offer(
                null, // id_user (null for admin-created offers)
                $description,
                $media,
                $nom,
                $numero,
                $email,
                $type_don
            );

            $result = $offerController->addOffer($offer);
            if ($result) {
                $success = "Offre créée avec succès.";
                // Clear form on success
                $nom = $numero = $email = $type_don = $description = '';
            } else {
                $error = "Erreur lors de la création de l'offre.";
            }
        }
    }
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
?>
<!-- Fragment: Add Offer (suitable for AJAX injection into dashboard) -->
<link rel="stylesheet" href="offer_actions.css">
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="updateoffer.css">

<?php if ($embed): ?>
<div class="fragment-offer-edit">
<?php else: ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Offre</title>
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
<div class="fragment-offer-edit">
<?php endif; ?>
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showoffer.php'); return false; }">
            Retour aux Offres
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Ajouter une Nouvelle Offre</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo e($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" action="add_offer.php<?= $embed ? '?embed=1' : ''; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo e($nom ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="numero" class="form-label">Numéro *</label>
                        <input type="text" class="form-control" id="numero" name="numero" value="<?php echo e($numero ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo e($email ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type_don" class="form-label">Type de Don *</label>
                        <select class="form-control" id="type_don" name="type_don" required>
                            <option value="">-- Choisissez --</option>
                            <option value="money" <?php echo (isset($type_don) && $type_don === 'money') ? 'selected' : ''; ?>>Argent</option>
                            <option value="food" <?php echo (isset($type_don) && $type_don === 'food') ? 'selected' : ''; ?>>Nourriture</option>
                            <option value="clothing" <?php echo (isset($type_don) && $type_don === 'clothing') ? 'selected' : ''; ?>>Vêtements</option>
                            <option value="medicines" <?php echo (isset($type_don) && $type_don === 'medicines') ? 'selected' : ''; ?>>Médicaments</option>
                            <option value="equipment" <?php echo (isset($type_don) && $type_don === 'equipment') ? 'selected' : ''; ?>>Matériel</option>
                            <option value="services" <?php echo (isset($type_don) && $type_don === 'services') ? 'selected' : ''; ?>>Services</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo e($description ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="media" class="form-label">Media (Optionnel)</label>
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
                    <button type="submit" class="btn btn-primary">Créer l'Offre</button>
                    <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showoffer.php'); return false; }">Annuler</a>
                </div>
            </form>
        </div>
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

<script src="offer.js"></script>
<script src="add_offer.js"></script>

