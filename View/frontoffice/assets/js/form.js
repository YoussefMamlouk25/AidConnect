// form.js — simple, robust validation for volunteer-form.php
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('volunteerForm');
  if (!form) return;

  const fields = {
    nom: document.getElementById('nom'),
    numero: document.getElementById('numero'),
    email: document.getElementById('email'),
    type_don: document.getElementById('type_don'),
    description: document.getElementById('description'),
    media: document.getElementById('media')
  };

  const submitBtn = document.getElementById('submitBtn');
  const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

  // Patterns
  const phonePattern = /^[0-9]{8,15}$/;
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // Helpers to show/hide errors consistently
  function setError(id, message) {
    const input = fields[id];
    const error = document.getElementById(`${id}Error`);
    if (input) input.classList.add('is-invalid');
    if (error) {
      error.textContent = message || '';
      error.style.display = message ? 'block' : 'none';
    }
  }

  function clearError(id) {
    const input = fields[id];
    const error = document.getElementById(`${id}Error`);
    if (input) input.classList.remove('is-invalid');
    if (error) {
      error.textContent = '';
      error.style.display = 'none';
    }
  }

  // Field validators — return true/false and manage messages
  function validateNom() {
    const v = (fields.nom.value || '').trim();
    if (!v) { setError('nom', 'Le nom est requis.'); return false; }
    clearError('nom'); return true;
  }

  function validateNumero() {
    const v = (fields.numero.value || '').trim();
    if (!v) { setError('numero', 'Le numéro est requis.'); return false; }
    if (!phonePattern.test(v)) { setError('numero', 'Le numéro doit contenir 8 à 15 chiffres.'); return false; }
    clearError('numero'); return true;
  }

  function validateEmail() {
    const v = (fields.email.value || '').trim();
    if (!v) { setError('email', "L'email est requis."); return false; }
    if (!emailPattern.test(v)) { setError('email', 'Adresse email invalide.'); return false; }
    clearError('email'); return true;
  }

  function validateTypeDon() {
    const v = (fields.type_don.value || '').trim();
    if (!v) { setError('type_don', 'Veuillez sélectionner un type de don.'); return false; }
    clearError('type_don'); return true;
  }

  function validateDescription() {
    const v = (fields.description.value || '').trim();
    if (!v) { setError('description', 'La description est requise.'); return false; }
    clearError('description'); return true;
  }

  // Media is optional; validate only if provided (size and basic type)
  function validateMedia() {
    const input = fields.media;
    const file = input && input.files ? input.files[0] : null;
    if (!file) { clearError('media'); return true; } // optional

    const maxSize = 10 * 1024 * 1024; // 10MB
    const allowed = [
      'image/jpeg', 'image/png', 'image/webp',
      'video/mp4', 'video/quicktime'
    ];

    if (file.size > maxSize) {
      setError('media', 'Le fichier est trop volumineux (max 10MB).');
      return false;
    }
    if (!allowed.includes(file.type)) {
      setError('media', 'Type de fichier non pris en charge (JPG, PNG, WEBP, MP4, MOV).');
      return false;
    }
    clearError('media'); return true;
  }

  function validateAll() {
    const results = [
      validateNom(),
      validateNumero(),
      validateEmail(),
      validateTypeDon(),
      validateDescription(),
      validateMedia()
    ];
    return results.every(Boolean);
  }

  function refreshSubmitState() {
    // We don't want to require media; so skip media in gating state
    const ready =
      validateNom() &&
      validateNumero() &&
      validateEmail() &&
      validateTypeDon() &&
      validateDescription();
    if (submitBtn) submitBtn.disabled = !ready;
  }

  // Attach real-time listeners
  fields.nom.addEventListener('input', () => { validateNom(); refreshSubmitState(); });
  fields.numero.addEventListener('input', () => { validateNumero(); refreshSubmitState(); });
  fields.email.addEventListener('input', () => { validateEmail(); refreshSubmitState(); });
  fields.type_don.addEventListener('change', () => { validateTypeDon(); refreshSubmitState(); });
  fields.description.addEventListener('input', () => { validateDescription(); refreshSubmitState(); });
  fields.media.addEventListener('change', () => { validateMedia(); });

  // Initial state
  refreshSubmitState();

  // Submit
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!validateAll()) {
      // Focus first invalid input
      const firstInvalid =
        Object.values(fields).find(el => el.classList.contains('is-invalid'));
      if (firstInvalid && typeof firstInvalid.focus === 'function') {
        firstInvalid.focus();
      }
      return;
    }
    if (spinner) spinner.classList.remove('d-none');
    if (submitBtn) submitBtn.disabled = true;
    form.submit();
  });
});

