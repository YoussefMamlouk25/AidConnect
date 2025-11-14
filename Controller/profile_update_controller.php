<?php
session_start();

require_once '../config.php';
require_once '../Model/user.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ../View/frontoffice/login.php?error=' . urlencode('Veuillez vous connecter.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/frontoffice/profile_edit.php');
    exit;
}

$userId = (int) $_SESSION['id_user'];

$name           = trim($_POST['name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$tel            = trim($_POST['tel'] ?? '');
$cin            = trim($_POST['cin'] ?? '');
$region         = trim($_POST['region'] ?? '');
$dateNaissance  = trim($_POST['date_naissance'] ?? '');
$currentPicture = trim($_POST['current_pic'] ?? '');

if ($name === '' || $email === '') {
    header('Location: ../View/frontoffice/profile_edit.php?error=' . urlencode('Nom et email sont obligatoires.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../View/frontoffice/profile_edit.php?error=' . urlencode('Email invalide.'));
    exit;
}

if ($cin !== '' && !preg_match('/^\\d{8}$/', $cin)) {
    header('Location: ../View/frontoffice/profile_edit.php?error=' . urlencode('Le CIN doit contenir 8 chiffres.'));
    exit;
}

if ($tel !== '' && !preg_match('/^\\d{8}$/', $tel)) {
    header('Location: ../View/frontoffice/profile_edit.php?error=' . urlencode('Le téléphone doit contenir 8 chiffres.'));
    exit;
}

$profilePicPath = $currentPicture !== '' ? $currentPicture : null;

if (!empty($_FILES['profile_pic']['name'])) {
    $targetDir = realpath(__DIR__ . '/../public/uploads/profile');

    if ($targetDir === false) {
        $targetDir = __DIR__ . '/../public/uploads/profile';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
    }

    $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        header('Location: ../View/frontoffice/profile_edit.php?error=' . urlencode('Format d\'image non supporté.'));
        exit;
    }

    $filename    = 'user_' . $userId . '_' . time() . '.' . $ext;
    $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
        header('Location: ../View/frontoffice/profile_edit.php?error=' . urlencode('Échec du téléchargement de la photo.'));
        exit;
    }

    $profilePicPath = 'public/uploads/profile/' . $filename;
}

$userModel = new UserModel();
$updated   = $userModel->updateProfile(
    $userId,
    $name,
    $email,
    $tel,
    $region,
    $cin,
    $dateNaissance,
    $profilePicPath
);

if ($updated) {
    $_SESSION['name'] = $name;
    header('Location: ../View/frontoffice/profile.php?success=' . urlencode('Profil mis à jour.'));
    exit;
}

header('Location: ../View/frontoffice/profile_edit.php?error=' . urlencode('Impossible de mettre à jour le profil.'));
exit;
