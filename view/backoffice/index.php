<?php
session_start();
require_once '../../public/config.php';
require_once '../../model/alert.php';
require_once '../../controller/alertcontroller.php';

$alertC = new AlertController();

// Gérer la suppression d'un alert
if (isset($_POST['delete']) && isset($_POST['delete_id'])) {
    $result = $alertC->deleteAlert($_POST['delete_id']);
    if ($result) {
        exit();
    } else {
        $error = "Erreur lors de la suppression de l'alert.";
    }
}

// Récupérer les alerts de l'utilisateur (id_user = 5 pour l'exemple)
$id_user = 5;
$alerts = $alertC->getAllAlerts();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>SocialV | reclamation  Inbox</title>

      <link rel="shortcut icon" href="../assets/images/favicon.ico" />
      <link rel="stylesheet" href="../assets/css/libs.min.css">
      <link rel="stylesheet" href="../assets/css/socialv.css?v=4.0.0">
      <link rel="stylesheet" href="../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
      <link rel="stylesheet" href="../assets/vendor/remixicon/fonts/remixicon.css">
      <link rel="stylesheet" href="../assets/vendor/vanillajs-datepicker/dist/css/datepicker.min.css">
      <link rel="stylesheet" href="../assets/vendor/font-awesome-line-awesome/css/all.min.css">
      <link rel="stylesheet" href="../assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
      <link rel="stylesheet" href="style.css">

  </head>
  <body class="  ">
    <nav class="sidenav">
      <ul>
        <li><a href="">recalamation</a></li>
      </ul>
    </nav>
    <div class="wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-10 offset-md-1">
            <div class="card">
              <div class="card-header">
                <h4>Admin recalamation</h4>
              </div>
              <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <div id="chats-list">
                  <?php if (!empty($alerts)): ?>
                    <?php foreach ($alerts as $index => $alert): ?>
                      <div class="chat-item mb-4 p-3 border rounded">
                        <h5>Report Name: <?php echo htmlspecialchars($alert['titre']); ?></h5>
                        <div class="chat-history mb-3" style="max-height: 200px; overflow-y: auto;">
                          <div class="message user-message mb-2"><strong>Message:</strong> <?php echo htmlspecialchars($alert['description']); ?></div>
                          <?php if (!empty($alert['media'])): ?>
                            <div class="message user-message mb-2"><strong>Attachment:</strong> <a href="../../public/uploads/<?php echo htmlspecialchars($alert['media']); ?>" target="_blank">View Attachment</a></div>
                          <?php endif; ?>
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $alert['id_reclamation']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                       
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p>No reports found.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Wrapper End -->

    <!-- Backend Bundle JavaScript -->
    <script src="../assets/js/libs.min.js"></script>
    <script src="../assets/js/app.js"></script>
  
  </body>
</html>
