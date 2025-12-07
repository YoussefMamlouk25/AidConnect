<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Veuillez vous connecter.");
    exit;
}

require_once '../../config.php';

// Fetch user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}

// CORRECT PROFILE PIC PATH
if (!empty($user['profile_pic'])) {
    $profilePic = "../../" . $user['profile_pic'];
} else {
    $profilePic = "assets/default_profile.png";
}

// FIX created_at (in your DB you have created_at, not date_creation)
$memberSince = !empty($user['created_at']) ? $user['created_at'] : "Non spécifié";

// FIX role (avoid empty role)
$role = !empty($user['role']) ? $user['role'] : "Utilisateur";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - AidConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-pic {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0d6efd;
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4" style="max-width:700px;margin:auto;">

        <!-- PROFILE PICTURE -->
        <div class="text-center mb-3">
            <img src="<?= $profilePic ?>" class="profile-pic">
        </div>

        <h2 class="text-center"><?= htmlspecialchars($user['name']) ?></h2>
        <p class="text-center text-muted">Membre depuis : <?= htmlspecialchars($memberSince) ?></p>

        <hr>

        <table class="table">
            <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
            <tr><th>Téléphone</th><td><?= htmlspecialchars($user['tel']) ?></td></tr>
            <tr><th>CIN</th><td><?= htmlspecialchars($user['cin']) ?></td></tr>
            <tr><th>Région</th><td><?= htmlspecialchars($user['region']) ?></td></tr>
            <tr><th>Date de naissance</th><td><?= htmlspecialchars($user['date_naissance']) ?></td></tr>
            <tr><th>Rôle</th><td><?= htmlspecialchars($role) ?></td></tr>
        </table>

        <div class="d-flex justify-content-between mt-4">
            <a href="../../index.php" class="btn btn-secondary">Retour</a>
            <a href="profile_edit.php" class="btn btn-primary">Modifier mon profil</a>
        </div>

    </div>

</div>

</body>
</html>
