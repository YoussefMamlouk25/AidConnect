<?php
session_start();
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - AidConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card p-4 shadow" style="width:450px;">

        <h3 class="text-center mb-3">Créer un compte</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form id="signupForm" action="../../Controller/signupcontroller.php" method="POST">

            <label class="form-label">Nom complet</label>
            <input type="text" name="name" class="form-control mb-3" >

            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-control mb-3" >

            <label class="form-label">CIN</label>
            <input type="text" name="cin" class="form-control mb-3" >

            <label class="form-label">Date de naissance</label>
            <input type="date" name="date_naissance" class="form-control mb-3" >

            <label class="form-label">Téléphone</label>
            <input type="text" name="tel" class="form-control mb-3" >

            <label class="form-label">Région</label>
            <select name="region" class="form-control mb-3" >
                <option value="">-- Sélectionnez une région --</option>
                <option value="Tunis">Tunis</option>
                <option value="Ariana">Ariana</option>
                <option value="Ben Arous">Ben Arous</option>
                <option value="Manouba">Manouba</option>
                <option value="Nabeul">Nabeul</option>
                <option value="Zaghouan">Zaghouan</option>
                <option value="Bizerte">Bizerte</option>
                <option value="Béja">Béja</option>
                <option value="Jendouba">Jendouba</option>
                <option value="Le Kef">Le Kef</option>
                <option value="Siliana">Siliana</option>
                <option value="Sousse">Sousse</option>
                <option value="Monastir">Monastir</option>
                <option value="Mahdia">Mahdia</option>
                <option value="Sfax">Sfax</option>
                <option value="Kairouan">Kairouan</option>
                <option value="Kasserine">Kasserine</option>
                <option value="Sidi Bouzid">Sidi Bouzid</option>
                <option value="Gabès">Gabès</option>
                <option value="Medenine">Medenine</option>
                <option value="Tataouine">Tataouine</option>
                <option value="Gafsa">Gafsa</option>
                <option value="Tozeur">Tozeur</option>
                <option value="Kebili">Kebili</option>
            </select>

            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control mb-4" >

            <button class="btn btn-primary w-100">Créer un compte</button>
        </form>

    </div>
</div>

<script src="js/control.js"></script>

</body>
</html>
