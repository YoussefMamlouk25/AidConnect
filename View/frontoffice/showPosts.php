<?php
session_start();

// TEMP until merging with account system
$id_user = 1;

require_once "../../config.php";

// Fetch posts with user info
$stmt = $conn->query("
    SELECT p.*, u.name, u.profile_pic 
    FROM posts p
    JOIN user u ON p.id_user = u.id_user
    ORDER BY p.id_post DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tous les Posts - AidConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f0f2f5;
        }

        /* FACEBOOK-LIKE POST CARD */
        .post-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0px 3px 14px rgba(0,0,0,0.10);
            padding: 20px 25px;
            margin: 25px auto;
            max-width: 700px;
        }

        /* Header (User info) */
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .post-user-pic {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 2px solid #1877f2;
        }

        .post-user-name {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }

        .post-region {
            font-size: 0.85rem;
            color: #606770;
        }

        .post-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1877f2;
            margin-top: 8px;
        }

        .post-description {
            font-size: 1.1rem;
            margin-top: 10px;
        }

        /* Beautiful Media */
        .post-media img,
        .post-media video {
            width: 100%;
            max-height: 420px;
            border-radius: 12px;
            object-fit: cover;
            object-position: center;
            background: #000;
            margin-top: 18px;
            box-shadow: 0px 6px 14px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">
    <a class="navbar-brand text-primary" href="../../index.php">AidConnect</a>
</nav>

<div class="container mt-4">

    <h2 class="mb-4 text-primary text-center"><i class="bi bi-file-text"></i> Tous les Posts</h2>

    <?php foreach ($posts as $p): ?>

        <?php
            $profilePic = !empty($p['profile_pic'])
                ? "../../" . $p['profile_pic']
                : "https://cdn-icons-png.flaticon.com/512/847/847969.png"; // default avatar
        ?>

        <div class="post-card">

            <!-- USER HEADER -->
            <div class="post-header">
                <img src="<?= $profilePic ?>" class="post-user-pic">
                <div>
                    <p class="post-user-name"><?= htmlspecialchars($p['name']) ?></p>
                    <p class="post-region"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($p['region']) ?></p>
                </div>
            </div>

            <!-- TITLE -->
            <div class="post-title"><?= htmlspecialchars($p['titre']) ?></div>

            <!-- DESCRIPTION -->
            <div class="post-description">
                <?= nl2br(htmlspecialchars($p['description'])) ?>
            </div>

            <!-- MEDIA -->
            <?php if (!empty($p['media'])): ?>
                <div class="post-media">
                    <?php
                        $ext = strtolower(pathinfo($p['media'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg','jpeg','png'])):
                    ?>
                        <img src="../../<?= $p['media'] ?>">
                    <?php elseif ($ext === 'mp4'): ?>
                        <video controls>
                            <source src="../../<?= $p['media'] ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- OWNER ACTIONS -->
            <div class="mt-3">
                <?php if ($p['id_user'] == $id_user): ?>
                    <a href="editPost.php?id=<?= $p['id_post'] ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>

                    <a href="../../Controller/PostController.php?action=delete&id=<?= $p['id_post'] ?>"
                       onclick="return confirm('Supprimer ce post ?')"
                       class="btn btn-sm btn-danger">
                        <i class="bi bi-trash"></i> Supprimer
                    </a>
                <?php endif; ?>
            </div>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>
