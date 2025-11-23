<?php
session_start();

require_once '../config.php';
require_once '../Model/user.php';

// Connexion depuis la classe Config
$conn = Config::getConnexion();

// Model
$userModel = new UserModel($conn);

// ========================== LOGIN ==========================
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // On récupère l'utilisateur
    $user = $userModel->login($email);

    // Vérifier password hashé
    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = $user;

        header("Location: ../View/frontoffice/showPosts.php");
        exit;
    } else {
        header("Location: ../View/frontoffice/login.php?error=1");
        exit;
    }
}

// ========================== LOGOUT ==========================
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../View/frontoffice/login.php");
    exit;
}
