(function(){
  var fileInput = document.getElementById('media');
  var newPreview = document.getElementById('mediaPreview');
  var existingInput = document.getElementById('existing_media');
  var existingGrid = document.getElementById('existingMediaGrid');
  var dropzone = document.getElementById('mediaDropzone');
  var dt = new DataTransfer();

  // Parse existing files from hidden input
  var existingFiles = [];
  try { existingFiles = JSON.parse(existingInput.value || '[]'); } catch(e){ existingFiles = []; }

  function updateExistingInput(){ existingInput.value = JSON.stringify(existingFiles); }

  // Delegate remove for existing items
  if (existingGrid) {
    existingGrid.addEventListener('click', function(e){
      var btn = e.target.closest('.media-remove-btn');
      if(!btn) return;
      var item = btn.closest('.media-preview-item');
      if(!item) return;
      var name = item.getAttribute('data-filename');
      // remove from DOM and from existingFiles
      item.remove();
      existingFiles = existingFiles.filter(function(n){ return n !== name; });
      updateExistingInput();
    });
  }

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

  // Ensure form submit contains current state
  var form = document.getElementById('volunteerForm');
  if(form){
    form.addEventListener('submit', function(){ fileInput.files = dt.files; existingInput.value = JSON.stringify(existingFiles); });
  }
})();

