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
        $media = $offerData['media']; // Keep existing media by default

        // Handle media upload if provided
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $original = basename($_FILES['media']['name']);
            $mediaName = time() . '_' . $original;
            $targetPath = $uploadDir . $mediaName;
            if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
                // Delete old media if exists
                if (!empty($offerData['media'])) {
                    $oldMediaPath = $uploadDir . $offerData['media'];
                    if (file_exists($oldMediaPath)) {
                        @unlink($oldMediaPath);
                    }
                }
                $media = $mediaName;
            } else {
                $error = "Erreur lors du téléchargement du fichier.";
            }
        }

        if (!isset($error)) {
            $offer = new Offer(
                $offerData['id_user'],
                $description,
                $media,
                $nom,
                $numero,
                $email,
                $type_don
            );

            $result = $offerController->updateOffer($offer, $id_offer);
            if ($result) {
                $success = "Offre mise à jour avec succès.";
                // Refresh data
                $offerData = $offerController->getOfferById($id_offer);
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        }
    }
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!-- Fragment: Update Offer (suitable for AJAX injection into dashboard) -->
<link rel="stylesheet" href="offer_actions.css">
<link rel="stylesheet" href="dashboard.css">
<body>
    <main class="main-content">
<div class="fragment-offer-edit">
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showoffer.php'); return false; }">
            Retour aux Offres
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Modifier l'Offre #<?php echo e($id_offer); ?></h4>
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

            <form method="POST" enctype="multipart/form-data" action="updateoffer.php?id_offer=<?php echo urlencode($id_offer); ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo e($offerData['nom']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="numero" class="form-label">Numéro *</label>
                        <input type="text" class="form-control" id="numero" name="numero" value="<?php echo e($offerData['num']); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo e($offerData['email']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type_don" class="form-label">Type de Don *</label>
                        <select class="form-control" id="type_don" name="type_don" required>
                            <option value="">-- Choisissez --</option>
                            <option value="money" <?php echo $offerData['type_don'] === 'money' ? 'selected' : ''; ?>>Argent</option>
                            <option value="food" <?php echo $offerData['type_don'] === 'food' ? 'selected' : ''; ?>>Nourriture</option>
                            <option value="clothing" <?php echo $offerData['type_don'] === 'clothing' ? 'selected' : ''; ?>>Vêtements</option>
                            <option value="medicines" <?php echo $offerData['type_don'] === 'medicines' ? 'selected' : ''; ?>>Médicaments</option>
                            <option value="equipment" <?php echo $offerData['type_don'] === 'equipment' ? 'selected' : ''; ?>>Matériel</option>
                            <option value="services" <?php echo $offerData['type_don'] === 'services' ? 'selected' : ''; ?>>Services</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo e($offerData['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="media" class="form-label">Media (Optionnel)</label>
                    <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
                    <?php if (!empty($offerData['media'])): ?>
                        <small class="text-muted">Fichier actuel: <?php echo e($offerData['media']); ?></small>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="dashboard.php" class="btn btn-secondary" onclick="if(typeof loadContent === 'function'){ loadContent('showoffer.php'); return false; }">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
    </main>
</div>
<script src="offer.js"></script>
