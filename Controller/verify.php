<?php
require_once '../Model/user.php';

$token = $_GET['token'] ?? '';

if ($token === '') {
    die('Token invalide.');
}

$model = new UserModel();

if ($model->verifyEmail($token)) {
    header('Location: ../View/frontoffice/login.php?success=Email vérifié avec succès.');
    exit;
}

die('Lien invalide ou déjà utilisé.');
