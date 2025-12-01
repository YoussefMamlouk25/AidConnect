<?php
session_start();

<<<<<<< HEAD
require_once '../Model/user.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/frontoffice/login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../View/frontoffice/login.php?error=' . urlencode('Veuillez remplir tous les champs.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../View/frontoffice/login.php?error=' . urlencode('Email invalide.'));
    exit;
}

$userModel = new UserModel();
$result    = $userModel->login($email, $password);

if ($result['success']) {
    $user = $result['user'];

    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'] ?? 'volunteer';

    header('Location: ../View/frontoffice/login_success.php');
    exit;
}

header('Location: ../View/frontoffice/login.php?error=' . urlencode($result['error'] ?? 'Connexion impossible.'));
exit;
=======
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
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
