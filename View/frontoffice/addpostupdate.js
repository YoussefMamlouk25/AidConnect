document.addEventListener("DOMContentLoaded", function() {

  const form = document.querySelector("form");

  // 🔹 Ajouter dynamiquement les placeholders
  form.titre.placeholder = "Ex : Problème résolu dans l'école X";
  form.description.placeholder = "Décris le problème résolu...";
  form.intervention.placeholder = "Personne ou organisation ayant intervenu";
  form.joursReponse.placeholder = "Ex : 1";
  form.joursResolution.placeholder = "Ex : 1";

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

  // 🔹 Description
  const descInput = form.description;
  descInput.addEventListener("keyup", () => {
    if (descInput.value.trim().length < 10)
      showMessage(descInput, "La description doit contenir au moins 10 caractères.", "red");
    else
      showMessage(descInput, "Description correcte", "green");
  });

  // 🔹 Ville
  const villeSelect = form.ville;
  villeSelect.addEventListener("change", () => {
    if (!villeSelect.value)
      showMessage(villeSelect, "Veuillez choisir une ville.", "red");
    else
      showMessage(villeSelect, "Ville sélectionnée", "green");
  });

  // 🔹 Intervention
  const interventionInput = form.intervention;
  interventionInput.addEventListener("keyup", () => {
    if (interventionInput.value.trim().length < 3)
      showMessage(interventionInput, "Veuillez indiquer un nom ou organisation valide.", "red");
    else
      showMessage(interventionInput, "Nom correct", "green");
  });

  // 🔹 Temps pour répondre (minimum 1)
  const joursReponseInput = form.joursReponse;
  joursReponseInput.addEventListener("blur", () => {
    const val = parseInt(joursReponseInput.value);
    if (isNaN(val) || val < 1)
      showMessage(joursReponseInput, "Veuillez entrer un nombre de jours valide (minimum 1).", "red");
    else
      showMessage(joursReponseInput, "Valeur valide", "green");
  });

  // 🔹 Temps pour résoudre (minimum 1)
  const joursResolutionInput = form.joursResolution;
  joursResolutionInput.addEventListener("blur", () => {
    const val = parseInt(joursResolutionInput.value);
    if (isNaN(val) || val < 1)
      showMessage(joursResolutionInput, "Veuillez entrer un nombre de jours valide (minimum 1).", "red");
    else
      showMessage(joursResolutionInput, "Valeur valide", "green");
  });

  // ---------------- Validation finale lors du submit ----------------
  form.addEventListener("submit", function(e) {
    let valid = true;

    // Titre
    if (titreInput.value.trim().length < 3) {
      showMessage(titreInput, "Le titre doit contenir au moins 3 caractères.", "red");
      valid = false;
    }

    // Description
    if (descInput.value.trim().length < 10) {
      showMessage(descInput, "La description doit contenir au moins 10 caractères.", "red");
      valid = false;
    }

    // Ville
    if (!villeSelect.value) {
      showMessage(villeSelect, "Veuillez choisir une ville.", "red");
      valid = false;
    }

    // Intervention
    if (interventionInput.value.trim().length < 3) {
      showMessage(interventionInput, "Veuillez indiquer un nom ou organisation valide.", "red");
      valid = false;
    }

    // Temps pour répondre
    const valReponse = parseInt(joursReponseInput.value);
    if (isNaN(valReponse) || valReponse < 1) {
      showMessage(joursReponseInput, "Veuillez entrer un nombre de jours valide (minimum 1).", "red");
      valid = false;
    }

    // Temps pour résoudre
    const valResolution = parseInt(joursResolutionInput.value);
    if (isNaN(valResolution) || valResolution < 1) {
      showMessage(joursResolutionInput, "Veuillez entrer un nombre de jours valide (minimum 1).", "red");
      valid = false;
    }

    if (!valid) {
      e.preventDefault(); // Empêche l’envoi si invalide
    } else {
      alert("✅ Post résolu publié avec succès !");
    }
  });

});
