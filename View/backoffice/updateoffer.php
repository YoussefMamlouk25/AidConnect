<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/offer.php';
require_once '../../Controller/offercontroller.php';
require_once 'layout.php';

$offerController = new OfferController();

$id_offer = isset($_GET['id_offer']) ? (int)$_GET['id_offer'] : 0;

if ($id_offer <= 0) {
    header('Location: showoffer.php');
    exit();
}

$offerData = $offerController->getOfferById($id_offer);

if (!$offerData) {
    header('Location: showoffer.php');
    exit();
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $type_don = trim($_POST['type_don'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($nom) || empty($numero) || empty($email) || empty($type_don) || empty($description)) {
        $error = "Tous les champs sont requis.";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $numero)) {
        $error = "Le numéro doit contenir 8 à 15 chiffres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (!in_array($type_don, ['money','food','clothing','medicines','equipment','services'], true)) {
        $error = "Type de don invalide.";
    } else {
        $media = $offerData['media']; // Keep existing media by default

                // Handle media upload if provided
                if (isset($_FILES['media'])) {
                    $uploadDir = __DIR__ . '/../../public/uploads/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    if (!empty($offerData['media'])) {
                        $oldMediaFiles = json_decode($offerData['media'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($oldMediaFiles)) {
                            foreach ($oldMediaFiles as $oldMediaFile) {
                                $oldMediaPath = $uploadDir . $oldMediaFile;
                                if (file_exists($oldMediaPath)) {
                                    @unlink($oldMediaPath);
                                }
                            }
                        } else {
                            $oldMediaPath = $uploadDir . $offerData['media'];
                            if (file_exists($oldMediaPath)) {
                                @unlink($oldMediaPath);
                            }
                        }
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

        if (!isset($error)) {
            $offer = new Offer(
                $offerData['id_user'],
                $description,
                $media,
                $nom,
                $numero,
                $email,
                $type_don
            );

            // Désactiver l'affichage des erreurs pour éviter de corrompre le JSON
            $oldDisplayErrors = ini_get('display_errors');
            ini_set('display_errors', '0');
            
            // Capturer toute sortie du contrôleur
            ob_start();
            $result = $offerController->updateOffer($offer, $id_offer);
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
                    $response = array('success' => true, 'redirect' => 'showoffer.php');
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    exit();
                } else {
                    $success = "Offre mise à jour avec succès.";
                    // Refresh data
                    $offerData = $offerController->getOfferById($id_offer);
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
        }
    }
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

ob_start();
?>
<div class="offers-fragment add-form">
    <h2>Modifier l'Offre #<?php echo e($id_offer); ?></h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message">
            <?php echo e($error); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div style="color: green; padding: 10px; background: #e6ffe6; border: 1px solid green; margin-bottom: 20px; border-radius: 8px;">
            <?php echo e($success); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="updateoffer.php?id_offer=<?php echo urlencode($id_offer); ?><?php echo $isAjax ? '&ajax=1' : ''; ?>" id="editOfferForm">
        <p>
            <label>Nom :
                <input type="text" name="nom" value="<?php echo e($offerData['nom']); ?>" required>
            </label>
        </p>

        <p>
            <label>Numéro :
                <input type="tel" name="numero" value="<?php echo e($offerData['num']); ?>" required>
            </label>
        </p>

        <p>
            <label>Email :
                <input type="email" name="email" value="<?php echo e($offerData['email']); ?>" required>
            </label>
        </p>

        <p>
            <label>Type de Don :
                <select name="type_don" required>
                    <option value="">-- Choisissez --</option>
                    <option value="money" <?php echo $offerData['type_don'] === 'money' ? 'selected' : ''; ?>>Argent</option>
                    <option value="food" <?php echo $offerData['type_don'] === 'food' ? 'selected' : ''; ?>>Nourriture</option>
                    <option value="clothing" <?php echo $offerData['type_don'] === 'clothing' ? 'selected' : ''; ?>>Vêtements</option>
                    <option value="medicines" <?php echo $offerData['type_don'] === 'medicines' ? 'selected' : ''; ?>>Médicaments</option>
                    <option value="equipment" <?php echo $offerData['type_don'] === 'equipment' ? 'selected' : ''; ?>>Matériel</option>
                    <option value="services" <?php echo $offerData['type_don'] === 'services' ? 'selected' : ''; ?>>Services</option>
                </select>
            </label>
        </p>

        <p>
            <label>Description :
                <textarea name="description" rows="5" required><?php echo e($offerData['description']); ?></textarea>
            </label>
        </p>

        <p>
            <label>Media actuel :
                <?php if (!empty($offerData['media'])): ?>
                    <div class="current-media">
                        <?php
                            $mediaFiles = json_decode($offerData['media'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                                foreach ($mediaFiles as $mediaFile) {
                                    $mediaUrl = '../../public/uploads/' . $mediaFile;
                                    $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                    if (in_array($ext, ['mp4','mov','webm'])) {
                                        echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls style="width:150px; height:150px; object-fit:cover; border-radius:6px; margin:5px;"></video>';
                                    } else {
                                        echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Current media" style="width:150px; height:150px; object-fit:cover; border-radius:6px; margin:5px;">';
                                    }
                                }
                            } else {
                                $mediaUrl = '../../public/uploads/' . $offerData['media'];
                                $ext = strtolower(pathinfo($offerData['media'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['mp4','mov','webm'])) {
                                    echo '<video src="' . htmlspecialchars($mediaUrl) . '" controls style="width:150px; height:150px; object-fit:cover; border-radius:6px; margin:5px;"></video>';
                                } else {
                                    echo '<img src="' . htmlspecialchars($mediaUrl) . '" alt="Current media" style="width:150px; height:150px; object-fit:cover; border-radius:6px; margin:5px;">';
                                }
                            }
                        ?>
                    </div>
                <?php else: ?>
                    <small>Aucun média</small>
                <?php endif; ?>
            </label>
        </p>

        <p>
            <label>Nouveau Media (optionnel) :
                <div class="media-dropzone" id="mediaDropzone" data-preview-target="mediaPreviewNew" data-max-files="4">
                    <input type="file" class="media-file-input" id="mediaNew" name="media[]" accept="image/*,video/*" multiple>
                    <div class="dropzone-content">
                        <div class="dropzone-icon">📁</div>
                        <div class="dropzone-text">Glissez-déposez vos fichiers ici</div>
                        <div class="dropzone-link">ou cliquez pour parcourir</div>
                        <div class="dropzone-hint">Formats supportés: JPG, PNG, GIF - max 4 fichiers</div>
                    </div>
                </div>
                <div id="mediaPreviewNew" class="media-preview-grid mt-3" aria-live="polite"></div>
            </label>
        </p>

        <p class="form-actions">
            <button type="submit" class="btn btn-primary btn-submit" id="btnValidateOffer">
                <i class="ri-save-line me-1"></i> Valider
            </button>
            <a href="showoffer.php" class="btn btn-secondary btn-cancel" onclick="if(typeof loadContent === 'function'){ event.preventDefault(); loadContent('showoffer.php?ajax=1'); return false; }">
                <i class="ri-close-line me-1"></i> Annuler
            </a>
        </p>
    </form>
</div>
<?php
$content = ob_get_clean();

if ($isAjax) {
    echo '<link rel="stylesheet" href="assets/css/addform.css">';
    echo $content;
    echo '<script src="assets/js/media-dropzone.js"></script>';
    echo "<script>
    document.addEventListener(\"DOMContentLoaded\", function() {
        const form = document.getElementById('editOfferForm');
        const btnValidate = document.getElementById('btnValidateOffer');
        
        if (form && btnValidate && typeof loadContent === \"function\") {
            form.addEventListener(\"submit\", function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const btn = btnValidate;
                
                // Désactiver le bouton et afficher le chargement
                btn.disabled = true;
                btn.innerHTML = '<i class=\"ri-loader-4-line me-1\"></i> Traitement...';
                
                fetch(form.action, {
                    method: \"POST\",
                    body: formData,
                    headers: {
                        \"X-Requested-With\": \"XMLHttpRequest\"
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(\"Erreur HTTP: \" + response.status);
                    }
                    const contentType = response.headers.get(\"content-type\");
                    if (!contentType || !contentType.includes(\"application/json\")) {
                        return response.text().then(text => {
                            console.error(\"Réponse non-JSON reçue:\", text);
                            throw new Error(\"Réponse invalide du serveur\");
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.redirect) {
                        loadContent(data.redirect + \"?ajax=1\");
                    } else {
                        alert(data.error || \"Erreur lors de la mise à jour\");
                        btn.disabled = false;
                        btn.innerHTML = '<i class=\"ri-save-line me-1\"></i> Valider';
                    }
                })
                .catch(error => {
                    console.error(\"Erreur:\", error);
                    alert(\"Erreur lors de la mise à jour. Veuillez réessayer.\");
                    btn.disabled = false;
                    btn.innerHTML = '<i class=\"ri-save-line me-1\"></i> Valider';
                });
            });
        }
    });
    </script>";
} else {
    renderBackofficeLayout('Modifier une Offre', $content, ['assets/css/addform.css'], ['assets/js/media-dropzone.js']);
}
