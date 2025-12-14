<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once 'layout.php';

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
        // Gestion des images (multiple, max 4)
        $media = ''; // Chaîne vide au lieu de null si pas de photo
        if (isset($_FILES['media'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            // Créer le dossier s'il n'existe pas
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

ob_start();
?>
<div class="offers-fragment add-form">
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
                <div class="media-dropzone" id="mediaDropzone" data-preview-target="mediaPreview" data-max-files="4">
                    <input type="file" class="media-file-input" id="media" name="media[]" accept="image/*" multiple>
                    <div class="dropzone-content">
                        <div class="dropzone-icon">📁</div>
                        <div class="dropzone-text">Glissez-déposez vos fichiers ici</div>
                        <div class="dropzone-link">ou cliquez pour parcourir</div>
                        <div class="dropzone-hint">Formats supportés: JPG, PNG, GIF - max 4 fichiers</div>
                    </div>
                </div>
                <div id="mediaPreview" class="media-preview-grid mt-3" aria-live="polite"></div>
            </label>
        </p>

        <p>
            <label>Date de création :
                <input type="date" name="dateCreation" id="dateCreation">
            </label>
        </p>

        <p class="form-actions">
            <button type="submit" class="btn btn-primary btn-submit">
                <i class="ri-save-line me-1"></i> Publier
            </button>
            <?php if (!$isAjax): ?>
            <a href="showpostback.php" class="btn btn-secondary btn-cancel">
                <i class="ri-close-line me-1"></i> Annuler
            </a>
            <?php endif; ?>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();

if ($isAjax) {
    echo $content;
    echo '<script src="assets/js/media-dropzone.js"></script>';
} else {
    renderBackofficeLayout('Ajouter un Post', $content, ['assets/css/addform.css'], ['assets/js/addpost.js', 'assets/js/media-dropzone.js']);
}
?>
