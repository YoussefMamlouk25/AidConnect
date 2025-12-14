<?php
session_start();

require_once '../Model/user.php';
require_once '../Model/EmailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/frontoffice/signup.php');
    exit;
}

$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$cin            = trim($_POST['cin'] ?? '');
$date_naissance = trim($_POST['date_naissance'] ?? '');
$password       = trim($_POST['password'] ?? '');
$confirm        = trim($_POST['confirm'] ?? '');
$tel            = trim($_POST['tel'] ?? '');
$region         = trim($_POST['region'] ?? '');

if ($name === '' || $email === '' || $password === '' || $confirm === '') {
    header('Location: ../View/frontoffice/signup.php?error=' . urlencode('Veuillez remplir les champs obligatoires.'));
    exit;
}

if (strlen($name) < 3) {
    header('Location: ../View/frontoffice/signup.php?error=' . urlencode('Le nom doit contenir au moins 3 caractères.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../View/frontoffice/signup.php?error=' . urlencode('Email invalide.'));
    exit;
}

if ($password !== $confirm) {
    header('Location: ../View/frontoffice/signup.php?error=' . urlencode('Les mots de passe ne correspondent pas.'));
    exit;
}

if ($cin !== '' && !preg_match('/^\d{8}$/', $cin)) {
    header('Location: ../View/frontoffice/signup.php?error=' . urlencode('Le CIN doit contenir exactement 8 chiffres.'));
    exit;
}

if ($tel !== '' && !preg_match('/^\d{8}$/', $tel)) {
    header('Location: ../View/frontoffice/signup.php?error=' . urlencode('Le numéro de téléphone doit contenir exactement 8 chiffres.'));
    exit;
}

if ($date_naissance !== '') {
    $timestamp = strtotime($date_naissance);
    if ($timestamp === false) {
        header('Location: ../View/frontoffice/signup.php?error=' . urlencode('Date de naissance invalide.'));
        exit;
    }
}

$userModel = new UserModel();
$result    = $userModel->signup($name, $email, $password, $cin, $date_naissance, $tel, $region);

if ($result['success']) {
    // Envoyer l'email de vérification
    $emailService = new EmailService();
    $emailSent = $emailService->sendVerificationEmail($email, $name, $result['verification_code']);
    
    // Stocker l'email en session pour la page de vérification
    $_SESSION['verification_email'] = $email;
    $_SESSION['verification_name'] = $name;
    
    // Rediriger vers la page de vérificationee
    $message = $emailSent 
        ? 'Compte créé avec succès ! Un code de vérification a été envoyé à votre email.'
        : 'Compte créé avec succès ! Code de vérification : ' . $result['verification_code'] . ' (Email non envoyé - mode développement)';
    
    header('Location: ../View/frontoffice/verify_email.php?email=' . urlencode($email) . '&message=' . urlencode($message));
    exit;
}

header('Location: ../View/frontoffice/signup.php?error=' . urlencode($result['error'] ?? 'Inscription impossible.'));
exit;
