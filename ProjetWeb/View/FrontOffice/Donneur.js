document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("donorForm");

  // Helper pour afficher message d'erreur sous l'input
  function showError(input, msg) {
    let span = input.nextElementSibling;
    if (!span || span.tagName !== "SPAN") {
      span = document.createElement("span");
      input.parentNode.appendChild(span);
    }
    span.textContent = msg;
    span.style.color = "red";
    input.classList.add("input-error");
  }

  // Supprimer l'erreur quand l'utilisateur corrige le champ
  function clearError(input) {
    const span = input.nextElementSibling;
    if (span && span.tagName === "SPAN") span.remove();
    input.classList.remove("input-error");
  }

  // ---------------- Validation dynamique ----------------
  const fields = ["lastName","firstName","email","nationalId","phone","age","address","donationMethod"];
  fields.forEach(name => {
    const input = form[name];
    input.addEventListener("input", () => {
      if (input.checkValidity()) clearError(input);
    });
    input.addEventListener("change", () => {
      if (input.checkValidity()) clearError(input);
    });
  });

  // Validation spéciale pour nationalId
  form.nationalId.addEventListener("input", () => {
    if (!form.nationalId.value || /\s/.test(form.nationalId.value))
      showError(form.nationalId, "Le numéro de carte d'identité ne doit pas contenir d'espaces.");
    else clearError(form.nationalId);
  });

  // Validation spéciale pour phone
  form.phone.addEventListener("input", () => {
    const regexPhone = /^\+?\d{7,15}$/;
    if (!regexPhone.test(form.phone.value))
      showError(form.phone, "Numéro invalide (+216 ** *** ***).");
    else clearError(form.phone);
  });

  // Validation finale au submit
  form.addEventListener("submit", function(e) {
    let valid = true;

    // Last Name
    if (form.lastName.value.trim().length < 2) {
      showError(form.lastName, "Le nom doit contenir au moins 2 caractères.");
      valid = false;
    }

    // First Name
    if (form.firstName.value.trim().length < 2) {
      showError(form.firstName, "Le prénom doit contenir au moins 2 caractères.");
      valid = false;
    }

    // Email
    if (!form.email.checkValidity()) {
      showError(form.email, "Email invalide.");
      valid = false;
    }

    // National ID
    if (!form.nationalId.value || /\s/.test(form.nationalId.value)) {
      showError(form.nationalId, "Le numéro de carte d'identité ne doit pas contenir d'espaces.");
      valid = false;
    }

    // Phone
    const regexPhone = /^\+?\d{7,15}$/;
    if (!regexPhone.test(form.phone.value)) {
      showError(form.phone, "Numéro invalide (+216 ** *** ***).");
      valid = false;
    }

    // Age
    const age = parseInt(form.age.value, 10);
    if (isNaN(age) || age < 0 || age > 130) {
      showError(form.age, "Veuillez entrer un âge valide.");
      valid = false;
    }

    // Address
    if (form.address.value.trim().length < 3) {
      showError(form.address, "Le lieu de résidence doit contenir au moins 3 caractères.");
      valid = false;
    }

    // Donation Method
    if (!form.donationMethod.value) {
      showError(form.donationMethod, "Veuillez choisir une méthode de don.");
      valid = false;
    }

    if (!valid) {
      e.preventDefault();
      return;
    }

    // Sauvegarde des données
    const donorData = {
      lastName: form.lastName.value,
      firstName: form.firstName.value,
      age: form.age.value,
      nationalId: form.nationalId.value,
      email: form.email.value,
      phone: form.phone.value,
      address: form.address.value,
      donationMethod: form.donationMethod.value
    };

    let donors = JSON.parse(localStorage.getItem("donors") || "[]");
    donors.push(donorData);
    localStorage.setItem("donors", JSON.stringify(donors));

    alert("Don enregistré avec succès !");
    form.reset();
  });
});
