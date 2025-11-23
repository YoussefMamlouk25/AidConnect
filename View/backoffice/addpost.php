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
            new DateTime() // date de création actuelle
        );

        // Récupérer id_user depuis la session si disponible, sinon null
        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
        $result = $postC->addPost($post, $id_user);
        if ($result) {
            header('Location: showpostback.php');
            exit();
        } else {
            $error = "Erreur lors de l'ajout du post. Veuillez vérifier les logs d'erreur ou réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidConnect - Ajouter un Post</title>
    <link rel="stylesheet" href="addpost.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <main>
        <h1>Ajouter un Post</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
        <p>
            <label>Titre :
                <input type="text" name="titre">
            </label>
        </p>

        <p>
            <label>Nom :
                <input type="text" name="nom">
            </label>
        </p>

        <p>
            <label>Numéro de téléphone :
                <input type="tel" name="numero" >
            </label>
        </p>

        <p>
            <label>Email :
                <input type="email" name="email" >
            </label>
        </p>

        <p>
            <label>Région :
                <select name="region">
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
                <textarea name="description" rows="5"></textarea>
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
                <input type="date" name="dateCreation">
            </label>
        </p>

        <p>
            <button type="submit">Publier</button>
        </p>
        </form>
        </main>
    </div>
</div>
    
    <script src="dashboard.js"></script>
    <script src="addpost.js"></script>
</body>
</html>
