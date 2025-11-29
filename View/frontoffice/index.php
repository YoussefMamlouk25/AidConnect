<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AidConnect - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR (inside index, no header.php needed) -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold text-primary" href="index.php">
            AidConnect
        </a>

        <div class="collapse navbar-collapse">

            <ul class="navbar-nav ms-auto">

                <?php if (!isset($_SESSION['user_id'])): ?>

                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="login.php">Se connecter</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="signup.php">Créer un compte</a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <span class="nav-link fw-bold">Bonjour, <?= htmlspecialchars($_SESSION['name']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="profile.php">Mon profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="../../Controller/logout.php">Déconnexion</a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>

    </div>
</nav>



<!-- HERO SECTION -->
<section class="py-5 text-center bg-light">
    <div class="container">

        <h1 class="display-4 fw-bold text-primary">AidConnect</h1>

        <p class="lead mt-3">
            La plateforme qui connecte ceux qui veulent aider avec ceux qui ont besoin de soutien.
        </p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="signup.php" class="btn btn-primary btn-lg mt-3 me-2">Créer un compte</a>
            <a href="login.php" class="btn btn-outline-primary btn-lg mt-3">Se connecter</a>

        <?php else: ?>
            <a href="showPosts.php" class="btn btn-primary btn-lg mt-3 me-2">Voir les posts</a>
            <a href="profile.php" class="btn btn-outline-primary btn-lg mt-3">Mon profil</a>
        <?php endif; ?>

    </div>
</section>


<!-- FEATURES SECTION -->
<section class="container py-5">
    <div class="row text-center">

        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <h4 class="text-primary">Publier un besoin</h4>
                <p>Postez une demande d’aide et attirez des bénévoles.</p>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="addPost.php" class="btn btn-primary">Créer un post</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary">Connexion</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <h4 class="text-primary">Aider les autres</h4>
                <p>Explorez les offres de bénévoles ou proposez votre aide.</p>
                <a href="volunteer-view.php" class="btn btn-primary">Voir les offres</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <h4 class="text-primary">Communauté</h4>
                <p>Rejoignez un réseau actif de solidarité et d’entraide.</p>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="signup.php" class="btn btn-primary">Rejoindre</a>
                <?php else: ?>
                    <a href="profile.php" class="btn btn-outline-primary">Votre espace</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>



<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    AidConnect © <?= date("Y") ?> — Tous droits réservés.
</footer>

</body>
</html>
