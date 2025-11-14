document.addEventListener('DOMContentLoaded', () => {
  // Global helper functions
  const readFileAsDataUrl = (file) =>
    new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(reader.result);
      reader.onerror = () => reject(reader.error);
      reader.readAsDataURL(file);
    });

  const createObjectUrlFromDataUrl = (dataUrl) => {
    try {
      const [header, base64Data] = dataUrl.split(',');
      const mimeMatch = header.match(/data:(.*);base64/);
      const mimeType = mimeMatch ? mimeMatch[1] : 'application/octet-stream';
      const byteCharacters = atob(base64Data);
      const byteNumbers = new Array(byteCharacters.length);
      for (let i = 0; i < byteCharacters.length; i += 1) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
      }
      const byteArray = new Uint8Array(byteNumbers);
      const blob = new Blob([byteArray], { type: mimeType });
      const objectUrl = URL.createObjectURL(blob);
      return objectUrl;
    } catch (error) {
      console.error('Failed to create object URL from media data', error);
      return '';
    }
  };

  const renderMedia = (volunteer) => {
    if (!volunteer.mediaData) return '';

    const mediaUrl = createObjectUrlFromDataUrl(volunteer.mediaData);
    if (!mediaUrl) return '';

    if (volunteer.mediaType && volunteer.mediaType.startsWith('video')) {
      return `
        <div class="mt-3">
          <video class="img-fluid rounded-3 shadow-sm" controls style="max-width: 100%; max-height: 300px;">
            <source src="${mediaUrl}" type="${volunteer.mediaType}">
            Your browser does not support the video tag.
          </video>
        </div>
      `;
    } else {
      return `
        <div class="mt-3">
          <img src="${mediaUrl}" alt="Volunteer media" class="img-fluid rounded-3 shadow-sm" style="max-width: 100%; max-height: 300px; object-fit: cover;">
        </div>
      `;
    }
  };

  // Volunteer registration form logic
  (function initVolunteerForm() {
    const form = document.getElementById('volunteerForm');
    if (!form) return;

    const submitBtn = document.getElementById('submitBtn');
    const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;
    const nom = document.getElementById('nom');
    const numero = document.getElementById('numero');
    const email = document.getElementById('email');
    const type_don = document.getElementById('type_don');
    const description = document.getElementById('description');
    const media = document.getElementById('media');

    if (!submitBtn || !spinner || !nom || !numero || !email || !type_don || !description || !media) {
      return;
    }

    function checkFormValidity() {
      const isValid =
        nom.value.trim() &&
        numero.value.trim() &&
        email.value.trim() &&
        type_don.value.trim() &&
        description.value.trim();
      submitBtn.disabled = !isValid;
    }

    submitBtn.disabled = false;

    nom.addEventListener('input', checkFormValidity);
    numero.addEventListener('input', checkFormValidity);
    email.addEventListener('input', checkFormValidity);
    type_don.addEventListener('change', checkFormValidity);
    description.addEventListener('input', checkFormValidity);
    media.addEventListener('change', checkFormValidity);

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      event.stopPropagation();

      let isValid = true;

      // Validate nom
      const nomErrorIcon = nom.parentElement.querySelector('.error-icon');
      const nomSuccessIcon = nom.parentElement.querySelector('.success-icon');
      if (!nom.value.trim()) {
        nom.classList.add('is-invalid');
        if (nomErrorIcon) nomErrorIcon.style.display = 'block';
        if (nomSuccessIcon) nomSuccessIcon.style.display = 'none';
        isValid = false;
      } else {
        nom.classList.remove('is-invalid');
        if (nomErrorIcon) nomErrorIcon.style.display = 'none';
        if (nomSuccessIcon) nomSuccessIcon.style.display = 'block';
      }

      // Validate numero with pattern (8-15 digits)
      const phonePattern = /^[0-9]{8,15}$/;
      const numeroErrorIcon = numero.parentElement.querySelector('.error-icon');
      const numeroSuccessIcon = numero.parentElement.querySelector('.success-icon');
      if (!numero.value.trim() || !phonePattern.test(numero.value.trim())) {
        numero.classList.add('is-invalid');
        if (numeroErrorIcon) numeroErrorIcon.style.display = 'block';
        if (numeroSuccessIcon) numeroSuccessIcon.style.display = 'none';
        isValid = false;
      } else {
        numero.classList.remove('is-invalid');
        if (numeroErrorIcon) numeroErrorIcon.style.display = 'none';
        if (numeroSuccessIcon) numeroSuccessIcon.style.display = 'block';
      }

      // Validate email with pattern
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const emailErrorIcon = email.parentElement.querySelector('.error-icon');
      const emailSuccessIcon = email.parentElement.querySelector('.success-icon');
      if (!email.value.trim() || !emailPattern.test(email.value.trim())) {
        email.classList.add('is-invalid');
        if (emailErrorIcon) emailErrorIcon.style.display = 'block';
        if (emailSuccessIcon) emailSuccessIcon.style.display = 'none';
        isValid = false;
      } else {
        email.classList.remove('is-invalid');
        if (emailErrorIcon) emailErrorIcon.style.display = 'none';
        if (emailSuccessIcon) emailSuccessIcon.style.display = 'block';
      }

      // Validate type_don
      const typeDonErrorIcon = type_don.parentElement.querySelector('.error-icon');
      const typeDonSuccessIcon = type_don.parentElement.querySelector('.success-icon');
      if (!type_don.value.trim()) {
        type_don.classList.add('is-invalid');
        if (typeDonErrorIcon) typeDonErrorIcon.style.display = 'block';
        if (typeDonSuccessIcon) typeDonSuccessIcon.style.display = 'none';
        isValid = false;
      } else {
        type_don.classList.remove('is-invalid');
        if (typeDonErrorIcon) typeDonErrorIcon.style.display = 'none';
        if (typeDonSuccessIcon) typeDonSuccessIcon.style.display = 'block';
      }

      // Validate description
      const descriptionErrorIcon = description.parentElement.querySelector('.error-icon');
      const descriptionSuccessIcon = description.parentElement.querySelector('.success-icon');
      if (!description.value.trim()) {
        description.classList.add('is-invalid');
        if (descriptionErrorIcon) descriptionErrorIcon.style.display = 'block';
        if (descriptionSuccessIcon) descriptionSuccessIcon.style.display = 'none';
        isValid = false;
      } else {
        description.classList.remove('is-invalid');
        if (descriptionErrorIcon) descriptionErrorIcon.style.display = 'none';
        if (descriptionSuccessIcon) descriptionSuccessIcon.style.display = 'block';
      }

      if (isValid) {
        spinner.classList.remove('d-none');
        submitBtn.disabled = true;

        // Let the form submit naturally to PHP
        form.submit();
      }

      form.classList.add('was-validated');
    });
  })();

  // Volunteer listing page logic - REMOVED: Now handled by AJAX in volunteer-display.php

  // Volunteer update page logic - REMOVED: Now handled by database in volunteer-display.php

  // Volunteer update page logic
  (function initVolunteerUpdate() {
    const updatesContainer = document.getElementById('updatesList');
    if (!updatesContainer) return;

    const activeObjectUrls = [];

    const cleanupObjectUrls = () => {
      while (activeObjectUrls.length) {
        URL.revokeObjectURL(activeObjectUrls.pop());
      }
    };

    const escapeHtml = (value = '') =>
      String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const formatTimestamp = (iso) => {
      const date = iso ? new Date(iso) : new Date();
      if (Number.isNaN(date.getTime())) {
        return 'Updated just now';
      }
      return `Updated on: ${date.toLocaleString()}`;
    };



    const renderUpdates = () => {
      cleanupObjectUrls();
      const volunteers = JSON.parse(localStorage.getItem('volunteers')) || [];

      updatesContainer.innerHTML = '';

      if (!volunteers.length) {
        updatesContainer.innerHTML = `
          <div class="text-center py-5">
            <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No registered volunteers yet</h4>
            <p class="text-muted mb-4">Register a volunteer to start sharing updates and impact stories.</p>
            <a href="volunteer-form.php" class="btn btn-primary px-4">
              <i class="fas fa-plus-circle me-2"></i>Register Volunteer
            </a>
          </div>
        `;
        return;
      }

      volunteers.forEach((volunteer, index) => {
        const card = document.createElement('div');
        card.className = 'volunteer-update-card';

        const updates = volunteer.updates || [];

        const updatesMarkup = updates.length
          ? updates
              .map((update) => {
                const safeDescription = escapeHtml(update.description || '');
                let mediaMarkup = '';

                if (update.mediaData) {
                  const mediaUrl = createObjectUrlFromDataUrl(update.mediaData);
                  if (mediaUrl) {
                    if ((update.mediaType || '').startsWith('video')) {
                      mediaMarkup = `
                        <div class="mt-3">
                          <video class="w-100 rounded-3 shadow-sm" controls preload="metadata">
                            <source src="${mediaUrl}" type="${update.mediaType || 'video/mp4'}">
                            Your browser does not support the video tag.
                          </video>
                        </div>
                      `;
                    } else {
                      mediaMarkup = `
                        <div class="mt-3">
                          <img src="${mediaUrl}" alt="Update media" class="img-fluid rounded-3 shadow-sm">
                        </div>
                      `;
                    }
                  }
                }

                return `
                  <article class="bg-light border border-light rounded-3 p-3 mb-3 shadow-sm">
                    <p class="mb-2 text-secondary">${safeDescription || 'No description provided.'}</p>
                    ${mediaMarkup}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <small class="text-muted">${formatTimestamp(update.timestamp)}</small>
                      <button class="btn btn-outline-danger btn-sm delete-update-btn" data-volunteer-index="${index}" data-update-index="${updates.indexOf(update)}">
                        <i class="fas fa-trash-alt me-1"></i>Delete
                      </button>
                    </div>
                  </article>
                `;
              })
              .join('')
          : `
              <div class="bg-light border border-light rounded-3 p-3 text-center text-muted">
                No updates shared yet. Be the first to add one!
              </div>
            `;

        card.innerHTML = `
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
              <div class="d-flex flex-column flex-md-row align-items-md-center gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                  <img src="assets/images/user/user-01.jpg" alt="Avatar" class="rounded-circle border border-2 border-primary-subtle" style="width: 56px; height: 56px; object-fit: cover;">
                  <div>
                  <h5 class="mb-1 text-dark">${escapeHtml(volunteer.nom || 'Volunteer')}</h5>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-muted small">
                      <span><i class="fas fa-phone me-1 text-primary"></i>${escapeHtml(volunteer.num || 'N/A')}</span>
                      <span><i class="fas fa-envelope me-1 text-success"></i>${escapeHtml(volunteer.email || 'N/A')}</span>
                      <span><i class="fas fa-hand-holding-heart me-1 text-warning"></i>${escapeHtml(volunteer.type_don || 'Not specified')}</span>
                    </div>
                    ${volunteer.mediaData ? renderMedia(volunteer) : ''}
                  </div>
                </div>
                <div class="ms-md-auto">
                  <button class="btn btn-outline-primary px-4 toggle-update-btn" data-index="${index}">
                    <i class="fas fa-sync-alt me-2"></i>Update
                  </button>
                </div>
              </div>

              <div class="update-form-wrapper border border-light rounded-3 p-3 p-md-4 mb-4 bg-light d-none" id="update-form-${index}">
                <form class="volunteer-update-form" data-index="${index}" novalidate>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Share an update</label>
                    <textarea class="form-control form-control-lg rounded-3 shadow-sm" rows="3" placeholder="Describe the latest progress..." required></textarea>
                    <div class="invalid-feedback">Please add a short description.</div>
                  </div>
                  <div class="mb-4">
                    <label class="form-label fw-semibold">Add photo or video</label>
                    <input type="file" class="form-control form-control-lg rounded-3" accept="image/*,video/*">
                    <small class="text-muted d-block mt-2">Supported formats: JPG, PNG, MP4, MOV. Max size 10MB.</small>
                  </div>
                  <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary post-update-btn px-4">
                      <span class="spinner-border spinner-border-sm align-middle d-none" role="status" aria-hidden="true"></span>
                      <span class="label-text">Post Update</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-4 cancel-update-btn" data-index="${index}">
                      Cancel
                    </button>
                  </div>
                </form>
              </div>

              <div class="updates-feed">
                <h6 class="text-uppercase text-muted fw-semibold mb-3">Latest updates</h6>
                ${updatesMarkup}
              </div>
            </div>
          </div>
        `;

        updatesContainer.appendChild(card);
      });
    };

    const toggleFormVisibility = (index, forceClose = false) => {
      const formWrapper = document.getElementById(`update-form-${index}`);
      const toggleBtn = updatesContainer.querySelector(`.toggle-update-btn[data-index="${index}"]`);
      if (!formWrapper || !toggleBtn) return;

      const shouldClose = forceClose || !formWrapper.classList.contains('d-none');
      if (shouldClose) {
        formWrapper.classList.add('d-none');
        toggleBtn.classList.remove('btn-primary', 'text-white');
        toggleBtn.classList.add('btn-outline-primary');
        toggleBtn.innerHTML = `<i class="fas fa-sync-alt me-2"></i>Update`;
        return;
      }

      formWrapper.classList.remove('d-none');
      toggleBtn.classList.remove('btn-outline-primary');
      toggleBtn.classList.add('btn-primary', 'text-white');
      toggleBtn.innerHTML = `<i class="fas fa-times me-2"></i>Close`;
    };

    const handleUpdateSubmit = async (form) => {
      const index = parseInt(form.getAttribute('data-index'), 10);
      if (Number.isNaN(index)) return;

      const descriptionField = form.querySelector('textarea');
      const fileInput = form.querySelector('input[type="file"]');
      const submitBtn = form.querySelector('.post-update-btn');
      const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;
      const labelText = submitBtn ? submitBtn.querySelector('.label-text') : null;

      if (!descriptionField || !submitBtn || !spinner || !labelText) return;

      const description = descriptionField.value.trim();

      if (!description) {
        descriptionField.classList.add('is-invalid');
        form.classList.add('was-validated');
        return;
      }
      descriptionField.classList.remove('is-invalid');

      const file = fileInput && fileInput.files ? fileInput.files[0] : null;

      if (file && file.size > 10 * 1024 * 1024) {
        const message = 'File is too large. Please upload a file smaller than 10MB.';
        if (window.Swal) {
          Swal.fire('File Too Large', message, 'warning');
        } else {
          alert(message);
        }
        return;
      }

      spinner.classList.remove('d-none');
      labelText.classList.add('d-none');
      submitBtn.disabled = true;

      try {
        const volunteers = JSON.parse(localStorage.getItem('volunteers')) || [];
        const volunteer = volunteers[index];
        if (!volunteer) {
          throw new Error('Volunteer not found');
        }

        let mediaDataUrl = null;
        if (file) {
          mediaDataUrl = await readFileAsDataUrl(file);
        }

        const newUpdate = {
          description,
          mediaData: mediaDataUrl,
          mediaType: file ? file.type : null,
          timestamp: new Date().toISOString()
        };

        if (!Array.isArray(volunteer.updates)) {
          volunteer.updates = [];
        }
        volunteer.updates.unshift(newUpdate);

        volunteers[index] = volunteer;
        localStorage.setItem('volunteers', JSON.stringify(volunteers));

        form.reset();
        form.classList.remove('was-validated');
        toggleFormVisibility(index, true);
        renderUpdates();

        if (window.Swal) {
          Swal.fire({
            icon: 'success',
            title: 'Update posted!',
            text: 'Your update is now visible under the volunteer post.',
            timer: 1600,
            showConfirmButton: false
          });
        }
      } catch (error) {
        console.error('Unable to post update', error);
        const message = 'Unable to post update right now. Please try again.';
        if (window.Swal) {
          Swal.fire('Something went wrong', message, 'error');
        } else {
          alert(message);
        }
      } finally {
        spinner.classList.add('d-none');
        labelText.classList.remove('d-none');
        submitBtn.disabled = false;
      }
    };

    const handleDeleteUpdate = (volunteerIndex, updateIndex) => {
      if (window.Swal) {
        Swal.fire({
          title: 'Are you sure?',
          text: 'This update will be permanently deleted.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            const volunteers = JSON.parse(localStorage.getItem('volunteers')) || [];
            if (volunteers[volunteerIndex] && volunteers[volunteerIndex].updates) {
              volunteers[volunteerIndex].updates.splice(updateIndex, 1);
              localStorage.setItem('volunteers', JSON.stringify(volunteers));
              renderUpdates();
              Swal.fire('Deleted!', 'The update has been deleted.', 'success');
            }
          }
        });
      } else {
        if (confirm('Are you sure you want to delete this update?')) {
          const volunteers = JSON.parse(localStorage.getItem('volunteers')) || [];
          if (volunteers[volunteerIndex] && volunteers[volunteerIndex].updates) {
            volunteers[volunteerIndex].updates.splice(updateIndex, 1);
            localStorage.setItem('volunteers', JSON.stringify(volunteers));
            renderUpdates();
          }
        }
      }
    };

    updatesContainer.addEventListener('click', (event) => {
      const toggleBtn = event.target.closest('.toggle-update-btn');
      if (toggleBtn) {
        const index = parseInt(toggleBtn.getAttribute('data-index'), 10);
        if (!Number.isNaN(index)) {
          toggleFormVisibility(index);
        }
        return;
      }

      const cancelBtn = event.target.closest('.cancel-update-btn');
      if (cancelBtn) {
        const index = parseInt(cancelBtn.getAttribute('data-index'), 10);
        if (!Number.isNaN(index)) {
          toggleFormVisibility(index, true);
        }
        return;
      }

      const deleteBtn = event.target.closest('.delete-update-btn');
      if (deleteBtn) {
        const volunteerIndex = parseInt(deleteBtn.getAttribute('data-volunteer-index'), 10);
        const updateIndex = parseInt(deleteBtn.getAttribute('data-update-index'), 10);
        if (!Number.isNaN(volunteerIndex) && !Number.isNaN(updateIndex)) {
          handleDeleteUpdate(volunteerIndex, updateIndex);
        }
      }
    });

    updatesContainer.addEventListener('submit', (event) => {
      const form = event.target.closest('.volunteer-update-form');
      if (form) {
        event.preventDefault();
        event.stopPropagation();
        handleUpdateSubmit(form);
      }
    });

    renderUpdates();
  })();
});

