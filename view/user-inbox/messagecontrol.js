// Debug mode for development
var DEBUG = true;

function log(message) {
    if (DEBUG) {
        console.log('[MessageControl]', message);
    }
}

function toggleMessageForm(id) {
    log('toggleMessageForm called for id: ' + id);
    
    // Close all other message forms first
    var forms = document.querySelectorAll('.message-form');
    for (var i = 0; i < forms.length; i++) {
        var f = forms[i];
        if (f.id !== 'message-form-' + id) {
            f.style.display = 'none';
        }
    }
    
    // Toggle the selected form
    var form = document.getElementById('message-form-' + id);
    if (!form) {
        log('Form not found: message-form-' + id);
        return;
    }
    
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        // Auto-focus on textarea when opening
        var textarea = document.getElementById('message-' + id);
        if (textarea) textarea.focus();
    } else {
        form.style.display = 'none';
    }
}

function resetMessageForm(id) {
    log('resetMessageForm called for id: ' + id);
    var form = document.getElementById('message-form-inner-' + id);
    if (form) {
        form.reset();
        document.getElementById('id_message-' + id).value = '';
        document.getElementById('message_action-' + id).value = 'add';
        document.getElementById('submit-btn-' + id).innerText = 'Send';
        document.getElementById('message-status-' + id).innerHTML = '';
        var existingMediaInput = document.getElementById('existing_media-' + id);
        if (existingMediaInput) {
            existingMediaInput.value = '';
        }
    }
}


function deleteMessage(id_message, id_reclamation) {
    log('deleteMessage: id_message=' + id_message + ', id_reclamation=' + id_reclamation);

    if (!confirm('Are you sure you want to delete this message?')) return;

    var formData = new FormData();
    formData.append('message_action', 'delete');
    formData.append('id_message', id_message);
    formData.append('id_reclamation', id_reclamation);
    formData.append('isadmin', '0'); // user role isadmin 0 here

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status >= 200 && xhr.status < 300) {
                log('Delete response: ' + xhr.responseText);
                alert('Message deleted successfully.');
                window.location.reload();
            } else {
                alert('Failed to delete message.');
                log('Delete error: ' + xhr.status);
            }
        }
    };
    xhr.send(formData);
}

// Implement editMessage for editing a user message
function editMessage(id_message, message, id_reclamation, media) {
    var form = document.getElementById('message-form-inner-' + id_reclamation);
    if (form) {
        form.querySelector('input[name="id_message"]').value = id_message;
        // Decode HTML entities in the message text
        var textarea = form.querySelector('textarea[name="message"]');
        if (textarea) {
            var tempElement = document.createElement('textarea');
            tempElement.innerHTML = message;
            textarea.value = tempElement.value;
        }
        form.querySelector('input[name="message_action"]').value = 'update';
        var existingMediaInput = document.getElementById('existing_media-' + id_reclamation);
        if (existingMediaInput) {
            existingMediaInput.value = media || '';
        }
        form.querySelector('button[type="submit"]').innerText = 'Update';
        toggleMessageForm(id_reclamation);
    }
}

function submitMessageForm(event, id) {
    event.preventDefault();
    var form = event.target;
    var message = form.querySelector('textarea[name="message"]').value.trim();
    var statusDiv = document.getElementById('message-status-' + id);

    if (!message) {
        statusDiv.innerHTML = '<span style="color: red;">Please enter a message.</span>';
        return false;
    }

    // Show loading state
    var submitBtn = form.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;

    log('Submitting message form for id: ' + id);
    
    // Submit the form
    form.submit();
    return false;
}

// Add keyboard shortcut support
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // Close all message forms when ESC is pressed
        var forms = document.querySelectorAll('.message-form');
        for (var i = 0; i < forms.length; i++) {
            forms[i].style.display = 'none';
        }
    }
});
