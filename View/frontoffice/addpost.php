<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once 'layout.php';

$postC = new PostController();
$error = '';

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
        $media = '';
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $mediaName = basename($_FILES['media']['name']);
            $media = $mediaName;
            move_uploaded_file($_FILES['media']['tmp_name'], $uploadDir . $mediaName);
        }

        // Gestion de la date de création
        $dateCreation = null;
        if (isset($_POST['dateCreation']) && !empty($_POST['dateCreation'])) {
            $dateCreation = new DateTime($_POST['dateCreation']);
        } else {
            $dateCreation = new DateTime();
        }

        // Création de l'objet Post
        $post = new Post(
            null,
            $_POST['titre'],
            $_POST['nom'],
            $_POST['numero'],
            $_POST['email'],
            $_POST['region'],
            $_POST['description'],
            $media,
            $dateCreation
        );

        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
        $result = $postC->addPost($post, $id_user);
        if ($result) {
            header('Location: showPosts.php');
            exit();
        } else {
            $error = "Erreur lors de l'ajout du post. Veuillez vérifier les logs d'erreur ou réessayer.";
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="ri-add-circle-line me-2"></i>Ajouter un Post</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>

                    <div class="mb-3">
                        <label for="numero" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="numero" name="numero" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="region" class="form-label">Région <span class="text-danger">*</span></label>
                        <select class="form-control" id="region" name="region" required>
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
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="media" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="media" name="media" accept="image/*">
                        <small class="text-muted">Format accepté: JPG, PNG, GIF (optionnel)</small>
                    </div>

                    <div class="mb-3">
                        <label for="dateCreation" class="form-label">Date de création</label>
                        <input type="date" class="form-control" id="dateCreation" name="dateCreation">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i>Publier
                        </button>
                        <a href="showPosts.php" class="btn btn-secondary">
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
renderLayout('Ajouter un Post', $content);
?>
