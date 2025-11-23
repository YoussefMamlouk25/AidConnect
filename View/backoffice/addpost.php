<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';

$postC = new PostController();

if (
    isset($_POST['titre']) &&
    isset($_POST['nom']) &&
    isset($_POST['numero']) &&
    isset($_POST['email']) &&
    isset($_POST['region']) &&
    isset($_POST['description'])
) {
    if (
        !empty($_POST['titre']) &&
        !empty($_POST['nom']) &&
        !empty($_POST['numero']) &&
        !empty($_POST['email']) &&
        !empty($_POST['region']) &&
        !empty($_POST['description'])
    ) {
        // Gestion de l'image
        $media = ''; // Chaîne vide au lieu de null si pas de photo
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/';
            // Créer le dossier s'il n'existe pas
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $mediaName = basename($_FILES['media']['name']);
            $media = $mediaName;
            move_uploaded_file($_FILES['media']['tmp_name'], $uploadDir . $mediaName);
        }

        // Gestion de la date de création
        $dateCreation = new DateTime(); // Par défaut, date actuelle
        if (isset($_POST['dateCreation']) && !empty($_POST['dateCreation'])) {
            try {
                $dateCreation = new DateTime($_POST['dateCreation']);
            } catch (Exception $e) {
                // Si la date est invalide, utiliser la date actuelle
                $dateCreation = new DateTime();
            }
        }

        // Création de l'objet Post
        $post = new Post(
            null, // id
            $_POST['titre'],
            $_POST['nom'],
            $_POST['numero'],
            $_POST['email'],
            $_POST['region'],
            $_POST['description'],
            $media,
            $dateCreation
        );

        // Récupérer id_user depuis la session si disponible, sinon null
        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
        $result = $postC->addPost($post, $id_user);
                if ($result) {
            // Si AJAX, recharger la page showpostback
            if ((isset($_GET['ajax']) && $_GET['ajax'] == '1') || (isset($_POST['ajax']) && $_POST['ajax'] == '1')) {
                echo '<script>if(typeof loadContent==="function"){ loadContent("showpostback.php"); }</script>';
                exit();
            } else {
                header('Location: showpostback.php');
                exit();
            }
        } else {
            $error = "Erreur lors de l'ajout du post. Veuillez vérifier les logs d'erreur ou réessayer.";
        }
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
    <title>AidConnect - Ajouter un Post</title>
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
    <h2>Ajouter un Post</h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="<?php echo $isAjax ? '?ajax=1' : ''; ?>">
        <?php if ($isAjax): ?>
            <input type="hidden" name="ajax" value="1">
        <?php endif; ?>
        
        <p>
            <label>Titre :
                <input type="text" name="titre" id="titre">
            </label>
        </p>

        <p>
            <label>Nom :
                <input type="text" name="nom" id="nom">
            </label>
        </p>

        <p>
            <label>Numéro de téléphone :
                <input type="tel" name="numero" id="numero">
            </label>
        </p>

        <p>
            <label>Email :
                <input type="email" name="email" id="email">
            </label>
        </p>

        <p>
            <label>Région :
                <select name="region" id="region">
                    <option value="">-- Sélectionner --</option>
                    <option value="Ariana">Ariana</option>
                    <option value="Béja">Béja</option>
                    <option value="Ben Arous">Ben Arous</option>
                    <option value="Bizerte">Bizerte</option>
                    <option value="Gabes">Gabes</option>
                    <option value="Gafsa">Gafsa</option>
                    <option value="Jendouba">Jendouba</option>
                    <option value="Kairouan">Kairouan</option>
                    <option value="Kasserine">Kasserine</option>
                    <option value="Kébili">Kébili</option>
                    <option value="Le Kef">Le Kef</option>
                    <option value="Mahdia">Mahdia</option>
                    <option value="Manouba">Manouba</option>
                    <option value="Médenine">Médenine</option>
                    <option value="Monastir">Monastir</option>
                    <option value="Nabeul">Nabeul</option>
                    <option value="Sfax">Sfax</option>
                    <option value="Sidi Bouzid">Sidi Bouzid</option>
                    <option value="Siliana">Siliana</option>
                    <option value="Sousse">Sousse</option>
                    <option value="Tataouine">Tataouine</option>
                    <option value="Tozeur">Tozeur</option>
                    <option value="Tunis">Tunis</option>
                    <option value="Zaghouan">Zaghouan</option>
                </select>
            </label>
        </p>

        <p>
            <label>Description :
                <textarea name="description" id="description" rows="5"></textarea>
            </label>
        </p>

        <p>
            <label>Photo :
                <div class="photo-upload">
                    <input type="file" name="media" id="media" accept="image/*" class="file-input-hidden">
                    <label for="media" class="photo-icon">
                        📷 Ajouter une photo
                    </label>
                </div>
            </label>
        </p>

        <p>
            <label>Date de création :
                <input type="date" name="dateCreation" id="dateCreation">
            </label>
        </p>

        <p>
            <button type="submit">Publier</button>
        </p>
    </form>
</div>
<?php if (!$isAjax): ?>
    </div>
</div>
    <script src="dashboard.js"></script>
<?php endif; ?>
    <script src="addpost.js"></script>
<?php if (!$isAjax): ?>
</body>
</html>
<?php endif; ?>
