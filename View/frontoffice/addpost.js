document.addEventListener("DOMContentLoaded", function() {

  const form = document.querySelector("form");

  // 🔹 Ajouter dynamiquement les placeholders
  form.titre.placeholder = "Ex : Aide pour une école sans mur";
  form.description.placeholder = "Décris le besoin ou le problème...";
  form.contact.placeholder = "Nom ou organisation";
  form.telephone.placeholder = "Ex : 55 555 555";
  form.email.placeholder = "Ex : exemple@gmail.com";
  form.datePublication.placeholder = "Sélectionnez une date";

  // --- Fonction pour afficher un message d'erreur ou de succès ---
  function showMessage(input, msg, color) {
    let span = input.nextElementSibling;
    
    // Si le span n'existe pas, on le crée
    if (!span || span.tagName !== "SPAN") {
      span = document.createElement("span");
      span.style.display = "block"; // Pour le mettre sous le champ
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

  // 🔹 Description
  const descInput = form.description;
  descInput.addEventListener("keyup", () => {
    if (descInput.value.trim().length < 10)
      showMessage(descInput, "La description doit contenir au moins 10 caractères.", "red");
    else
      showMessage(descInput, "Description correcte", "green");
  });

  // 🔹 Type d’aide
  const typeSelect = form.type;
  typeSelect.addEventListener("change", () => {
    if (!typeSelect.value)
      showMessage(typeSelect, "Veuillez choisir un type d’aide.", "red");
    else
      showMessage(typeSelect, "Type sélectionné", "green");
  });

  // 🔹 Région
  const regionSelect = form.region;
  regionSelect.addEventListener("change", () => {
    if (!regionSelect.value)
      showMessage(regionSelect, "Veuillez choisir une région.", "red");
    else
      showMessage(regionSelect, "Région sélectionnée", "green");
  });

  // 🔹 Téléphone
  const telInput = form.telephone;
  const regexTel = /^[0-9]{8}$/; // Numéro tunisien à 8 chiffres
  telInput.addEventListener("blur", () => {
    if (!regexTel.test(telInput.value.trim()))
      showMessage(telInput, "Numéro invalide (8 chiffres requis).", "red");
    else
      showMessage(telInput, "Numéro valide", "green");
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
/*
  // 🔹 Date de publication
  const dateInput = form.datePublication;
  dateInput.addEventListener("change", () => {
    if (!dateInput.value) {
      showMessage(dateInput, "Veuillez sélectionner une date de publication.", "red");
    } else {
      const selectedDate = new Date(dateInput.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (selectedDate > today) {
        showMessage(dateInput, "La date ne peut pas être dans le futur.", "red");
      } else {
        showMessage(dateInput, "Date valide", "green");
      }
    }
  });
/*
  // 🔹 Photo
  const photoInput = form.photo;
  photoInput.addEventListener("change", () => {
    const file = photoInput.files[0];
    if (file) {
      const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
      const maxSize = 5 * 1024 * 1024; // 5MB
      if (!allowedTypes.includes(file.type)) {
        showMessage(photoInput, "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.", "red");
      } else if (file.size > maxSize) {
        showMessage(photoInput, "La taille du fichier ne doit pas dépasser 5MB.", "red");
      } else {
        showMessage(photoInput, "Photo valide", "green");
      }
    } else {
      showMessage(photoInput, "Aucune photo sélectionnée", "orange");
    }
  });
*/
  // ---------------- Validation finale lors du submit ----------------
  form.addEventListener("submit", function(e) {
    let valid = true;

    // Titre
    if (titreInput.value.trim().length < 3) {
      showMessage(titreInput, "Le titre doit contenir au moins 3 caractères.", "red");
      valid = false;
    }

    // Type d’aide
    if (!typeSelect.value) {
      showMessage(typeSelect, "Veuillez choisir un type d’aide.", "red");
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

    // Téléphone
    if (!regexTel.test(telInput.value.trim())) {
      showMessage(telInput, "Numéro invalide (8 chiffres requis).", "red");
      valid = false;
    }

    // Email
    if (!regexMail.test(emailInput.value.trim())) {
      showMessage(emailInput, "Adresse e-mail invalide.", "red");
      valid = false;
    }

    // Type de besoin (radio)
    const categorie = document.querySelector('input[name="categorie"]:checked');
    if (!categorie) {
      alert("Veuillez sélectionner le type de besoin (matériel ou financier).");
      valid = false;
    }

    // Date de publication
    if (!dateInput.value) {
      showMessage(dateInput, "Veuillez sélectionner une date de publication.", "red");
      valid = false;
    } else {
      const selectedDate = new Date(dateInput.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      if (selectedDate > today) {
        showMessage(dateInput, "La date ne peut pas être dans le futur.", "red");
        valid = false;
      }
    }

    // Personne à contacter
    const contactInput = form.contact;
    if (contactInput.value.trim().length < 3) {
      showMessage(contactInput, "Veuillez indiquer un nom ou une organisation valide.", "red");
      valid = false;
    }

    // Photo (optionnel, mais si sélectionnée, vérifier)
    const file = photoInput.files[0];
    if (file) {
      const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
      const maxSize = 5 * 1024 * 1024; // 5MB
      if (!allowedTypes.includes(file.type)) {
        showMessage(photoInput, "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.", "red");
        valid = false;
      } else if (file.size > maxSize) {
        showMessage(photoInput, "La taille du fichier ne doit pas dépasser 5MB.", "red");
        valid = false;
      }
    }

    if (!valid) {
      e.preventDefault(); // Empêche l’envoi si invalide
    } else {
      alert("✅ Formulaire validé avec succès !");
    }
  });

});
