<?php
// Démarrer le buffer de sortie pour capturer toute sortie non désirée
if (!ob_get_level()) {
    ob_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactiver l'affichage des erreurs pour éviter de corrompre le JSON

session_start();

require_once '../../config.php';
require_once '../../Model/postupdate.php';
require_once '../../Controller/postupdatecontroller.php';
require_once '../../Controller/postcontroller.php';

$postUpdateC = new PostUpdateController();
$postController = new PostController();
$error = '';

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

// Vérifier si id_post est présent
if (!isset($_GET['id_post']) || empty($_GET['id_post'])) {
    if ($isAjax) {
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'ID post manquant'], JSON_UNESCAPED_UNICODE);
        exit();
    } else {
        header('Location: showpostback.php');
        exit();
    }
}

$id_post = (int)$_GET['id_post'];

// Vérifier que le post existe dans la table post
$postData = $postController->getPostById($id_post);
if (!$postData) {
    $error = "Post introuvable.";
    if ($isAjax) {
        if (ob_get_level()) {
            ob_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
        exit();
    }
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
        // Capturer toute sortie du contrôleur pour éviter de corrompre le JSON
        ob_start();
        $result = $postUpdateC->addPostUpdate($postUpdate);
        $controllerOutput = ob_get_clean();
        
        if ($result) {
            // Si c'est une requête AJAX, retourner un JSON pour rediriger
            if ($isAjax) {
                // Nettoyer toute sortie précédente
                while (ob_get_level()) {
                    ob_end_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                header('Cache-Control: no-cache, must-revalidate');
                $response = array('success' => true, 'redirect' => 'showpostupdateback.php');
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit();
            } else {
                header('Location: showpostupdateback.php');
                exit();
            }
        } else {
            $error = "Erreur lors de l'ajout du post résolu.";
            if ($isAjax) {
                // Nettoyer toute sortie précédente
                while (ob_get_level()) {
                    ob_end_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                header('Cache-Control: no-cache, must-revalidate');
                $response = array('success' => false, 'error' => $error);
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit();
            }
        }
    }
}

// Si ce n'est pas AJAX, afficher la page complète avec sidebar
if (!$isAjax) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AidConnect - Marquer comme Résolu</title>
  <link rel="stylesheet" href="assets/css/addpostupdate.css">
  <link rel="stylesheet" href="assets/css/addform.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<div class="layout">
  <?php include 'sidebar.php'; ?>
  <div class="main-content">
<?php
}
?>
    <main>
    <div class="offers-fragment add-form">
    <h2>Marquer le Post comme Résolu</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
 
    <?php if ($postData): ?>
        <div class="post-info">
            <h3>Post concerné : <?php echo htmlspecialchars($postData['titre']); ?></h3>
            <p><strong>Région :</strong> <?php echo htmlspecialchars($postData['region'] ?? ''); ?></p>
        </div>
    <?php endif; ?>

    <form action="addpostupdate.php?id_post=<?php echo urlencode($id_post); ?><?php echo $isAjax ? '&ajax=1' : ''; ?>" method="post" enctype="multipart/form-data" id="resolvePostForm">
        <p>
            <label>Description de la résolution :
                <textarea name="texte" rows="5" required></textarea>
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
                <input type="text" name="intervention" placeholder="Nom de la personne ou organisation">
            </label>
        </p>

        <p>
            <label>Temps pour répondre (jours) :
                <input type="number" name="joursReponse" min="0" placeholder="Ex: 2">
            </label>
        </p>

        <p>
            <label>Temps pour résoudre (jours) :
                <input type="number" name="joursResolution" min="0" placeholder="Ex: 7" required>
            </label>
        </p>

        <p class="form-actions">
            <button type="button" class="btn btn-primary btn-submit" onclick="if(typeof loadContent === 'function'){ const form = document.getElementById('resolvePostForm'); if(form){ const formData = new FormData(form); fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json()).then(d => { if(d.success && d.redirect) loadContent(d.redirect + '?ajax=1'); else alert(d.error || 'Erreur'); }).catch(e => { console.error(e); alert('Erreur lors de la résolution'); }); } }">
                <i class="ri-checkbox-circle-line me-1"></i> Valider
            </button>
            <a href="showpostback.php" class="btn btn-secondary btn-cancel" onclick="if(typeof loadContent === 'function'){ event.preventDefault(); loadContent('showpostback.php?ajax=1'); return false; }">
                <i class="ri-close-line me-1"></i> Annuler
            </a>
        </p>
    </form>
    </div>
    </main>
<?php if (!$isAjax): ?>
  </div>
</div>
  <script src="assets/js/dashboard.js"></script>
<?php endif; ?>
  <script src="assets/js/jsentiter2.js"></script>
  <?php if ($isAjax): ?>
  <link rel="stylesheet" href="assets/css/addform.css">
  <link rel="stylesheet" href="assets/css/addpostupdate.css">
  <script>
  // Le formulaire est géré directement par l'onclick du bouton Valider
  </script>
  <?php endif; ?>
<?php if (!$isAjax): ?>
</body>
</html>
<?php endif; ?>
