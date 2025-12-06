<?php
session_start();
require_once '../../public/config.php';
require_once '../../model/alert.php';
require_once '../../controller/alertcontroller.php';

$alertC = new AlertController();

$messC = null;
$messageStatus = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && isset($_POST['delete_id'])) {
        // Delete alert
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
        $isadmin = isset($_POST['isadmin']) ? intval($_POST['isadmin']) : 1;
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
        // Pass current admin flag as 1 when updating message
        $success = $messC->updateMessage($messageObj, 1);
        $messageStatus = $success ? 'Message updated successfully.' : 'Failed to update message.';
    } elseif ($action === 'delete') {
        if ($message_id) {
            $success = $messC->deleteMessage($message_id, 1);
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
$alerts = $alertC->getAllAlerts()->fetchAll();

// Calculate statistics
$totalCount = count($alerts);
$recentCount = 0;
$todayCount = 0;

foreach ($alerts as $alert) {
    // Count reclamations from last 7 days
    if (isset($alert['date'])) {
        $alertDate = strtotime($alert['date']);
        $sevenDaysAgo = strtotime('-7 days');
        if ($alertDate >= $sevenDaysAgo) {
            $recentCount++;
        }
        
        // Count today's reclamations
        if (date('Y-m-d', $alertDate) === date('Y-m-d')) {
            $todayCount++;
        }
    }
}

// Get message controller for response time calculation
require_once '../../controller/messagecontrol.php';
if (!$messC) $messC = new MessageController();

// Calculate average response time (simplified - time to first admin message)
$totalResponseTime = 0;
$countWithResponse = 0;
foreach ($alerts as $alert) {
    $messages = $messC->getMessagesByIdReclamation($alert['id_reclamation']);
    if ($messages) {
        $adminMessages = array_filter($messages, function($msg) {
            return intval($msg['isadmin']) == 1;
        });
        if (!empty($adminMessages)) {
            $countWithResponse++;
        }
    }
}
$avgResponseRate = $totalCount > 0 ? round(($countWithResponse / $totalCount) * 100) : 0;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>SocialV | reclamation  Inbox</title>

      <link rel="stylesheet" href="../../assets/css/libs.min.css">
      <link rel="stylesheet" href="../../assets/css/socialv.css">
      <link rel="stylesheet" href="../../assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">

  </head>
  <body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid py-4" style="flex: 1; background-color: #f8f9fa;">
            
            <!-- Dashboard Section -->
            <div id="section-dashboard" class="section-content" style="display: none;">
                <h2 class="mb-4">Dashboard</h2>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50">Total Reclamations</h6>
                                        <h2 class="mb-0"><?php echo $totalCount; ?></h2>
                                    </div>
                                    <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50">Last 7 Days</h6>
                                        <h2 class="mb-0"><?php echo $recentCount; ?></h2>
                                    </div>
                                    <i class="fas fa-calendar-week fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50">Today</h6>
                                        <h2 class="mb-0"><?php echo $todayCount; ?></h2>
                                    </div>
                                    <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white-50">Response Rate</h6>
                                        <h2 class="mb-0"><?php echo $avgResponseRate; ?>%</h2>
                                    </div>
                                    <i class="fas fa-reply fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Overview</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Reclamations:</strong> <?php echo $totalCount; ?></p>
                                <p><strong>Reclamations with Admin Response:</strong> <?php echo $countWithResponse; ?> (<?php echo $avgResponseRate; ?>%)</p>
                                <p><strong>Pending Response:</strong> <?php echo $totalCount - $countWithResponse; ?></p>
                                <p class="mb-0"><strong>Activity:</strong> <?php echo $todayCount; ?> new today, <?php echo $recentCount; ?> in last 7 days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reclamations Section -->
            <div id="section-reclamations" class="section-content active">
            <div class="row h-100">
                <!-- Left Column: Reclamations List -->
                <div class="col-md-4" style="height: calc(100vh - 48px); overflow-y: auto;">
                    <h4 class="mb-3 text-primary">Reclamations</h4>
                    
                    <!-- Search and Filter -->
                    <div class="card mb-3">
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Search by title or description..." onkeyup="filterReclamations()">
                            </div>
                            <div class="d-flex gap-2">
                                <select id="sort-select" class="form-select form-select-sm" onchange="sortReclamations()">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                </select>
                                <button class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
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
                                <small class="text-primary">Click to view discussion</small>
                            </a>
                        <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No reclamations found.</div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Chat Details -->
                <div class="col-md-8" style="height: calc(100vh - 48px); overflow-y: auto; border-left: 1px solid #dee2e6; background-color: white;">
                    <div id="chat-placeholder" class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="fas fa-comments fa-4x mb-3"></i>
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
                                <div class="p-3 border-bottom bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1"><?php echo htmlspecialchars($alert['titre']); ?></h5>
                                            <small class="text-muted"><?php echo htmlspecialchars($alert['description']); ?></small>
                                        </div>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this reclamation?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $alert['id_reclamation']; ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger ms-2">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                    <?php if (!empty($alert['media'])): ?>
                                        <div class="mt-2 p-2 bg-white rounded border">
                                            <i class="fas fa-paperclip text-info"></i>
                                            <strong class="text-info">Reclamation Attachment:</strong>
                                            <a href="../../public/uploads/<?php echo htmlspecialchars($alert['media']); ?>" target="_blank" class="ms-2 btn btn-sm btn-outline-info">
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
                                            $alignClass = $isAdminMsg ? 'align-self-end text-end' : 'align-self-start';
                                            $bubbleClass = $isAdminMsg ? 'bg-primary text-white' : 'bg-white border';
                                    ?>
                                        <div class="d-flex flex-column mb-3 <?php echo $alignClass; ?>" style="max-width: 75%;">
                                            <div class="p-3 rounded <?php echo $bubbleClass; ?> shadow-sm">
                                                <p class="mb-1"><?php echo htmlspecialchars($msg['message']); ?></p>
                                                <?php if (!empty($msg['media'])): ?>
                                                    <div class="mt-2">
                                                        <a href="../../public/uploads/messages/<?php echo htmlspecialchars($msg['media']); ?>" target="_blank" class="<?php echo $isAdminMsg ? 'text-white' : 'text-primary'; ?> text-decoration-underline">
                                                            <i class="fas fa-file"></i> Attachment
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <small class="text-muted mt-1 <?php echo $isAdminMsg ? 'text-end' : ''; ?>">
                                                <?php echo $isAdminMsg ? 'You (Admin)' : 'User'; ?> • <?php echo !empty($msg['date']) ? htmlspecialchars($msg['date']) : 'Just now'; ?>
                                                <!-- Edit and Delete buttons only for admin messages -->
                                                <?php if ($isAdminMsg): ?>
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
                                        <input type="hidden" name="isadmin" value="1">
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
            </div> <!-- End Reclamations Section -->
        </div>
    </div>

    <script>
        // Section navigation
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });
            
            // Show selected section
            var targetSection = document.getElementById('section-' + sectionName);
            if (targetSection) {
                targetSection.style.display = 'block';
                targetSection.classList.add('active');
            }
            
            // Update nav active state
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                link.classList.add('text-white');
            });
            var navLink = document.getElementById('nav-' + sectionName);
            if (navLink) {
                navLink.classList.add('active');
                navLink.classList.remove('text-white');
            }
        }

        // Search and filter functions
        function filterReclamations() {
            var searchText = document.getElementById('search-input').value.toLowerCase();
            var items = document.querySelectorAll('.list-group-item');
            
            items.forEach(function(item) {
                var title = item.querySelector('h5') ? item.querySelector('h5').textContent.toLowerCase() : '';
                var desc = item.querySelector('p') ? item.querySelector('p').textContent.toLowerCase() : '';
                
                if (title.includes(searchText) || desc.includes(searchText)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function sortReclamations() {
            var sortValue = document.getElementById('sort-select').value;
            var listGroup = document.querySelector('.list-group');
            var items = Array.from(document.querySelectorAll('.list-group-item'));
            
            items.sort(function(a, b) {
                var idA = parseInt(a.id.replace('list-item-', ''));
                var idB = parseInt(b.id.replace('list-item-', ''));
                
                if (sortValue === 'newest') {
                    return idB - idA; // Higher ID = newer
                } else {
                    return idA - idB; // Lower ID = older
                }
            });
            
            // Re-append items in sorted order
            items.forEach(function(item) {
                listGroup.appendChild(item);
            });
        }

        function clearFilters() {
            document.getElementById('search-input').value = '';
            document.getElementById('sort-select').value = 'newest';
            filterReclamations();
            sortReclamations();
        }

        function showChat(id) {
            console.log('Switching to chat:', id);
            
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
                console.log('Found panel:', selectedPanel);
                selectedPanel.style.display = 'flex';
                selectedPanel.classList.add('d-flex');
            } else {
                console.error('Panel not found for id:', id);
            }

            // Update active state in list
            var items = document.getElementsByClassName('list-group-item');
            for (var i = 0; i < items.length; i++) {
                items[i].classList.remove('active');
            }
            var listItem = document.getElementById('list-item-' + id);
            if (listItem) listItem.classList.add('active');
        }

        // Keep existing helper functions
        function resetMessageForm(id_reclamation) {
            var form = document.getElementById('message-form-inner-' + id_reclamation);
            if (form) form.reset();
            document.getElementById('id_message-' + id_reclamation).value = '';
            document.getElementById('message_action-' + id_reclamation).value = 'add';
            document.getElementById('message-status-' + id_reclamation).textContent = '';
            
            // Clear visual feedback
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
                        // Reload to show new message
                        window.location.reload(); 
                        // Ideally we would just append the message via JS to avoid reload, but reload is safer for now
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
            
            // Get form elements
            var messageInput = document.getElementById('message-' + id_reclamation);
            var idMessageInput = document.getElementById('id_message-' + id_reclamation);
            var actionInput = document.getElementById('message_action-' + id_reclamation);
            var existingMediaInput = document.getElementById('existing_media-' + id_reclamation);
            
            if (!messageInput || !idMessageInput || !actionInput) {
                console.error('Form elements not found');
                alert('Error: Could not find message form elements');
                return;
            }

            // Set the form to update mode
            idMessageInput.value = id_message;
            actionInput.value = 'update';
            
            // Decode HTML entities in the message text
            var tempTextarea = document.createElement('textarea');
            tempTextarea.innerHTML = messageText;
            messageInput.value = tempTextarea.value;
            
            // Set existing media if any
            if (existingMediaInput) {
                existingMediaInput.value = mediaFile || '';
            }

            // Add visual feedback - change input border color
            messageInput.style.borderColor = '#0d6efd';
            messageInput.style.borderWidth = '2px';
            
            // Focus on the message input
            messageInput.focus();
            messageInput.select(); // Select the text for easy editing
            
            // Show a small indicator
            var statusDiv = document.getElementById('message-status-' + id_reclamation);
            if (statusDiv) {
                statusDiv.innerHTML = '<span class="text-primary"><i class="fas fa-edit"></i> Editing message - modify and click send to update</span>';
            }
            
            console.log('Edit mode activated');
        }

        function deleteMessage(id_message, id_reclamation) {
            if (!confirm('Are you sure you want to delete this message?')) return;
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

    <!-- Backend Bundle JavaScript -->
    <script src="../../assets/js/libs.min.js"></script>
    <script src="../../assets/js/app.js"></script>
  </body>
</html>
