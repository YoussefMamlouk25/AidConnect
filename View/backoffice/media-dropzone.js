(function () {
  function initDropzone(dropzone) {
    if (!dropzone) return;
    var fileInput = dropzone.querySelector('.media-file-input');
    if (!fileInput) return;

    var previewTargetId = dropzone.getAttribute('data-preview-target');
    var preview =
      (previewTargetId && document.getElementById(previewTargetId)) ||
      dropzone.parentElement.querySelector('.media-preview-grid');
    if (!preview) {
      // Create preview container if it doesn't exist
      preview = document.createElement('div');
      preview.className = 'media-preview-grid';
      preview.id = previewTargetId || 'mediaPreview';
      dropzone.parentElement.appendChild(preview);
    }

    var allowMultiple = fileInput.hasAttribute('multiple');
    var dt = new DataTransfer();
    var maxFilesAttr = dropzone.getAttribute('data-max-files');
    var maxFiles = maxFilesAttr ? parseInt(maxFilesAttr, 10) : null;
    if (Number.isNaN(maxFiles)) {
      maxFiles = null;
    }
    var errorTargetId = dropzone.getAttribute('data-error-target');
    var errorEl = errorTargetId ? document.getElementById(errorTargetId) : null;

    function setError(message) {
      if (!errorEl) return;
      errorEl.textContent = message || '';
      errorEl.style.display = message ? 'block' : 'none';
    }

    function syncState() {
      fileInput.files = dt.files;
      dropzone.classList.toggle('has-files', dt.files.length > 0);
    }

    function isDuplicate(file) {
      return Array.prototype.some.call(dt.files, function (existing) {
        return (
          existing.name === file.name &&
          existing.size === file.size &&
          existing.lastModified === file.lastModified
        );
      });
    }

    function addFiles(files) {
      if (!files || !files.length) return;
      var limitReached = false;
      Array.prototype.forEach.call(files, function (file) {
        if (!allowMultiple) {
          dt = new DataTransfer();
        } else if (maxFiles && dt.files.length >= maxFiles) {
          limitReached = true;
          return;
        } else if (isDuplicate(file)) {
          return;
        }

        // Validate file type
        var allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (file.type && !allowedTypes.includes(file.type) && !file.type.startsWith('video/')) {
          setError('Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou vidéo.');
          return;
        }

        dt.items.add(file);
      });

      if (limitReached) {
        setError(
          'Vous pouvez sélectionner au maximum ' +
            maxFiles +
            ' fichier' +
            (maxFiles > 1 ? 's' : '')
        );
      } else {
        setError('');
      }

      syncState();
      renderPreviews();
    }

    function renderPreviews() {
      preview.innerHTML = '';
      Array.prototype.forEach.call(dt.files, function (file, index) {
        var item = document.createElement('div');
        item.className = 'media-preview-item';
        var isVideo = file.type && file.type.indexOf('video/') === 0;
        var mediaElement = document.createElement(isVideo ? 'video' : 'img');
        if (isVideo) {
          mediaElement.controls = true;
          mediaElement.className = 'media-preview-video';
        }
        var reader = new FileReader();
        reader.onload = function (e) {
          mediaElement.src = e.target.result;
        };
        reader.readAsDataURL(file);

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'media-remove-btn';
        removeBtn.innerHTML = '&times;';
        removeBtn.setAttribute('aria-label', 'Remove media');
        removeBtn.addEventListener('click', function () {
          removeFile(index);
        });

        item.appendChild(mediaElement);
        item.appendChild(removeBtn);
        preview.appendChild(item);
      });
    }

    function removeFile(index) {
      var newDt = new DataTransfer();
      Array.prototype.forEach.call(dt.files, function (file, idx) {
        if (idx !== index) {
          newDt.items.add(file);
        }
      });
      dt = newDt;
      syncState();
      renderPreviews();
      setError('');
    }

    fileInput.addEventListener('change', function () {
      addFiles(fileInput.files);
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
      dropzone.addEventListener(eventName, function (e) {
        e.preventDefault();
        e.stopPropagation();
      });
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
      dropzone.addEventListener(eventName, function () {
        dropzone.classList.add('drag-over');
      });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
      dropzone.addEventListener(eventName, function (e) {
        if (eventName === 'dragleave' && dropzone.contains(e.relatedTarget)) {
          return;
        }
        dropzone.classList.remove('drag-over');
      });
    });

    dropzone.addEventListener('drop', function (e) {
      var files = e.dataTransfer.files;
      if (files && files.length) {
        addFiles(files);
      }
    });

    var form = fileInput.form;
    if (form) {
      form.addEventListener('submit', function () {
        syncState();
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    var dropzones = document.querySelectorAll('.media-dropzone');
    dropzones.forEach(initDropzone);
  });
})();

