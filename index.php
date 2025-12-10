<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AidConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to bottom right, #f6f9ff, #eef2f7);
        }

        .navbar-brand {
            font-weight: 600;
        }

        .btn-main {
            border-radius: 50px;
            padding: 5px 15px;
        }

        /* Hero Title */
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            color: #1f64e0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.05);
            animation: fadeIn 1s ease-out;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #657184;
            max-width: 760px;
            margin: 15px auto 0;
            animation: fadeIn 1.2s ease-out;
        }

        /* Feature Cards */
        .feature-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 35px 25px;
            border: 1px solid #e0e5ef;
            transition: all 0.35s ease;
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.14);
            border-color: #b9c6ff;
        }

        /* Icon Style */
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: white;
            margin: auto;
        }

        /* Blue gradient for icons */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #3c8bfd, #1a62e8);
            box-shadow: 0 6px 14px rgba(44, 108, 255, 0.35);
        }

        /* Fade-in Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
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

        <div class="d-flex">
            <?php if (isset($_SESSION['user_id'])): ?>

                <!-- ADMIN ONLY BUTTON -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="View/backoffice/users.php" class="btn btn-warning btn-main mx-1">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                <?php endif; ?>

                <!-- USER BUTTONS -->
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
<div class="container mt-5 mb-5">
    <div class="text-center">

        <h1 class="hero-title">
            AidConnect – La solidarité moderne, simple et rapide.
        </h1>

        <p class="hero-subtitle">
            Une plateforme citoyenne où chacun peut demander de l'aide, proposer des services 
            et contribuer au bien-être de sa communauté. Ensemble, nous créons un espace 
            d’entraide sécurisé, moderne et efficace.
        </p>

        <!-- Feature Cards -->
        <div class="row mt-5 justify-content-center">

            <!-- Card 1 -->
            <div class="col-md-3 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3 bg-gradient-primary">
                        <i class="bi bi-pin-angle-fill"></i>
                    </div>
                    <h5 class="fw-bold text-primary">Publiez un besoin</h5>
                    <p class="text-muted mb-0">
                        Postez une demande et trouvez quelqu’un pour vous aider rapidement.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-3 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3 bg-gradient-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h5 class="fw-bold text-primary">Aidez d'autres citoyens</h5>
                    <p class="text-muted mb-0">
                        Proposez vos compétences ou votre temps pour soutenir votre communauté.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-3 mb-4">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3 bg-gradient-primary">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <h5 class="fw-bold text-primary">Suivez les mises à jour</h5>
                    <p class="text-muted mb-0">
                        Recevez les dernières informations sur les demandes, offres et résolutions.
                    </p>
                </div>
            </div>

        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
