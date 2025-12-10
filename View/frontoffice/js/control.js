document.getElementById('signupForm').addEventListener('submit', function(e) {
    const f = e.target;

    const name = f.name.value.trim();
    const email = f.email.value.trim();
    const cin = f.cin.value.trim();
    const tel = f.tel.value.trim();
    const dateNaissance = f.date_naissance.value;
    const region = f.region.value;
    const password = f.password.value.trim();

    // NAME > 10
    if (name.length <= 10) {
        e.preventDefault();
        alert("Le nom doit contenir plus de 10 caractères");
        return;
    }

    // EMAIL must contain @ and .com
    if (!email.includes("@") || !email.endsWith(".")) {
        e.preventDefault();
        alert("L'email doit contenir '@' et se terminer par '.'");
        return;
    }

    // CIN = 8 digits
    if (!/^\d{8}$/.test(cin)) {
        e.preventDefault();
        alert("Le CIN doit contenir exactement 8 chiffres");
        return;
    }

    // TEL = 8 digits
    if (!/^\d{8}$/.test(tel)) {
        e.preventDefault();
        alert("Le numéro de téléphone doit contenir exactement 8 chiffres");
        return;
    }

    // DATE NAISSANCE >= 18 years
    if (!dateNaissance) {
        e.preventDefault();
        alert("Veuillez entrer votre date de naissance");
        return;
    }

    const dob = new Date(dateNaissance);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }

    if (age < 18) {
        e.preventDefault();
        alert("Vous devez avoir au moins 18 ans");
        return;
    }

    // REGION must be selected
    if (!region) {
        e.preventDefault();
        alert("Veuillez sélectionner une région");
        return;
    }

    // PASSWORD minimal rule (optional)
    if (password.length < 6) {
        e.preventDefault();
        alert("Le mot de passe doit contenir au moins 6 caractères");
        return;
    }
});
