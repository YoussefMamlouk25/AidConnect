<?php
require_once '../../config.php';
require_once '../../Controller/postupdatecontroller.php';

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

// Si ce n'est pas AJAX, afficher la page complète avec sidebar
if (!$isAjax) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression de mise à jour post</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
<?php
}
?>

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
<?php if (!$isAjax): ?>
    </div>
</div>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
<?php endif; ?>
