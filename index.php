<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AidConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .btn-main {
            border-radius: 50px;
            padding: 5px 15px;
        }

        /* Hero Styling */
        .hero-title {
            font-size: 2.6rem;
            font-weight: 700;
            color: #0057D9;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: #6c757d;
        }

        .feature-card {
            transition: 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 22px rgba(0,0,0,0.10);
        }
    </style>
</head>

<body>

<!-- 🔵 NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">
    <a class="navbar-brand text-primary" href="index.php">AidConnect</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a class="btn btn-outline-primary btn-main mx-1" href="View/frontoffice/showPosts.php">Posts</a></li>
            <li class="nav-item"><a class="btn btn-outline-primary btn-main mx-1" href="View/frontoffice/showResolu.php">Posts Résolus</a></li>
            <li class="nav-item"><a class="btn btn-outline-primary btn-main mx-1" href="View/frontoffice/showOffers.php">Offres</a></li>
            <li class="nav-item"><a class="btn btn-outline-primary btn-main mx-1" href="View/frontoffice/showUpdates.php">Updates Offres</a></li>
        </ul>

        <!-- Right Buttons -->
        <div class="d-flex">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="View/frontoffice/profile.php" class="btn btn-primary btn-main mx-1">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
                <a href="Controller/logout.php" class="btn btn-danger btn-main mx-1">Logout</a>

            <?php else: ?>
                <a href="View/frontoffice/login.php" class="btn btn-primary btn-main mx-1">Login</a>
                <a href="View/frontoffice/signup.php" class="btn btn-success btn-main mx-1">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>



<!-- 🔵 HERO SECTION -->
<div class="container mt-5">
    <div class="text-center">

        <h1 class="hero-title">
            AidConnect – La solidarité moderne, simple et rapide.
        </h1>

        <p class="mt-3 hero-subtitle">
            Une plateforme citoyenne où chacun peut demander de l'aide, proposer des services 
            et contribuer au bien-être de sa communauté.
            Ensemble, nous créons un espace d’entraide sécurisé et efficace.
        </p>

        <!-- Feature Cards -->
        <div class="row mt-5 justify-content-center">

            <div class="col-md-3 mb-3">
                <div class="p-4 shadow-sm rounded bg-white h-100 feature-card">
                    <h5 class="text-primary fw-semibold">📌 Publiez un besoin</h5>
                    <p class="text-muted mb-0">Postez une demande et trouvez quelqu’un pour vous aider rapidement.</p>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-4 shadow-sm rounded bg-white h-100 feature-card">
                    <h5 class="text-primary fw-semibold">🤝 Aidez d'autres citoyens</h5>
                    <p class="text-muted mb-0">Proposez vos compétences ou votre temps pour soutenir votre communauté.</p>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-4 shadow-sm rounded bg-white h-100 feature-card">
                    <h5 class="text-primary fw-semibold">🔔 Suivez les mises à jour</h5>
                    <p class="text-muted mb-0">Recevez les dernières informations sur les demandes, offres et résolutions.</p>
                </div>
            </div>

        </div>



    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
