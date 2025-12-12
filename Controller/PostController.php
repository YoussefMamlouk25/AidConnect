<?php
session_start();
require_once "../config.php";
require_once "../Model/PostModel.php";

$model = new PostModel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {

        // 🚨 TEMP UNTIL MERGE WITH ACCOUNT BRANCH
        $id_user = 1;

        $titre = trim($_POST['titre']);
        $description = trim($_POST['description']);
        $region = trim($_POST['region']);
        $media_path = "";

        // Upload media
        if (!empty($_FILES['media']['name'])) {
            $file = $_FILES['media'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            $allowed = ['jpg', 'jpeg', 'png', 'mp4'];

            if (in_array($ext, $allowed)) {
                $media_path = "uploads/posts/" . uniqid("post_") . "." . $ext;
                move_uploaded_file($file['tmp_name'], "../" . $media_path);
            }
        }

        // Insert Post
        $model->addPost($id_user, $titre, $description, $region, $media_path);

        // Temporary success redirect
        header("Location: ../index.php?success=Post ajouté");
        exit;
    }
}
?>
