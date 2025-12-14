<?php
session_start();
//f
require_once '../config.php';
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

    // Vérifier si l'email est vérifié
    if (!isset($user['email_verified']) || $user['email_verified'] != 1) {
        // Stocker l'email en session pour la page de vérification
        $_SESSION['verification_email'] = $email;
        header('Location: ../View/frontoffice/verify_email.php?email=' . urlencode($email) . '&error=' . urlencode('Veuillez vérifier votre email avant de vous connecter.'));
        exit;
    }

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
