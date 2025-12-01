<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php?error=' . urlencode('Veuillez vous connecter pour publier un post.'));
    exit();
}

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once 'layout.php';

$postC = new PostController();
$error = '';

$accountName = trim($_SESSION['name'] ?? '');
$accountEmail = trim($_SESSION['email'] ?? '');

if (
    isset($_POST['titre']) &&
    isset($_POST['numero']) &&
    isset($_POST['email']) &&
    isset($_POST['region']) &&
    isset($_POST['description'])
) {
    if (
        !empty($_POST['titre']) &&
        !empty($_POST['numero']) &&
        !empty($_POST['email']) &&
        !empty($_POST['region']) &&
        !empty($_POST['description'])
    ) {
<<<<<<< HEAD
        $authorName = $accountName;
        if ($authorName === '') {
            $error = "Nom du compte introuvable. Veuillez mettre à jour votre profil.";
        }

        $contactEmail = trim($_POST['email']);
        if (empty($contactEmail) && $accountEmail !== '') {
            $contactEmail = $accountEmail;
        }

        if (!empty($contactEmail) && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        }

        if (!empty($error)) {
            // skip remaining processing
        } else {
=======
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
        // Gestion des médias (max 4 images)
        $media = '';
        $maxFiles = 4;
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $uploadedMedia = [];
        $fileCount = 0;

        if (isset($_FILES['media']) && is_array($_FILES['media']['name'])) {
            $uploadDir = '../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['media']['name'] as $index => $originalName) {
                $errorCode = $_FILES['media']['error'][$index];
                if ($errorCode === UPLOAD_ERR_NO_FILE || empty($originalName)) {
                    continue;
                }

                if ($fileCount >= $maxFiles) {
                    $error = "Vous pouvez télécharger au maximum {$maxFiles} photos.";
                    break;
                }

                if ($errorCode !== UPLOAD_ERR_OK) {
                    $error = "Erreur lors du téléchargement de l'image " . htmlspecialchars($originalName);
                    break;
                }

                $tmpName = $_FILES['media']['tmp_name'][$index];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $tmpName);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedMime, true)) {
                    $error = "Format non pris en charge pour le fichier " . htmlspecialchars($originalName);
                    break;
                }

                $safeName = time() . '_' . $index . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
                if (move_uploaded_file($tmpName, $uploadDir . $safeName)) {
                    $uploadedMedia[] = $safeName;
                    $fileCount++;
                } else {
                    $error = "Impossible d'enregistrer le fichier " . htmlspecialchars($originalName);
                    break;
                }
            }

            if (!empty($error) && count($uploadedMedia) > 0) {
                foreach ($uploadedMedia as $fileName) {
                    $filePath = $uploadDir . $fileName;
                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }
                $uploadedMedia = [];
            }

            if (empty($error) && count($uploadedMedia) > 0) {
                $media = json_encode($uploadedMedia);
            }
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
            $authorName,
            $_POST['numero'],
            $contactEmail,
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
                        <label class="form-label">Nom du compte</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($accountName); ?>" disabled>
                        <input type="hidden" name="nom" value="<?php echo htmlspecialchars($accountName); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="numero" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="numero" name="numero" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($accountEmail); ?>" required>
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
                        <label for="media" class="form-label">Media (optionnel)</label>
                        <div class="media-dropzone" id="mediaDropzone" data-preview-target="mediaPreview" data-error-target="mediaError" data-max-files="4">
                            <input type="file" class="media-file-input" id="media" name="media[]" accept="image/*" multiple>
                            <div class="dropzone-content">
                                <div class="dropzone-icon">📁</div>
                                <div class="dropzone-text">Glissez-déposez vos fichiers ici</div>
                                <div class="dropzone-link">ou cliquez pour parcourir</div>
                                <div class="dropzone-hint">Formats supportés: JPG, PNG, GIF – max 4 fichiers</div>
                            </div>
                        </div>
                        <div id="mediaPreview" class="media-preview-grid mt-3" aria-live="polite"></div>
                        <div class="invalid-feedback d-block" id="mediaError" style="display: none;"></div>
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
renderLayout(
    'Ajouter un Post',
    $content,
    [
        'assets/css/volunteer-form.css',
        'assets/css/media-lightbox.css'
    ],
    [
        'assets/js/media-dropzone.js'
    ]
);
?>
