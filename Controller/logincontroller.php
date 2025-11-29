<?php
session_start();

require_once '../config.php';
require_once '../Model/UserModel.php';

$model = new UserModel($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/frontoffice/login.php");
    exit;
}

$email    = trim($_POST['email']);
$password = trim($_POST['password']);

$result = $model->login($email, $password);

if ($result['success']) {
    $u = $result['user'];

    $_SESSION['user_id'] = $u['id_user'];
    $_SESSION['name']    = $u['name'];
    $_SESSION['email']   = $u['email'];
    $_SESSION['role']    = $u['role'];

    header("Location: ../View/frontoffice/login_success.php");
    exit;
}

header("Location: ../View/frontoffice/login.php?error=" . urlencode($result['error']));
exit;
