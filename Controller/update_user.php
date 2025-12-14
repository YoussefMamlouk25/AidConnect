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

if ($id <= 0 || $name === '' || $email === '') {
    header('Location: ../View/backoffice/edit_user.php?id=' . $id . '&error=' . urlencode('Champs requis manquants.'));
    exit;
}

$userModel = new UserModel();
$userModel->updateUserByAdmin($id, $name, $email, $tel, $region, $role);

header('Location: ../View/backoffice/users.php?updated=1');
exit;
