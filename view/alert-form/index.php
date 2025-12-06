<?php
session_start();
require_once '../../public/config.php';
require_once '../../model/alert.php';
require_once '../../controller/alertcontroller.php';
$alertC = new AlertController();

// Handle form submission for adding new alerts
if (
    isset($_POST['issue-title']) &&
    isset($_POST['issue-description'])
) {
    if (
        !empty($_POST['issue-title']) &&
        !empty($_POST['issue-description'])
    ) {
        // Gestion de l'image
        $media = ''; // Chaîne vide au lieu de null si pas de photo
        if (isset($_FILES['attachments']) && $_FILES['attachments']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/';
            // Créer le dossier s'il n'existe pas
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $mediaName = basename($_FILES['attachments']['name']);
            $media = $mediaName;
            move_uploaded_file($_FILES['attachments']['tmp_name'], $uploadDir . $mediaName);
        }

        // Création de l'objet Post
        $alert = new Post(
            null, // id
            $_POST['issue-title'],
            null, // iduser
            $_POST['issue-description'],
            $media,
            new DateTime() // date de création actuelle
        );

        // Récupérer id_user depuis la session si disponible, sinon null
        $id_user = 5;
        $result = $alertC->addAlert($alert, $id_user);
        if ($result) {
            header('Location: ../user-inbox/index.php');
            exit();
        } else {
            $error = "Erreur lors de l'ajout de l'alert. Veuillez vérifier les logs d'erreur ou réessayer.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>SocialV | Report Issue</title>

      <link rel="shortcut icon" href="../assets/images/favicon.ico" />
      <link rel="stylesheet" href="../assets/css/libs.min.css">
      <link rel="stylesheet" href="../assets/css/socialv.css?v=4.0.0">
      <link rel="stylesheet" href="../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="../assets/vendor/remixicon/fonts/remixicon.css">
      <link rel="stylesheet" href="../assets/vendor/vanillajs-datepicker/dist/css/datepicker.min.css">
      <link rel="stylesheet" href="../assets/vendor/font-awesome-line-awesome/css/all.min.css">
      <link rel="stylesheet" href="../assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
  </head>
  <body class="  ">
    <!-- loader Start -->

    <!-- loader END -->
    <!-- Nav Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Fundraising Network</a>
        <div class="d-flex">
          <a href="../frontoffice/index.php" class="btn btn-primary ms-2"><i class="fas fa-home"></i> My Portal</a>
          <a href="../user-inbox/index.php" class="btn btn-outline-primary ms-2">User Inbox (Old)</a>
        </div>
      </div>
    </nav>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="card shadow-lg">
            <div class="card-header bg-info text-white">
              <i class="fas fa-plus-circle"></i> Report Issue
            </div>
            <div class="card-body">
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
              <?php endif; ?>
              <form id="report-form" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="reporter-name" class="form-label">Reporter Name</label>
                  <input type="text" class="form-control" id="reporter-name" name="reporter-name">
                </div>
                <div class="mb-3">
                  <label for="contact-email" class="form-label">Contact Email</label>
                  <input type="email" class="form-control" id="contact-email" name="contact-email">
                </div>
                <div class="mb-3">
                  <label for="issue-title" class="form-label">Issue Title</label>
                  <input type="text" class="form-control" id="issue-title" name="issue-title" required>
                </div>
                <div class="mb-3">
                  <label for="issue-description" class="form-label">Issue Description</label>
                  <textarea class="form-control" id="issue-description" name="issue-description" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="attachments" class="form-label">Upload Attachments (Images/Videos)</label>
                  <input type="file" class="form-control" id="attachments" name="attachments" multiple accept="image/*,video/*">
                </div>
                <button type="submit" class="btn btn-primary mt-2">Submit Report</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Wrapper End -->

    <!-- Backend Bundle JavaScript -->
    <script src="../assets/js/libs.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="control.js"></script>

  </body>
</html>
