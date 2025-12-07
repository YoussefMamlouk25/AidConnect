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
    </style>
</head>
<body>

<!-- 🔵 NAVBAR (Même template que showPosts.php) -->
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

        <!-- 🔵 Right Buttons -->
        <div class="d-flex">

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- BUTTON PROFILE SI CONNECTÉ -->
                <a href="View/frontoffice/profile.php" class="btn btn-primary btn-main mx-1">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
                <a href="Controller/logout.php" class="btn btn-danger btn-main mx-1">Logout</a>

            <?php else: ?>
                <!-- BUTTON LOGIN SI NON CONNECTÉ -->
                <a href="View/frontoffice/login.php" class="btn btn-primary btn-main mx-1">Login</a>
                <a href="View/frontoffice/signup.php" class="btn btn-success btn-main mx-1">Sign Up</a>
            <?php endif; ?>

        </div>
    </div>
</nav>

<!-- 🔵 PAGE CONTENT -->
<div class="container mt-5">
    <div class="text-center">
        <h1 class="text-primary">Bienvenue sur AidConnect</h1>
        <p class="lead text-muted mt-2">La plateforme d'entraide entre citoyens.</p>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
