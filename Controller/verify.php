<?php
require_once '../config.php';

if (!isset($_GET['token'])) {
    die("Token manquant");
}

$token = $_GET['token'];

// Récupérer l'utilisateur avec ce token
$stmt = $conn->prepare("SELECT * FROM user WHERE verify_token = ?");
$stmt->execute([$token]);

if ($stmt->rowCount() == 0) {
    die("Lien invalide ou expiré");
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Mettre verified = 1
$update = $conn->prepare("UPDATE user SET verified = 1, verify_token = NULL WHERE id_user = ?");
$update->execute([$user['id_user']]);

// Redirection automatique vers login
header("refresh:4;url=../View/frontoffice/login.php?success=" . urlencode("Votre compte est maintenant vérifié !"));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Vérification réussie</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card p-4 shadow text-center" style="max-width:450px;">
        <h2 class="text-success">✔ Compte vérifié</h2>
        <p class="text-muted">Vous serez redirigé dans quelques secondes...</p>
        <a href="../View/frontoffice/login.php" class="btn btn-primary w-100">Aller au login maintenant</a>
    </div>
</div>

</body>
</html>
