<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/offer.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$offerC = new OfferController();

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

        // Server-side validation (kept simple and consistent with JS)
        if (!preg_match('/^[0-9]{8,15}$/', $numero)) {
            $error = "Le numéro doit contenir 8 à 15 chiffres.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } elseif (!in_array($type_don, ['money','food','clothing','medicines','equipment','services'], true)) {
            $error = "Type de don invalide.";
        } else {
            // Upload media (optional)
            $media = null;
            if (isset($_FILES['media'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
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


            if (!isset($error)) {
                $offer = new Offer(
                    isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : null, // id_user
                    $description, // description
                    $media,       // media
                    $nom,         // nom
                    $numero,      // num
                    $email,       // email
                    $type_don,    // type_don
                    null          // id_offer
                );
                $result = $offerC->addOffer($offer);
                if ($result) {
                    header('Location: volunteer-view.php?id=' . urlencode($result));
                    exit();
                } else {
                    $error = "Erreur lors de l'ajout de l'offre. Veuillez réessayer.";
                }
            }
        }
    } else {
        $error = "Tous les champs requis doivent être remplis.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AidConnect – Register New Volunteer</title>
  <link rel="stylesheet" href="template.css" />
  <link rel="stylesheet" href="assets/css/libs.min.css" />
  <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0" />
  <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css" />
  <link rel="stylesheet" href="assets/css/custom.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="volunteer-form-body">
  <?php include 'header.php'; ?>

  <div class="wrapper">
    <section class="sign-up-page">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 pt-5 pb-5 d-flex align-items-center justify-content-center">
            <form method="POST" enctype="multipart/form-data"
                  class="sign-in-from bg-white p-4 rounded shadow-lg volunteer-form-container"
                  id="volunteerForm" novalidate>
              <p class="text-muted">Enter your details to register as a volunteer.</p>

              <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo htmlspecialchars($error); ?>
                </div>
              <?php endif; ?>

              <div class="form-group mb-3">
                <label for="nom" class="fw-bold">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nom" name="nom" required />
              </div>

              <div class="form-group mb-3">
                <label for="numero" class="fw-bold">Numéro <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="numero" name="numero" required />
              </div>

              <div class="form-group mb-3">
                <label for="email" class="fw-bold">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required />
              </div>

              <div class="form-group mb-3">
                <label for="type_don" class="fw-bold">Type Don <span class="text-danger">*</span></label>
                <select class="form-control" id="type_don" name="type_don" required>
                  <option value="">-- Choisissez --</option>
                  <option value="money">Argent</option>
                  <option value="food">Nourriture</option>
                  <option value="clothing">Vêtements</option>
                  <option value="medicines">Médicaments</option>
                  <option value="equipment">Matériel</option>
                  <option value="services">Services</option>
                </select>
              </div>

              <div class="form-group mb-3">
                <label for="description" class="fw-bold">Description <span class="text-danger">*</span></label>
                <div class="input-group">
                  <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
              </div>
              <div class="form-group mb-4">
                <label for="media" class="fw-bold">Media (optionnel)</label>
                <input type="file" class="form-control" id="media" name="media[]" accept="image/*" multiple />
                <div class="invalid-feedback d-block" id="mediaError" style="display: none;"></div>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                  Enregistrer
                </button>
              </div>

              <div class="text-center mt-3">
                <a href="volunteer-view.php" class="text-primary">View Registered Volunteers</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
  <script src="form-validation.js"></script>
</body>
</html>