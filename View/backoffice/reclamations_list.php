<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../frontoffice/login.php');
    exit;
}

require_once __DIR__ . '/../../Controller/reclamationcontroller.php';
require_once __DIR__ . '/../../Model/Reclamation.php';
require_once __DIR__ . '/../../Controller/messagecontrol.php';
require_once __DIR__ . '/../../Model/message.php';

$controller = new ReclamationController();
$reclamations = $controller->getAllReclamations()->fetchAll();
$messC = new MessageController();
$messageStatus = '';

// Gérer la suppression de réclamation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['delete_id'])) {
    $controller->deleteReclamation($_POST['delete_id']);
    header('Location: reclamationcontroller.php?action=list&embed=1');
    exit;
}

// Gérer les actions sur les messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_action'])) {
    $id_reclamation = isset($_POST['id_reclamation']) ? $_POST['id_reclamation'] : null;
    $messageContent = isset($_POST['message']) ? $_POST['message'] : null;
    $isadmin = isset($_POST['isadmin']) ? intval($_POST['isadmin']) : 1;
    $message_id = isset($_POST['id_message']) ? $_POST['id_message'] : null;
    $action = isset($_POST['message_action']) ? $_POST['message_action'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
    $existingMedia = isset($_POST['existing_media']) ? $_POST['existing_media'] : null;

    $media = $existingMedia;
    if (isset($_FILES['message_media']) && $_FILES['message_media']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../public/uploads/messages/';
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
            $messageStatus = $success ? 'Message ajouté avec succès.' : 'Erreur lors de l\'ajout du message.';
        } elseif ($action === 'update') {
            $success = $messC->updateMessage($messageObj, 1);
            $messageStatus = $success ? 'Message modifié avec succès.' : 'Erreur lors de la modification du message.';
        } elseif ($action === 'delete') {
            if ($message_id) {
                $success = $messC->deleteMessage($message_id, 1);
                $messageStatus = $success ? 'Message supprimé avec succès.' : 'Erreur lors de la suppression du message.';
            } else {
                $messageStatus = 'ID du message manquant pour la suppression.';
            }
        }
    } else {
        $messageStatus = 'Paramètres de message manquants.';
    }
}

// Vérifier si c'est une requête AJAX (embed)
$isEmbed = isset($_GET['embed']) && $_GET['embed'] == '1';

if (!$isEmbed) {
    require_once __DIR__ . '/layout.php';
}

ob_start();
?>
<div class="container-fluid py-4">
    <div class="row h-100">
        <!-- Left Column: Réclamations List -->
        <div class="col-md-4" style="height: calc(100vh - 48px); overflow-y: auto;">
            <h4 class="mb-3 text-primary">
                <i class="ri-mail-line me-2"></i>Réclamations
            </h4>
            
            <?php if (!empty($messageStatus)): ?>
                <div class="alert alert-info mb-3"><?= htmlspecialchars($messageStatus) ?></div>
            <?php endif; ?>

            <?php if (empty($reclamations)): ?>
                <div class="alert alert-info">
                    <i class="ri-inbox-line me-2"></i>
                    Aucune réclamation pour le moment.
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php 
                    require_once __DIR__ . '/../../Model/user.php';
                    $userModel = new UserModel();
                    foreach ($reclamations as $reclamation): 
                        $user = $userModel->getUserById($reclamation['id_user']);
                    ?>
                        <a href="#" class="list-group-item list-group-item-action" 
                           onclick="showChat(<?= $reclamation['id_reclamation'] ?>); return false;" 
                           id="list-item-<?= $reclamation['id_reclamation'] ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1 text-truncate" style="max-width: 70%;">
                                    <?= htmlspecialchars($reclamation['titre']) ?>
                                </h5>
                                <small class="text-muted">#<?= $reclamation['id_reclamation'] ?></small>
                            </div>
                            <p class="mb-1 text-truncate"><?= htmlspecialchars($reclamation['description']) ?></p>
                            <small class="text-muted">
                                <?php if ($user): ?>
                                    <?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?>
                                <?php else: ?>
                                    Utilisateur supprimé
                                <?php endif; ?>
                                • <?= date('d/m/Y', strtotime($reclamation['date'])) ?>
                            </small>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Chat Details -->
        <div class="col-md-8" style="height: calc(100vh - 48px); overflow-y: auto; border-left: 1px solid #dee2e6; background-color: white;">
            <div id="chat-placeholder" class="d-flex align-items-center justify-content-center h-100 text-muted">
                <div class="text-center">
                    <i class="ri-message-3-line" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Sélectionnez une réclamation pour voir la discussion</h5>
                </div>
            </div>

            <?php if (!empty($reclamations)): ?>
                <?php foreach ($reclamations as $reclamation): 
                    $user = $userModel->getUserById($reclamation['id_user']);
                    $messages = $messC->getMessagesByIdReclamation($reclamation['id_reclamation']);
                ?>
                    <div id="chat-panel-<?= $reclamation['id_reclamation'] ?>" class="chat-panel h-100 flex-column" style="display: none;">
                        <!-- Header -->
                        <div class="p-3 border-bottom bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1"><?= htmlspecialchars($reclamation['titre']) ?></h5>
                                    <small class="text-muted"><?= htmlspecialchars($reclamation['description']) ?></small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="ri-user-line me-1"></i>
                                        <?php if ($user): ?>
                                            <?= htmlspecialchars($user['name'] ?? 'Utilisateur') ?>
                                        <?php else: ?>
                                            Utilisateur supprimé
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <form method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation?');">
                                    <input type="hidden" name="delete_id" value="<?= $reclamation['id_reclamation'] ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger ms-2">
                                        <i class="ri-delete-bin-line"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                            <?php if (!empty($reclamation['media'])): ?>
                                <div class="mt-2 p-2 bg-white rounded border">
                                    <i class="ri-attachment-line text-primary me-2"></i>
                                    <strong class="text-primary">Pièce jointe:</strong>
                                    <a href="../../public/uploads/<?= htmlspecialchars($reclamation['media']) ?>" 
                                       target="_blank" class="ms-2 btn btn-sm btn-outline-primary">
                                        <i class="ri-external-link-line"></i> Voir le fichier
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Messages Area -->
                        <div class="flex-grow-1 p-3" style="overflow-y: auto; background-color: #f0f2f5;">
                            <?php if ($messages): 
                                foreach ($messages as $msg):
                                    $isAdminMsg = intval($msg['isadmin']) == 1;
                                    $alignClass = $isAdminMsg ? 'align-self-end text-end' : 'align-self-start';
                                    $bubbleClass = $isAdminMsg ? 'bg-primary text-white' : 'bg-white border';
                            ?>
                                <div class="d-flex flex-column mb-3 <?= $alignClass ?>" style="max-width: 75%;">
                                    <div class="p-3 rounded <?= $bubbleClass ?> shadow-sm">
                                        <p class="mb-1"><?= htmlspecialchars($msg['message']) ?></p>
                                        <?php if (!empty($msg['media'])): ?>
                                            <div class="mt-2">
                                                <a href="../../public/uploads/messages/<?= htmlspecialchars($msg['media']) ?>" 
                                                   target="_blank" 
                                                   class="<?= $isAdminMsg ? 'text-white' : 'text-primary' ?> text-decoration-underline">
                                                    <i class="ri-attachment-line"></i> Pièce jointe
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted mt-1 <?= $isAdminMsg ? 'text-end' : '' ?>">
                                        <?= $isAdminMsg ? 'Vous (Admin)' : 'Utilisateur' ?> • 
                                        <?= !empty($msg['date']) ? htmlspecialchars($msg['date']) : 'Aujourd\'hui' ?>
                                        <?php if ($isAdminMsg): ?>
                                            <button class="btn btn-link btn-sm p-0 ms-2 text-primary" 
                                                    onclick="editMessage(<?= $msg['id_message'] ?>, '<?= htmlspecialchars(addslashes($msg['message'])) ?>', <?= $reclamation['id_reclamation'] ?>, '<?= isset($msg['media']) ? htmlspecialchars(addslashes($msg['media'])) : '' ?>')" 
                                                    title="Modifier">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button class="btn btn-link btn-sm p-0 ms-1 text-danger" 
                                                    onclick="deleteMessage(<?= $msg['id_message'] ?>, <?= $reclamation['id_reclamation'] ?>)" 
                                                    title="Supprimer">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php 
                                endforeach; 
                            else:
                            ?>
                                <div class="text-center text-muted mt-5">
                                    <i class="ri-message-3-line" style="font-size: 3rem;"></i>
                                    <p class="mt-3">Aucun message pour le moment. Commencez la conversation!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Input Area -->
                        <div class="p-3 border-top bg-white">
                            <form method="POST" enctype="multipart/form-data" 
                                  id="message-form-inner-<?= $reclamation['id_reclamation'] ?>" 
                                  onsubmit="return submitMessageForm(event, <?= $reclamation['id_reclamation'] ?>)">
                                <input type="hidden" name="id_reclamation" value="<?= $reclamation['id_reclamation'] ?>">
                                <input type="hidden" name="isadmin" value="1">
                                <input type="hidden" name="id_message" id="id_message-<?= $reclamation['id_reclamation'] ?>" value="">
                                <input type="hidden" name="message_action" id="message_action-<?= $reclamation['id_reclamation'] ?>" value="add">
                                <input type="hidden" name="existing_media" id="existing_media-<?= $reclamation['id_reclamation'] ?>" value="">
                                
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="document.getElementById('message_media-<?= $reclamation['id_reclamation'] ?>').click()">
                                        <i class="ri-attachment-line"></i>
                                    </button>
                                    <input type="file" name="message_media" id="message_media-<?= $reclamation['id_reclamation'] ?>" 
                                           style="display: none;" 
                                           onchange="if(this.files.length > 0) alert('Fichier sélectionné: ' + this.files[0].name);">
                                    <input type="text" name="message" id="message-<?= $reclamation['id_reclamation'] ?>" 
                                           class="form-control" placeholder="Tapez un message..." required>
                                    <button type="submit" class="btn btn-primary" id="submit-btn-<?= $reclamation['id_reclamation'] ?>">
                                        <i class="ri-send-plane-line"></i>
                                    </button>
                                </div>
                                <div id="message-status-<?= $reclamation['id_reclamation'] ?>" class="small mt-1"></div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Chat functions
function showChat(id) {
    // Hide placeholder
    var placeholder = document.getElementById('chat-placeholder');
    if (placeholder) {
        placeholder.style.display = 'none';
        placeholder.classList.remove('d-flex');
    }
    
    // Hide all chat panels
    var panels = document.getElementsByClassName('chat-panel');
    for (var i = 0; i < panels.length; i++) {
        panels[i].style.display = 'none';
        panels[i].classList.remove('d-flex');
    }
    
    // Show selected chat panel
    var selectedPanel = document.getElementById('chat-panel-' + id);
    if (selectedPanel) {
        selectedPanel.style.display = 'flex';
        selectedPanel.classList.add('d-flex');
    }

    // Update active state in list
    var items = document.getElementsByClassName('list-group-item');
    for (var i = 0; i < items.length; i++) {
        items[i].classList.remove('active');
    }
    var listItem = document.getElementById('list-item-' + id);
    if (listItem) listItem.classList.add('active');
}

function resetMessageForm(id_reclamation) {
    var form = document.getElementById('message-form-inner-' + id_reclamation);
    if (form) form.reset();
    document.getElementById('id_message-' + id_reclamation).value = '';
    document.getElementById('message_action-' + id_reclamation).value = 'add';
    document.getElementById('message-status-' + id_reclamation).textContent = '';
    
    var messageInput = document.getElementById('message-' + id_reclamation);
    if (messageInput) {
        messageInput.style.borderColor = '';
        messageInput.style.borderWidth = '';
    }
}

function submitMessageForm(event, id_reclamation) {
    event.preventDefault();
    var form = document.getElementById('message-form-inner-' + id_reclamation);
    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status >= 200 && xhr.status < 300) {
                window.location.reload();
            } else {
                document.getElementById('message-status-' + id_reclamation).textContent = 'Erreur lors de l\'envoi du message.';
                document.getElementById('message-status-' + id_reclamation).style.color = 'red';
            }
        }
    };
    xhr.send(formData);
    return false;
}

function editMessage(id_message, messageText, id_reclamation, mediaFile) {
    var messageInput = document.getElementById('message-' + id_reclamation);
    var idMessageInput = document.getElementById('id_message-' + id_reclamation);
    var actionInput = document.getElementById('message_action-' + id_reclamation);
    var existingMediaInput = document.getElementById('existing_media-' + id_reclamation);
    
    if (!messageInput || !idMessageInput || !actionInput) {
        alert('Erreur: Éléments du formulaire introuvables');
        return;
    }

    idMessageInput.value = id_message;
    actionInput.value = 'update';
    
    var tempTextarea = document.createElement('textarea');
    tempTextarea.innerHTML = messageText;
    messageInput.value = tempTextarea.value;
    
    if (existingMediaInput) {
        existingMediaInput.value = mediaFile || '';
    }

    messageInput.style.borderColor = '#0d6efd';
    messageInput.style.borderWidth = '2px';
    
    messageInput.focus();
    messageInput.select();
    
    var statusDiv = document.getElementById('message-status-' + id_reclamation);
    if (statusDiv) {
        statusDiv.innerHTML = '<span style="color: #0d6efd;"><i class="ri-edit-line"></i> Modification du message - modifiez et cliquez sur envoyer pour mettre à jour</span>';
    }
}

function deleteMessage(id_message, id_reclamation) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce message?')) return;
    var formData = new FormData();
    formData.append('message_action', 'delete');
    formData.append('id_message', id_message);
    formData.append('isadmin', '1');
    formData.append('id_reclamation', id_reclamation);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status >= 200 && xhr.status < 300) {
            window.location.reload();
        }
    };
    xhr.send(formData);
}
</script>

<?php
if (!$isEmbed) {
    $content = ob_get_clean();
    renderBackofficeLayout('Réclamations', $content);
} else {
    echo ob_get_clean();
}
?>
