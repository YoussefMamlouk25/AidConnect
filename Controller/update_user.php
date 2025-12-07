<?php
session_start();

require_once '../Model/user.php';

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
$password = trim($_POST['password'] ?? '');
$password_confirm = trim($_POST['password_confirm'] ?? '');

if ($id <= 0 || $name === '' || $email === '') {
    header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Champs requis manquants.'));
    exit;
}

// Validate password if provided
if (!empty($password)) {
    if (strlen($password) < 6) {
        header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Le mot de passe doit contenir au moins 6 caractères.'));
        exit;
    }
    
    if ($password !== $password_confirm) {
        header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Les mots de passe ne correspondent pas.'));
        exit;
    }
}

$userModel = new UserModel();
$userModel->updateUserByAdmin($id, $name, $email, $tel, $region, $role);

// Update password if provided (store in plain text for admin viewing)
if (!empty($password)) {
    $userModel->changePasswordPlainText($id, $password);
}

header('Location: ../View/backoffice/users.php?updated=1');
exit;
