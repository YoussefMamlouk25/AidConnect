<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once 'layout.php';

$postController = new PostController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: showpostback.php');
    exit();
}

$postData = $postController->getPostById($id);

if (!$postData) {
    header('Location: showpostback.php');
    exit();
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($titre) || empty($nom) || empty($numero) || empty($email) || empty($region) || empty($description)) {
        $error = "Tous les champs sont requis.";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $numero)) {
        $error = "Le numéro doit contenir 8 à 15 chiffres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } else {
        $media = $postData['media']; // Keep existing media by default

        // Handle media upload if provided
        if (isset($_FILES['media'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (!empty($postData['media'])) {
                $oldMediaFiles = json_decode($postData['media'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($oldMediaFiles)) {
                    foreach ($oldMediaFiles as $oldMediaFile) {
                        $oldMediaPath = $uploadDir . $oldMediaFile;
                        if (file_exists($oldMediaPath)) {
                            @unlink($oldMediaPath);
                        }
                    }
                } else {
                    $oldMediaPath = $uploadDir . $postData['media'];
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

        // Gestion de la date
        $dateCreation = null;
        if (isset($_POST['dateCreation']) && !empty($_POST['dateCreation'])) {
            $dateCreation = new DateTime($_POST['dateCreation']);
        } else {
            // Utiliser la date existante ou la date actuelle
            if (!empty($postData['date_creation'])) {
                $dateCreation = new DateTime($postData['date_creation']);
            } else {
                $dateCreation = new DateTime();
            }
        }

        if (!isset($error)) {
            $post = new Post(
                $id,
                $titre,
                $nom,
                $numero,
                $email,
                $region,
                $description,
                $media,
                $dateCreation
            );

            $result = $postController->updatePost($post, $id);
            if ($result) {
                $success = "Post mis à jour avec succès.";
                // Refresh data
                $postData = $postController->getPostById($id);
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        }
    }
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }


// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

ob_start();
?>
<div class="offers-fragment add-form">
    <h2>Modifier le Post #<?php echo e($id); ?></h2>
    
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

    <form method="POST" enctype="multipart/form-data" action="updatepost.php?id=<?php echo urlencode($id); ?><?php echo $isAjax ? '&ajax=1' : ''; ?>">
        <?php if ($isAjax): ?>
            <input type="hidden" name="ajax" value="1">
        <?php endif; ?>
        <p>
            <label>Titre :
                <input type="text" name="titre" value="<?php echo e($postData['titre']); ?>" required>
            </label>
        </p>

        <p>
            <label>Nom :
                <input type="text" name="nom" value="<?php echo e($postData['nom']); ?>" required>
            </label>
        </p>

        <p>
            <label>Numéro :
                <input type="tel" name="numero" value="<?php echo e($postData['Numéro'] ?? $postData['numero'] ?? ''); ?>" required>
            </label>
        </p>

        <p>
            <label>Email :
                <input type="email" name="email" value="<?php echo e($postData['email']); ?>" required>
            </label>
        </p>

        <p>
            <label>Région :
                <select name="region" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="Ariana" <?php echo ($postData['region'] === 'Ariana') ? 'selected' : ''; ?>>Ariana</option>
                    <option value="Béja" <?php echo ($postData['region'] === 'Béja') ? 'selected' : ''; ?>>Béja</option>
                    <option value="Ben Arous" <?php echo ($postData['region'] === 'Ben Arous') ? 'selected' : ''; ?>>Ben Arous</option>
                    <option value="Bizerte" <?php echo ($postData['region'] === 'Bizerte') ? 'selected' : ''; ?>>Bizerte</option>
                    <option value="Gabes" <?php echo ($postData['region'] === 'Gabes') ? 'selected' : ''; ?>>Gabes</option>
                    <option value="Gafsa" <?php echo ($postData['region'] === 'Gafsa') ? 'selected' : ''; ?>>Gafsa</option>
                    <option value="Jendouba" <?php echo ($postData['region'] === 'Jendouba') ? 'selected' : ''; ?>>Jendouba</option>
                    <option value="Kairouan" <?php echo ($postData['region'] === 'Kairouan') ? 'selected' : ''; ?>>Kairouan</option>
                    <option value="Kasserine" <?php echo ($postData['region'] === 'Kasserine') ? 'selected' : ''; ?>>Kasserine</option>
                    <option value="Kébili" <?php echo ($postData['region'] === 'Kébili') ? 'selected' : ''; ?>>Kébili</option>
                    <option value="Le Kef" <?php echo ($postData['region'] === 'Le Kef') ? 'selected' : ''; ?>>Le Kef</option>
                    <option value="Mahdia" <?php echo ($postData['region'] === 'Mahdia') ? 'selected' : ''; ?>>Mahdia</option>
                    <option value="Manouba" <?php echo ($postData['region'] === 'Manouba') ? 'selected' : ''; ?>>Manouba</option>
                    <option value="Médenine" <?php echo ($postData['region'] === 'Médenine') ? 'selected' : ''; ?>>Médenine</option>
                    <option value="Monastir" <?php echo ($postData['region'] === 'Monastir') ? 'selected' : ''; ?>>Monastir</option>
                    <option value="Nabeul" <?php echo ($postData['region'] === 'Nabeul') ? 'selected' : ''; ?>>Nabeul</option>
                    <option value="Sfax" <?php echo ($postData['region'] === 'Sfax') ? 'selected' : ''; ?>>Sfax</option>
                    <option value="Sidi Bouzid" <?php echo ($postData['region'] === 'Sidi Bouzid') ? 'selected' : ''; ?>>Sidi Bouzid</option>
                    <option value="Siliana" <?php echo ($postData['region'] === 'Siliana') ? 'selected' : ''; ?>>Siliana</option>
                    <option value="Sousse" <?php echo ($postData['region'] === 'Sousse') ? 'selected' : ''; ?>>Sousse</option>
                    <option value="Tataouine" <?php echo ($postData['region'] === 'Tataouine') ? 'selected' : ''; ?>>Tataouine</option>
                    <option value="Tozeur" <?php echo ($postData['region'] === 'Tozeur') ? 'selected' : ''; ?>>Tozeur</option>
                    <option value="Tunis" <?php echo ($postData['region'] === 'Tunis') ? 'selected' : ''; ?>>Tunis</option>
                    <option value="Zaghouan" <?php echo ($postData['region'] === 'Zaghouan') ? 'selected' : ''; ?>>Zaghouan</option>
                </select>
            </label>
        </p>

        <p>
            <label>Description :
                <textarea name="description" rows="5" required><?php echo e($postData['description']); ?></textarea>
            </label>
        </p>

        <p>
            <label>Media actuel :
                <?php if (!empty($postData['media'])): ?>
                    <div class="current-media">
                        <?php
                            $mediaFiles = json_decode($postData['media'], true);
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
                                $mediaUrl = '../../public/uploads/' . $postData['media'];
                                $ext = strtolower(pathinfo($postData['media'], PATHINFO_EXTENSION));
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

        <p>
            <label>Date de création :
                <?php
                    $dateValue = '';
                    if (!empty($postData['date_creation'])) {
                        try {
                            $dateObj = new DateTime($postData['date_creation']);
                            $dateValue = $dateObj->format('Y-m-d');
                        } catch (Exception $e) {
                            $dateValue = '';
                        }
                    }
                ?>
                <input type="date" name="dateCreation" value="<?php echo e($dateValue); ?>">
            </label>
        </p>

        <p class="form-actions">
            <button type="submit" class="btn btn-primary btn-submit">
                <i class="ri-save-line me-1"></i> Mettre à jour
            </button>
            <a href="showpostback.php" class="btn btn-secondary btn-cancel" onclick="if(typeof loadContent === 'function'){ loadContent('showpostback.php'); return false; }">
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
    renderBackofficeLayout('Modifier un Post', $content, ['addform.css'], ['addpost.js', 'media-dropzone.js']);
}
