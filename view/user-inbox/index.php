<?php
session_start();
require_once '../../public/config.php';
require_once '../../model/alert.php';
require_once '../../controller/alertcontroller.php';

$alertC = new AlertController();

$messC = null;
$messageStatus = '';
$currentIsAdmin = 0; // user role

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && isset($_POST['delete_id'])) {
        $result = $alertC->deleteAlert($_POST['delete_id']);
        if ($result) {
            exit();
        } else {
            $error = "Erreur lors de la suppression de l'alert.";
        }
    }
    if (isset($_POST['message_action'])) {
        require_once '../../controller/messagecontrol.php';
        require_once '../../model/message.php';
        if (!$messC) {
            $messC = new MessageController();
        }

        $id_reclamation = isset($_POST['id_reclamation']) ? $_POST['id_reclamation'] : null;
        $messageContent = isset($_POST['message']) ? $_POST['message'] : null;
        $isadmin = isset($_POST['isadmin']) ? intval($_POST['isadmin']) : 0;
        $message_id = isset($_POST['id_message']) ? $_POST['id_message'] : null;
        $action = isset($_POST['message_action']) ? $_POST['message_action'] : null;
        $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
        $existingMedia = isset($_POST['existing_media']) ? $_POST['existing_media'] : null;

        $media = $existingMedia;
        if (isset($_FILES['message_media']) && $_FILES['message_media']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/messages/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['message_media']['name']));
            if (move_uploaded_file($_FILES['message_media']['tmp_name'], $uploadDir . $fileName)) {
                $media = $fileName;
            }
        }

        $messageObj = new Message($id_reclamation, $messageContent, $date, $isadmin, $message_id, $media);

        if ($id_reclamation && isset($isadmin)) {
            if ($action === 'add') {
                $success = $messC->addMessage($messageObj);
                $messageStatus = $success ? 'Message added successfully.' : 'Failed to add message.';
            } elseif ($action === 'update') {
                $success = $messC->updateMessage($messageObj, $currentIsAdmin);
                $messageStatus = $success ? 'Message updated successfully.' : 'Failed to update message.';
            } elseif ($action === 'delete') {
                if ($message_id) {
                    $success = $messC->deleteMessage($message_id, $currentIsAdmin);
                    $messageStatus = $success ? 'Message deleted successfully.' : 'Failed to delete message.';
                } else {
                    $messageStatus = 'Missing message ID for deletion.';
                }
            }
        } else {
            $messageStatus = 'Missing required message parameters.';
        }
    }
}

// Récupérer les alerts de l'utilisateur (id_user = 5 pour l'exemple)
$id_user = 5;
$alerts = $alertC->getAlertsByUser($id_user);
?>
<!doctype html>
<html lang="en">
      <head>
        <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
          <title>SocialV | reclamation  Inbox</title>
       
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
          
      </head>
  <body>
    <!-- Sidebar is optional, otherwise remove or make Bootstrap vertical nav -->
    <div class="container-fluid min-vh-100 py-4">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="card shadow">
            <div class="card-header bg-info text-white">
              <h4 class="mb-0">User Reclamation Inbox</h4>
            </div>
            <div class="card-body">
              <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
              <?php endif; ?>
              <?php if (!empty($messageStatus)): ?>
                <div class="alert alert-info mb-4">Status: <?php echo htmlspecialchars($messageStatus); ?></div>
              <?php endif; ?>
              <div id="reports-list">
                <?php if (!empty($alerts)): ?>
                  <?php require_once '../../controller/messagecontrol.php';
                  if (!$messC) $messC = new MessageController(); ?>
                  <?php foreach ($alerts as $index => $alert): ?>
                    <div class="card mb-4">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                          <h5 class="mb-1">Report Name: <?php echo htmlspecialchars($alert['titre']); ?></h5>
                          <span>
                            <form method="POST" style="display: inline;">
                              <input type="hidden" name="delete_id" value="<?php echo $alert['id_reclamation']; ?>">
                              <button type="submit" name="delete" class="btn btn-danger btn-sm me-2">Delete</button>
                            </form>
                            <a href="../update/index.php?edit_id=<?php echo $alert['id_reclamation']; ?>" class="btn btn-warning btn-sm me-2">Edit</a>
                            <button class="btn btn-primary btn-sm toggle-message" data-target="message-form-<?php echo $alert['id_reclamation']; ?>" type="button">Message</button>
                          </span>
                        </div>
                        <div class="mb-3 mt-2">
                          <div><strong>Description:</strong> <?php echo htmlspecialchars($alert['description']); ?></div>
                          <?php if (!empty($alert['media'])): ?>
                            <div class="mt-2"><span class="badge bg-info text-dark">Attachment:</span> <a href="../../public/uploads/<?php echo htmlspecialchars($alert['media']); ?>" target="_blank" class="badge bg-info text-dark">View Attachment</a></div>
                          <?php endif; ?>
                        </div>
                        <div class="existing-messages mt-3">
                          <h6>Messages:</h6>
                          <?php 
                            $messages = $messC->getMessagesByIdReclamation($alert['id_reclamation']);
                            if ($messages) {
                              foreach ($messages as $msg) {
                                $sender = (intval($msg['isadmin']) == 1) ? 'Admin' : 'User';
                                echo '<div class="bg-light p-2 mb-2 rounded">';
                                echo '<div class="d-flex justify-content-between align-items-center">';
                                echo '<span><strong>' . htmlspecialchars($sender) . ':</strong> ' . htmlspecialchars($msg['message']);
                                if (!empty($msg['date'])) {
                                  echo ' <span class="text-muted" style="font-size:0.85em;">(' . htmlspecialchars($msg['date']) . ')</span>';
                                }
                                echo '</span>';
                                if (intval($msg['isadmin']) == 0) {
                                  echo '<span>'; 
                                  $mediaValue = isset($msg['media']) ? $msg['media'] : '';
                                  echo '<button type="button" class="btn btn-link btn-sm text-primary me-2" onclick="editMessage(' . $msg['id_message'] . ', \'' . htmlspecialchars(addslashes($msg['message'])) . '\', ' . $alert['id_reclamation'] . ', \'' . htmlspecialchars(addslashes($mediaValue)) . '\')">Edit</button>';
                                  echo '<button type="button" class="btn btn-link btn-sm text-danger" onclick="deleteMessage(' . $msg['id_message'] . ', ' . $alert['id_reclamation'] . ')">Delete</button>';
                                  echo '</span>';
                                }
                                echo '</div>';
                                if (!empty($msg['media'])) {
                                  $mediaSafe = htmlspecialchars($msg['media']);
                                  echo '<div class="mt-1"><a class="badge bg-secondary text-decoration-none" target="_blank" href="../../public/uploads/messages/' . $mediaSafe . '">View Attachment</a></div>';
                                }
                                echo '</div>';
                              }
                            } else {
                              echo '<p class="text-muted">No messages.</p>';
                            }
                          ?>
                        </div>
                        <!-- Message Form -->
                        <div id="message-form-<?php echo $alert['id_reclamation']; ?>" class="message-form card card-body p-3 mt-3" style="display:none;">
                          <form method="POST" enctype="multipart/form-data" id="message-form-inner-<?php echo $alert['id_reclamation']; ?>" onsubmit="return submitMessageForm(event, <?php echo $alert['id_reclamation']; ?>)">
                            <input type="hidden" name="id_reclamation" value="<?php echo $alert['id_reclamation']; ?>">
                            <input type="hidden" name="isadmin" value="0">
                            <input type="hidden" name="id_message" id="id_message-<?php echo $alert['id_reclamation']; ?>" value="">
                            <input type="hidden" name="message_action" id="message_action-<?php echo $alert['id_reclamation']; ?>" value="add">
                            <input type="hidden" name="existing_media" id="existing_media-<?php echo $alert['id_reclamation']; ?>" value="">
                            <div class="mb-2">
                              <label for="message-<?php echo $alert['id_reclamation']; ?>" class="form-label">Message:</label>
                              <textarea name="message" id="message-<?php echo $alert['id_reclamation']; ?>" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-2">
                              <label for="message_media-<?php echo $alert['id_reclamation']; ?>" class="form-label">Attachment (optional):</label>
                              <input type="file" class="form-control" name="message_media" id="message_media-<?php echo $alert['id_reclamation']; ?>" accept="image/*,video/*,application/pdf">
                            </div>
                            <button type="submit" class="btn btn-success btn-sm me-2" id="submit-btn-<?php echo $alert['id_reclamation']; ?>">Send</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleMessageForm(<?php echo $alert['id_reclamation']; ?>); resetMessageForm(<?php echo $alert['id_reclamation']; ?>);">Cancel</button>
                          </form>
                          <div id="message-status-<?php echo $alert['id_reclamation']; ?>" class="mt-2"></div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No reports found.</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="../assets/js/libs.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="messagecontrol.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var toggles = document.querySelectorAll('.toggle-message');
        for (var i = 0; i < toggles.length; i++) {
          toggles[i].addEventListener('click', function(event) {
            event.preventDefault();
            var targetId = this.getAttribute('data-target');
            if (targetId) {
              var id = targetId.replace('message-form-', '');
              toggleMessageForm(id);
            }
          });
        }
      });
    </script>
  </body>
</html>
