<?php
session_start();

require_once '../Model/user.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/frontoffice/login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$model = new UserModel();
$result = $model->login($email, $password);

if ($result['success']) {
    $user = $result['user'];

    if (!$user['verified']) {
        header('Location: ../View/frontoffice/login.php?error=Veuillez vérifier votre email.');
        exit;
    }

    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];

    header('Location: ../View/frontoffice/login_success.php');
    exit;
}

header('Location: ../View/frontoffice/login.php?error=' . urlencode($result['error']));
exit;
