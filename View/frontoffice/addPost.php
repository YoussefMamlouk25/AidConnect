<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Veuillez vous connecter");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Post - AidConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f6f9ff; }
        .card {
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .btn-main {
            border-radius: 12px;
        }
    </style>
</head>

<body>

<div class="container mt-5">

    <div class="card p-4 mx-auto" style="max-width: 620px;">
        <h3 class="text-center text-primary mb-4">
            <i class="bi bi-file-earmark-plus"></i> Créer un Post
        </h3>

        <form action="../../Controller/PostController.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id_user" value="<?= $_SESSION['user_id'] ?>">

            <div class="mb-3">
                <label class="form-label">Titre</label>
                <input type="text" class="form-control" name="titre" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Région</label>
                <select class="form-select" name="region" required>
                    <option selected disabled>Choisir une région</option>
                    <option>Tunis</option>
                    <option>Ariana</option>
                    <option>Ben Arous</option>
                    <option>Manouba</option>
                    <option>Nabeul</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Media (image ou vidéo)</label>
                <input type="file" class="form-control" name="media">
            </div>

            <button type="submit" class="btn btn-primary btn-main w-100">
                <i class="bi bi-check-circle"></i> Publier
            </button>

        </form>
    </div>

</div>

</body>
</html>
