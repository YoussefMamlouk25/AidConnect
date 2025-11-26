<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/offer.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$offerC = new OfferController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: volunteer-view.php');
    exit();
}

$offerData = $offerC->getOfferById($id);

if (!$offerData) {
    header('Location: volunteer-view.php');
    exit();
}

$error = null;
$success = null;

if (
    isset($_POST['nom']) &&
    isset($_POST['numero']) &&
    isset($_POST['email']) &&
    isset($_POST['type_don']) &&
    isset($_POST['description'])
) {
    if (
        !empty($_POST['nom']) &&
        !empty($_POST['numero']) &&
        !empty($_POST['email']) &&
        !empty($_POST['type_don']) &&
        !empty($_POST['description'])
    ) {
        $nom = trim($_POST['nom']);
        $numero = trim($_POST['numero']);
        $email = trim($_POST['email']);
        $type_don = trim($_POST['type_don']);
        $description = trim($_POST['description']);

        
        if (!preg_match('/^[0-9]{8,15}$/', $numero)) {
            $error = "Le numéro doit contenir 8 à 15 chiffres.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } elseif (!in_array($type_don, ['money','food','clothing','medicines','equipment','services'], true)) {
            $error = "Type de don invalide.";
        } else {
            
            $media = $offerData['media']; 
            
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
                    isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : $offerData['id_user'],
                    $description,
                    $media,
                    $nom,
                    $numero,
                    $email,
                    $type_don,
                    $id
                );
                
                $result = $offerC->updateOffer($offer, $id);
                
                if ($result) {
                    header('Location: volunteer-view.php?id=' . urlencode($id));
                    exit();
                } else {
                    $error = "Erreur lors de la mise à jour de l'offre. Veuillez réessayer.";
                }
            }
        }
    } else {
        $error = "Tous les champs requis doivent être remplis.";
    }
}

$nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($offerData['nom']);
$numero = isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : htmlspecialchars($offerData['num']);
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($offerData['email']);
$type_don = isset($_POST['type_don']) ? htmlspecialchars($_POST['type_don']) : htmlspecialchars($offerData['type_don']);
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($offerData['description']);
$currentMedia = $offerData['media'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AidConnect | Edit Volunteer</title>

    <link rel="stylesheet" href="assets/css/libs.min.css">
    <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0">
    <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/volunteer-form.css">
    <script src="javascript.js"></script>
  </head>

  <body class="volunteer-form-body">

    <header class="header bg-white shadow-sm">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-4">
            <a href="volunteer-view.php" class="d-flex align-items-center text-decoration-none">
              <i class="fas fa-arrow-left text-primary me-2"></i>
              <h4 class="mb-0 text-primary ms-2">AidConnect</h4>
            </a>
          </div>
          <div class="col-4 text-center">
            <button class="btn btn-outline-primary" onclick="window.location.href='volunteer-form.php'">Add New Volunteer</button>
          </div>
        </div>
      </div>
    </header>

    <div class="wrapper">
      <section class="sign-up-page">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-6 pt-5 pb-5 d-flex align-items-center justify-content-center">

              <form method="POST" enctype="multipart/form-data"
                    class="sign-in-from bg-white p-4 rounded shadow-lg volunteer-form-container"
                    id="volunteerForm">

                <h4 class="mb-3 text-primary">Edit Volunteer</h4>
                <p class="text-muted">Update volunteer information below.</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group mb-3">
                  <label for="nom" class="fw-bold">Nom</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>" required>
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label for="numero" class="fw-bold">Numéro</label>
                  <div class="input-group">
                    <input type="tel" class="form-control" id="numero" name="numero" value="<?php echo $numero; ?>" required>
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label for="email" class="fw-bold">Email</label>
                  <div class="input-group">
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label for="type_don" class="fw-bold">Type Don</label>
                  <div class="input-group">
                    <select class="form-control" id="type_don" name="type_don" required>
                      <option value="">-- Choisissez --</option>
                      <option value="money" <?php echo ($type_don === 'money') ? 'selected' : ''; ?>>Argent</option>
                      <option value="food" <?php echo ($type_don === 'food') ? 'selected' : ''; ?>>Nourriture</option>
                      <option value="clothing" <?php echo ($type_don === 'clothing') ? 'selected' : ''; ?>>Vêtements</option>
                      <option value="medicines" <?php echo ($type_don === 'medicines') ? 'selected' : ''; ?>>Médicaments</option>
                      <option value="equipment" <?php echo ($type_don === 'equipment') ? 'selected' : ''; ?>>Matériel</option>
                      <option value="services" <?php echo ($type_don === 'services') ? 'selected' : ''; ?>>Services</option>
                    </select>
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label for="description" class="fw-bold">Description</label>
                  <div class="input-group">
                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $description; ?></textarea>
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-4">
                  <label for="media" class="fw-bold">Media (optionnel)</label>
                  <div class="media-dropzone" id="mediaDropzone" data-preview-target="mediaPreview" data-error-target="mediaError" data-max-files="4">
                    <input type="file" class="media-file-input" id="media" name="media[]" accept="image/*,video/*" multiple>
                    <div class="dropzone-content">
                      <div class="dropzone-icon">📁</div>
                      <div class="dropzone-text">Glissez-déposez vos fichiers ici</div>
                      <div class="dropzone-link">ou cliquez pour parcourir</div>
                      <div class="dropzone-hint">Formats supportés: JPG, PNG, GIF, MP4, MOV, WEBM - max 4 fichiers</div>
                    </div>
                  </div>
                  <div id="mediaPreview" class="media-preview-grid mt-3" aria-live="polite"></div>
                  <div class="invalid-feedback d-block" id="mediaError" style="display: none;"></div>
                  <small class="text-muted d-block mt-2">
                    Laissez vide pour conserver les médias actuels. Téléchargez de nouveaux fichiers pour les remplacer.
                  </small>
                  <?php if (!empty($currentMedia)): ?>
                    <div class="mt-3">
                      <label class="fw-bold text-muted small d-block mb-2">Current Media:</label>
                      <div class="d-flex flex-wrap gap-2">
                        <?php
                          $mediaFiles = json_decode($currentMedia, true);
                          if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                            foreach ($mediaFiles as $mediaFile) {
                              $mediaUrl = '../../public/uploads/' . htmlspecialchars($mediaFile);
                              $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                              if (in_array($ext, ['mp4','mov','webm'])) {
                                echo '<div class="current-media-item"><video src="' . $mediaUrl . '" controls class="current-media-preview"></video></div>';
                              } else {
                                echo '<div class="current-media-item"><img src="' . $mediaUrl . '" alt="Current media" class="current-media-preview"></div>';
                              }
                            }
                          } else {
                            $mediaUrl = '../../public/uploads/' . htmlspecialchars($currentMedia);
                            $ext = strtolower(pathinfo($currentMedia, PATHINFO_EXTENSION));
                            if (in_array($ext, ['mp4','mov','webm'])) {
                              echo '<div class="current-media-item"><video src="' . $mediaUrl . '" controls class="current-media-preview"></video></div>';
                            } else {
                              echo '<div class="current-media-item"><img src="' . $mediaUrl . '" alt="Current media" class="current-media-preview"></div>';
                            }
                          }
                        ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <i class="fas fa-save me-2"></i>Update Volunteer
                  </button>
                  <a href="volunteer-view.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                  </a>
                </div>

                <div class="text-center mt-3">
                  <a href="volunteer-view.php" class="text-primary">← Back to Volunteers List</a>
                </div>

              </form>

            </div>
          </div>
        </div>
      </section>
    </div>

    <script src="assets/js/media-dropzone.js"></script>
  </body>
  <style>
    .current-media-item {
      position: relative;
      display: inline-block;
    }
    .current-media-preview {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #e4e6eb;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .current-media-preview:hover {
      transform: scale(1.05);
      border-color: #1877f2;
    }
    .current-media-preview[src*=".mp4"],
    .current-media-preview[src*=".mov"],
    .current-media-preview[src*=".webm"] {
      width: 140px;
    }
  </style>
</html>
