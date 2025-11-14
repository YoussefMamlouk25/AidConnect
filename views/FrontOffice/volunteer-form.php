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
        $description= trim($_POST['description']);

        if (!preg_match('/^[0-9]{8,15}$/', $numero)) {
            $error = "Le numéro doit contenir 8 à 15 chiffres.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Adresse email invalide.";
        } elseif (!in_array($type_don, ['cash','card','online'], true)) {
            $error = "Type de don invalide.";
        } else {
            // Upload media (optional)
            $media = null;
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
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
                $offer = new Offer(
                    isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : null, // id_user (nullable if not logged)
                    $description,  // description
                    $media,        // media
                    $nom,          // nom
                    $numero,       // num
                    $email,        // email
                    $type_don,     // type_don
                    null           // id_offer
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AidConnect | Register New Volunteer</title>

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
            <a href="#" class="d-flex align-items-center">
              <img src="assets/images/logo.png" alt="AidConnect Logo" class="logo-img">
              <h4 class="mb-0 text-primary ms-2">AidConnect</h4>
            </a>
          </div>
          <div class="col-4 text-center">
            <button class="btn btn-outline-primary" onclick="window.location.href='volunteer-form.php'">Volunteer</button>
          </div>
        </div>
      </div>
    </header>

    <div class="wrapper">
      <section class="sign-up-page">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-6 pt-5 pb-5 d-flex align-items-center justify-content-center">

              <!-- FIXED: method POST + enctype -->
              <form method="POST" enctype="multipart/form-data"
                    class="sign-in-from bg-white p-4 rounded shadow-lg volunteer-form-container"
                    id="volunteerForm">

                <p class="text-muted">Enter your details to register as a volunteer.</p>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group mb-3">
                  <label for="nom" class="fw-bold">Nom</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="nom" name="nom">
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label for="numero" class="fw-bold">Numéro</label>
                  <div class="input-group">
                    <input type="tel" class="form-control" id="numero" name="numero">
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label for="email" class="fw-bold">Email</label>
                  <div class="input-group">
                    <input type="email" class="form-control" id="email" name="email">
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label for="type_don" class="fw-bold">Type Don</label>
                  <div class="input-group">
                    <select class="form-control" id="type_don" name="type_don">
                      <option value="">-- Choisissez --</option>
                      <option value="cash">Cash</option>
                      <option value="card">Card</option>
                      <option value="online">Online</option>
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
                    <textarea class="form-control" id="description" name="description"></textarea>
                    <span class="input-group-text">
                      <i class="fas fa-check-circle text-success success-icon"></i>
                      <i class="fas fa-exclamation-circle text-danger error-icon"></i>
                    </span>
                  </div>
                </div>

                <div class="form-group mb-4">
                  <label for="media" class="fw-bold">Media</label>
                  <input type="file" class="form-control" id="media" name="media">
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

  </body>
</html>
