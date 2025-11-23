<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/postupdate.php';
require_once '../../Controller/postupdatecontroller.php';
require_once '../../Controller/postcontroller.php';
require_once 'layout.php';

$postUpdateC = new PostUpdateController();
$postController = new PostController();
$error = '';

// Vérifier si id_post est présent
if (!isset($_GET['id_post']) || empty($_GET['id_post'])) {
    header('Location: showPosts.php');
    exit();
}

$id_post = (int)$_GET['id_post'];

// Vérifier que le post existe dans la table post
$postData = $postController->getPostById($id_post);
if (!$postData) {
    $error = "Post introuvable.";
} else {
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

        // Calculer les temps de réponse et de résolution
        $datePost = new DateTime($postData['date_creation']);
        $dateNow = new DateTime();
        $diff = $dateNow->diff($datePost);
        $tempReponse = isset($_POST['joursReponse']) && !empty($_POST['joursReponse']) ? (int)$_POST['joursReponse'] : $diff->days;
        $tempResolue = isset($_POST['joursResolution']) && !empty($_POST['joursResolution']) ? (int)$_POST['joursResolution'] : $diff->days;

        // Créer l'objet PostUpdate (ordre des paramètres selon le constructeur)
        $postUpdate = new PostUpdate(
            $id_post, // id_post
            $_POST['texte'], // texte
            null, // id_update_post
            $id_user, // id_user
            $media, // media
            $_POST['intervention'], // realiser
            $tempReponse, // temp_reponse
            $tempResolue // temp_resolue
        );

        $result = $postUpdateC->addPostUpdate($postUpdate);
        if ($result) {
            header('Location: showpostupdate.php');
            exit();
        } else {
            $error = "Erreur lors de l'ajout de la résolution.";
        }
    }
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="ri-checkbox-circle-line me-2"></i>Marquer le Post comme Résolu</h4>
            </div>
            <div class="card-body">
                <?php if ($postData): ?>
                    <div class="alert alert-info">
                        <h6 class="mb-2"><i class="ri-file-list-3-line me-2"></i>Post concerné :</h6>
                        <strong><?php echo htmlspecialchars($postData['titre']); ?></strong>
                        <?php if (!empty($postData['region'])): ?>
                            <br><small><i class="ri-map-pin-line me-1"></i><?php echo htmlspecialchars($postData['region']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="texte" class="form-label">Description de la résolution <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="texte" name="texte" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Format accepté: JPG, PNG, GIF (optionnel)</small>
                    </div>

                    <div class="mb-3">
                        <label for="intervention" class="form-label">Intervention réalisée par <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="intervention" name="intervention" required>
                    </div>

                    <div class="mb-3">
                        <label for="joursReponse" class="form-label">Temps pour répondre (jours) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="joursReponse" name="joursReponse" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="joursResolution" class="form-label">Temps pour résoudre (jours) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="joursResolution" name="joursResolution" min="0" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-2"></i>Publier le post résolu
                        </button>
                        <a href="showpostupdate.php" class="btn btn-secondary">
                            <i class="ri-close-line me-2"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Marquer comme Résolu', $content);
?>
