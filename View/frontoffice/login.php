<?php
session_start();
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - AidConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card p-4 shadow" style="width:400px;">
        
        <h3 class="text-center mb-3">Connexion</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="../../Controller/logincontroller.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" class="form-control mb-3" required>

            <label>Mot de passe</label>
            <input type="password" name="password" class="form-control mb-4" required>

            <button class="btn btn-primary w-100">Se connecter</button>
        </form>

        <p class="mt-3 text-center">
            Pas de compte ? <a href="signup.php">Créer un compte</a>
        </p>

    </div>
</div>

</body>
</html>
