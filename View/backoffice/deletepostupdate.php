<?php
require_once '../../config.php';
require_once '../../Controller/postupdatecontroller.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $postUpdateC = new PostUpdateController();
    $postUpdateC->deletePostUpdate($id);
    header('Location: dashboard.php');
    exit();
} else {
    echo "ID du post résolu non fourni.";
}
?>

