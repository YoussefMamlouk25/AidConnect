<?php
session_start();
//ff
require_once '../Model/user.php';

// Only admin can access
if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/backoffice/users.php');
    exit;
}

$id     = (int) ($_POST['id_user'] ?? 0);
$name   = trim($_POST['name'] ?? '');
$email  = trim($_POST['email'] ?? '');
$tel    = trim($_POST['tel'] ?? '');
$region = trim($_POST['region'] ?? '');
$role   = trim($_POST['role'] ?? 'volunteer');
$role   = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';

$password = trim($_POST['password'] ?? '');
$password_confirm = trim($_POST['password_confirm'] ?? '');

if ($id <= 0 || $name === '' || $email === '') {
    header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Champs requis manquants.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Email invalide.'));
    exit;
}

// Validate password if provided
if ($password !== '') {
    if (strlen($password) < 6) {
        header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Mot de passe trop court.'));
        exit;
    }

    if ($password !== $password_confirm) {
        header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Les mots de passe ne correspondent pas.'));
        exit;
    }
}

$userModel = new UserModel();

// Update basic info
$userModel->updateUserByAdmin($id, $name, $email, $tel, $region, $role);

// Update password if provided (HASHED)
if ($password !== '') {
    $userModel->changePassword($id, $password);
}

header('Location: ../View/backoffice/users.php?updated=1');
exit;
