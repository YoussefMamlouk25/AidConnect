<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/offer.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$offerC = new OfferController();
$error = '';

if (
    isset($_POST['nom']) &&
    isset($_POST['numero']) &&
    isset($_POST['email']) &&
    isset($_POST['type_don']) &&
    isset($_POST['description'])
) {
    if (
        !empty($_POST['nom']) &&
        !empty($_POST['numero']) &&
        !empty($_POST['email']) &&
        !empty($_POST['type_don']) &&
        !empty($_POST['description'])
    ) {
        $nom = trim($_POST['nom']);
        $numero = trim($_POST['numero']);
        $email = trim($_POST['email']);
        $type_don = trim($_POST['type_don']);
        $description = trim($_POST['description']);

        // Server-side validation
        if (!preg_match('/^[0-9]{8,15}$/', $numero)) {
            $error = "Le numéro doit contenir 8 à 15 chiffres.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } elseif (!in_array($type_don, ['money','food','clothing','medicines','equipment','services'], true)) {
            $error = "Type de don invalide.";
        } else {
            // Upload media (optional)
            $media = null;
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
                    isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : null,
                    $description,
                    $media,
                    $nom,
                    $numero,
                    $email,
                    $type_don,
                    null
                );
                $result = $offerC->addOffer($offer);
                if ($result) {
                    // Rediriger vers showoffer.php après succès
                    if ((isset($_GET['ajax']) && $_GET['ajax'] == '1') || (isset($_POST['ajax']) && $_POST['ajax'] == '1')) {
                        // Si AJAX, recharger la page showoffer
                        echo '<script>if(typeof loadContent==="function"){ loadContent("showoffer.php"); }</script>';
                        exit();
                    } else {
                        header('Location: showoffer.php');
                        exit();
                    }
                } else {
                    $error = "Erreur lors de l'ajout de l'offre. Veuillez réessayer.";
                }
            }
        }
    } else {
        $error = "Tous les champs requis doivent être remplis.";
    }
}

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

// Si ce n'est pas AJAX, afficher la page complète avec sidebar
if (!$isAjax) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidConnect - Ajouter une Offre</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="addpost.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
<?php
}
?>
<div class="offers-fragment">
    <h2>Ajouter une Offre</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="<?php echo $isAjax ? '?ajax=1' : ''; ?>">
        <?php if ($isAjax): ?>
            <input type="hidden" name="ajax" value="1">
        <?php endif; ?>

        <p>
            <label>Nom :
                <input type="text" name="nom" id="nom">
            </label>
        </p>

        <p>
            <label>Numéro :
                <input type="tel" name="numero" id="numero">
            </label>
        </p>

        <p>
            <label>Email :
                <input type="email" name="email" id="email">
            </label>
        </p>

        <p>
            <label>Type Don :
                <select name="type_don" id="type_don">
                    <option value="">-- Choisissez --</option>
                    <option value="money">Argent</option>
                    <option value="food">Nourriture</option>
                    <option value="clothing">Vêtements</option>
                    <option value="medicines">Médicaments</option>
                    <option value="equipment">Matériel</option>
                    <option value="services">Services</option>
                </select>
            </label>
        </p>

        <p>
            <label>Description :
                <textarea name="description" id="description" rows="5"></textarea>
            </label>
        </p>

        <p>
            <label>Media (optionnel) :
                <input type="file" name="media[]" id="media" accept="image/*" multiple>
            </label>
        </p>

        <p>
            <button type="submit">Enregistrer</button>
        </p>
    </form>
</div>
<?php if (!$isAjax): ?>
    </div>
</div>
    <script src="dashboard.js"></script>
</body>
</html>
<?php endif; ?>
