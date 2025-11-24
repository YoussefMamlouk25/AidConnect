(function(){
  var fileInput = document.getElementById('media');
  var preview = document.getElementById('mediaPreview');
  var dropzone = document.getElementById('mediaDropzone');
  // Use DataTransfer to manage a mutable file list
  var dt = new DataTransfer();

  function addFiles(files) {
    Array.prototype.forEach.call(files, function(f){
      var exists = Array.prototype.some.call(dt.files, function(existing){
        return existing.name === f.name && existing.size === f.size && existing.lastModified === f.lastModified;
      });
      if(!exists){ dt.items.add(f); }
    });
    fileInput.files = dt.files;
    renderPreviews();
    if(dt.files.length > 0 && dropzone) {
      dropzone.classList.add('has-files');
    } else if(dropzone) {
      dropzone.classList.remove('has-files');
    }
  }

  function renderPreviews(){
    preview.innerHTML = '';
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
      reader.onload = function(e){ mediaElement.src = e.target.result; };
      reader.readAsDataURL(file);
      
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'media-remove-btn';
      btn.innerHTML = '&times;';
      btn.setAttribute('aria-label','Remove photo');
      btn.addEventListener('click', function(){ removeFile(idx); });
      item.appendChild(mediaElement);
      item.appendChild(btn);
      preview.appendChild(item);
    });
  }

  function removeFile(index){
    var newDt = new DataTransfer();
    Array.prototype.forEach.call(dt.files, function(f,i){
      if(i!==index) newDt.items.add(f);
    });
    dt = newDt;
    fileInput.files = dt.files;
    renderPreviews();
    if(dt.files.length > 0 && dropzone) {
      dropzone.classList.add('has-files');
    } else if(dropzone) {
      dropzone.classList.remove('has-files');
    }
  }

  fileInput.addEventListener('change', function(e){
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

  // Ensure when the form is submitted, fileInput.files reflects current dt
  var form = document.getElementById('volunteerForm');
  if(form){
    form.addEventListener('submit', function(){ fileInput.files = dt.files; });
  }
})();

