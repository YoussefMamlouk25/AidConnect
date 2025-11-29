<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue</title>
</head>
<body>
    <h2>Bienvenue, <?= htmlspecialchars($_SESSION['name']) ?> !</h2>
    <p>Connexion réussie.</p>
    <a href="../../Controller/logout.php">Se déconnecter</a>
</body>
</html>
