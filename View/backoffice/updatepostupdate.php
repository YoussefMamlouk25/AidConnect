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

// Récupérer l'id de l'enregistrement update_post à modifier
$id = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
} elseif (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
} else {
    header('Location: showpostupdateback.php');
    exit();
}

// Charger les données de l'update_post
$postUpdateData = $postUpdateC->getPostUpdateById($id);
if (!$postUpdateData) {
    header('Location: showpostupdateback.php');
    exit();
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
            // Désactiver l'affichage des erreurs pour éviter de corrompre le JSON
            $oldDisplayErrors = ini_get('display_errors');
            ini_set('display_errors', '0');
            
            // Capturer toute sortie du contrôleur pour éviter de corrompre le JSON
            ob_start();
            $result = $postUpdateC->updatePostUpdate($postUpdate, $id);
            $controllerOutput = ob_get_clean();
            
            // Réactiver l'affichage des erreurs
            ini_set('display_errors', $oldDisplayErrors);
            
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
                $error = "Erreur lors de la mise à jour.";
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
        } else {
            $error = "Veuillez remplir le texte de la résolution.";
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

// Préparer valeurs pour le formulaire
$texte = isset($_POST['texte']) ? htmlspecialchars($_POST['texte']) : htmlspecialchars($postUpdateData['texte'] ?? '');
$realiser = isset($_POST['intervention']) ? htmlspecialchars($_POST['intervention']) : (isset($postUpdateData['realiser']) && $postUpdateData['realiser'] !== '0' ? htmlspecialchars($postUpdateData['realiser']) : '');
$joursReponse = isset($_POST['joursReponse']) ? htmlspecialchars($_POST['joursReponse']) : htmlspecialchars($postUpdateData['temp_reponse'] ?? '');
$joursResolution = isset($_POST['joursResolution']) ? htmlspecialchars($_POST['joursResolution']) : htmlspecialchars($postUpdateData['temp_resolue'] ?? '');
$currentMedia = isset($postUpdateData['media']) ? htmlspecialchars($postUpdateData['media']) : '';

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

// Si ce n'est pas AJAX, afficher la page complète avec sidebar
if (!$isAjax) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier une résolution</title>
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/addpostupdate.css">
  <link rel="stylesheet" href="assets/css/addform.css">
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
    <h2>Modifier la résolution</h2>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($postData)): ?>
      <div class="post-info">
        <h3>Post parent : <?php echo htmlspecialchars($postData['titre'] ?? ''); ?></h3>
        <p><strong>Région :</strong> <?php echo htmlspecialchars($postData['region'] ?? ''); ?></p>
      </div>
    <?php endif; ?>

    <form action="updatepostupdate.php?id=<?php echo urlencode($id); ?><?php echo $isAjax ? '&ajax=1' : ''; ?>" method="post" enctype="multipart/form-data" id="editPostUpdateForm">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <p>
            <label>Description de la résolution :
                <textarea name="texte" rows="5" required><?php echo $texte; ?></textarea>
            </label>
        </p>

        <p>
            <label>Photo actuelle :
                <?php if (!empty($currentMedia)): ?>
                    <div class="current-media">
                        <?php
                            $mediaUrl = '../../public/uploads/' . htmlspecialchars($currentMedia);
                            $ext = strtolower(pathinfo($currentMedia, PATHINFO_EXTENSION));
                            if (in_array($ext, ['mp4','mov','webm'])) {
                                echo '<video src="'.htmlspecialchars($mediaUrl).'" controls style="width:150px; height:150px; object-fit:cover; border-radius:6px; margin:5px;"></video>';
                            } else {
                                echo '<img src="'.htmlspecialchars($mediaUrl).'" alt="Current media" style="width:150px; height:150px; object-fit:cover; border-radius:6px; margin:5px;">';
                            }
                        ?>
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
                <input type="text" name="intervention" value="<?php echo $realiser; ?>" placeholder="Nom de la personne ou organisation">
            </label>
        </p>

        <p>
            <label>Temps pour répondre (jours) :
                <input type="number" name="joursReponse" min="0" value="<?php echo $joursReponse; ?>" placeholder="Ex: 2">
            </label>
        </p>

        <p>
            <label>Temps pour résoudre (jours) :
                <input type="number" name="joursResolution" min="0" value="<?php echo $joursResolution; ?>" placeholder="Ex: 7" required>
            </label>
        </p>

        <p class="form-actions">
            <button type="button" class="btn btn-primary btn-submit" id="btnValidatePostUpdate">
                <i class="ri-save-line me-1"></i> Valider
            </button>
            <a href="showpostupdateback.php" class="btn btn-secondary btn-cancel" onclick="if(typeof loadContent === 'function'){ event.preventDefault(); loadContent('showpostupdateback.php?ajax=1'); return false; }">
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
  document.addEventListener("DOMContentLoaded", function() {
      const btnValidate = document.getElementById("btnValidatePostUpdate");
      const form = document.getElementById("editPostUpdateForm");
      
      if (btnValidate && form && typeof loadContent === "function") {
          btnValidate.addEventListener("click", function(e) {
              e.preventDefault();
              
              const formData = new FormData(form);
              const btn = this;
              
              // Désactiver le bouton et afficher le chargement
              btn.disabled = true;
              btn.innerHTML = '<i class="ri-loader-4-line me-1"></i> Traitement...';
              
              fetch(form.action, {
                  method: "POST",
                  body: formData,
                  headers: {
                      "X-Requested-With": "XMLHttpRequest"
                  }
              })
              .then(response => {
                  if (!response.ok) {
                      throw new Error("Erreur HTTP: " + response.status);
                  }
                  const contentType = response.headers.get("content-type");
                  if (!contentType || !contentType.includes("application/json")) {
                      return response.text().then(text => {
                          console.error("Réponse non-JSON reçue:", text);
                          throw new Error("Réponse invalide du serveur");
                      });
                  }
                  return response.json();
              })
              .then(data => {
                  if (data.success && data.redirect) {
                      loadContent(data.redirect + "?ajax=1");
                  } else {
                      alert(data.error || "Erreur lors de la mise à jour");
                      btn.disabled = false;
                      btn.innerHTML = '<i class="ri-save-line me-1"></i> Valider';
                  }
              })
              .catch(error => {
                  console.error("Erreur:", error);
                  alert("Erreur lors de la mise à jour. Veuillez réessayer.");
                  btn.disabled = false;
                  btn.innerHTML = '<i class="ri-save-line me-1"></i> Valider';
              });
          });
      }
  });
  </script>
  <?php endif; ?>
<?php if (!$isAjax): ?>
</body>
</html>
<?php endif; ?>
