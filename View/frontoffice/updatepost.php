<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once 'layout.php';

$postController = new PostController();
$error = '';
$success = false;

// Vérifier si l'ID est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: showPosts.php');
    exit();
}

$id = (int)$_GET['id'];

// Charger les données du post
$postData = $postController->getPostById($id);

if (!$postData) {
    $error = "Post introuvable.";
} else {
    // Traitement du formulaire soumis
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
            $media = !empty($postData['media']) ? $postData['media'] : '';
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
                $dateCreation = new DateTime($postData['date_creation'] ?? 'now');
            }

            // Créer l'objet Post
            $post = new Post(
                $id,
                $_POST['titre'],
                $_POST['nom'],
                $_POST['numero'],
                $_POST['email'],
                $_POST['region'],
                $_POST['description'],
                $media,
                $dateCreation
            );

            $result = $postController->updatePost($post);
            if ($result) {
                header('Location: showPosts.php');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour du post.";
            }
        } else {
            $error = "Veuillez remplir tous les champs obligatoires.";
        }
    }
}

// Préparer les valeurs pour le formulaire
$titre = isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : (isset($postData['titre']) ? htmlspecialchars($postData['titre']) : '');
$nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : (isset($postData['nom']) ? htmlspecialchars($postData['nom']) : '');
$numero = isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : (isset($postData['Numéro']) ? htmlspecialchars($postData['Numéro']) : (isset($postData['numero']) ? htmlspecialchars($postData['numero']) : ''));
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($postData['email']) ? htmlspecialchars($postData['email']) : '');
$region = isset($_POST['region']) ? htmlspecialchars($_POST['region']) : (isset($postData['region']) ? htmlspecialchars($postData['region']) : '');
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : (isset($postData['description']) ? htmlspecialchars($postData['description']) : '');
$dateCreation = isset($_POST['dateCreation']) ? htmlspecialchars($_POST['dateCreation']) : (isset($postData['date_creation']) ? htmlspecialchars($postData['date_creation']) : '');
$currentMedia = isset($postData['media']) ? htmlspecialchars($postData['media']) : '';

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="ri-edit-line me-2"></i>Modifier un Post</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $titre; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="numero" class="form-label">Numéro de téléphone <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="numero" name="numero" value="<?php echo $numero; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="region" class="form-label">Région <span class="text-danger">*</span></label>
                        <select class="form-control" id="region" name="region" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="Ariana" <?php echo ($region == 'Ariana') ? 'selected' : ''; ?>>Ariana</option>
                            <option value="Béja" <?php echo ($region == 'Béja') ? 'selected' : ''; ?>>Béja</option>
                            <option value="Ben Arous" <?php echo ($region == 'Ben Arous') ? 'selected' : ''; ?>>Ben Arous</option>
                            <option value="Bizerte" <?php echo ($region == 'Bizerte') ? 'selected' : ''; ?>>Bizerte</option>
                            <option value="Gabes" <?php echo ($region == 'Gabes') ? 'selected' : ''; ?>>Gabes</option>
                            <option value="Gafsa" <?php echo ($region == 'Gafsa') ? 'selected' : ''; ?>>Gafsa</option>
                            <option value="Jendouba" <?php echo ($region == 'Jendouba') ? 'selected' : ''; ?>>Jendouba</option>
                            <option value="Kairouan" <?php echo ($region == 'Kairouan') ? 'selected' : ''; ?>>Kairouan</option>
                            <option value="Kasserine" <?php echo ($region == 'Kasserine') ? 'selected' : ''; ?>>Kasserine</option>
                            <option value="Kébili" <?php echo ($region == 'Kébili') ? 'selected' : ''; ?>>Kébili</option>
                            <option value="Le Kef" <?php echo ($region == 'Le Kef') ? 'selected' : ''; ?>>Le Kef</option>
                            <option value="Mahdia" <?php echo ($region == 'Mahdia') ? 'selected' : ''; ?>>Mahdia</option>
                            <option value="Manouba" <?php echo ($region == 'Manouba') ? 'selected' : ''; ?>>Manouba</option>
                            <option value="Médenine" <?php echo ($region == 'Médenine') ? 'selected' : ''; ?>>Médenine</option>
                            <option value="Monastir" <?php echo ($region == 'Monastir') ? 'selected' : ''; ?>>Monastir</option>
                            <option value="Nabeul" <?php echo ($region == 'Nabeul') ? 'selected' : ''; ?>>Nabeul</option>
                            <option value="Sfax" <?php echo ($region == 'Sfax') ? 'selected' : ''; ?>>Sfax</option>
                            <option value="Sidi Bouzid" <?php echo ($region == 'Sidi Bouzid') ? 'selected' : ''; ?>>Sidi Bouzid</option>
                            <option value="Siliana" <?php echo ($region == 'Siliana') ? 'selected' : ''; ?>>Siliana</option>
                            <option value="Sousse" <?php echo ($region == 'Sousse') ? 'selected' : ''; ?>>Sousse</option>
                            <option value="Tataouine" <?php echo ($region == 'Tataouine') ? 'selected' : ''; ?>>Tataouine</option>
                            <option value="Tozeur" <?php echo ($region == 'Tozeur') ? 'selected' : ''; ?>>Tozeur</option>
                            <option value="Tunis" <?php echo ($region == 'Tunis') ? 'selected' : ''; ?>>Tunis</option>
                            <option value="Zaghouan" <?php echo ($region == 'Zaghouan') ? 'selected' : ''; ?>>Zaghouan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $description; ?></textarea>
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
                        <label for="media" class="form-label">Nouvelle photo (laisser vide pour conserver l'actuelle)</label>
                        <input type="file" class="form-control" id="media" name="media" accept="image/*">
                        <small class="text-muted">Format accepté: JPG, PNG, GIF (optionnel)</small>
                    </div>

                    <div class="mb-3">
                        <label for="dateCreation" class="form-label">Date de création</label>
                        <input type="date" class="form-control" id="dateCreation" name="dateCreation" value="<?php echo $dateCreation; ?>">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i>Mettre à jour
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
renderLayout('Modifier un Post', $content);
?>
