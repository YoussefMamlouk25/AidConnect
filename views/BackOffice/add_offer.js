(function () {
  function bindAddForm() {
    var form = document.querySelector('.fragment-offer-edit form');
    if (!form || form.dataset.offerBound === '1') return;
    if (typeof loadContent !== 'function') return;
    form.dataset.offerBound = '1';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var action = form.getAttribute('action') || window.location.href;
      var fd = new FormData(form);
      fetch(action, { method: 'POST', body: fd })
        .then(function (resp) { return resp.text(); })
        .then(function (html) {
          var container = document.getElementById('content-area');
          if (container) {
            container.innerHTML = html;
            if (typeof initOfferFragments === 'function') {
              initOfferFragments();
            }
            if (typeof initAddOfferFragments === 'function') {
              initAddOfferFragments();
            }
          } else {
            document.open(); document.write(html); document.close();
          }
          // Check if form was submitted successfully (no error message)
          var errorAlert = container ? container.querySelector('.alert-danger') : null;
          if (!errorAlert && typeof loadContent === 'function') {
            // Reload offers list after successful creation
            setTimeout(function() {
              loadContent('showoffer.php');
            }, 1500);
          }
        })
        .catch(function (err) {
          console.error('AJAX submit error', err);
          alert('Erreur lors de la requête.');
        });
    });
  }

  window.initAddOfferFragments = function () {
    bindAddForm();
    initMediaEditors('.fragment-offer-edit');
    document.querySelectorAll('#content-area .fragment-offer-edit, .fragment-offer-edit').forEach(function(c){ initMediaEditors(null, c); });
  };

  // Initialize media preview/remove behavior
  function initMediaEditors(fragmentSelector, containerOverride) {
    var container = containerOverride || (fragmentSelector ? document.querySelector(fragmentSelector) : null);
    if (!container) {
      container = document.querySelector('#content-area .fragment-offer-edit') || document.querySelector('.fragment-offer-edit');
    }
    if (!container) return;
    if (container.dataset.mediaBound === '1') return;
    container.dataset.mediaBound = '1';

    var fileInput = container.querySelector('#media');
    var newPreview = container.querySelector('#mediaPreview');
    var dropzone = container.querySelector('#mediaDropzone');
    if (!fileInput) return;

    var dt = new DataTransfer();

    function addFiles(files) {
      Array.prototype.forEach.call(files, function(f){
        var exists = Array.prototype.some.call(dt.files, function(ex){
          return ex.name===f.name && ex.size===f.size && ex.lastModified===f.lastModified;
        });
        if(!exists) dt.items.add(f);
      });
      fileInput.files = dt.files;
      renderNewPreviews();
      if(dt.files.length > 0 && dropzone) {
        dropzone.classList.add('has-files');
      } else if(dropzone) {
        dropzone.classList.remove('has-files');
      }
    }

    function renderNewPreviews(){
      if(!newPreview) return;
      newPreview.innerHTML = '';
      Array.prototype.forEach.call(dt.files, function(file, idx){
        var item = document.createElement('div');
        item.className = 'media-preview-item';
        var isVideo = file.type && file.type.startsWith('video/');
        var mediaElement;
        
        if (isVideo) {
          mediaElement = document.createElement('video');
          mediaElement.controls = true;
          mediaElement.className = 'media-preview-video';
        } else {
          mediaElement = document.createElement('img');
        }
        
        var reader = new FileReader();
        reader.onload = function(ev){ mediaElement.src = ev.target.result; };
        reader.readAsDataURL(file);
        
        var btn = document.createElement('button');
        btn.type='button';
        btn.className='media-remove-btn';
        btn.innerHTML='&times;';
        btn.addEventListener('click', function(){ removeNewFile(idx); });
        item.appendChild(mediaElement);
        item.appendChild(btn);
        newPreview.appendChild(item);
      });
    }

    function removeNewFile(index){
      var newDt = new DataTransfer();
      Array.prototype.forEach.call(dt.files, function(f,i){
        if(i!==index) newDt.items.add(f);
      });
      dt = newDt;
      fileInput.files = dt.files;
      renderNewPreviews();
      if(dt.files.length > 0 && dropzone) {
        dropzone.classList.add('has-files');
      } else if(dropzone) {
        dropzone.classList.remove('has-files');
      }
    }

    fileInput.addEventListener('change', function(){
      addFiles(fileInput.files);
    });

    // Drag and drop functionality
    if (dropzone) {
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function(e) {
          e.preventDefault();
          e.stopPropagation();
        });
      });

      ['dragenter', 'dragover'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function() {
          dropzone.classList.add('drag-over');
        });
      });

      ['dragleave', 'drop'].forEach(function(eventName) {
        dropzone.addEventListener(eventName, function() {
          dropzone.classList.remove('drag-over');
        });
      });

      dropzone.addEventListener('drop', function(e) {
        var files = e.dataTransfer.files;
        if (files.length > 0) {
          addFiles(files);
        }
      });
    }

    var form = container.querySelector('form');
    if(form){
      form.addEventListener('submit', function(){
        fileInput.files = dt.files;
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initAddOfferFragments);
  } else {
    window.initAddOfferFragments();
  }
})();

