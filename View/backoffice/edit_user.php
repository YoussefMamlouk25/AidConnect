<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontoffice/login.php?error=Accès refusé");
    exit;
}

require_once '../../config.php';

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width:600px;">

    <h3 class="text-primary mb-3">Modifier utilisateur</h3>

    <form action="../../Controller/update_user.php" method="POST">

        <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

        <label>Nom</label>
        <input class="form-control mb-2" type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">

        <label>Email</label>
        <input class="form-control mb-2" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

        <label>Téléphone</label>
        <input class="form-control mb-2" type="text" name="tel" value="<?= htmlspecialchars($user['tel']) ?>">

        <label>Région</label>
        <input class="form-control mb-2" type="text" name="region" value="<?= htmlspecialchars($user['region']) ?>">

        <label>Rôle</label>
        <select class="form-control mb-3" name="role">
            <option value="volunteer" <?= $user['role']=='volunteer'?'selected':'' ?>>Volunteer</option>
            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
        </select>

        <button class="btn btn-primary">Enregistrer</button>
        <a href="users.php" class="btn btn-secondary">Annuler</a>
    </form>

</div>

</body>
</html>
