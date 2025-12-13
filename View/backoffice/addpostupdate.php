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
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $mediaName = time() . '_' . basename($_FILES['photo']['name']);
            $targetPath = $uploadDir . $mediaName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $media = $mediaName;
            }
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
            // Si AJAX, recharger la page showpostback
            if ((isset($_GET['ajax']) && $_GET['ajax'] == '1') || (isset($_POST['ajax']) && $_POST['ajax'] == '1')) {
                echo '<script>if(typeof loadContent==="function"){ loadContent("showpostback.php"); }</script>';
                exit();
            } else {
                header('Location: showpostupdateback.php');
                exit();
            }
        } else {
            $error = "Erreur lors de l'ajout du post résolu.";
        }
    }
}

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

ob_start();
?>
<div class="offers-fragment add-form">
    <h2>Marquer le Post comme Résolu</h2>
    
    <?php if (isset($error) && !empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($postData): ?>
        <div class="post-info" style="background: #f0f9ff; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #0f5fcc;">
            <h3 style="margin: 0 0 0.5rem 0; color: #0f5fcc;">Post concerné : <?php echo htmlspecialchars($postData['titre']); ?></h3>
            <p style="margin: 0; color: #666;"><strong>Région :</strong> <?php echo htmlspecialchars($postData['region'] ?? 'N/A'); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="<?php echo $isAjax ? '?id_post=' . urlencode($id_post) . '&ajax=1' : '?id_post=' . urlencode($id_post); ?>">
        <?php if ($isAjax): ?>
            <input type="hidden" name="ajax" value="1">
        <?php endif; ?>
        
        <p>
            <label>Description de la résolution :
                <textarea name="texte" id="texte" rows="5"></textarea>
            </label>
        </p>

        <p>
            <label>Photo :
                <div class="media-dropzone" id="mediaDropzone" data-preview-target="mediaPreview" data-max-files="1">
                    <input type="file" class="media-file-input" id="photo" name="photo" accept="image/*">
                    <div class="dropzone-content">
                        <div class="dropzone-icon">📁</div>
                        <div class="dropzone-text">Glissez-déposez votre fichier ici</div>
                        <div class="dropzone-link">ou cliquez pour parcourir</div>
                        <div class="dropzone-hint">Formats supportés: JPG, PNG, GIF</div>
                    </div>
                </div>
                <div id="mediaPreview" class="media-preview-grid mt-3" aria-live="polite"></div>
            </label>
        </p>

        <p>
            <label>Intervention réalisée par :
                <input type="text" name="intervention" id="intervention">
            </label>
        </p>

        <p>
            <label>Temps pour répondre (jours) :
                <input type="number" name="joursReponse" id="joursReponse" min="0">
            </label>
        </p>

        <p>
            <label>Temps pour résoudre (jours) :
                <input type="number" name="joursResolution" id="joursResolution" min="0">
            </label>
        </p>

        <p class="form-actions">
            <button type="submit" class="btn btn-primary btn-submit">
                <i class="ri-checkbox-circle-line me-1"></i> Publier le post résolu
            </button>
            <a href="showpostback.php" class="btn btn-secondary btn-cancel" onclick="if(typeof loadContent==='function'){ loadContent('showpostback.php'); return false; }">
                <i class="ri-close-line me-1"></i> Annuler
            </a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();

if ($isAjax) {
    echo $content;
    echo '<script src="media-dropzone.js"></script>';
} else {
    renderBackofficeLayout('Marquer le Post comme Résolu', $content, ['addform.css'], ['media-dropzone.js']);
}
?>
