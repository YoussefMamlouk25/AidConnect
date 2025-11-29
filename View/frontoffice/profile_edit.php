<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Veuillez vous connecter");
    exit;
}

require_once '../../config.php';

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$profilePic = $user['profile_pic'] ? $user['profile_pic'] : "assets/default_profile.png";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .profile-pic {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0d6efd;
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4" style="max-width:700px; margin:auto;">
        <h2 class="text-primary mb-3">Modifier mon profil</h2>

        <div class="text-center mb-4">
            <img src="<?= htmlspecialchars($profilePic) ?>" class="profile-pic">
        </div>

        <form action="../../Controller/profile_update_controller.php" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Nom complet</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="tel" value="<?= htmlspecialchars($user['tel']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">CIN</label>
                <input type="text" name="cin" value="<?= htmlspecialchars($user['cin']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Région</label>
                <input type="text" name="region" value="<?= htmlspecialchars($user['region']) ?>" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Nouvelle photo de profil (optionnel)</label>
                <input type="file" name="profile_pic" class="form-control" accept="image/*">
            </div>

            <div class="d-flex justify-content-between">
                <a href="profile.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>

        </form>
    </div>

</div>

</body>
</html>
