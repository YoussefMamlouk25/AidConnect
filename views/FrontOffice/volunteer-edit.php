<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/offer.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$offerC = new OfferController();

// Get the offer ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: volunteer-view.php');
    exit();
}

// Fetch the offer to edit
$offerData = $offerC->getOfferById($id);

if (!$offerData) {
    header('Location: volunteer-view.php');
    exit();
}

$error = null;
$success = null;

// Handle form submission
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

        // Validation
        if (!preg_match('/^[0-9]{8,15}$/', $numero)) {
            $error = "Le numéro doit contenir 8 à 15 chiffres.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } elseif (!in_array($type_don, ['cash','card','online'], true)) {
            $error = "Type de don invalide.";
        } else {
            // Handle media upload
            $media = $offerData['media']; // Keep existing media by default
            
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Delete old media file if it exists
                if (!empty($offerData['media'])) {
                    $oldMediaPath = $uploadDir . $offerData['media'];
                    if (file_exists($oldMediaPath)) {
                        @unlink($oldMediaPath);
                    }
                }
                
                $original = basename($_FILES['media']['name']);
                $mediaName = time() . '_' . $original;
                $targetPath = $uploadDir . $mediaName;
                
                if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
                    $media = $mediaName;
                } else {
                    $error = "Erreur lors du téléchargement du fichier.";
                }
            }

            if (!isset($error)) {
                // Create Offer object with updated data
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
                
                // Update the offer
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

// Use POST data if available (for form persistence on error), otherwise use database data
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
                      <option value="cash" <?php echo ($type_don === 'cash') ? 'selected' : ''; ?>>Cash</option>
                      <option value="card" <?php echo ($type_don === 'card') ? 'selected' : ''; ?>>Card</option>
                      <option value="online" <?php echo ($type_don === 'online') ? 'selected' : ''; ?>>Online</option>
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
                  <label for="media" class="fw-bold">Media</label>
                  <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
                  <small class="text-muted d-block mt-2">
                    Leave empty to keep current media. Upload a new file to replace it.
                  </small>
                  <?php if (!empty($currentMedia)): ?>
                    <div class="mt-3">
                      <label class="text-muted small">Current Media:</label>
                      <?php
                        $mediaUrl = '../../public/uploads/' . $currentMedia;
                        $ext = strtolower(pathinfo($currentMedia, PATHINFO_EXTENSION));
                        if (in_array($ext, ['mp4','mov','webm'])) {
                          echo '<div class="mt-2"><video src="' . htmlspecialchars($mediaUrl) . '" controls class="media-preview-video"></video></div>';
                        } else {
                          echo '<div class="mt-2"><img src="' . htmlspecialchars($mediaUrl) . '" alt="Current media" class="media-preview"></div>';
                        }
                      ?>
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

  </body>
</html>
