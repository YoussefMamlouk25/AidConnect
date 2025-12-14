document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[method="POST"][enctype="multipart/form-data"]');
  if (!form) return;

  const numero = form.querySelector('input[name="numero"]');
  const email  = form.querySelector('input[name="email"]');
  const nom    = form.querySelector('input[name="nom"]');
  const type   = form.querySelector('select[name="type_don"]');
  const desc   = form.querySelector('textarea[name="description"]');

  const phoneRe = /^[0-9]{8,15}$/;
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  form.addEventListener('submit', (e) => {
    let ok = true;
    if (!nom.value.trim()) ok = false;
    if (!phoneRe.test((numero.value || '').trim())) ok = false;
    if (!emailRe.test((email.value || '').trim())) ok = false;
    if (!type.value.trim()) ok = false;
    if ((desc.value || '').trim().length < 5) ok = false;

    if (!ok) {
      e.preventDefault();
      alert('Veuillez vérifier les champs du formulaire.');
    }
  });
});