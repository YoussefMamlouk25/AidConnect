<?php
session_start();

require_once '../config.php';
require_once '../Model/UserModel.php';

$model = new UserModel($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/frontoffice/signup.php");
    exit;
}

$name           = $_POST['name'];
$email          = $_POST['email'];
$cin            = $_POST['cin'];
$date_naissance = $_POST['date_naissance'];
$password       = $_POST['password'];
$tel            = $_POST['tel'];
$region         = $_POST['region'];

$result = $model->signup($name, $email, $cin, $date_naissance, $password, $tel, $region);

if ($result['success']) {
    header("Location: ../View/frontoffice/login.php?success=" . urlencode("Compte créé ! Vérifiez votre email."));
    exit;
}

header("Location: ../View/frontoffice/signup.php?error=" . urlencode($result['error']));
exit;
