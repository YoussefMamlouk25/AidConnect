<?php
session_start();
require_once '../../public/config.php';
require_once '../../model/alert.php';
require_once '../../controller/alertcontroller.php';

$alertC = new AlertController();
$messC = null;
$messageStatus = '';
$formError = '';
$formSuccess = '';
$currentIsAdmin = 0; // user role

// Handle reclamation creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_reclamation'])) {
    if (!empty($_POST['issue-title']) && !empty($_POST['issue-description'])) {
        $media = '';
        if (isset($_FILES['attachments']) && $_FILES['attachments']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../public/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $mediaName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['attachments']['name']));
            $media = $mediaName;
            move_uploaded_file($_FILES['attachments']['tmp_name'], $uploadDir . $mediaName);
        }

        $alert = new Post(
            null,
            $_POST['issue-title'],
            null,
            $_POST['issue-description'],
            $media,
            new DateTime()
        );

        $id_user = 5; // Example user ID
        $result = $alertC->addAlert($alert, $id_user);
        if ($result) {
            $formSuccess = 'Reclamation created successfully!';
        } else {
            $formError = 'Error creating reclamation. Please try again.';
        }
    } else {
        $formError = 'Please fill in all required fields.';
    }
}

// Handle reclamation deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['delete_id'])) {
    $result = $alertC->deleteAlert($_POST['delete_id']);
    if ($result) {
        exit();
    } else {
        $error = "Error deleting reclamation.";
    }
}

// Handle messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_action'])) {
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

// Get user's reclamations
$id_user = 5;
$alerts = $alertC->getAlertsByUser($id_user);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My Portal | Reclamations</title>

    <link rel="stylesheet" href="../../assets/css/libs.min.css">
    <link rel="stylesheet" href="../../assets/css/socialv.css">
    <link rel="stylesheet" href="../../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .section-content {
            display: none;
        }
        .section-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid py-4" style="flex: 1; background-color: #f8f9fa;">
            
            <!-- Reclamations Section (Master-Detail) -->
            <div id="section-reclamations" class="section-content active">
                <div class="row h-100">
                    <!-- Left Column: Reclamations List -->
                    <div class="col-md-4" style="height: calc(100vh - 48px); overflow-y: auto;">
                        <h4 class="mb-3" style="color: #0d6efd;">My Reclamations</h4>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($messageStatus)): ?>
                            <div class="alert alert-info mb-4">Status: <?php echo htmlspecialchars($messageStatus); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($alerts)): ?>
                            <div class="list-group">
                            <?php foreach ($alerts as $index => $alert): ?>
                                <a href="#" class="list-group-item list-group-item-action" onclick="showChat(<?php echo $alert['id_reclamation']; ?>); return false;" id="list-item-<?php echo $alert['id_reclamation']; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1 text-truncate" style="max-width: 70%;"><?php echo htmlspecialchars($alert['titre']); ?></h5>
                                        <small class="text-muted">#<?php echo $alert['id_reclamation']; ?></small>
                                    </div>
                                    <p class="mb-1 text-truncate"><?php echo htmlspecialchars($alert['description']); ?></p>
                                    <small style="color: #0d6efd;">Click to view discussion</small>
                                </a>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You don't have any reclamations yet. Click "Create New" to submit one!
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Right Column: Chat Details -->
                    <div class="col-md-8" style="height: calc(100vh - 48px); overflow-y: auto; border-left: 1px solid #dee2e6; background-color: white;">
                        <div id="chat-placeholder" class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <div class="text-center">
                                <i class="fas fa-comments fa-4x mb-3" style="color: #0d6efd;"></i>
                                <h5>Select a reclamation to view details</h5>
                            </div>
                        </div>

                        <?php if (!empty($alerts)): ?>
                            <?php 
                            require_once '../../controller/messagecontrol.php';
                            if (!$messC) $messC = new MessageController(); 
                            ?>
                            <?php foreach ($alerts as $index => $alert): ?>
                                <div id="chat-panel-<?php echo $alert['id_reclamation']; ?>" class="chat-panel h-100 flex-column" style="display: none;">
                                    <!-- Header -->
                                    <div class="p-3 border-bottom bg-primary">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1 text-white">
                                                <h5 class="mb-1"><?php echo htmlspecialchars($alert['titre']); ?></h5>
                                                <small><?php echo htmlspecialchars($alert['description']); ?></small>
                                            </div>
                                            <div>
                                                <a href="../update/index.php?edit_id=<?php echo $alert['id_reclamation']; ?>" class="btn btn-sm btn-light me-2">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this reclamation?');">
                                                    <input type="hidden" name="delete_id" value="<?php echo $alert['id_reclamation']; ?>">
                                                    <button type="submit" name="delete" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <?php if (!empty($alert['media'])): ?>
                                            <div class="mt-2 p-2 bg-white rounded">
                                                <i class="fas fa-paperclip text-primary"></i>
                                                <strong class="text-primary">Attachment:</strong>
                                                <a href="../../public/uploads/<?php echo htmlspecialchars($alert['media']); ?>" target="_blank" class="ms-2 btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt"></i> View File
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Messages Area -->
                                    <div class="flex-grow-1 p-3" style="overflow-y: auto; background-color: #f0f2f5;">
                                        <?php 
                                        $messages = $messC->getMessagesByIdReclamation($alert['id_reclamation']);
                                        if ($messages): 
                                            foreach ($messages as $msg):
                                                $isAdminMsg = intval($msg['isadmin']) == 1;
                                                $alignClass = $isAdminMsg ? 'align-self-start' : 'align-self-end text-end';
                                                $bubbleClass = $isAdminMsg ? 'bg-white border' : 'text-white' ;
                                                $bubbleStyle = $isAdminMsg ? '' : 'background-color: #0d6efd;';
                                        ?>
                                            <div class="d-flex flex-column mb-3 <?php echo $alignClass; ?>" style="max-width: 75%;">
                                                <div class="p-3 rounded <?php echo $bubbleClass; ?> shadow-sm" style="<?php echo $bubbleStyle; ?>">
                                                    <p class="mb-1"><?php echo htmlspecialchars($msg['message']); ?></p>
                                                    <?php if (!empty($msg['media'])): ?>
                                                        <div class="mt-2">
                                                            <a href="../../public/uploads/messages/<?php echo htmlspecialchars($msg['media']); ?>" target="_blank" class="<?php echo $isAdminMsg ? 'text-primary' : 'text-white'; ?> text-decoration-underline">
                                                                <i class="fas fa-file"></i> Attachment
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted mt-1 <?php echo $isAdminMsg ? '' : 'text-end'; ?>">
                                                    <?php echo $isAdminMsg ? 'Admin' : 'You'; ?> • <?php echo !empty($msg['date']) ? htmlspecialchars($msg['date']) : 'Just now'; ?>
                                                    <?php if (!$isAdminMsg): ?>
                                                        <button class="btn btn-link btn-sm p-0 ms-2 text-primary" onclick="editMessage(<?php echo $msg['id_message']; ?>, '<?php echo htmlspecialchars(addslashes($msg['message'])); ?>', <?php echo $alert['id_reclamation']; ?>, '<?php echo isset($msg['media']) ? htmlspecialchars(addslashes($msg['media'])) : ''; ?>')" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-link btn-sm p-0 ms-1 text-danger" onclick="deleteMessage(<?php echo $msg['id_message']; ?>, <?php echo $alert['id_reclamation']; ?>)" title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        <?php 
                                            endforeach; 
                                        else:
                                        ?>
                                            <div class="text-center text-muted mt-5">
                                                <p>No messages yet. Start the conversation!</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Input Area -->
                                    <div class="p-3 border-top bg-white">
                                        <form method="POST" enctype="multipart/form-data" id="message-form-inner-<?php echo $alert['id_reclamation']; ?>" onsubmit="return submitMessageForm(event, <?php echo $alert['id_reclamation']; ?>)">
                                            <input type="hidden" name="id_reclamation" value="<?php echo $alert['id_reclamation']; ?>">
                                            <input type="hidden" name="isadmin" value="0">
                                            <input type="hidden" name="id_message" id="id_message-<?php echo $alert['id_reclamation']; ?>" value="">
                                            <input type="hidden" name="message_action" id="message_action-<?php echo $alert['id_reclamation']; ?>" value="add">
                                            <input type="hidden" name="existing_media" id="existing_media-<?php echo $alert['id_reclamation']; ?>" value="">
                                            
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('message_media-<?php echo $alert['id_reclamation']; ?>').click()"><i class="fas fa-paperclip"></i></button>
                                                <input type="file" name="message_media" id="message_media-<?php echo $alert['id_reclamation']; ?>" style="display: none;" onchange="if(this.files.length > 0) alert('File selected: ' + this.files[0].name);">
                                                <input type="text" name="message" id="message-<?php echo $alert['id_reclamation']; ?>" class="form-control" placeholder="Type a message..." required>
                                                <button type="submit" class="btn btn-primary" id="submit-btn-<?php echo $alert['id_reclamation']; ?>"><i class="fas fa-paper-plane"></i></button>
                                            </div>
                                            <div id="message-status-<?php echo $alert['id_reclamation']; ?>" class="small mt-1"></div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Create New Section -->
            <div id="section-create" class="section-content">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow-lg">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Create New Reclamation</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($formError)): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($formError); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($formSuccess)): ?>
                                    <div class="alert alert-success">
                                        <?php echo htmlspecialchars($formSuccess); ?>
                                        <button type="button" class="btn btn-sm btn-success ms-2" onclick="showSection('reclamations')">View My Reclamations</button>
                                    </div>
                                <?php endif; ?>
                                <form id="report-form" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="create_reclamation" value="1">
                                    <div class="mb-3">
                                        <label for="issue-title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="issue-title" name="issue-title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="issue-description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="issue-description" name="issue-description" rows="4" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="attachments" class="form-label">Upload Attachment (Optional)</label>
                                        <input type="file" class="form-control" id="attachments" name="attachments" accept="image/*,video/*,application/pdf">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Submit Reclamation
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="../../assets/js/libs.min.js"></script>
    <script src="../../assets/js/app.js"></script>
    <script>
        // Section navigation
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById('section-' + sectionName).classList.add('active');
            
            // Update nav active state
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.getElementById('nav-' + sectionName).classList.add('active');
        }

        // Chat functions
        function showChat(id) {
            console.log('Switching to chat:', id);
            
            var placeholder = document.getElementById('chat-placeholder');
            if (placeholder) {
                placeholder.style.display = 'none';
                placeholder.classList.remove('d-flex');
            }
            
            var panels = document.getElementsByClassName('chat-panel');
            for (var i = 0; i < panels.length; i++) {
                panels[i].style.display = 'none';
                panels[i].classList.remove('d-flex');
            }
            
            var selectedPanel = document.getElementById('chat-panel-' + id);
            if (selectedPanel) {
                console.log('Found panel:', selectedPanel);
                selectedPanel.style.display = 'flex';
                selectedPanel.classList.add('d-flex');
            } else {
                console.error('Panel not found for id:', id);
            }

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
                        document.getElementById('message-status-' + id_reclamation).textContent = 'Error sending message.';
                        document.getElementById('message-status-' + id_reclamation).style.color = 'red';
                    }
                }
            };
            xhr.send(formData);
            return false;
        }

        function editMessage(id_message, messageText, id_reclamation, mediaFile) {
            console.log('Editing message:', id_message, 'for reclamation:', id_reclamation);
            
            var messageInput = document.getElementById('message-' + id_reclamation);
            var idMessageInput = document.getElementById('id_message-' + id_reclamation);
            var actionInput = document.getElementById('message_action-' + id_reclamation);
            var existingMediaInput = document.getElementById('existing_media-' + id_reclamation);
            
            if (!messageInput || !idMessageInput || !actionInput) {
                console.error('Form elements not found');
                alert('Error: Could not find message form elements');
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
                statusDiv.innerHTML = '<span style="color: #0d6efd;"><i class="fas fa-edit"></i> Editing message - modify and click send to update</span>';
            }
            
            console.log('Edit mode activated');
        }

        function deleteMessage(id_message, id_reclamation) {
            if (!confirm('Are you sure you want to delete this message?')) return;
            var formData = new FormData();
            formData.append('message_action', 'delete');
            formData.append('id_message', id_message);
            formData.append('isadmin', '0');
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

        // Set initial active nav
        document.getElementById('nav-reclamations').classList.add('active');
    </script>
</body>
</html>
