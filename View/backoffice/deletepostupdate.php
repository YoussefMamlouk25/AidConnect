<?php
require_once '../../config.php';
require_once '../../Controller/postupdatecontroller.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression de mise à jour post</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">

<?php
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $postUpdateC = new PostUpdateController();
    $postUpdateC->deletePostUpdate($id);
    header('Location: showpostupdateback.php');
    exit();
} else {
    echo "ID du post résolu non fourni.";
}
?>
    </div>
</div>
    <script src="dashboard.js"></script>
</body>
</html>
