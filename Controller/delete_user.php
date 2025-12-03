<?php
session_start();

require_once '../Model/user.php';

if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $userModel = new UserModel();
    $userModel->deleteUser($id);
}

header('Location: ../View/backoffice/users.php');
exit;
