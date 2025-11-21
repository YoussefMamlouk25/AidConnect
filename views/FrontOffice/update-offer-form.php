<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Model/updateoffer.php';
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/../../Controller/offercontroller.php';

$updateOfferC = new UpdateOfferController();
$offerC = new OfferController();

$id_offer = isset($_GET['id_offer']) ? (int)$_GET['id_offer'] : 0;

if ($id_offer <= 0) {
    header('Location: volunteer-view.php');
    exit();
}

$offerData = $offerC->getOfferById($id_offer);

if (!$offerData) {
    header('Location: volunteer-view.php');
    exit();
}

$id_user = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;

$is_offer_creator = ($id_user > 0 && $offerData['id_user'] == $id_user);

$hasReplied = false;
if ($id_user > 0 && !$is_offer_creator) {
    if ($updateOfferC->checkUserHasReplied($id_offer, $id_user)) {
        $error = "You have already replied to this offer. You can only reply once.";
        $hasReplied = true;
    }
}

$error = isset($error) ? $error : null;

if (
    isset($_POST['texte']) &&
    !$hasReplied
) {
    if (!empty($_POST['texte'])) {
        $texte = trim($_POST['texte']);

        if ($is_offer_creator) {
            $texte = '📝 Update: ' . $texte;
        } else {
            $texte = '✅ Volunteering: ' . $texte;
        }

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
            
            if ($is_offer_creator) {
                $userId = $offerData['id_user'] ? (int)$offerData['id_user'] : null;
            } else {
                $userId = $id_user > 0 ? $id_user : null;
            }

            if (empty(trim($texte))) {
                $error = "Le texte ne peut pas être vide.";
            } else {
                $updateOffer = new UpdateOffer(
                    $id_offer,
                    $userId,
                    $texte,
                    $media,
                    null
                );
                try {
                    $result = $updateOfferC->addUpdateOffer($updateOffer);
                    if ($result) {
                        header('Location: update-offer-view.php?id_offer=' . urlencode($id_offer));
                        exit();
                    } else {
                        $error = "Erreur lors de l'ajout. Veuillez réessayer.";
                    }
                } catch (PDOException $e) {
                    error_log("Error adding update (PDO): " . $e->getMessage());
                    $error = "Erreur de base de données: " . htmlspecialchars($e->getMessage());
                } catch (Exception $e) {
                    error_log("Error adding update: " . $e->getMessage());
                    $error = "Erreur lors de l'ajout: " . htmlspecialchars($e->getMessage());
                }
            }
        }
    } else {
        $error = "Le texte de la réponse est requis.";
    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AidConnect | Reply to Offer</title>

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
        </div>
      </div>
    </header>

    <div class="wrapper">
      <section class="sign-up-page">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8 pt-5 pb-5 d-flex align-items-center justify-content-center">
              <div class="sign-in-from bg-white p-4 rounded shadow-lg volunteer-form-container">
                <?php if ($is_offer_creator): ?>
                  <h4 class="mb-3 text-primary"><i class="fas fa-sync-alt me-2"></i>Update Your Offer</h4>
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>You are the creator of this offer. You can post multiple updates.
                  </div>
                <?php else: ?>
                  <h4 class="mb-3 text-primary">Reply to Offer</h4>
                <?php endif; ?>

                <div class="card mb-4 border-primary">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-gift me-2"></i>Original Offer</h5>
                  </div>
                  <div class="card-body">
                    <p><strong>Name:</strong> <?php echo OfferController::escape($offerData['nom']); ?></p>
                    <p><strong>Email:</strong> <?php echo OfferController::escape($offerData['email']); ?></p>
                    <p><strong>Type:</strong> <?php echo OfferController::escape($offerData['type_don']); ?></p>
                    <p><strong>Description:</strong> <?php echo nl2br(OfferController::escape($offerData['description'])); ?></p>
                  </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo OfferController::escape($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($hasReplied) && $hasReplied): ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-info-circle me-2"></i>You have already replied to this offer. 
                        <a href="update-offer-view.php?id_offer=<?php echo $id_offer; ?>" class="alert-link">View your reply</a>
                    </div>
                <?php else: ?>
                    <form method="POST" enctype="multipart/form-data" id="replyForm">
                        <div class="form-group mb-3">
                          <label for="texte" class="fw-bold"><?php echo $is_offer_creator ? 'Update Text' : 'Your Reply (Volunteering)'; ?></label>
                          <textarea class="form-control" id="texte" name="texte" rows="5" required placeholder="<?php echo $is_offer_creator ? 'Write your update here...' : 'Write your reply here...'; ?>"></textarea>
                          <?php if (!$is_offer_creator): ?>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>By replying, you are confirming that you are volunteering for this offer.</small>
                          <?php endif; ?>
                        </div>

                        <div class="form-group mb-4">
                          <label for="media" class="fw-bold">Media (Optional)</label>
                          <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
                        </div>

                        <div class="d-grid gap-2">
                          <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i><?php echo $is_offer_creator ? 'Post Update' : 'Submit Reply'; ?>
                          </button>
                          <a href="volunteer-view.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                          </a>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="text-center mt-3">
                  <a href="volunteer-view.php" class="text-primary">← Back to Offers</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </body>
</html>

