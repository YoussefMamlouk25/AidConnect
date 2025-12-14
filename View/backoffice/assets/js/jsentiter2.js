document.addEventListener("DOMContentLoaded", function () {
  try {
    console.log('jsentiter2: loaded');

    const form = document.querySelector('form');
    console.log('jsentiter2: form=', form);
    if (!form) return;

    // helper: cherche un champ par plusieurs noms/ids
    function getField(candidates) {
      for (const name of candidates) {
        if (!name) continue;
        // par name dans form
        if (form.elements && form.elements[name]) return form.elements[name];
        // par querySelector
        const elByName = form.querySelector('[name="' + name + '"]');
        if (elByName) return elByName;
        const elById = document.getElementById(name);
        if (elById && form.contains(elById)) return elById;
      }
      return null;
    }

    // Champs possibles pour addpostupdate / updatepostupdate
    const texteInput = getField(['texte', 'description']);
    const interventionInput = getField(['intervention']);
    const joursReponseInput = getField(['joursReponse']);
    const joursResolutionInput = getField(['joursResolution']);
    const photoInput = (getField(['photo', 'media']) || form.querySelector('input[type=file]'));

    console.log('jsentiter2: fields =>', {
      texteInput, interventionInput, joursReponseInput, joursResolutionInput, photoInput
    });

    // placeholders (sécurisé)
    function safeSetPlaceholder(el, text) {
      try {
        if (!el) return;
        // only set if element is input/textarea/select and has placeholder property
        const tag = el.tagName ? el.tagName.toLowerCase() : '';
        if (tag === 'input' || tag === 'textarea' || tag === 'select') {
          if (typeof el.placeholder !== 'undefined') el.placeholder = text;
        }
      } catch (err) { console.warn('placeholder error', err); }
    }
    safeSetPlaceholder(texteInput, "Décrivez la résolution...");
    safeSetPlaceholder(interventionInput, "Intervention réalisée par...");
    safeSetPlaceholder(joursReponseInput, "Ex : 2");
    safeSetPlaceholder(joursResolutionInput, "Ex : 7");

    // show message
    function showMessage(input, msg, color) {
      if (!input) return;
      let span = input.nextElementSibling;
      if (!span || span.tagName !== 'SPAN') {
        span = document.createElement('span');
        span.style.display = 'block';
        span.style.marginTop = '4px';
        input.parentNode.appendChild(span);
      }
      span.textContent = msg;
      span.style.color = color;
      span.style.fontSize = '13px';
    }
    function clearMessage(input) {
      if (!input) return;
      const span = input.nextElementSibling;
      if (span && span.tagName === 'SPAN') span.remove();
    }

    // media preview utility (robuste si input est dans un label)
    let currentObjectUrl = null;
    function ensureMediaLinkExists(photoEl) {
      let link = document.getElementById('media-link');
      if (!link) {
        link = document.createElement('a');
        link.id = 'media-link';
        link.style.display = 'inline-flex';
        link.style.alignItems = 'center';
        link.style.gap = '8px';
        link.style.marginLeft = '8px';
        link.style.textDecoration = 'none';
        link.style.color = '#0057d8';
        // insérer après l'input si possible
        try {
          if (photoEl && photoEl.parentNode) {
            if (photoEl.nextSibling) photoEl.parentNode.insertBefore(link, photoEl.nextSibling);
            else photoEl.parentNode.appendChild(link);
          } else document.body.appendChild(link);
        } catch (e) {
          document.body.appendChild(link);
        }
      }
      return link;
    }

    function setMediaPreview(photoEl) {
      if (!photoEl) return;
      const file = photoEl.files && photoEl.files[0];
      const link = ensureMediaLinkExists(photoEl);
      if (!file) {
        link.style.display = 'none';
        if (currentObjectUrl) URL.revokeObjectURL(currentObjectUrl);
        currentObjectUrl = null;
        return;
      }
      const allowed = ['image/jpeg','image/png','image/gif'];
      if (!allowed.includes(file.type)) { link.style.display = 'none'; return; }
      if (currentObjectUrl) URL.revokeObjectURL(currentObjectUrl);
      const url = URL.createObjectURL(file);
      currentObjectUrl = url;
      link.href = url; link.target = '_blank';
      link.textContent = 'Prévisualiser la photo';
      let img = link.querySelector('img');
      if (!img) {
        img = document.createElement('img');
        img.style.width = '70px';
        img.style.height = '70px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '6px';
        img.alt = 'Prévisualisation';
        link.insertBefore(img, link.firstChild);
      }
      img.src = url;
      link.style.display = 'inline-flex';
    }

    // attach photo listener
    if (photoInput) {
      photoInput.addEventListener('change', function () {
        const file = photoInput.files && photoInput.files[0];
        if (!file) { clearMessage(photoInput); setMediaPreview(photoInput); return; }
        const allowed = ['image/jpeg','image/png','image/gif'];
        const maxSize = 5 * 1024 * 1024;
        if (!allowed.includes(file.type)) showMessage(photoInput,'Format invalide (JPG/PNG/GIF)','red');
        else if (file.size > maxSize) showMessage(photoInput,'Max 5MB','red');
        else showMessage(photoInput,'Photo valide','green');
        setMediaPreview(photoInput);
      });
      // init preview if a file is already selected (edit form)
      if (photoInput.files && photoInput.files.length > 0) setMediaPreview(photoInput);
    }

    // dynamic validation like addpost.js
    if (texteInput) {
      texteInput.addEventListener('keyup', function () {
        if (texteInput.value.trim().length < 10) showMessage(texteInput, 'La description doit contenir au moins 10 caractères.', 'red');
        else showMessage(texteInput, 'Description correcte', 'green');
      });
    }
    if (interventionInput) {
      interventionInput.addEventListener('keyup', function () {
        if (interventionInput.value.trim().length < 3) showMessage(interventionInput, 'Minimum 3 caractères.', 'red');
        else showMessage(interventionInput, 'Intervention correcte', 'green');
      });
    }
    if (joursReponseInput) {
      joursReponseInput.addEventListener('blur', function () {
        const v = parseInt(joursReponseInput.value);
        if (isNaN(v) || v < 1) showMessage(joursReponseInput, 'Minimum 1 jour.', 'red');
        else showMessage(joursReponseInput, 'OK', 'green');
      });
    }
    if (joursResolutionInput) {
      joursResolutionInput.addEventListener('blur', function () {
        const v = parseInt(joursResolutionInput.value);
        if (isNaN(v) || v < 1) showMessage(joursResolutionInput, 'Minimum 1 jour.', 'red');
        else showMessage(joursResolutionInput, 'OK', 'green');
      });
    }

    // final submit validation
    form.addEventListener('submit', function (e) {
      let valid = true;
      if (texteInput && texteInput.value.trim().length < 10) { showMessage(texteInput, 'La description doit contenir au moins 10 caractères.', 'red'); valid = false; }
      if (interventionInput && interventionInput.value.trim().length < 3) { showMessage(interventionInput, 'Minimum 3 caractères.', 'red'); valid = false; }
      if (joursReponseInput) { const v = parseInt(joursReponseInput.value); if (isNaN(v) || v < 1) { showMessage(joursReponseInput, 'Minimum 1 jour.', 'red'); valid = false; } }
      if (joursResolutionInput) { const v2 = parseInt(joursResolutionInput.value); if (isNaN(v2) || v2 < 1) { showMessage(joursResolutionInput, 'Minimum 1 jour.', 'red'); valid = false; } }
      if (photoInput && photoInput.files && photoInput.files.length > 0) { const f = photoInput.files[0]; const allowed = ['image/jpeg','image/png','image/gif']; if (!allowed.includes(f.type)) { showMessage(photoInput, 'Type de fichier non autorisé.', 'red'); valid = false; } if (f.size > 5 * 1024 * 1024) { showMessage(photoInput, 'La taille du fichier ne doit pas dépasser 5MB.', 'red'); valid = false; } }

      if (!valid) {
        e.preventDefault();
        // focus first invalid
        const firstErr = form.querySelector('span');
        if (firstErr) {
          const el = firstErr.previousElementSibling;
          if (el && el.focus) el.focus();
        }
      }
    });

  } catch (err) {
    console.error('jsentiter2 error:', err);
  }
});
