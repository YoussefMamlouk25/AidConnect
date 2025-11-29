<?php
session_start();

require_once '../config.php';
require_once '../Model/UserModel.php';

$model = new UserModel($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/frontoffice/signup.php");
    exit;
}

$name           = trim($_POST['name']);
$email          = trim($_POST['email']);
$cin            = trim($_POST['cin']);
$date_naissance = trim($_POST['date_naissance']);
$password       = trim($_POST['password']);
$tel            = trim($_POST['tel']);
$region         = trim($_POST['region']);

$result = $model->signup($name, $email, $cin, $date_naissance, $password, $tel, $region);

if ($result['success']) {
    header("Location: ../View/frontoffice/login.php?success=Compte créé");
    exit;
}

header("Location: ../View/frontoffice/signup.php?error=" . urlencode($result['error']));
exit;
