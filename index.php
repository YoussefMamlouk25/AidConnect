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
            background: #f6f9ff;
        }

        #mainContent {
            min-height: 500px;
            animation: fade .3s ease;
        }

        @keyframes fade {
            from { opacity: 0.3; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .click-item {
            cursor: pointer;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">
    <a class="navbar-brand text-primary" style="font-weight:600;" href="#">AidConnect</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">

        <ul class="navbar-nav mx-auto">

            <!-- POSTS DROPDOWN -->
            <li class="nav-item dropdown mx-1">
                <a class="btn btn-outline-primary btn-main dropdown-toggle" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-file-text"></i> Posts
                </a>
                <ul class="dropdown-menu shadow-sm">
                    <li><span class="dropdown-item click-item" onclick="loadPage('View/frontoffice/showPosts.php')">
                        <i class="bi bi-file-earmark-text"></i> Tous les Posts
                    </span></li>
                    <li><span class="dropdown-item click-item" onclick="loadPage('View/frontoffice/showResolu.php')">
                        <i class="bi bi-check-circle"></i> Posts Résolus
                    </span></li>
                </ul>
            </li>

            <!-- OFFRES DROPDOWN -->
            <li class="nav-item dropdown mx-1">
                <a class="btn btn-outline-primary btn-main dropdown-toggle" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-geo-alt"></i> Offres
                </a>
                <ul class="dropdown-menu shadow-sm">
                    <li><span class="dropdown-item click-item" onclick="loadPage('View/frontoffice/showOffers.php')">
                        <i class="bi bi-geo"></i> Toutes les Offres
                    </span></li>
                    <li><span class="dropdown-item click-item" onclick="loadPage('View/frontoffice/showUpdates.php')">
                        <i class="bi bi-pencil-square"></i> Updates Offres
                    </span></li>
                </ul>
            </li>

        </ul>

        <div class="d-flex">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="View/backoffice/dashboard.php" class="btn btn-warning mx-1">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                <?php endif; ?>

                <span class="btn btn-primary mx-1 click-item" onclick="loadPage('View/frontoffice/profile.php')">
                    <i class="bi bi-person-circle"></i> Profile
                </span>

                <a href="Controller/logout.php" class="btn btn-danger mx-1">Logout</a>

            <?php else: ?>
                <a href="View/frontoffice/login.php" class="btn btn-primary mx-1">Login</a>
                <a href="View/frontoffice/signup.php" class="btn btn-success mx-1">Sign Up</a>
            <?php endif; ?>
        </div>

    </div>
</nav>



<!-- MAIN CONTENT CONTAINER -->
<div class="container mt-4">

    <!-- ADD POST BUTTON -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="text-center mb-4">
        <span class="btn btn-primary px-4 py-2 click-item"
              style="border-radius: 50px; font-size: 1.1rem;"
              onclick="loadPage('View/frontoffice/addPost.php')">
            <i class="bi bi-plus-circle"></i> Ajouter un Post
        </span>
    </div>
    <?php endif; ?>

    <!-- THIS DIV CHANGES CONTENT -->
    <div id="mainContent" class="p-4 bg-white shadow-sm rounded-4">
        <h3 class="text-center text-secondary">Bienvenue sur AidConnect 💙</h3>
        <p class="text-center text-muted mt-2">Choisissez une option dans la barre de navigation.</p>
    </div>

</div>


<script>
function loadPage(page) {
    fetch(page)
        .then(response => response.text())
        .then(html => {
            document.getElementById("mainContent").innerHTML = html;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(err => {
            document.getElementById("mainContent").innerHTML =
                "<div class='alert alert-danger'>Erreur de chargement.</div>";
        });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
