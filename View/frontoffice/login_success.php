<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$name = htmlspecialchars($_SESSION['name']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Bienvenue</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.card { border-radius:18px; }
.emoji { font-size:55px; }
</style>
</head>

<body>

<div class="container" style="margin-top:100px; max-width:550px;">
    <div class="card shadow p-5 text-center">
        <div class="emoji mb-3">💙</div>
        <h2 class="mb-3">Bienvenue, <strong><?= $name ?></strong> 🎉</h2>
        <p class="text-muted mb-4">Connexion réussie.</p>
        <a href="../../index.php" class="btn btn-primary w-100">Aller à l'accueil</a>
    </div>
</div>

</body>
</html>
