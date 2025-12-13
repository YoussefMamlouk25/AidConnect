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

            $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;

            // Créer l'objet PostUpdate (ordre selon le constructeur: id_post, texte, id_update_post, id_user, media, realiser, temp_reponse, temp_resolue)
            $postUpdate = new PostUpdate(
                $postUpdateData['id_post'], // id_post
                $_POST['texte'], // texte
                $id, // id_update_post
                $id_user, // id_user
                $media, // media
                $_POST['intervention'], // realiser
                $_POST['joursReponse'], // temp_reponse
                $_POST['joursResolution'] // temp_resolue
            );

            $result = $postUpdateC->updatePostUpdate($postUpdate);
            if ($result) {
                header('Location: showpostupdate.php');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        } else {
            $error = "Le texte est requis.";
        }
    }
}

// Préparer les valeurs pour le formulaire
$texte = isset($_POST['texte']) ? htmlspecialchars($_POST['texte']) : (isset($postUpdateData['texte']) ? htmlspecialchars($postUpdateData['texte']) : '');
$realiser = isset($_POST['intervention']) ? htmlspecialchars($_POST['intervention']) : (isset($postUpdateData['realiser']) ? htmlspecialchars($postUpdateData['realiser']) : '');
$joursReponse = isset($_POST['joursReponse']) ? htmlspecialchars($_POST['joursReponse']) : (isset($postUpdateData['temp_reponse']) ? htmlspecialchars($postUpdateData['temp_reponse']) : '');
$joursResolution = isset($_POST['joursResolution']) ? htmlspecialchars($_POST['joursResolution']) : (isset($postUpdateData['temp_resolue']) ? htmlspecialchars($postUpdateData['temp_resolue']) : '');
$currentMedia = isset($postUpdateData['media']) ? htmlspecialchars($postUpdateData['media']) : '';

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="ri-edit-line me-2"></i>Modifier la résolution</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($postData)): ?>
                    <div class="alert alert-info">
                        <h6 class="mb-2"><i class="ri-file-list-3-line me-2"></i>Post parent :</h6>
                        <strong><?php echo htmlspecialchars($postData['titre'] ?? ''); ?></strong>
                        <?php if (!empty($postData['region'])): ?>
                            <br><small><i class="ri-map-pin-line me-1"></i><?php echo htmlspecialchars($postData['region']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    
                    <div class="mb-3">
                        <label for="texte" class="form-label">Description de la résolution <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="texte" name="texte" rows="5" required><?php echo $texte; ?></textarea>
                    </div>

                    <?php if (!empty($currentMedia)): ?>
                        <div class="mb-3">
                            <label class="form-label">Photo actuelle</label>
                            <div>
                                <img src="../../public/uploads/<?php echo $currentMedia; ?>" alt="Photo actuelle" class="img-thumbnail" style="max-width: 200px;">
                                <br><small class="text-muted">Photo actuelle</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Nouvelle photo (laisser vide pour conserver l'actuelle)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Format accepté: JPG, PNG, GIF (optionnel)</small>
                    </div>

                    <div class="mb-3">
                        <label for="intervention" class="form-label">Intervention réalisée par <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="intervention" name="intervention" value="<?php echo $realiser; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="joursReponse" class="form-label">Temps pour répondre (jours) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="joursReponse" name="joursReponse" min="0" value="<?php echo $joursReponse; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="joursResolution" class="form-label">Temps pour résoudre (jours) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="joursResolution" name="joursResolution" min="0" value="<?php echo $joursResolution; ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i>Mettre à jour
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
renderLayout('Modifier la résolution', $content);
?>
