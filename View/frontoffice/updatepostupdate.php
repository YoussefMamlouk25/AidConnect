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

// Récupérer l'id de l'enregistrement update_post à modifier
$id = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
} elseif (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
} else {
    header('Location: showpostupdate.php');
    exit();
}

// Charger les données de l'update_post
$postUpdateData = $postUpdateC->getPostUpdateById($id);
if (!$postUpdateData) {
    $error = "Entrée introuvable.";
} else {
    // Charger le post parent si besoin
    $postData = $postController->getPostById($postUpdateData['id_post']);

    // Traitement du formulaire
    if (isset($_POST['texte']) && isset($_POST['intervention']) && isset($_POST['joursReponse']) && isset($_POST['joursResolution'])) {
        if (!empty($_POST['texte'])) {
            // Gestion de l'image
            $media = !empty($postUpdateData['media']) ? $postUpdateData['media'] : '';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../public/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                // Supprimer l'ancienne image si elle existe
                if (!empty($postUpdateData['media']) && file_exists($uploadDir . $postUpdateData['media'])) {
                    unlink($uploadDir . $postUpdateData['media']);
                }
                $mediaName = basename($_FILES['photo']['name']);
                $media = $mediaName;
                move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $mediaName);
            }

            // realiser
            $realiser = !empty($_POST['intervention']) ? $_POST['intervention'] : '';
            $texte = $_POST['texte'];
            $temp_reponse = isset($_POST['joursReponse']) ? (int)$_POST['joursReponse'] : null;
            $temp_resolue = isset($_POST['joursResolution']) ? (int)$_POST['joursResolution'] : null;

            // Récupérer l'id_user existant ou depuis la session
            $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : ($postUpdateData['id_user'] ?? null);

            // Créer objet PostUpdate (id_update_post en paramètre 3)
            $postUpdate = new PostUpdate(
                $postUpdateData['id_post'], // id_post parent
                $texte,
                $id, // id_update_post
                $id_user,
                $media,
                $realiser,
                $temp_reponse,
                $temp_resolue
            );

            // Appeler la méthode de mise à jour
            $result = $postUpdateC->updatePostUpdate($postUpdate, $id);
            if ($result) {
                header('Location: showpostupdate.php');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        } else {
            $error = "Veuillez remplir le texte de la résolution.";
        }
    }
}

// Préparer valeurs pour le formulaire
$texte = isset($_POST['texte']) ? htmlspecialchars($_POST['texte']) : htmlspecialchars($postUpdateData['texte'] ?? '');
$realiser = isset($_POST['intervention']) ? htmlspecialchars($_POST['intervention']) : htmlspecialchars($postUpdateData['realiser'] ?? '');
$joursReponse = isset($_POST['joursReponse']) ? htmlspecialchars($_POST['joursReponse']) : htmlspecialchars($postUpdateData['temp_reponse'] ?? '');
$joursResolution = isset($_POST['joursResolution']) ? htmlspecialchars($_POST['joursResolution']) : htmlspecialchars($postUpdateData['temp_resolue'] ?? '');
$currentMedia = isset($postUpdateData['media']) ? htmlspecialchars($postUpdateData['media']) : '';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier une résolution</title>
  <link rel="stylesheet" href="addpostupdate.css">
  <link rel="stylesheet" href="addpost.css">
</head>
<body>
  <main>
    <h1>Modifier la résolution</h1>

    <?php if (!empty($error)): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($postData)): ?>
      <div class="post-info">
        <h2>Post parent : <?php echo htmlspecialchars($postData['titre'] ?? ''); ?></h2>
        <p><strong>Région :</strong> <?php echo htmlspecialchars($postData['region'] ?? ''); ?></p>
      </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <p>
            <label>Description de la résolution :<br>
                <textarea name="texte" rows="5"><?php echo $texte; ?></textarea>
            </label>
        </p>

        <p>
            <label>Photo actuelle :
                <?php if (!empty($currentMedia)): ?>
                    <div class="current-media">
                        <img src="../../public/uploads/<?php echo $currentMedia; ?>" alt="Photo actuelle">
                        <small>Photo actuelle</small>
                    </div>
                <?php else: ?>
                    <small>Aucune photo</small>
                <?php endif; ?>
            </label>
        </p>

        <p>
            <label>Ajouter/Remplacer la photo :
                <div class="photo-upload">
                    <input type="file" name="photo" id="photo" accept="image/*" class="file-input-hidden">
                    <label for="photo" class="photo-icon">📷 Ajouter une photo</label>
                </div>
            </label>
        </p>

        <p>
            <label>Intervention réalisée par :
                <input type="text" name="intervention" value="<?php echo $realiser; ?>">
            </label>
        </p>

        <p>
            <label>Temps pour répondre (jours) :
                <input type="number" name="joursReponse" min="0" value="<?php echo $joursReponse; ?>">
            </label>
        </p>

        <p>
            <label>Temps pour résoudre (jours) :
                <input type="number" name="joursResolution" min="0" value="<?php echo $joursResolution; ?>">
            </label>
        </p>

        <p>
            <button type="submit" class="btn-update">Mettre à jour</button>
        </p>

        <p>
            <a href="showpostupdate.php" class="btn-cancel">Annuler</a>
        </p>
    </form>
  </main>

  <script src="jsentiter2.js"></script>
</body>
</html>
