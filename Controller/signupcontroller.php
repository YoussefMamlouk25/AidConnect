<?php
session_start();

require_once '../Model/user.php';
require_once 'send_verification_mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/frontoffice/signup.php');
    exit;
}

$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$password       = trim($_POST['password'] ?? '');
$confirm        = trim($_POST['confirm'] ?? '');
$cin            = trim($_POST['cin'] ?? '');
$date_naissance = trim($_POST['date_naissance'] ?? '');
$tel            = trim($_POST['tel'] ?? '');
$region         = trim($_POST['region'] ?? '');

if ($name === '' || $email === '' || $password === '' || $confirm === '') {
    header('Location: ../View/frontoffice/signup.php?error=Champs obligatoires manquants');
    exit;
}

if ($password !== $confirm) {
    header('Location: ../View/frontoffice/signup.php?error=Les mots de passe ne correspondent pas');
    exit;
}

$token = bin2hex(random_bytes(32));

$model = new UserModel();
$result = $model->signup($name, $email, $password, $cin, $date_naissance, $tel, $region, $token);

if ($result['success']) {
    sendVerificationEmail($email, $name, $token);
    header('Location: ../View/frontoffice/login.php?success=Compte créé. Vérifiez votre email.');
    exit;
}

header('Location: ../View/frontoffice/signup.php?error=' . urlencode($result['error']));
exit;
