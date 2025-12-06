// Function to validate reporter name
function validateReporterName() {
    const nameInput = document.getElementById('reporter-name');
    const name = nameInput.value.trim();
    if (name === '') {
        alert('Reporter Name is required.');
        nameInput.focus();
        return false;
    }
    return true;
}

// Function to validate contact email
function validateContactEmail() {
    const emailInput = document.getElementById('contact-email');
    const email = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        alert('Contact Email is required.');
        emailInput.focus();
        return false;
    }
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        emailInput.focus();
        return false;
    }
    return true;
}

// Function to validate issue title
function validateIssueTitle() {
    const titleInput = document.getElementById('issue-title');
    const title = titleInput.value.trim();
    if (title === '') {
        alert('Issue Title is required.');
        titleInput.focus();
        return false;
    }
    return true;
}

// Function to validate issue description
function validateIssueDescription() {
    const descInput = document.getElementById('issue-description');
    const desc = descInput.value.trim();
    if (desc === '') {
        alert('Issue Description is required.');
        descInput.focus();
        return false;
    }
    return true;
}

// Function to validate severity
function validateSeverity() {
    const severitySelect = document.getElementById('severity');
    const severity = severitySelect.value;
    if (severity === '') {
        alert('Please select a Severity Level.');
        severitySelect.focus();
        return false;
    }
    return true;
}

// Function to validate issue type
function validateIssueType() {
    const typeSelect = document.getElementById('issue-type');
    const type = typeSelect.value;
    if (type === '') {
        alert('Please select an Issue Type.');
        typeSelect.focus();
        return false;
    }
    if (type === 'other') {
        const customTypeInput = document.getElementById('custom-issue-type');
        const customType = customTypeInput.value.trim();
        if (customType === '') {
            alert('Please specify the custom issue type.');
            customTypeInput.focus();
            return false;
        }
    }
    return true;
}

// Function to validate attachments (optional, but can check file types if needed)
function validateAttachments() {
    const fileInput = document.getElementById('attachments');
    const files = fileInput.files;
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/avi'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload only images or videos.');
                fileInput.focus();
                return false;
            }
        }
    }
    return true;
}

// Main validation function
function validateForm() {
    return validateReporterName() &&
           validateContactEmail() &&
           validateIssueTitle() &&
           validateIssueDescription() &&
           validateSeverity() &&
           validateIssueType() &&
           validateAttachments();
}

// Attach event listener to form submit
document.getElementById('report-form').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault(); 
    }
});
