<?php
session_start();
require_once '../../public/config.php';
require_once '../../model/alert.php';
require_once '../../controller/alertcontroller.php';

$alertC = new AlertController();

$error = '';
$success = '';
$alertId = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;

// Fetch alert data by ID
$alertData = null;
if ($alertId > 0) {
    $alertData = $alertC->getAlertById($alertId);
    if (!$alertData) {
        $error = "Alert not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id_reclamation']) ? intval($_POST['id_reclamation']) : 0;
    $title = isset($_POST['issue-title']) ? trim($_POST['issue-title']) : '';
    $description = isset($_POST['issue-description']) ? trim($_POST['issue-description']) : '';
    $media = isset($_POST['current_media']) ? $_POST['current_media'] : '';

    if ($id <= 0) {
        $error = "Invalid alert ID.";
    } elseif (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } else {
        // Handle file upload if any
        if (isset($_FILES['attachments']) && $_FILES['attachments']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/';
            // Create directory if not exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $mediaName = basename($_FILES['attachments']['name']);
            if (move_uploaded_file($_FILES['attachments']['tmp_name'], $uploadDir . $mediaName)) {
                $media = $mediaName;
            }
        }
        // Update alert object
        $alert = new Post($id, $title, null, $description, $media, null);

        if ($alertC->updateAlert($alert, $id)) {
            $success = "Alert updated successfully.";
            // Refresh alert data
            $alertData = $alertC->getAlertById($id);
        } else {
            $error = "Failed to update alert.";
        }
    }
} 

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>SocialV | Edit Alert</title>
      
      <link rel="shortcut icon" href="../assets/images/favicon.ico" />
      <link rel="stylesheet" href="../assets/css/libs.min.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="../assets/vendor/remixicon/fonts/remixicon.css">
      <link rel="stylesheet" href="../assets/vendor/vanillajs-datepicker/dist/css/datepicker.min.css">
      <link rel="stylesheet" href="../assets/vendor/font-awesome-line-awesome/css/all.min.css">
      <link rel="stylesheet" href="../assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Fundraising Network</a>
        <div class="d-flex">
          <a href="../user-inbox/index.php" class="btn btn-outline-primary ms-2">User Inbox</a>
        </div>
      </div>
    </nav>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="card shadow-lg">
            <div class="card-header bg-info text-white">
              <i class="fas fa-edit"></i> Edit Alert
            </div>
            <div class="card-body">
              <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
              <?php endif; ?>
              <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
              <?php endif; ?>
              <?php if ($alertData): ?>
              <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_reclamation" value="<?php echo htmlspecialchars($alertData['id_reclamation']); ?>">
                <input type="hidden" name="current_media" value="<?php echo htmlspecialchars($alertData['media']); ?>">
                <div class="mb-3">
                  <label for="issue-title" class="form-label fw-bold">Issue Title</label>
                  <input type="text" class="form-control" id="issue-title" name="issue-title" required value="<?php echo htmlspecialchars($alertData['titre']); ?>">
                </div>
                <div class="mb-3">
                  <label for="issue-description" class="form-label fw-bold">Issue Description</label>
                  <textarea class="form-control" id="issue-description" name="issue-description" rows="4" required><?php echo htmlspecialchars($alertData['description']); ?></textarea>
                </div>
                <div class="mb-3">
                  <label for="attachments" class="form-label fw-bold">Upload Attachments (Images/Videos)</label>
                  <input type="file" class="form-control" id="attachments" name="attachments" multiple accept="image/*,video/*">
                </div>
                <?php if ($alertData['media']): ?>
                  <div class="mb-3">
                    <label class="form-label fw-bold">Current Attachment</label>
                    <div>
                      <a href="../../public/uploads/<?php echo htmlspecialchars($alertData['media']); ?>" target="_blank" class="badge bg-info text-dark text-wrap p-2">
                        <?php echo htmlspecialchars($alertData['media']); ?>
                      </a>
                    </div>
                  </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary mt-3">Update Alert</button>
              </form>
              <?php else: ?>
              <p>Alert data not available.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="../assets/js/libs.min.js"></script>
    <script src="../assets/js/app.js"></script>
  </body>
</html>
