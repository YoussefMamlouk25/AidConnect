<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontoffice/login.php?error=Accès refusé");
    exit;
}

require_once '../../config.php';

$stmt = $conn->query("SELECT * FROM user ORDER BY id_user DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .pfp {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0d6efd;
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-4 text-primary">Gestion des utilisateurs</h2>

    <table class="table table-bordered table-striped shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>Photo</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Région</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $u): ?>

                <?php
                    // PROFILE PIC PATH
                    if (!empty($u['profile_pic'])) {
                        // Example: uploads/profile/user_7_1764515787.png
                        $pic = "../../" . $u['profile_pic'];
                    } else {
                        // Default picture
                        $pic = "../frontoffice/assets/default_profile.png";
                    }
                ?>

                <tr>

                    <!-- PROFILE PIC -->
                    <td>
                        <img src="<?= $pic ?>" class="pfp">
                    </td>

                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['tel']) ?></td>
                    <td><?= htmlspecialchars($u['region']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>

                    <!-- ACTION BUTTONS -->
                    <td>
                        <a href="edit_user.php?id=<?= $u['id_user'] ?>" 
                           class="btn btn-sm btn-warning">Modifier</a>

                        <a href="../../Controller/delete_user.php?id=<?= $u['id_user'] ?>"
                           onclick="return confirm('Supprimer cet utilisateur ?')"
                           class="btn btn-sm btn-danger">Supprimer</a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
