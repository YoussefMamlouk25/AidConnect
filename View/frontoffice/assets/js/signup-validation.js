document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signupForm');
    if (!form) {
        return;
    }

    form.addEventListener('submit', function (e) {
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const cin = form.cin.value.trim();
        const tel = form.tel.value.trim();
        const dateNaissance = form.date_naissance.value;
        const region = form.region.value;
        const password = form.password.value.trim();
        const confirm = form.confirm.value.trim();

        if (name.length < 3) {
            e.preventDefault();
            alert('Le nom doit contenir au moins 3 caractères');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            alert('Veuillez saisir un email valide');
            return;
        }

        if (cin !== '' && !/^\d{8}$/.test(cin)) {
            e.preventDefault();
            alert('Le CIN doit contenir exactement 8 chiffres');
            return;
        }

        if (tel !== '' && !/^\d{8}$/.test(tel)) {
            e.preventDefault();
            alert('Le numéro de téléphone doit contenir exactement 8 chiffres');
            return;
        }

        if (dateNaissance) {
            const dob = new Date(dateNaissance);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            if (age < 18) {
                e.preventDefault();
                alert('Vous devez avoir au moins 18 ans');
                return;
            }
        }

        if (!region) {
            e.preventDefault();
            alert('Veuillez sélectionner une région');
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 6 caractères');
            return;
        }

        if (password !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas');
            return;
        }
    });
});
