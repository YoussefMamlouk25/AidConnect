function toggleMessageForm(id) {
    var form = document.getElementById('message-form-' + id);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function resetMessageForm(id) {
    var form = document.getElementById('message-form-inner-' + id);
    form.reset();
    document.getElementById('id_message-' + id).value = '';
    document.getElementById('message_action-' + id).value = 'add';
    document.getElementById('submit-btn-' + id).innerText = 'Send';
}

function editMessage(id_message, message, id_reclamation) {
    var form = document.getElementById('message-form-inner-' + id_reclamation);
    if (form) {
        form.querySelector('input[name="id_message"]').value = id_message;
        form.querySelector('textarea[name="message"]').value = message;
        form.querySelector('input[name="message_action"]').value = 'update';
        form.querySelector('button[type="submit"]').innerText = 'Update';
        toggleMessageForm(id_reclamation);
    }
}

function deleteMessage(id_message, id_reclamation) {
    if (!confirm('Are you sure you want to delete this message?')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';

    var actionInput = document.createElement('input');
    actionInput.name = 'message_action';
    actionInput.value = 'delete';
    form.appendChild(actionInput);

    var idMessageInput = document.createElement('input');
    idMessageInput.name = 'id_message';
    idMessageInput.value = id_message;
    form.appendChild(idMessageInput);

    var idReclamationInput = document.createElement('input');
    idReclamationInput.name = 'id_reclamation';
    idReclamationInput.value = id_reclamation;
    form.appendChild(idReclamationInput);

    document.body.appendChild(form);
    form.submit();
}

function submitMessageForm(event, id) {
    event.preventDefault();
    var form = event.target;

    var message = form.querySelector('textarea[name="message"]').value;
    var statusDiv = document.getElementById('message-status-' + id);

    if (!message) {
        statusDiv.innerHTML = '<span style="color: red;">Please fill out the message field.</span>';
        return false;
    }

    form.submit();
    statusDiv.innerHTML = '<span style="color: green;">Sending message...</span>';
    return false;
}
