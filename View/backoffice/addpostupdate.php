<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/postupdate.php';
require_once '../../Controller/postupdatecontroller.php';
require_once '../../Controller/postcontroller.php';

$postUpdateC = new PostUpdateController();
$postController = new PostController();
$error = '';

// Vérifier si id_post est présent
if (!isset($_GET['id_post']) || empty($_GET['id_post'])) {
    header('Location: showpostback.php');
    exit();
}

$id_post = (int)$_GET['id_post'];

// Vérifier que le post existe dans la table post
$postData = $postController->getPostById($id_post);
if (!$postData) {
    $error = "Post introuvable.";
} else {
    // S'assurer que l'id_post existe bien dans la table post
    // $id_post sera utilisé pour la table update_post
    // Traitement du formulaire
    if (isset($_POST['texte']) &&
        isset($_POST['intervention']) &&
        isset($_POST['joursReponse']) &&
        isset($_POST['joursResolution'])) {

        // Gestion de l'image
        $media = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $mediaName = basename($_FILES['photo']['name']);
            $media = $mediaName;
            move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $mediaName);
        }

        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;

        // realiser : stocke directement la valeur du champ "Intervention réalisée par"
        $realiser = !empty($_POST['intervention']) ? $_POST['intervention'] : '';

        $texte = $_POST['texte'];

        // Créer l'objet
        $postUpdate = new PostUpdate(
            $id_post,
            $texte,
            null,
            $id_user,
            $media,
            $realiser,
            $_POST['joursReponse'],
            $_POST['joursResolution']
        );

        // Insérer dans update_post avec l'id_post de la table post
        $result = $postUpdateC->addPostUpdate($postUpdate);
        if ($result) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Erreur lors de l'ajout du post résolu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AidConnect - Marquer comme Résolu</title>
  <link rel="stylesheet" href="addpostupdate.css">
</head>
<body>
  <main>
    <h1>Marquer le Post comme Résolu</h1>
    
 
    <?php if ($postData): ?>
        <div class="post-info">
            <h2>Post concerné : <?php echo htmlspecialchars($postData['titre']); ?></h2>
            <p><strong>Région :</strong> <?php echo htmlspecialchars($postData['region'] ?? ''); ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <p>
            <label>Description de la résolution :<br>
                <textarea name="texte" rows="5" ></textarea>
            </label>
        </p>

        <p>
            <label>Ajouter une photo :
                <div class="photo-upload">
                 <input type="file" name="photo" id="photo" accept="image/*" class="file-input-hidden">

                <label for="photo" class="photo-icon">
                    📷 Ajouter une photo
                </label>
                </div>
            </label>
        </p>

        <p>
            <label>Intervention réalisée par :
                <input type="text" name="intervention" >
            </label>
            
        </p>

        <p>
            <label>Temps pour répondre (jours) :
                <input type="number" name="joursReponse" min="0" >
            </label>
        </p>

        <p>
            <label>Temps pour résoudre (jours) :
                <input type="number" name="joursResolution" min="0" >
            </label>
        </p>

        <p>
            <button type="submit">Publier le post résolu</button>
            <a href="dashboard.php" class="btn-cancel">Annuler</a>
        </p>
    </form>
  </main>

  <script src="./jsentiter2.js"></script>
</body>
</html>
