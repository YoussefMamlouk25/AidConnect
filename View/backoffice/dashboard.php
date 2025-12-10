<?php
session_start();

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontoffice/login.php?error=Accès refusé");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard – AidConnect</title>

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
            display: flex;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            height: 100vh;
            background: white;
            border-right: 1px solid #e2e6ef;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
        }

        .sidebar h4 {
            font-weight: 700;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            padding: 12px 15px;
            color: #444;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .sidebar a:hover {
            background: #eef3ff;
            color: #2563eb;
        }

        /* MAIN CONTENT */
        .main {
            margin-left: 260px;
            padding: 25px;
            width: 100%;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        iframe {
            width: 100%;
            height: 85vh;
            border: none;
            border-radius: 12px;
            background: white;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>AidConnect</h4>

    <a onclick="loadPage('dashboard_home.php')">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a onclick="loadPage('users.php')">
        <i class="bi bi-people"></i> Utilisateurs
    </a>

    <a onclick="loadPage('posts.php')">
        <i class="bi bi-file-text"></i> Posts
    </a>

    <a onclick="loadPage('offers.php')">
        <i class="bi bi-geo-alt"></i> Offres
    </a>

    <a onclick="loadPage('resolved.php')">
        <i class="bi bi-check-circle"></i> Posts Résolus
    </a>

    <a onclick="loadPage('updates.php')">
        <i class="bi bi-pencil-square"></i> Updates Offres
    </a>

    <a onclick="loadPage('claims.php')">
        <i class="bi bi-chat-dots"></i> Réclamations
    </a>

    <a onclick="loadPage('events.php')">
        <i class="bi bi-calendar-event"></i> Événements
    </a>
</div>

<!-- MAIN AREA -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <h2>Panneau d'administration</h2>
        <div class="search-bar">
            <input type="text" class="form-control" placeholder="Rechercher...">
        </div>
    </div>

    <!-- IFRAME CONTENT LOADER -->
    <iframe id="contentFrame" src="dashboard_home.php"></iframe>

</div>


<!-- JS -->
<script>
function loadPage(page) {
    document.getElementById('contentFrame').src = page;
}
</script>

</body>
</html>
