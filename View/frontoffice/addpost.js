document.addEventListener("DOMContentLoaded", function () {

  const form = document.querySelector("form");

  // 🔹 Ajouter dynamiquement les placeholders
  form.titre.placeholder = "Ex : Aide pour une école sans mur";
  form.description.placeholder = "Décris le besoin ou le problème...";
  form.nom.placeholder = "Votre nom";
  form.numero.placeholder = "Ex : 12345678";
  form.email.placeholder = "Ex : exemple@gmail.com";

  // --- Fonction pour afficher un message d'erreur ou de succès ---
  function showMessage(input, msg, color) {
    let span = input.nextElementSibling;
    if (!span || span.tagName !== "SPAN") {
      span = document.createElement("span");
      span.style.display = "block";
      span.style.marginTop = "3px";
      input.parentNode.appendChild(span);
    }
    span.textContent = msg;
    span.style.color = color;
    span.style.fontSize = "13px";
  }

  // ---------------- Validation dynamique ----------------

  // 🔹 Titre
  const titreInput = form.titre;
  titreInput.addEventListener("keyup", () => {
    if (titreInput.value.trim().length < 3)
      showMessage(titreInput, "Le titre doit contenir au moins 3 caractères.", "red");
    else
      showMessage(titreInput, "Titre correct", "green");
  });

  // 🔹 Nom
  const nomInput = form.nom;
  nomInput.addEventListener("keyup", () => {
    if (nomInput.value.trim().length < 2)
      showMessage(nomInput, "Le nom doit contenir au moins 2 caractères.", "red");
    else
      showMessage(nomInput, "Nom correct", "green");
  });

  // 🔹 Numéro de téléphone
  const numeroInput = form.numero;
  const regexTel = /^[0-9]{8,15}$/; // Numéro entre 8 et 15 chiffres
  numeroInput.addEventListener("blur", () => {
    if (!regexTel.test(numeroInput.value.trim()))
      showMessage(numeroInput, "Numéro invalide (8 à 15 chiffres requis).", "red");
    else
      showMessage(numeroInput, "Numéro valide", "green");
  });

  // 🔹 Email
  const emailInput = form.email;
  const regexMail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  emailInput.addEventListener("blur", () => {
    if (!regexMail.test(emailInput.value.trim()))
      showMessage(emailInput, "Adresse e-mail invalide.", "red");
    else
      showMessage(emailInput, "Email valide", "green");
  });

  // 🔹 Région
  const regionSelect = form.region;
  regionSelect.addEventListener("change", () => {
    if (!regionSelect.value)
      showMessage(regionSelect, "Veuillez choisir une région.", "red");
    else
      showMessage(regionSelect, "Région sélectionnée", "green");
  });

  // 🔹 Description
  const descInput = form.description;
  descInput.addEventListener("keyup", () => {
    if (descInput.value.trim().length < 10)
      showMessage(descInput, "La description doit contenir au moins 10 caractères.", "red");
    else
      showMessage(descInput, "Description correcte", "green");
  });

  // 🔹 Photo (optionnel)
  const mediaInput = form.media;
  mediaInput.addEventListener("change", () => {
    const file = mediaInput.files[0];
    if (file) {
      const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
      const maxSize = 5 * 1024 * 1024; // 5MB
      if (!allowedTypes.includes(file.type)) {
        showMessage(mediaInput, "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.", "red");
      } else if (file.size > maxSize) {
        showMessage(mediaInput, "La taille du fichier ne doit pas dépasser 5MB.", "red");
      } else {
        showMessage(mediaInput, "Photo valide", "green");
      }
    } else {
      showMessage(mediaInput, "Aucune photo sélectionnée", "orange");
    }
  });

  // ---------------- Validation finale lors du submit ----------------
  form.addEventListener("submit", function(e) {
    let valid = true;

    // Titre
    if (titreInput.value.trim().length < 3) {
      showMessage(titreInput, "Le titre doit contenir au moins 3 caractères.", "red");
      valid = false;
    }

    // Nom
    if (nomInput.value.trim().length < 2) {
      showMessage(nomInput, "Le nom doit contenir au moins 2 caractères.", "red");
      valid = false;
    }

    // Numéro de téléphone
    if (!regexTel.test(numeroInput.value.trim())) {
      showMessage(numeroInput, "Numéro invalide (8 à 15 chiffres requis).", "red");
      valid = false;
    }

    // Email
    if (!regexMail.test(emailInput.value.trim())) {
      showMessage(emailInput, "Adresse e-mail invalide.", "red");
      valid = false;
    }

    // Région
    if (!regionSelect.value) {
      showMessage(regionSelect, "Veuillez choisir une région.", "red");
      valid = false;
    }

    // Description
    if (descInput.value.trim().length < 10) {
      showMessage(descInput, "La description doit contenir au moins 10 caractères.", "red");
      valid = false;
    }

    // Photo (optionnel, mais si sélectionnée, vérifier)
    const file = mediaInput.files[0];
    if (file) {
      const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
      const maxSize = 5 * 1024 * 1024; // 5MB
      if (!allowedTypes.includes(file.type)) {
        showMessage(mediaInput, "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.", "red");
        valid = false;
      } else if (file.size > maxSize) {
        showMessage(mediaInput, "La taille du fichier ne doit pas dépasser 5MB.", "red");
        valid = false;
      }
    }

    if (!valid) {
      e.preventDefault(); // Empêche l'envoi si invalide
    } else {
      alert("✅ Formulaire validé avec succès !");
    }
  });

});
